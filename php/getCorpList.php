<?php
require_once("rssfunction.php");
$html="<ul>";
$corplist=getCorpList();
if(isset($corplist)){
  foreach($corplist as $key=>$val){
    $html.="<li id='corplist_".$val["corpid"]."'>";
      $html.=$val["corpname"];
      $html.="<span id='newscount_".$val["corpid"]."'>";
      if(isset($val["newitem"])){
        $html.="【";
        $html.="<span id='newitem_".$val["corpid"]."'>";
        $html.=$val["newitem"]."</span>";
        $html.="】";
      }
      $html.="</span>";
    $html.="</li>";
  }
}

$html.="</ul>";
echo $html;
?>

