<?php
require_once("rssfunction.php");
$html="<ul id='ulentry'>";
//ボタンセット
$html.="<li id='libutton_0'>";
$html.="<input type='button' value='登録' name='btnset_0'>";
$html.="<input type='button' value='対象外' name='btndel_0'>";
$html.="<input type='button' value='新規' name='btnnew_0'>";
$html.="</li>";

//itemid
$html.="<li id='liitemid_0'>";
$html.="<span class='spantitle'>";
$html.="商品番号:";
$html.="</span>";
$html.="<span class='spanbody'>";
$html.="0";
$html.="</span>";
$html.="<div class='clr'></div>";
$html.="</li>";//liitem

//itemurl
$html.="<li id='liitemurl_0'>";
$html.="<span class='spantitle'>";
$html.="元URL:";
$html.="</span>";
$html.="<span class='spanbody'>";
$html.="</span>";
$html.="<div class='clr'></div>";
$html.="</li>";

//originalitemurl
$html.="<li id='lioriginalitemurl_0'>";
$html.="<span class='spantitle'>";
$html.="URL:";
$html.="</span>";
$html.="<span class='spanbody'>";
$html.="<input type='text' value='";
$html.="' name='entryoriginalitemurl_0'>";
$html.="</span>";
$html.="<div class='clr'></div>";
$html.="</li>";

//lincode
$html.="<li id='lilincode_0'>";
$html.="<span class='spantitle'>";
$html.="部門:";
$html.="</span>";
$html.="<span class='spanbody'>";
$html.="<select id='selectlincode_0'>";
foreach($LINMAS as $key1=>$val1){
  $html.="<option value='".$key1."'";
  if($key1==0){
    $html.=" selected";
  }
  $html.=">";
  $html.=$val1;
  $html.="</option>";
}
$html.="</select>";
$html.="</span>";
$html.="<div class='clr'></div>";
$html.="</li>";

//pagetitle
$html.="<li id='lipagetitle_0'>";
$html.="<span class='spantitle'>";
$html.="タイトル:";
$html.="</span>";
$html.="<span class='spanbody'>";
$html.="</span>";
$html.="<div class='clr'></div>";
$html.="</li>";

//originalpagetitle
$html.="<li id='lioriginalpagetitle_0'>";
$html.="<span class='spantitle'>";
$html.="商品名:";
$html.="</span>";
$html.="<span class='spanbody'>";
$html.="<input type='text' value='";
$html.="' name=entryoriginalpagetitle_0'>";
$html.="</span>";
$html.="<div class='clr'></div>";
$html.="</li>";

//itemcomment
$html.="<li id='liitemcomment_0'>";
$html.="<span class='spantitle'>";
$html.="コメント:";
$html.="</span>";
$html.="<textarea name='entryitemcomment_0'";
$html.=" cols=50 rows=4>";
$html.="</textarea>";
$html.="<div class='clr'></div>";
$html.="</li>";

//saleday
$html.="<li id='lisaleday_0'>";
$html.="<span class='spantitle'>";
$html.="発売日:";
$html.="</span>";
$html.="<span class='spanbody'>";
$html.="<input type='text' ";
$html.=" value='";
$html.="' name='entrysaleday_0'>";
$html.="</span>";
$html.="<div class='clr'></div>";
$html.="</li>";

//storesaleday
$html.="<li id='listoresaleday_0'>";
$html.="<span class='spantitle'>";
$html.="店舗販売日:";
$html.="</span>";
$html.="<span class='spanbody'>";
$html.="<input type='text' ";
$html.=" value='";
$html.="' name='entrystoresaleday_0'>";
$html.="</span>";
$html.="<div class='clr'></div>";
$html.="</li>";

//imgurl
$html.="<li id='liimgurl_0'>";
$html.="<span class='spantitle'>";
$html.="画像URL:";
$html.="</span>";
$html.="<span class='spanbody'>";
$html.="<input type='text' ";
$html.=" value='";
$html.="' name='entryimgurl_0'>";
$html.="</span>";
$html.="<div class='clr'></div>";
$html.="</li>";

//画像
$html.="<li id='liimg_0'>";
$html.="<span class='spantitle'>";
$html.="画像:";
$html.="</span>";
if(isset($val["img"])){
  foreach($val["img"] as $key1=>$val1){
    if($val1["status"]!=""){
      $html.="<img id='img_".$val["itemid"]."' src='";
      $html.=$val1["imgurl"];
      $html.="'>";
      break;
    }
  }
}
$html.="<div class='clr'></div>";
$html.="</li>";



$html.="</ul>";
?>
