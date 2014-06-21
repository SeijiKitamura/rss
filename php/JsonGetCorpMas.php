<?php
require_once("rssfunction.php");
$corpid=$_GET["requestcorpid"];

$corpary=getCorpList($corpid);
echo json_encode($corpary);
?>
