<?php
require_once("rssfunction.php");
$lincode=$_GET["requestlincode"];
$itemarray=getItemListURL($_GET["requestlincode"]);

$html="";
if(isset($itemarray)){
  echo "<pre>";
  print_r($itemarray);
  echo "</pre>";
  return;

  $html="<ul id='ullistentry'>";
  foreach($itemarray as $key=>$val){
    //ボタンセット
    $html.="<li id='libutton_".$val["itemid"]."'>";
//    $html.="<input type='button' value='登録' name='btnset_".$val["itemid"]."'>";
    $html.="<input type='button' value='対象外' name='btnout_".$val["itemid"]."'>";
    $html.="<input type='button' value='削除' name='btndel_".$val["itemid"]."'>";
    $html.="</li>";

    //itemid
    $html.="<li id='liitemid_".$val["itemid"]."'>";
    $html.="<span class='spantitle'>";
    $html.="商品番号:";
    $html.="</span>";
    $html.="<span class='spanbody'>";
    $html.="<div id='divitem'>";
    $html.=$val["itemid"];
    $html.="</div>";
    $html.="</span>";
    $html.="<div class='clr'></div>";
    $html.="</li>";//liitem

    //itemurl
    $html.="<li id='liitemurl_".$val["itemid"]."'>";
    $html.="<span class='spantitle'>";
    $html.="元URL:";
    $html.="</span>";
    $html.="<span class='spanbody'>";
    $html.="<a href='".$val["itemurl"]."' target='_blank' id='a_".$val["itemid"]."'>";
    $html.=$val["itemurl"];
    $html.="</a>";
    $html.="</span>";
    $html.="<div class='clr'></div>";
    $html.="</li>";

    //originalitemurl
    $html.="<li id='lioriginalitemurl_".$val["itemid"]."'>";
    $html.="<span class='spantitle'>";
    $html.="URL:";
    $html.="</span>";
    $html.="<span class='spanbody'>";
    $html.="<input type='text' value='";
    $html.=$val["originalitemurl"];
    $html.="' name='entryoriginalitemurl_".$val["itemid"]."'>";
    $html.="</span>";
    $html.="<div class='clr'></div>";
    $html.="</li>";

    //lincode
    $html.="<li id='lilincode_".$val["itemid"]."'>";
    $html.="<span class='spantitle'>";
    $html.="部門:";
    $html.="</span>";
    $html.="<span class='spanbody'>";
    $html.="<select id='selectlincode_".$val["itemid"]."'>";
    foreach($LINMAS as $key1=>$val1){
      $html.="<option value='".$key1."'";
      if($key1==$val["lincode"]){
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
    $html.="<li id='lipagetitle_".$val["itemid"]."'>";
    $html.="<span class='spantitle'>";
    $html.="タイトル:";
    $html.="</span>";
    $html.="<span class='spanbody'>";
    $html.=$val["pagetitle"];
    $html.="</span>";
    $html.="<div class='clr'></div>";
    $html.="</li>";

    //originalpagetitle
    $html.="<li id='lioriginalpagetitle_".$val["itemid"]."'>";
    $html.="<span class='spantitle'>";
    $html.="商品名:";
    $html.="</span>";
    $html.="<span class='spanbody'>";
    $html.="<input type='text' value='";
    $html.=$val["originaltitle"];
    $html.="' name='entryoriginalpagetitle_".$val["itemid"]."'>";
    $html.="</span>";
    $html.="<div class='clr'></div>";
    $html.="</li>";

    //itemcomment
    $html.="<li id='liitemcomment_".$val["itemid"]."'>";
    $html.="<span class='spantitle'>";
    $html.="コメント:";
    $html.="</span>";
    $html.="<textarea id='entryitemcomment_".$val["itemid"]."'";
    $html.=" cols=50 rows=4>";
    $html.=$val["itemcomment"];
    $html.="</textarea>";
    $html.="<div class='clr'></div>";
    $html.="</li>";

    //saleday
    $html.="<li id='lisaleday_".$val["itemid"]."'>";
    $html.="<span class='spantitle'>";
    $html.="発売日:";
    $html.="</span>";
    $html.="<span class='spanbody'>";
    $html.="<input type='text' ";
    $html.=" value='";
    if(strtotime($val["saleday"])!==strtotime("1970-1-1")){
      $html.=$val["saleday"];
    }
    $html.="' name='entrysaleday_".$val["itemid"]."'>";
    $html.="</span>";
    $html.="<div class='clr'></div>";
    $html.="</li>";

    //storesaleday
    $html.="<li id='listoresaleday_".$val["itemid"]."'>";
    $html.="<span class='spantitle'>";
    $html.="店舗販売日:";
    $html.="</span>";
    $html.="<span class='spanbody'>";
    $html.="<input type='text' ";
    $html.=" value='";
    if(strtotime($val["storesaleday"])!==strtotime("1970-1-1")){
      $html.=$val["storesaleday"];
    }
    $html.="' name='entrystoresaleday_".$val["itemid"]."'>";
    $html.="</span>";
    $html.="<div class='clr'></div>";
    $html.="</li>";

    //imgurl
    $html.="<li id='liimgurl_".$val["itemid"]."'>";
    $html.="<span class='spantitle'>";
    $html.="画像URL:";
    $html.="</span>";
    $html.="<span class='spanbody'>";
    $html.="<input type='text' ";
    $html.=" value='";
    if(isset($val["img"])){
      foreach($val["img"] as $key1=>$val1){
        if(isset($val1["status"])){
          $html.=$val1["imgurl"];
          break;
        }
      }
    }
    $html.="' name='entryimgurl_".$val["itemid"]."'>";
    $html.="</span>";
    $html.="<div class='clr'></div>";
    $html.="</li>";

    //画像
    $html.="<li id='liimg_".$val["itemid"]."'>";
    $html.="<span class='spantitle'>";
    $html.="画像:";
    $html.="</span>";
    $html.="<span class='spanimg'>";
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
    $html.="</span>";
    $html.="<div class='clr'></div>";
    $html.="</li>";

  }
  $html.="</ul>";
}

echo $html;


?>

