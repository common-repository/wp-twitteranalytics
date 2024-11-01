<!--Chart controls-->
<script type="text/javascript">
var InsideMatrix_api_get_chart_url="<?php echo INSIGHT_METRIX_API_URL."?action=show_chart&width=410&height=300" ?>";
function refreshChart()
{
$("#chartBox").html('<iframe src="'+InsideMatrix_api_get_chart_url+'" width="410" height="300" scrolling="no"></iframe>');
}
</script>
	<div id="ChartControl">
	<table width="100%" border="1" class="widefat">
		<thead>
		<tr><th><!--Chart Control-->Share of Voice</th><th style="text-align:right"><!--Chart--></th></tr>
		</thead>
		<tbody>
		<tr>
			<td>
			<span style="display:none">
			Chart Time:
			<select id="chatTime" onChange="changeChartTime()">
				<option value="300" <?php if($im_chartSettings["chartTime"]==300) echo ' selected="selected"'; ?>>5 min</option>
				<option value="600"<?php if($im_chartSettings["chartTime"]==600) echo ' selected="selected"'; ?>>10 min</option>
				<option value="900"<?php if($im_chartSettings["chartTime"]==900) echo ' selected="selected"'; ?>>15 min</option>
				<option value="1800"<?php if($im_chartSettings["chartTime"]==1200) echo ' selected="selected"'; ?>>30 min</option>
			</select>
			</span>
			
			<style type="text/css">
			#chartItemList{ min-height:30px; padding:10px;}
			#chartItemList ul li{ background:url(<?php echo INSIGHT_METRIX_URL; ?>/images/close.png) no-repeat left ;border:1px solid #dddddd; padding:3px; padding-left:18px; cursor:pointer; margin:2px; margin-bottom:5px; float:left;}
			</style>
			<div style="text-align:center; border:1px dashed #E7E7E7;">Queries</div>
			<div id="chartItemList" style="min-height:200px; background:#F9F9F9;border:1px dashed #E7E7E7; border-top:0;">
			<ul>
			<?php
			$i=1;
			if(is_array($im_chartSettings["chartItemList"]))
			{
				foreach($im_chartSettings["chartItemList"] as $chartItem)
				 {?>
				 <li title="Click me to remove" onClick="DeleteFromChart(<?php echo $i; $i++;?>)"><?php echo $chartItem?></li>
			   <?php
				 }
		    }
		   ?>
			
			</ul>
			<div style="clear:left"></div>
			</div>
			<div style="margin-bottom:3px; margin-top:3px; border:1px dashed #eeeeee;">
			<input type="button" class="button" value="Add Query" onClick="AddToChart()" />
			<input type="text" size="30" readonly="readonly" id="selected_query">
			</div>
			<div style="border:1px dashed #eeeeee;">
			<input style="width:120px" type="button" class="button" value="Refresh Chart" onClick="SaveChart()" /></div>
			
			</td>
			<td style="border-left:1px dashed #E7E7E7;">
			<div id="chartBox" style="text-align:right">
			<iframe src="<?php echo INSIGHT_METRIX_API_URL."?action=show_chart&width=410&height=300" ?>" width="410" height="300" scrolling="no"></iframe>
			</div>			
			</td>
		</tr>
		</tbody>
	</table>
	</div>
<!--end Chart controls-->