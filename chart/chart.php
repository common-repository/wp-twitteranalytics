<?php 
#http://code.google.com/apis/chart/interactive/docs/gallery/piechart.html
function im_shortcode_func($atts) {
	extract(shortcode_atts(array(
		'type' => 'pie',  #if no keyword found then keyword="default";
		'w'=>450,
		'h'=>300
	), $atts));

	if(!$type)$type="pie";
	//$width=$w; $height=$h;
	$output=show_im_chart($w,$h,false);
	return $output;	
}
add_shortcode('imchart', 'im_shortcode_func');


function show_im_chart($width=450,$height=300,$show=true)
{
if(!$width)$width=450;
if(!$height) $height=300;
global $wpdb, $im_query_table, $im_tweets_table;
	$im_chartSettings=get_option('im_chartSettings'); if(!is_array($im_chartSettings)){return;} 
	$chartTime=intval($im_chartSettings['chartTime']); if(!$chartTime) $chartTime=300;
	$min=$chartTime/60;
	$nowtime=time();
	$fromtime=$nowtime-$chartTime;
	//$query_id = $wpdb->get_var( $wpdb->prepare( "..." ) );
	 if(count($im_chartSettings["chartItemList"]))
	 {
		
		$listInLine=implode("', '" ,$im_chartSettings["chartItemList"]);
		$listInLine="'".$listInLine."'";
		$qlistrows=$wpdb->get_results('SELECT *  from  '.$im_query_table.' where query in ('.$listInLine.')', ARRAY_A);
		$qlist=array();
		$rtqidlist=array();
		foreach($qlistrows as $row)
		{
		$qlist[$row['id']]=$row['query'];
		$rtqidlist[]=$row['id'];
		}
		$rtqidlistline=implode(", ",$rtqidlist);
		#$tweetcountlist=$wpdb->get_results('SELECT qid, count(*) as tcount  from  im_tweets where qid in ('.$rtqidlistline.') and date>'.$fromtime.'  group by qid', ARRAY_A);
		$tweetcountlist=$wpdb->get_results('SELECT qid, count(*) as tcount  from  '.$im_tweets_table.' where qid in ('.$rtqidlistline.')  group by qid', ARRAY_A);

$output.='			
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn(\'string\', \'Query\');
        data.addColumn(\'number\', \'Tweet Count\');
        data.addRows([';
		  $totitem=count($tweetcountlist);$i=0;
		  foreach($tweetcountlist as $tweetcount)
		  {
		  $i++;
          $output.='[\''.$qlist[$tweetcount['qid']].'\',    '.$tweetcount['tcount'].']'; if($i!=$totitem){$output.=',';}#preventing IE issue with last comma.
		  }
          $output.=']);';
			
		$output.='	
        var options = {
          width: '.$width.', height: '.$height.',
		  chartArea:{left:10,top:10,width:"100%",height:"100%"},
          title: \'Insight Metrix Tweet Stat\'
        };

        var chart = new google.visualization.PieChart(document.getElementById(\'chart_div\'));
        chart.draw(data, options);
      }
    </script>
<div id="chart_div"></div>
			';
	if($show){echo $output;}else{return $output;}
	}#end of if(count($qidlist))
	return false;
}#end function show_im_chart()
?>