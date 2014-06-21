<?php
require_once("rssfunction.php");
$corpid=$_GET["requestcorpid"];
$itemarray=getDBItemList($corpid);

if(! isset($itemarray)){
  echo "err(エラー):データがありません";
  return false;
}

$html="";
$html.="<h2 id='h2_".$corpid."'>会社番号:".$corpid."</h2>";
$html.="<input type='button' value='全データ表示' name='allItem'>";
$html.="<ul id='ulitemlist'>";
foreach($itemarray as $key=>$val){
  $html.="<li id='li_".$val["itemid"]."'";
  if($val["status"]) $html.=" style='display:none' ";
  $html.=">";
  $html.="<dl id='dl_".$val["itemid"]."'>";

  //ボタン配置位置変更　および　URLリセット追加　ここから
  $html.="<dt></dt>";
  $html.="<dd>";
  $html.="<input type='button' value='対象外' name='itemout_".$val["itemid"]."'>";
  $html.="</dd>";

  $html.="<dt>元URL:</dt>";
  $html.="<dd><a href='".$val["itemurl"]."' target='_blank' id='url_".$val["itemid"]."'>";
  $html.=$val["itemurl"];
  $html.="</a>";
  $html.="</dd>";

  $html.="<dt>URL:</dt>";
  $html.="<dd><input name='originalitemurl_".$val["itemid"]."'type='text' value='";
  if($val["originalitemurl"]) $html.=$val["originalitemurl"];
  else $html.=$val["itemurl"];
  $html.="'>";
  $html.="<input type='button' value='タイトルリセット' name='title_".$val["itemid"]."'>";
  $html.="</dd>";

  $html.="<dt></dt>";
  $html.="<dd>";
  $html.="</dd>";

  $html.="<dt></dt>";
  $html.="<dd id='pagetitle_".$val["itemid"]."' style='display:none'>";
  $html.=$val["pagetitle"];
  $html.="</dd>";

  $html.="<dt>タイトル</dt>";
  $html.="<dd><textarea id='title_".$val["itemid"]."' cols='85' rows='5'>";
  if(! $val["originaltitle"]) $html.=$val["pagetitle"];
  else $html.=$val["originaltitle"];
  $html.="</textarea></dd>";

  $html.="<dt>発売日:</dt>";
  $html.="<dd>";
  $html.="<input type='text' value='";
  if(strtotime($val["saleday"])!==strtotime("1970/1/1")){
    $html.=$val["saleday"];
  }
  $html.="' name='saleday_".$val["itemid"]."'>";

  $html.="店舗取扱日:";
  $html.="<input type='text' value='";
  if(strtotime($val["storesaleday"])!==strtotime("1970/1/1")){
    $html.=$val["storesaleday"];
  }
  $html.="' name='storesaleday_".$val["itemid"]."'>";
  $html.="</dd>";

  $html.="<dt>コメント:</dt>";
  $html.="<dd><textarea id='itemcomment_".$val["itemid"]."' cols='85' rows='5'>";
  $html.=$val["itemcomment"];
  $html.="</textarea></dd>";

  $html.="<dt>部門：</dt>";
  $html.="<dd><select id='lincode_".$val["itemid"]."'>";
  foreach($LINMAS as $key1=>$val1){
    $html.="<option value='".$key1."'";
    if($val["lincode"]==$key1) $html.=" selected";
    $html.=">";
    $html.=$val1."</option>";
  }
  $html.="</select></dd>";

  $html.="<dt>画像URL：</dt>";
  $html.="<dd><input type='text' value='";
  if(isset($val["img"])){
    foreach($val["img"] as $key1=>$val1){
      if($val1["status"]){
        $html.=$val1["imgurl"];
        break;
      }
    }
  }
  $html.="' name='impurl_".$val["itemid"]."'>";
  $html.="<input type='button' value='画像リセット' name='img_".$val["itemid"]."'>";
  $html.="</dd>";

  $html.="<dt></dt>";
  $html.="<dd>";
  $html.="</dd>";

  $html.="<dt>画像一覧：</dt>";
  $html.="<dd><div class='imgdiv'>";
  if(isset($val["img"])){
    //選択画像検索
    $flg=0;
    foreach($val["img"] as $key1=>$val1){
      if($val1["status"]){
        $flg=1;
        break;
      }
    }

    foreach($val["img"] as $key1=>$val1){
      $html.="<img src='".$val1["imgurl"]."' id='img_".$val["itemid"]."_".$val1["imgid"]."' ";
      if($flg){
        if(! $val1["status"]){
          $html.=" style='display:none;'";
        }
      }
      $html.=">";
    }
  }
  $html.="</div></dd>";
  $html.="</dl><div class='clr'></div></li>";
}
$html.="</ul>";
echo $html;
?>
