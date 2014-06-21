<?php
require_once("rssfunction.php");
$corpid=$_GET["requestcorpid"];
$itemarray=getDBItemList($corpid);

if(! isset($itemarray)){
  echo "err(エラー):データがありません";
  return false;
}

$html ="<ul id='entryitemlist'>";
foreach($itemarray as $key=>$val){
  $html.="<li id='entryitemid_".$val["itemid"]."'>";
  if($val["status"]) $html.="<span style='color:red'>[".$val["status"]."]</span>";

  if(! $val["status"] && ! $val["lincode"]) $html.="<span style='color:red'>[New]</span>";

  if($val["originaltitle"]) $html.=$val["originaltitle"];
  else                      $html.=$val["pagetitle"];
  $html.="</li>";
}
$html.="</ul>";
echo $html;

?>
