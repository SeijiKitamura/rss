<?php
require_once("rssfunction.php");
$corplist=getCorpList();
echo json_encode($corplist);
?>
