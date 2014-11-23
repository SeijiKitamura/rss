<?php
require_once("rssfunction.php");
$corpid=$_GET["requestcorpid"];
$itemarray=getDBItemList2($corpid);

if(! isset($itemarray)){
  echo "err(エラー):データがありません";
  return false;
}

print_r($itemarray);
return;
$html="";
$html.="<ul id='entrylinitem'>";
foreach($itemarray as $key=>$val){
}//foreach($itemarray as $key=>$val){
$html.="</ul>";
echo $html;

?>

