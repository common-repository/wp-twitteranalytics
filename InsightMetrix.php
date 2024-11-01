<?php
/*
Plugin Name: InsightMetrix
Description: WP-TwitterAnalytics by InsightMetrix is a real-time Twitter monitoring, data archiving and analytics tool for social data mining.
Author: InsightMetrix
Author URI: http://www.insightmetrix.com
Version: 1.0
*/
define("INSIGHT_METRIX_PLUGIN_VERSION","1.0");
define("INSIGHT_METRIX_PLUGIN_SLUG",plugin_basename( __FILE__ ));
define("INSIGHT_METRIX_URL",plugins_url("",__FILE__ )); #without trailing slash (/)
define("INSIGHT_METRIX_PATH",plugin_dir_path(__FILE__)); #with trailing slash (/)
define("INSIGHT_METRIX_API_URL",INSIGHT_METRIX_URL."/api/insightmetrix-api.php");
global $wpdb; $im_query_table=$wpdb->prefix."im_query"; $im_tweets_table=$wpdb->prefix."im_tweets";

function InsightMetrix_add_page() #Adding Admin menu page
{
add_menu_page( "InsightMetrix", "InsightMetrix", 'manage_options', INSIGHT_METRIX_PLUGIN_SLUG, 'InsightMetrix', INSIGHT_METRIX_URL."/images/icon16.png");
}
add_action('admin_menu','InsightMetrix_add_page');

function InsightMetrix_admin_head() #adding css file to admin <head> section
{
	echo '<link rel="stylesheet" type="text/css" href="' .INSIGHT_METRIX_URL.'/css/styles.css">';
	echo '<link rel="stylesheet" type="text/css" href="' .INSIGHT_METRIX_URL.'/flexigrid/css/flexigrid.css">';
	?>
	<script type="text/javascript" src="<?php echo INSIGHT_METRIX_URL; ?>/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo INSIGHT_METRIX_URL; ?>/js/jquery-ui-1.js"></script>
	<script type="text/javascript" src="<?php echo INSIGHT_METRIX_URL; ?>/js/jquery-corner.js"></script>
	<script type="text/javascript">
	var InsideMatrix_api_save_tweets_url="<?php echo INSIGHT_METRIX_API_URL."?action=save_tweets" ?>"; //This url var is used in js/js.js to calling the search result
	var InsideMatrix_api_Add_Save_Chart_url="<?php echo INSIGHT_METRIX_API_URL."?action=savechart" ?>";
	var InsideMatrix_api_delete_url="<?php echo INSIGHT_METRIX_API_URL."?action=delete" ?>";
	var InsideMatrix_api_get_query_list_url="<?php echo INSIGHT_METRIX_API_URL."?action=get_query_list" ?>";
	var InsideMatrix_api_csv_downlod_url="<?php echo INSIGHT_METRIX_API_URL."?action=csv_downlod" ?>";
	var InsideMatrix_images_dir_url="<?php echo INSIGHT_METRIX_URL; ?>/images";
	var InsideMatrix_selected_query;
	var TwitterStreamingInterval=3000;// 1000=1 second
	var TwitterRecordingInterval=5000;
		
	var chartItemList= new Array("<?php global $im_chartSettings; if(is_array($im_chartSettings["chartItemList"])){echo implode('","' ,$im_chartSettings["chartItemList"]);}?>")
	//var chartItemList=new Array();
	var chartTime=300;
	var FlexiGridAutoRefreshOn=true;
		  $(document).ready(function() {
		  <?php include_once(INSIGHT_METRIX_PATH."js/InsightMetrix-js.php");?>
		  });
		 function AddToChart()
		 {
			 var i=0;
			 var q=$("#selected_query").val();
			 if(q!="")
			 {
			 for(i=0; i<chartItemList.length; i++) {if(chartItemList[i]==q) return;}
			 chartItemList.push(q);
			// alert(q+" is adding to chart..");
			 ReloadChartItemList();
			 }
		 }
		 function DeleteFromChart(id)
		 {
		 chartItemList[id]="";
		 ReloadChartItemList();
		 }
		 function ReloadChartItemList()
		 {
			var i=0;
			var html="<ul>";
			for(i=0; i<chartItemList.length; i++) {
				//myArray[i] = "Do Something Here".
				if(chartItemList[i]!=""){html=html+'<li onclick="DeleteFromChart('+i+')">'+chartItemList[i]+'</li>';}
			}
			html=html+"</ul>";
			$("#chartItemList").html(html);
		 
		 }
		function changeChartTime()
		{
		chartTime= $("#chatTime").val();
		} 
		function SaveChart()
		{
		var i=0; 
		//{"chartTime":300,"queryList":["Review","Barak Obama","Jenifer Lopez"]}
		var qData={"chartTime":chartTime,"chartItemList":[]};
			for(i=0; i<chartItemList.length; i++) 
			{
				if(chartItemList[i]!="")
				{
				qData.chartItemList[i]=chartItemList[i];
				}
			}
			$.ajax({
				url: InsideMatrix_api_Add_Save_Chart_url,
				type: "POST",
				//   dataType : "json",
				//   contentType : 'application/json',
				global: false,
				cache: true,
				async: true,
				data:qData,
				success: function(result){ /*alert(result);alert("Chart Has Been Created");*/ refreshChart();},
				error: function(xhr,errorThrown){}
			});
			
		}
		
		function DeleteRow(id)
		{
		var qData={"tweet_id":id};
			$.ajax({
				url: InsideMatrix_api_delete_url,
				type: "POST",
				//   dataType : "json",
				//   contentType : 'application/json',
				global: false,
				cache: true,
				async: true,
				data:qData,
				success: function(result){refresh_flexiGrid();  /*alert(result);*/ },
				error: function(xhr,errorThrown){}
			});
		}
		
		function DeleteQueryWithTweets()
		{
		var i=0; 
		var queryIdList=$("#queryList").val();
		//{"chartTime":300,"queryList":["Review","Barak Obama","Jenifer Lopez"]}
		if(queryIdList==null)
			{
			alert ("Select Some Query");
			}
			else
			{
			var sure=confirm("Are You Sure You Want To Delete?");
				if(sure)
				{
					var qData={"queryIdList":[]};
					for(i=0; i<queryIdList.length; i++) 
					{
						if(queryIdList[i]!="")
						{
						//alert(queryIdList[i]);
						qData.queryIdList[i]=queryIdList[i];
						}
					}
				
					$.ajax({
						url: InsideMatrix_api_delete_url,
						type: "POST",
						global: false,
						cache: true,
						async: true,
						data:qData,
						success: function(result){alert(result);refresh_queryList(); refresh_flexiGrid()},
						error: function(xhr,errorThrown){}
					});
					
				}
			
			}
		}//end DeleteQueryWithTweets()
		
		function refresh_queryList()
		{
		$("#reload_query_list").addClass('loading');
		$.ajax({
				url: InsideMatrix_api_get_query_list_url,
				type: "POST",
				global: false,
				cache: true,
				async: true,
				/*data:qData,*/
				success: function(result)
					{
					$("#queryList").html(result);
					$("#reload_query_list").removeClass('loading');
					//alert(result);
					
					},
				error: function(xhr,errorThrown){}
			});
		}
		function refresh_flexiGrid()
		{
		$("#flex1").flexReload();
		}
	</script>
	<?php
}
function add_InsightMetrix_admin_head()
{
	if(strpos($_SERVER['REQUEST_URI'], INSIGHT_METRIX_PLUGIN_SLUG)) #to ensure that current plugin page is being shown.
	{
	InsightMetrix_admin_head();
	}
}

add_action('admin_head', 'add_InsightMetrix_admin_head');


$im_chartSettings=get_option('im_chartSettings'); if(!is_array($im_chartSettings)){$im_chartSettings=array();} 


function InsightMetrix()
{

global $wpdb; global $im_chartSettings;
echo "<div style=\"width: 800px; padding: 10px; \" class=\"wrap\">";
echo '<h2><img src="'.INSIGHT_METRIX_URL.'/images/icon32.png"> InsightMetrix</h2>';?>

	<div style="border:1px solid #cccccc; padding:10px; margin-bottom:10px;border-radius: 5px 5px 5px 5px;">
		<div style="float:left; width:50%; text-align:left;">Version: <?php echo INSIGHT_METRIX_PLUGIN_VERSION;?><!-- | By <a href=" http://www.insightmetrix.com" target="_blank">InsightMetrix</a>--></div>
		<div style="float:right; width:50%; text-align:right;">
		<a href="http://www.insightmetrix.com/wptwitteranalyticsfaq.html" target="_blank">FAQ</a>&nbsp;
		<a href="http://www.insightmetrix.com/wptwitteranalyticsuserguide.html" target="_blank">User Guide</a>&nbsp;
		</div>
		<div style="clear:both"></div>
	</div>
<table width="100%" border="1" class="widefat" style="margin-bottom:20px;">
<thead>
<tr><th colspan="2" style="text-align:center">WP-TwitterAnalytics</th></tr>
</thead>
<tbody>
	<tr>
	<td>
	<div class="column ui-sortable"><!-- Left -->
        <div style="border-radius: 5px 5px 5px 5px;" id="panel1" class="panel">
        <h1 style="border-radius: 5px 5px 5px 5px;" class="panelheading"><span class="htext">My Queries</span></h1>

		<div class="panelcontent">
        <table align="center">
        <tbody><tr>
        <td>
        <input name="" class="searchinput" type="text">
        </td>
        <td>
        <input name="" value="Search" class="searchbtn" type="button">
        </td>
        </tr>
        </tbody></table>
        <h2>Monitoring</h2>

        <ul id="twList">

        </ul>
        </div>
		<div style="font-style:italic; padding-left:20px;">Simultaneous Query Limit = 5</div>
        <div class="panelfooter">
			<img src="<?php echo INSIGHT_METRIX_URL; ?>/images/control-record.png" align="absmiddle"> Record Query	
        	<img src="<?php echo INSIGHT_METRIX_URL; ?>/images/control-play.png" align="absmiddle"> Monitor
            <img src="<?php echo INSIGHT_METRIX_URL; ?>/images/control-pause.png" align="absmiddle"> Pause
            <img src="<?php echo INSIGHT_METRIX_URL; ?>/images/control-delete.png" align="absmiddle"> Delete Query
        </div>
        </div>
    </div>
	</td>
	<td style="border-left:1px dashed #E7E7E7;">
	<!-- Right -->
	<div class="column ui-sortable">
        <div style="border-radius: 5px 5px 5px 5px;" id="panel2" class="panel">
        <h1 style="border-radius: 5px 5px 5px 5px;" class="panelheading"><span class="htext">Real-time Monitoring</span></h1>
        <div class="panelcontent" id="tweetContent">		
        </div>
     		<div class="panelfooter" style="text-align:right">
			
            <br>
        </div>
        </div>

    </div>
	</td>
	</tr>
</tbody>
</table>
	
<!--Chart control-->
	<?php include_once(INSIGHT_METRIX_PATH."/chart/ChartControls.php");?>
<!--End Chart control-->
<?php include_once(INSIGHT_METRIX_PATH."flexigrid/FlexiGrid.php"); 
 	  include_once(INSIGHT_METRIX_PATH."includes/DeleteControl.php"); 


echo "</div>";
}#end function InsightMetrix

include_once(INSIGHT_METRIX_PATH."chart/chart.php"); #include chart functions and shortcodes 

function InsightMetrix_install() {
   global $wpdb; $im_query_table=$wpdb->prefix."im_query"; $im_tweets_table=$wpdb->prefix."im_tweets";
  $InsightMetrix_db_version = "1.0";
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');      
   $sql ="
		CREATE TABLE IF NOT EXISTS `".$im_query_table."` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `query` varchar(140) NOT NULL,
		  UNIQUE KEY `id` (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		";
   dbDelta($sql);

   $sql ="
		CREATE TABLE IF NOT EXISTS `".$im_tweets_table."` (
		  `qid` bigint(20) unsigned NOT NULL,
		  `id` bigint(20) unsigned NOT NULL,
		  `user` varchar(15) NOT NULL,
		  `text` varchar(140) NOT NULL,
		  `date` int(10) unsigned NOT NULL,
		  `location` varchar(25) NOT NULL,
		  `language` varchar(2) NOT NULL,
		  UNIQUE KEY `id` (`id`),
		  KEY `date` (`date`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		";
   dbDelta($sql);
 
   add_option("InsightMetrix_db_version", $InsightMetrix_db_version);
}
register_activation_hook(__FILE__,'InsightMetrix_install');
?>