<?php
require_once("php/rssfunction.php");

echo date("Y-m-d H:i:s")." 更新開始\n";

try{

 setRSS();
 echo date("Y-m-d H:i:s")." 更新終了\n";

}
catch(Exception $e){
	echo date("Y-m-d H:i:s")." ".$e->getMessage()."\n";
}
?>
