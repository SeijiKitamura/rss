<?php
require_once("php/rssfunction.php");

echo "<pre>";
echo date("Y-m-d H:i:s")." HTML作成開始\n";
$itemarray=getTimeLine();
foreach($itemarray as $key=>$val){
  print_r($val);
  getTanpinHtml($val);
}
echo date("Y-m-d H:i:s")." HTML作成終了\n";

?>
