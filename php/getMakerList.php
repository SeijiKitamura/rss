<?php
require_once("rssfunction.php");

//DB登録済みメーカー一覧を返す
try{
 $corplist=getCorpList();
 $html="";
// $html.="<li>";
// $html.="<span class='shortspan'>&nbsp;</span>";
// $html.="<span class='middlespan'>メーカー名</span>";
// $html.="<span class='widespan'>URL</span>";
// $html.="<span class='middlespan'>Part</span>";
// $html.="<div class='clr'></div>";
// $html.="</li>";
 foreach($corplist as $key=>$val){
  $html.="<li>";
//	$html.="<span class='shortspan'>";
//	$html.="<input type='button' value='削除' name='corp_".$key."'>";
//	$html.="</span>";
	$html.="<span class='middlespan'>".$val["corpname"]."</span>";
//	$html.="<span class='widespan'>";
//	$html.="<a href='".$val["url"]."' target=_blank>";
//	$html.=$val["url"];
//	$html.="</a></span>";
	$html.="<span class='middlespan'>".$val["part"]."</span>";
  $html.="<div class='clr'></div>";
  $html.="</li>";
 }
 echo $html;
}
catch(Exception $e){
	echo "<li>err:".$e->getMessage."</li>";
}
?>
