<?php
require_once("rssfunction.php");
try{
 getDiv($_GET["requesturl"],$_GET["requestpart"]);
}
catch(Exception $e){
	echo "err:".$e->getMessage();
}
?>
