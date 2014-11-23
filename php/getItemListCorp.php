<?php
require_once("rssfunction.php");
try{
  $corpid=$_GET["requestcorpid"];
  if(! $corpid || ! is_numeric($corpid)){
    throw new exception("会社番号が不正です(".$corpid.")");
  }
  $itemarray=getDBItemList($corpid);
  $html="";
  $html.="<ul>";
  if(isset($itemarray)){
    foreach($itemarray as $key=>$val){
      $itemid=$val["itemid"];
      $html.="<li id='itemid_".$itemid."'>";
      $html.="<div class='imgdiv'>";
      $html.="<div class='imgpick'>";
      if(isset($val["img"])){
        $imgurl="";
        foreach($val["img"] as $key1=>$val1){
          if($val1["status"]){
            $imgurl=$val1["imgurl"];
            $html.="<img id='imgpickup_".$itemid." src='".$imgurl."'>";
            break;
          }
        }
      }
      $html.="</div>";
      $html.="<input id='iptothreimg_".$itemid."' type='button' value='他の画像'>";
      $html.="</div>"; //div class='imgdiv'

      $html.="<div class='itemdiv'>";
      $html.="<dl>";
      $html.="<dt>ステータス:</dt>";
      $html.="<dd>".$val["status"]."</dd>";
      $html.="<dt>商品番号:</dt>";
      $html.="<dd>".$val["itemid"]."</dd>";
      $html.="<dt>元URL:</dt>";
      $html.="<dd><a href='".$val["itemurl"]."' target='_blank'>";
      $html.=$val["itemurl"]."</a></dd>";
      $html.="<dt>URL:</dt>";
      $html.="<dd><input id='ipturl_".$itemid."' type='text' value='";
      if($val["originalitemurl"]){
        $html.=$val["originalitemurl"];
      }
      else{
        $html.=$val["itemurl"];
      }
      $html.="'></dd>";
      $html.="<dt>部門:</dt>";
      $html.="<dd>";
      $html.="<select id='selectlin_".$itemid."'>";
      foreach($LINMAS as $key1=>$val1){
        $html.="<option value='".$key1."'";
        if($val["lincode"]===$key1){
          $html.=" selected";
        }
        $html.=">";
        $html.=$val1;
        $html.="</option>";
      }
      $html.="</select>";
      $html.="</dd>";
      $html.="<dt>タイトル:</dt>";
      $html.="<dd><input id='ipttitle_".$itemid."' type='text' value='";
      if($val["originaltitle"]){
        $html.=$val["originaltitle"];
      }
      else{
        $html.=$val["pagetitle"];
      }
      $html.="'></dd>";
      $html.="<dt>コメント:</dt>";
      $html.="<dd><input id='iptcomment_".$itemid."' type='text' value='";
      $html.=$val["itemcomment"];
      $html.="'></dd>";

      $html.="<dt>発売日:</dt>";
      $html.="<dd><input id='iptsaleday_".$itemid."' type='text' value='";
      if(strtotime($val["saleday"])!==strtotime("1970-1-1")){
        $html.=$val["saleday"];
      }
      $html.="'></dd>";

      $html.="<dt>店舗発売日:</dt>";
      $html.="<dd><input id='iptstoresaleday_".$itemid."' type='text' value='";
      if(strtotime($val["storesaleday"])!==strtotime("1970-1-1")){
        $html.=$val["storesaleday"];
      }
      $html.="'></dd>";

      $html.="<dt>データ取得日:</dt>";
      $html.="<dd>".date("Y-m-d",strtotime($val["idate"]))."</dd>";

      $html.="<dt>画像URL:</dt>";
      $html.="<dd><input id='iptimgurl_".$itemid."' type='text' value='".$imgurl."'></dd>";

      $html.="<dt>&nbsp;</dt>";
      $html.="<dd><input type='button' value='対象外'></dd>";

      $html.="</dl>";
      $html.="</div>";//div class='itemdiv'

      $html.="<div id='imglist_".$itemid."' class='imglist'>";
      if(isset($val["img"])){
        foreach($val["img"] as $key1=>$val1){
          $html.="<img src='".$val1["imgurl"]."'>";
        }
      }
      $html.="</div>";//div class='imglist'
      $html.="<div class='clr'><?div>";
      $html.="</li>";
    }
  }
  $html.="</ul>";
  echo $html;
  echo "<pre>";
  print_r($LINMAS);
  print_r($itemarray);
  echo "</pre>";
}
catch(Exception $e){
  throw new exception ("err:".$e->getMessage());
}

?>
