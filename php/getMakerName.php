<?php
require_once("rssfunction.php");

//DB登録済みメーカー一覧を返す
try{
 $corplist=getCorpList();
 $html="";
 $html.="<li>";
 $html.="<span class='middlespan'>メーカー名</span>";
 $html.="</li>";
 foreach($corplist as $key=>$val){
  $html.="<li>";
	$html.="<span class='middlespan'><a href='#'>";
	$html.=$val["corpname"];
	$html.="</a>";
	if($val["newitem"]) $html.="(".$val["newitem"].")";
  $html.="</span>";
  $html.="</li>";
 }
 echo $html;
}
catch(Exception $e){
	echo "<li>err:".$e->getMessage."</li>";
}
?>
