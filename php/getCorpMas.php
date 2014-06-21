<?php
require_once("rssfunction.php");

$corpname=$_GET["requestcorpname"];
$corplist=getCorpList();
foreach($corplist as $key=>$val){
	if($corpname===$val["corpname"]){
		echo json_encode($val);
		return;
	}
}
?>
