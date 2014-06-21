<?php
require_once("php/db.class.php");
try{
	$db=new DB();
	$db->CreateTable(TB_RSSITEM);
}
catch(Exception $e){
	echo "err(エラー):".$e->getMessage();
}
?>
