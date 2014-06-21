<?php
require_once("rssfunction.php");
$linkarray=getItemList($_GET["corpid"]);
setItemList($linkarray);
?>
