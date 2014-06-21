<?php
require_once("rssfunction.php");
$corpname=$_GET["requestcorpname"];
$url=$_GET["requesturl"];
$part=$_GET["requestpart"];

if(setPage($url,$part,$corpname)){
	if($linkarray=getItemList($url,$part)){
	 if($linkarray=setItemList($linkarray)){
		 if($linkarray=getLinkImg($linkarray)){
		  if($linkarray=setImgList($linkarray)){
    		echo "<pre>";
    		print_r($linkarray);
			}
		 }
	 }
 }
}
?>
