<!DOCTYPE html>
<html lang="ja">
 <head>
	 <meta charset="utf-8">
	 <title>URL登録画面</title>
	 <style type="text/css">
*{
margin:0;
padding:0;
}

div#wrapper{
 width :100%;
 overflow:hidden;
}

div#entryzone{
 width:48%;
 height:200px;
 float:left;
}

div#previewzone{
 width:75%;
 height:700px;
 float:left;
 overflow:auto;
}

div#listzone{
 width:20%;
 float:left;
 overflow:auto;
}

div.clr{
 width  :0;
 height :0;
 clear  :both;
}
ul{
list-style:none;
}

span.shortspan{
 display:block;
 width  :100px;
 float  :left;
 overflow:hidden;
}

span.middlespan{
 display:block;
 width  :400px;
 float  :left;
 overflow:hidden;
}

span.widespan{
 display:block;
 width  :500px;
 float  :left;
 overflow:hidden;
}



ul#ul_maker>li span{
 text-align:right;
}

ul#ul_maker>li input[type=text]{
 width  :500px;
 border :1px solid #000000;
}

ul#ul_maker>li input[type=button]{
 width  :100px;
}

ul#ul_listzone li input{
float:left;
}

dl#newslist{
 width :100%;
}

dl#newslist dt{
 width:15%;
 float:left;
 margin:20px 0;
}

dl#newslist dd{
 width:80%;
 float:left;
 margin:20px 0;
}

dl#newslist dd img{
 width:20%;
 float:left;
 margin:5px;
}

dl#newslist dd input{
 margin:0 10px;
}
   </style>
	 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

	 <script>
$(function(){
	//メーカーリスト表示
	getMakerList();

	//News全表示
	$("input[name=button_allnews]").click(function(){
	  var corpname=$("h2#h2_corpname").text();
	  getNews(corpname,0);
	});

	//News対象のみ表示
	$("input[name=button_select]").click(function(){
	  var corpname=$("h2#h2_corpname").text();
	  getNews(corpname,1);
	});


});

//登録済みメーカー一覧
function getMakerList(){
	var phpfile="php/getMakerName.php";
	$.get(phpfile,function(html){
			$("#ul_listzone").empty()
				               .append(html);

			//メーカー名イベントセット
			$("#ul_listzone li span a").click(function(){
				getNews($(this).text(),1);
			});
	});
}

//登録済みニュース一覧表示
//(opt 0=>全件表示 1=>対象のみ表示)
function getNews(corpname,opt){
	var phpfile="php/getNews.php";
	if(! corpname || corpname=="プレビュー") return false;
	if(! opt) opt=0;

	$.get(phpfile,{"requestcorpname":corpname,"requestoption":opt},function(html){
		//会社名表示
    $("h2#h2_corpname").text(corpname);
		//アイテム表示
		$("div#previewzone").empty()
			                  .append(html);

		//「対象外」イベントセット
		$("dl#newslist dd input[name^=btn_listout]").click(function(){
			setListOut($(this));
		});

		//「画像URL」イベントをセット
		$("dl#newslist dd input[name^=ipt_imgurl]").change(function(){
			var key=$(this).attr("name").split("_")[2];
			getItemImg($(this));
		});

		//「画像全表示」イベントセット
		$("dl#newslist dd input[name^=btn_allshow]").click(function(){
			getAllImg($(this));
		});

		//画像選択イベント
		$("dl#newslist dd div img").click(function(){
			var key=$(this).parent().attr("id").split("_")[1];
			var imgurl=$(this).attr("src");

			//画像URLにURLをセット
			$("input[name=ipt_imgurl_"+key+"]").val(imgurl);

			//登録
			setItemImg(key);
		});

		//「対象部門」変更イベント
		$("dl#newslist dd select").change(function(){
			var key=$(this).attr("id").split("_")[1];

			//登録
			setItemImg(key);
		});

		//「画像なし」イベント
		$("input[name^=btn_nothing]").click(function(){
			var key=$(this).attr("name").split("_")[2];
			console.log("success");

			//画像URL空欄
			$("input[name=ipt_imgurl_"+key).val("");

			//画像非表示
			$("div#divimg_"+key+" img").slideUp();

			//登録
			setItemImg(key);
		});
	});

}

//「画像URL」変更イベント
function getItemImg(obj){
 if(! obj.val().length) return false;

 var imgurl=obj.val();
 var key=obj.attr("name").split("_")[2];

 var img=$("<img>").attr({"src":imgurl});
 var div=$("div#divimg_"+key);
 div.find("img").slideUp();
 div.append(img);
 setItemImg(key);
}

//DB登録
function setItemImg(key){
  var phpfile="php/setItemImg.php";

	//引数セット
	var itemurl=$("a#a_"+key).attr("href");
	var imgurl=$("input[name=ipt_imgurl_"+key+"]").val();
	var lincode=$("select#selectlin_"+key).val();

	console.log("itemurl:"+itemurl);
	console.log("imgurl :" +imgurl);
	console.log("lincode:"+lincode);

 $.get(phpfile,{"requesturl":itemurl,"requestimgurl":imgurl,"requestlincode":lincode},function(html){
		if(html.match(/^err/)){
			confirm(html);
			return false;
		}

 	 $("div#divimg_"+key+" img").each(function(){
 	 	if($(this).attr("src")===imgurl){
 	 		$(this).siblings().slideUp();
 	 	 return false;
 	 	}
 	 });

	 getMakerList();

  });
}

//function setItemImg(obj){
// var phpfile="php/setItemImg.php";
// var imgurl=obj.attr("src");
// var key=obj.parent().attr("id").split("_")[1];
// var itemurl=$("#a_"+key).attr("href");
// var lincode=$("#selectlin_"+key).val();
//
// $("input[name=imp_imgurl_"+key).val(itemurl);
//
// $.get(phpfile,{"requesturl":itemurl,"requestimgurl":imgurl,"requestlincode":lincode},function(html){
//		if(html.match(/^err/)){
//			confirm(html);
//			return false;
//		}
//    obj.siblings().slideUp();
// });
//}

//「画像全表示」イベント
function getAllImg(obj){
	var key=obj.attr("name").split("_")[2];
	$("div#divimg_"+key+" img").slideDown();
}

//「対象外イベント
function setListOut(obj){
	var phpfile="php/setItemOut.php";

	var key=obj.attr("name").split("_")[2];
	var url=$("a#a_"+key).attr("href")
	var dt=$("dt#dt_"+key);
	var dd=$("dd#dd_"+key);
  console.log(url);

	$.get(phpfile,{"requesturl":url},function(html){
		if(html.match(/^err/)){
			confirm(html);
			return false;
		}
    dt.slideUp();
    dd.slideUp();
		getMakerList();
	});
}
   </script>
 </head>

 <body>
  <header>
   <img src="http://www2.kita-grp.co.jp/hp/img/logo2.jpg">

   </header>

  <div id="wrapper">

   <div id="listzone">
    <h2>登録済メーカー</h2>
    <input type="button" value="再表示" name="button_reload">
    <ul id="ul_listzone">
     <li>test</li>
    </ul>
   </div><!-- listzone-->

   <h2 id="h2_corpname">プレビュー</h2>
   <input type="button" value="全表示" name="button_allnews">
   <input type="button" value="対象のみ" name="button_select">
   <div id="previewzone">
   </div><!-- previewzone-->

  </div> <!-- wrapper-->

	<footer>
	</footer>
 </body>
</html>

