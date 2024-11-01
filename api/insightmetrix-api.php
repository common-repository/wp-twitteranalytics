<?php
#http://localhost/oneTarek/wp-content/plugins/InsightMetrix/api/insightmetrix-api.php?action=save_tweets
require_once("__inc_wp.php"); #Include Wordpress Core Enviroment. It Loads Theme Functions and Plugins also.
global $wpdb, $im_query_table, $im_tweets_table;
$action=$_REQUEST['action'];
switch($action)
{
case 'search_twitter': #this case is not being used currently.
  {
  $search_string=$_REQUEST['q'];
  $json=file_get_contents("http://search.twitter.com/search.json?q=".$search_string."&result_type=recent&rpp=5&callback=?");
  echo $json;
  break;
  }
 case 'save_tweets':
  {
  if ( !is_user_logged_in() ) {echo "Login Required..."; exit;}
  include_once('SaveTweetsClass.php');
  if($_POST['query'])
    {
	$tw = new SaveTweets($_POST);
	}
  break;
  }
 case 'flexigrid':
  {
  if ( !is_user_logged_in() ) {echo "Login Required..."; exit;}
  include_once(INSIGHT_METRIX_PATH."api/data.php");
  break;
  }
  case 'savechart':  #saving
  {
  if ( !is_user_logged_in() ) {echo "Login Required..."; exit;}
  if(!isset($_POST['chartTime']))exit;
  $chartTime=$_POST['chartTime'];
  $chartItemList=$_POST['chartItemList'];
  $tchartItemList=array();
	  if(is_array($chartItemList))
	  {
	  foreach($chartItemList as $item)
		{
		if($item!="" and  $item!="undefined")$tchartItemList[]=$item;
		}
	  }
	$im_chartSettings=array(
	"chartTime"=>intval($chartTime),
	"chartItemList"=>$tchartItemList	
	);
   update_option('im_chartSettings',$im_chartSettings);
   echo "Chart Settings Have Been Saved";
	break;
  }#end case savechart
  case 'delete':
  {
	  if ( !is_user_logged_in() ) {echo "Login Required..."; exit;}
	  $before=intval($_REQUEST['before']);
	  if($before)
	  {
	  $before_time=time()-600;
	  #deleteing all rows before some second.
	  $wpdb->query('DELETE FROM '.$im_tweets_table.' WHERE date<'.$before_time);
	  }
	  elseif(isset($_POST['queryIdList']) && is_array($_POST['queryIdList']))
	  {
	  #delete according to specific querys.
	  $queryIdList=$_POST['queryIdList'];
	  	if(count($queryIdList))
		{
		$queryIdListLine=implode(", ",$queryIdList);
		$wpdb->query("DELETE FROM ".$im_tweets_table." WHERE qid IN(".$queryIdListLine.")");
		$wpdb->query("DELETE FROM ".$im_query_table." WHERE id IN(".$queryIdListLine.")");
		echo "Deleted";
		}
	  
	  }
	  else
	  {
	  #deleteing a row.
	  $tweet_id=$_REQUEST['tweet_id']; #though tweet_id is a number , intval() will not work. it will return only maximum int number 2147483647
	  if($tweet_id)
		{
		$wpdb->query('DELETE FROM '.$im_tweets_table.' WHERE id='.$tweet_id);
		echo "Deleted ".$tweet_id;
		//echo "Deleted ".$tweet_id;
		}
	  }
	 break; 
  }
  case 'show_chart':
  {
  if(isset($_REQUEST['width']))$width=intval($_REQUEST['width']); 
  if(isset($_REQUEST['height']))$height=intval($_REQUEST['height']);
  show_im_chart($width,$height);
  break;
  }
  case 'get_query_list':
  {
	$querylist=$wpdb->get_results("SELECT * FROM ".$im_query_table, ARRAY_A);
	if(is_array($querylist))
	{
	 foreach($querylist as $query)
	 {
	 echo '<option value="'.$query['id'].'">'.$query['query'].'</option>';
	 }
	}
  break;
  }
  case 'csv_downlod':
  {
	  if ( !is_user_logged_in() ) {echo "Login Required..."; exit;}
	  
	  #$csv_output .= "\"date, user, query, tweet \"\n";
	  $csv_output .= "date, user, query, tweet \n";
	  #SELECT im_query.query as query ,im_tweets.* FROM im_query, im_tweets  WHERE im_query.id=im_tweets.qid
	  $tweetlist=$wpdb->get_results("SELECT ".$im_query_table.".query as query ,".$im_tweets_table.".* FROM ".$im_query_table.", ".$im_tweets_table."  WHERE ".$im_query_table.".id=".$im_tweets_table.".qid", ARRAY_A);
	  foreach($tweetlist as $tweet)
	  {
	  $text=str_replace("\n","",$tweet['text']);
	  $text=str_replace("\r","",$text);
	  $text=str_replace(",","",$text);
	  $date=date("d-M-Y h:i:s:a",$tweet['date']);
	  #$csv_output.= "\"".$date.", ".$tweet['user'].", ".$tweet['query'].", ".$text."\"\n";
	  $csv_output.= "".$date.", ".$tweet['user'].", ".$tweet['query'].", ".$text."\n";
	  }
	  $csv_output .= "";
	  $filename = "InsightMetrix_".date("d-m-Y_H-i",time());

		header("Content-type: application/csv; utf-8");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header('Pragma: no-cache');
		header( "Content-disposition: filename=".$filename.".csv");
		
		print $csv_output;

	  
	  
  break;
  }
}#end switch

?>