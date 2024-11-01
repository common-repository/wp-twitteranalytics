<?php
#https://code.google.com/p/flexigrid/source/browse/wiki/TutorialPropertiesAndDocumentation.wiki
#developed By oneTarek
#http://onetarek.com
global $wpdb, $im_query_table, $im_tweets_table;
$page = 1; // The current page
$sortname = $im_tweets_table.'.id'; // Sort column
$sortorder = 'desc'; // Sort order
$qtype = ''; // Search column
$query = ''; // Search string

// Get posted data
if (isset($_POST['page'])) { $page = $_POST['page'];}
if (isset($_POST['sortname'])) { $sortname = $_POST['sortname'];}
if (isset($_POST['sortorder'])) { $sortorder = $_POST['sortorder'];}
if (isset($_POST['qtype'])) {$qtype = $_POST['qtype'];}
if (isset($_POST['query'])) {$query = $_POST['query'];}
if (isset($_POST['rp'])) {$rp = $_POST['rp'];}

// Setup sort and search SQL using posted data
$sortSql = "order by $sortname $sortorder";
// Get total count of records
if($qtype != '' && $query != '')
{
	if($qtype=="query") #here query is the recorded tweet_search_string.
	{
	$sql = "select count(".$im_tweets_table.".text) from ".$im_tweets_table.", ".$im_query_table." where ".$im_query_table.".id=".$im_tweets_table.".qid and ".$im_query_table.".query='$query'";
	$searchSql = "where ".$im_query_table.".id=".$im_tweets_table.".qid and ".$im_query_table.".query='$query'";
	}
	elseif($qtype=="tweet")
	{
	$sql = "select count(".$im_tweets_table.".text) from ".$im_tweets_table.", ".$im_query_table." where ".$im_query_table.".id=".$im_tweets_table.".qid and ".$im_tweets_table.".text like '%$query%'";
	$searchSql = "where ".$im_query_table.".id=".$im_tweets_table.".qid and ".$im_tweets_table.".text like '%$query%'";
	}
}
else
{
$sql = "select count(*) from ".$im_tweets_table;
$searchSql = "where ".$im_tweets_table.".qid=".$im_query_table.".id ";
}

$total = $wpdb->get_var($wpdb->prepare($sql));
// Setup paging SQL
if(!$rp)$rp=10;
$pageStart = ($page-1)*$rp;
$limitSql = "limit $pageStart, $rp";
// Return JSON data
$data = array();
$data['page'] = $page;
$data['total'] = $total;
$data['rows'] = array();
//$searchSql = ($qtype != '' && $query != '') ? "where im_query.id=im_tweets.qid and $qtype = '$query'" : 'im_query.id=im_tweets.qid';
$sql = "SELECT ".$im_query_table.".query as query ,  ".$im_tweets_table.".id as id, ".$im_tweets_table.".date, ".$im_tweets_table.".user, ".$im_tweets_table.".text as tweet from ".$im_query_table.", ".$im_tweets_table." $searchSql $sortSql $limitSql";
$results = $wpdb->get_results($wpdb->prepare($sql), ARRAY_A);
foreach($results as $row) {
$date=date("d-M-Y h:i:s:a",$row['date']);
$data['rows'][] = array(
'id' => $row['id'],
'cell' => array($date ,$row['user'], $row['query'], $row['tweet'])
);
}
echo json_encode($data);
?>