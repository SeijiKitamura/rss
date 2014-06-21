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
}

div#entryzone{
 width:40%;
 height:200px;
 float:left;
}

div#previewzone{
 width:55%;
 height:1000px;
 float:right;
 overflow:auto;
}

div#listzone{
 width:40%;
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
 width  :200px;
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
 width  :300px;
 border :1px solid #000000;
}

ul#ul_maker>li input[type=button]{
 width  :100px;
}

ul#ul_listzone li input{
float:left;
}

   </style>
	 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

	 <script>
$(function(){
	$("input[name=corpname]").focus();

  getMakerList();

//URL変更イベント
	$("input[name=maker]").change(function(){
		getDiv();
	});

//part変更イベント
	$("input[name=part]").change(function(){
		getDiv();
	});

//「登録」イベント
	$("input[name=button_entry]").click(function(){
		setMaker();
	});

//「削除」イベント
	$("input[name=button_delete]").click(function(){
		delMaker();
	});

//「クリア」イベント
	$("input[name=button_clr]").click(function(){
		allClr();
	});

//「再表示」ボタンにイベントセット
	$("input[name=button_reload]").click(function(){
		 getMakerList();
	});
});

//登録済みメーカー一覧
function getMakerList(){
	var phpfile="php/getMakerList.php";
	$.get(phpfile,function(html){
			$("#ul_listzone").empty()
				               .append(html);

			//マウスオーバーセット
			$("#ul_listzone li").hover(
				function(){
					var cssstyle={ "color":"white"
					             	,"background-color":"red"
					             }
					$(this).css(cssstyle);
				}
				,function(){
					var cssstyle={ "color":"black"
					             	,"background-color":"white"
					             }
					$(this).css(cssstyle);
			});

			//メーカー名イベントセット
			//$("ul#ul_listzone li span.middlespan").click(function(){
			$("ul#ul_listzone li").click(function(){
				getMakerMas($(this));
			});
	});
}

//メーカーマスタゲット
function getMakerMas(obj){
	var phpfile="php/getCorpMas.php";
	var corpname=obj.find("span").eq(0).text();
	if(! corpname.length) return false;
	$.getJSON(phpfile,{"requestcorpname":corpname},function(json){
		if(! json) return false;

		$("input[name=corpname]").val(json.corpname);
		$("input[name=maker]").val(json.url);
		$("input[name=part]").val(json.part);
		getDiv();
	});
}
//ページプレビュ
function getDiv(){
	var url=$("input[name=maker]").val();
	var part=$("input[name=part]").val();

	if(! url.length) return false;
	var phpfile="php/getDiv.php";

	$("div#previewzone").empty()
		                  .append("データ受信中・・・");

	$.get(phpfile,{"requesturl":url,"requestpart":part},function(html){
		$("div#previewzone").empty()
			                  .append(html);
		//divを表示しているh1タグにイベントセット
		$("div#previewzone h1").click(function(){
			$("input[name=part]").val($(this).text());
			getDiv();
		});
	});
}

//メーカー登録
function setMaker(){
	var phpfile="php/setMaker.php";

	var corpname=$("input[name=corpname]").val();
	var url=$("input[name=maker]").val();
	var part=$("input[name=part]").val();
  if (! part) part="";
	//if(! corpname || ! url || ! part){
	if(! corpname || ! url  ){
		console.log("引数空欄");
		$("div#msgdiv").empty().text("空欄が有ります");
	 	return false;
	}

	$("div#msgdiv").empty().text("登録中・・・");

	$.get(phpfile,{"requestcorpname":corpname,"requesturl":url,"requestpart":part},function(html){
		if(html.match(/^err/)){
			$("ul#ul_listzone").empty()
				                 .append("<li>"+html+"</li>");
			return false;
		}
		console.log(html);
		alert("登録しました");
		$("div#previewzone").empty();
		getMakerList();
    allClr();
	});
}

//メーカー削除
function delMaker(){
	var phpfile="php/delMaker.php";
	var url=$("input[name=maker]").val();
  if(! url){
		console.log("引数空欄");
 		$("div#msgdiv").empty().text("URLが空欄です");
 	 	return false;
 	}

	if(! confirm("削除しますか?")) return false;

	$("div#msgdiv").empty().text("削除中・・・");

	$.get(phpfile,{"requesturl":url},function(html){
		if(html.match(/^err/)){
			$("ul#ul_listzone").empty()
				                 .append("<li>"+html+"</li>");
			return false;
		}
		console.log(html);
		alert("削除しました");
		$("div#previewzone").empty();
		getMakerList();
    allClr();
	});

}

//クリア
function allClr(){
	$("input[name=corpname]").val("");
	$("input[name=maker]").val("");
	$("input[name=part]").val("");
	$("div#previewzone").empty();
	$("div#msgdiv").empty();

	getMakerList();
	$("input[name=corpname]").focus();
}
   </script>
 </head>

 <body>
  <header>
   <img src="http://www2.kita-grp.co.jp/hp/img/logo2.jpg">

   </header>

  <div id="wrapper">

   <div id="entryzone">
    <h2>新規メーカー登録</h2>
    <ul id="ul_maker">
     <li><span class="shortspan">メーカー名:</span><input name="corpname" type="text" value=""></li>
     <li><span class="shortspan">URL:</span><input name="maker" type="text" value=""></li>
     <li><span class="shortspan">Part:</span><input name="part" type="text" value=""></li>
		 <li><span class="shortspan">&nbsp;</span>
				 <input type="button" value="登録"   name="button_entry">
				 <input type="button" value="削除"   name="button_delete">
				 <input type="button" value="クリア" name="button_clr">
     </li>
    </ul>
    <div id="msgdiv"></div>
   </div> <!-- entryzone-->

   <div id="previewzone">
   </div>

   <div id="listzone">
    <h2>登録済メーカー</h2>
    <input type="button" value="再表示" name="button_reload">
    <ul id="ul_listzone">
     <li>test</li>
    </ul>
   </div>


  </div> <!-- wrapper-->

	<footer>
	</footer>
 </body>
</html>

