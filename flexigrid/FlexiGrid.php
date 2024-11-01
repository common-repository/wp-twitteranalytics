<!--FlexiGrid-->
<?php echo '<script type="text/javascript" src="' .INSIGHT_METRIX_URL.'/flexigrid/js/flexigrid.js"></script>';?>
<div id="autoRefreshFlag"><span style="color:#009900">Auto Refresh is ON</span></div>
<table id="flex1"></table>

	<script type="text/javascript">
	var FlexiGridAutoRefreshInt;
	function doCommand_csv_download(){
	//alert("Downloading Records as CSV .........."); 
	document.location=InsideMatrix_api_csv_downlod_url;
	}
	function doCommand_delete() //http://www.kenthouse.com/blog/2009/07/fun-with-flexigrids/
	{
		var id;
		$('.trSelected').each(function() {
		id = $(this).attr('id');
		id = id.substring(id.lastIndexOf('row')+3);
		//alert("Deleting row " + id);
		});
		if(!id){alert('Select a row');}else{DeleteRow(id);}
	    //alert("Deleting...."); 
	}

	function flexiGridAutoRefresh(){$("#flex1").flexReload();}
	function FlexiGridAutoRefreshOnOff()
		{ 
		   if(FlexiGridAutoRefreshOn==true)
		   {
		    FlexiGridAutoRefreshOn=false;
		    FlexiGridAutoRefreshInt=window.clearInterval(FlexiGridAutoRefreshInt);
			$("#autoRefreshFlag").html('<span style="color:#ff0000">Auto Refresh Is OFF</span>');
		   }
		   else
		   {
		   FlexiGridAutoRefreshOn=true;
		   FlexiGridAutoRefreshInt = window.setInterval(function(){$("#flex1").flexReload();}, 10000);
		   $("#autoRefreshFlag").html('<span style="color:#009900">Auto Refresh is ON</span>');
		   }
		}
		$(function() {
				$("#flex1").flexigrid({
						url: '<?php echo INSIGHT_METRIX_API_URL."?action=flexigrid" ?>',
						dataType: 'json',
						colModel : [
								{display: 'Date', name : 'date', width : 150, sortable : true, align: 'left'},
								{display: 'User', name : 'User', width : 150, sortable : true, align: 'left'},
								{display: 'Query', name : 'query', width : 150, sortable : true, align: 'left'},
								{display: 'Tweet', name : 'tweet', width : 550, sortable : true, align: 'left'}
								
						],
						buttons : [
								{name: 'Download', bclass: 'csv', onpress : doCommand_csv_download},
								{separator: true},
								{name: 'Delete', bclass: 'delete', onpress : doCommand_delete}, 
								{separator: true},
								{name: 'Auto Refresh ON/OFF', bclass: 'autoRefreshOnOff', onpress : FlexiGridAutoRefreshOnOff},
								{separator: true}
						],
						/*
						searchitems : [
								{display: 'Query', name : 'query'},
								{display: 'Tweet', name : 'tweet', isdefault: true}
							
						],
						*/
						sortname: "id",
						sortorder: "desc",
						usepager: true,
						title: "Recorded Tweets",
						useRp: true,
						rp: 10,
						showTableToggleBtn: false,
						resizable: true,
						width: 800,
						height: 370,
						singleSelect: true
				});
				FlexiGridAutoRefreshInt = window.setInterval(function(){$("#flex1").flexReload();}, 10000);
		});

    </script>
<!--End FlexiGrid-->