<?php
/*
DeleteControl.php
*/
?>
<table class="widefat" width="100%" style="margin-top:20px;">
<thead>
	<tr>
		<th>
		<span id="reload_query_list" onclick="refresh_queryList()" title="Refresh This List" class="qReload">&nbsp;</span><span class="btnseparator"></span>
		Delete Recorded Data
		</th>
	</tr>
</thead>
<tbody>
	<tr>
		<td>
		<?php
		global $wpdb, $im_query_table, $im_tweets_table;
		$querylist=$wpdb->get_results("SELECT * FROM ".$im_query_table , ARRAY_A);
		//echo "<pre>"; print_r($querylist); echo "</pre>";
		?>
		<select name="queryList" id="queryList" multiple="multiple" style="height:120px; width:280px;">
		<?php 
		if(is_array($querylist))
		{
		 foreach($querylist as $query)
		 {
		 echo '<option value="'.$query['id'].'">'.$query['query'].'</option>';
		 }
		}
		 ?>
		</select>
		</td>
	</tr>
	<!--<tr><td style="font-size:10px">Hold down the Ctrl (windows) / Command (Mac) button to select multiple Queries</td></tr>-->
	<tr><td align="left"><input type="button" class="button" value="Delete Queries" onClick="DeleteQueryWithTweets()"></td></tr>
</tbody>
</table>
