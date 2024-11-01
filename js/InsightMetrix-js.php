
jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", (($(window).height() - this.outerHeight()) / 2) + $(window).scrollTop() + "px");
    this.css("left", (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft() + "px");
    return this;
}


$(function() {

		$("h1.panelheading").each(function () {
			$(this).append("<span class='box-icon'>-</span>");
		});

		$("span.box-icon").click(function () {
			//console.log($(this).parents("div.panel"));
			$(this).parents("div.panel").find("div.panelcontent").toggle();
			$(this).parents("div.panel").find("div.panelfooter").toggle();
		});


		$('div.panel').corner("5px");
		$('h1.panelheading').corner("5px");
		//$('input.searchinput').corner("5px");
		//$('.searchbtn').corner("5px");

		$("div#wrapper").center();

		$( ".column" ).sortable({
			connectWith: ".column",
			containtment: "div#wrapper",
			handle: "h1.panelheading"
		});
	});
	
//Damyanov code	
var tStream = new Array();
var currTweet = false;       
var update = false;
var saveToDb = new Array();
var records = new Array();
//var limit = 10;
var limit = 5;
var maxlimit=5;






function updateDb(query){
//alert("updateDb");
since_id ='&since_id='+tStream[query].dbId;
//alert(saveToDb[query]);
//console.log('writing...'+query); #commented by onetarek

$.getJSON("http://search.twitter.com/search.json?q="+encodeURIComponent(query)+"&result_type=recent&geocode&rpp=5&callback=?"+since_id, function(data) { 
      
     if(data.results.length > 0 && tStream[query].dbId < data.results[0].id ){
	   //console.log(query+' - '+data.results.length);

	qData= {"query":query,"length":data.results.length,records:[],data:[{id:[],user:[],text:[]/*,link:[]*/,date:[],location:[],language:[]}]};
	
	for(i=0;i<data.results.length;i++){
	location[i]=data.results[i].location? '' : location[i]=data.results[i].location='undefined'; 
	qData.data[0].id[i]=data.results[i].id;
	qData.data[0].user[i]=data.results[i].from_user;
	qData.data[0].text[i]=data.results[i].text;
	//qData.link[0]='http://twitter.com/'+data.results[i].from_user;
	qData.data[0].date[i]=data.results[i].created_at;
	qData.data[0].location[i]=data.results[i].location;
	qData.data[0].language[i]=data.results[i].iso_language_code;
	//qData.records[i];
     	}
	//console.log(records);
	
	tStream[query].dbId=data.results[0].id;	
	
	//recData.query[query]=qData;
	//console.log(tStream[query].lastId+' - '+tStream[query].dbId);
	//console.log(query); #commented by onetarek
//alert("start ajax call");
 $.ajax({
       url: InsideMatrix_api_save_tweets_url,
       type: "POST",
    //   dataType : "json",
    //   contentType : 'application/json',
       global: false,
       cache: true,
       async: true,
       data:qData,
    success: function(result){ 
	   
	     
            },
    error: function(xhr,errorThrown){

           }
      });
	
   }

 });
	/*
	if(FlexiGridAutoRefreshOn==true)
	{
	$("#flex1").flexReload(); // For FlexiGrid Automatic Refresh When Database update. Auto Refresh Flag is on.
	}
	*/
}


function loadData(query){

if(tStream[query].record===true){
saveToDb[query] = window.setInterval(function(){updateDb(query);}, TwitterRecordingInterval); 
}

}

function updateQ(query){
	   if(tStream[query].pause===true){
	   clearInterval(update);
       $('#tweetContent').html(tStream[query].content);
	   $(".pause").show();
	   
       }else{
	    getTweet(query);
	   $('#tweetContent').html(tStream[query].content);
	 }	    
	   }


function show(q,li){ 
	//onetarek
	InsideMatrix_selected_query=q;
	$("#selected_query").val(q);
	//end onetarek
if(update){
clearInterval(update);
}
 query = q;

 currTweet = q;
 //$('#tweetContent').html(tStream[query].content);
document.getElementById('tweetContent').innerHTML=tStream[query].content;
 $(li).addClass("current");
     if(tStream[query].pause===true){
	 $(".pause").show();
	 }else
	 
	 {
       update = window.setInterval(function(){ updateQ(query);}, TwitterStreamingInterval);    
	   //alert('setInterval updateQ...'+update);//onetarek    
   }
}

function pause(q){
var query = jQuery(q).parent().text();


tStream[query].pause=true;

$(q).css("background-image","url("+InsideMatrix_images_dir_url+"/control-pause-active.png)");
$(q).parent().children("a.ico_play").css("background-image","url("+InsideMatrix_images_dir_url+"/control-play.png)");
$(q).unbind('hover');
$(q).parent().children("a.ico_play").hover(function(){
$(this).css("background-image","url("+InsideMatrix_images_dir_url+"/control-play-active.png)");
},function(){
$(this).css("background-image","url("+InsideMatrix_images_dir_url+"/control-play.png)");
});

   if(currTweet==query){
    $(".pause").show();
   }
}

function record(q){
var query = $(q).parent().text();

records[query]=query;
//console.log(records);
if(tStream[query].record===false){ 
   tStream[query].record=true;
   loadData(query);

    $(q).unbind('hover');
   $(q).css("background-image","url("+InsideMatrix_images_dir_url+"/control-record-active.png)");

}
else if(tStream[query].record===true){
tStream[query].record=false;
clearInterval(saveToDb[query]);
saveToDb[query]=false;
$(q).css("background-image","url("+InsideMatrix_images_dir_url+"/control-record.png)");
 $(q).hover(function(){
$(this).css("background-image","url("+InsideMatrix_images_dir_url+"/control-record-active.png)");
},function(){
$(this).css("background-image","url("+InsideMatrix_images_dir_url+"/control-record.png)");
});
}

}


function resume(q){
var query = $(q).parent().text();
 tStream[query].pause=false;
//console.log($(q).parent().html());

$(q).unbind('hover');
$(q).css("background-image","url("+InsideMatrix_images_dir_url+"/control-play-active.png)");
$(q).parent().children("a.ico_pause").css("background-image","url("+InsideMatrix_images_dir_url+"/control-pause.png)");


$(q).parent().children("a.ico_pause").hover(function(){
$(this).css("background-image","url("+InsideMatrix_images_dir_url+"/control-pause-active.png)");
},function(){
$(this).css("background-image","url("+InsideMatrix_images_dir_url+"/control-pause.png)");
});

    if(currTweet==query){
    $(".pause").fadeOut(500);
	show(query);
   }
}
function del(q){
var query = $(q).parent().text();
if(confirm('Delete '+query+' Query?')){
$(q).parent().remove();
limit++;
tStream[query]=false;
clearInterval(saveToDb[query]);
saveToDb[query]=false;
if(currTweet==query){
    $("#tweetContent").html('');
     clearInterval(update);
	 currTweet=false;
   }
}
}

function timeConvert(created_at){
var tTime=new Date(Date.parse(created_at));
var cTime=new Date();
var sinceMin=Math.round((cTime-tTime)/60000);
if(sinceMin==0){
				var sinceSec=Math.round((cTime-tTime)/1000);
				if(sinceSec<15)
					var since='less than 10 seconds ago';
				else if(sinceSec<20)
					var since='less than 20 seconds ago';
				else
					var since='half a minute ago';
			}
			else if(sinceMin==1){
				var sinceSec=Math.round((cTime-tTime)/1000);
				if(sinceSec==30)
					var since='half a minute ago';
				else if(sinceSec<60)
					var since='less than a minute ago';
				else
					var since='1 minute ago';
			}
			else if(sinceMin<45)
				var since=sinceMin+' minutes ago';
			else if(sinceMin>44&&sinceMin<60)
				var since='about 1 hour ago';
			else if(sinceMin<1440){
				var sinceHr=Math.round(sinceMin/60);
				if(sinceHr==1)
					var since='about 1 hour ago';
				else
					var since='about '+sinceHr+' hours ago';
			}
			else if(sinceMin>1439&&sinceMin<2880)
				var since='1 day ago';
			else{
				var sinceDay=Math.round(sinceMin/1440);
				var since=sinceDay+' days ago';
			}
			return since;
}

function getTweet(query){
//var escaped =escape(query);

	
$.getJSON("http://search.twitter.com/search.json?q="+encodeURIComponent(query)+"&result_type=recent&rpp=5&callback=?", function(data) {
     if(data.results.length > 0 && tStream[query].id < data.results[0].id ){
    //console.log(data.results[0].from_user);
	 //for(propery in data.results[0]){
	//  console.log(propery+" - "+data.results[0][propery]);	  
	// }
     var images = new Array(); 
	 var childImg = new Array();
     var  i = 0;
	 tStream[query].id=data.results[0].id;
	 tStream[query].content='<div class="pause"><center><span class="pause-msg">Paused</span></center></div><div class="twitStream" id="tweets" title="'+query+'">\n'
	 for(property in data.results){
images[i]= new Image(48,48);
images[i].src=data.results[i].profile_image_url;
images[i].setAttribute("alt", data.results[i].from_user);	 
childImg[i] = document.createElement('div');
childImg[i].appendChild(images[i]);
var when = timeConvert(data.results[i].created_at)	 
tStream[query].content = tStream[query].content +'<div class="tweet">\n\
<div class="tweet-left">\n\
<a href="http://twitter.com/'+data.results[i].from_user+'" target="blank">\n\
'+childImg[i].innerHTML+'\
</a>\n\
</div>\n\
<div clas="tweet-right">\n\
<p class="text">\n\
'+data.results[i].text+'\n\
<br />\n\
<a href="http://twitter.com/'+data.results[i].from_user+'" class="tweet-user" target="_blank">'+data.results[i].from_user+'</a>\n\
<span class="tweet-time">'+when+'</span>\n\
</p>\n\
</div>\n\
<br style="clear: both;" />\n\
</div>\n';
i++;}
tStream[query].content = tStream[query].content+'</div>';
}else if(data.results.length < 1){
tStream[query].content='<center>Empty<center>';}
    });


	
}

function addNewTweet(query){
  
	 
    if(query in tStream && tStream[query]!==false  || query.length <1){
	
   //$('#tweetContent').html(tStream[query].content);

	}else{
	tStream[query]={pause:false,record:false, id:0, dbId:0}
	getTweet(query);
	$("#twList").append('<li id="'+encodeURIComponent(query)+'"><a href="javascript:void(0);" class="ico_record"></a><span class="mct">'+query+'</span>\
<a href="javascript:void(0);" class="ico_delete"></a><a href="javascript:void(0);" class="ico_pause"></a><a href="javascript:void(0);" class="ico_play"></a></li>');
    limit--;
	$("#twList li[id='"+encodeURIComponent(query)+"']").hide().fadeIn(500);
	$(".searchinput").val(''); 	
	  if(currTweet===false){
	  setTimeout(function(){
	 
	  show(query,$("#twList li[id='"+encodeURIComponent(query)+"']"));
	//  $("#"+query+" .ico_play").css("background-image","url("+InsideMatrix_images_dir_url+"/control-play-active.png)");
	   },1000);	  
	  }
	}


$("#twList li:not(.current)").click(function(evt){
evt.stopImmediatePropagation();
$("#twList li").removeClass("current");
//show($(this).attr('id'));
show(decodeURIComponent($(this).attr('id')),this);
});
	
$("#twList li a").click(function(evt){
//alert($(this).parent().html());
evt.stopImmediatePropagation();
if($(this).attr('class')=='ico_play'){
resume(this);
}
else if($(this).attr('class')=='ico_pause'){
pause(this);
}
else if($(this).attr('class')=='ico_delete'){
del(this);
}
else if($(this).attr('class')=='ico_record'){
record(this);

}

})
}

$(".searchbtn").click(function(evt){
evt.stopImmediatePropagation();
//alert($("#tw a").attr('href'));
if(limit > 0){ 
addNewTweet($(".searchinput").val());
}else{
$(".searchinput").val('Query limit of '+maxlimit+' reached');
}

//oneTarek. http://onetarek.com
});