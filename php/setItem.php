<?php
require_once("rssfunction.php");

$itemid=$_GET["requestitemid"];
$corpid=$_GET["requestcorpid"];
$itemurl=$_GET["requestoriginalitemurl"];
$saleday=$_GET["requestsaleday"];
$storesaleday=$_GET["requeststoresaleday"];
$itemcomment=$_GET["requestitemcomment"];
$lincode=$_GET["requestlincode"];
$itemstatus=$_GET["requestitemstatus"];
$imgurl=$_GET["requestimgurl"];
$pagetitle=$_GET["requestpagetitle"];

setItem($itemid,$corpid,$itemurl,$pagetitle,$saleday,$storesaleday,$itemcomment,$lincode,$itemstatus,$imgurl);
?>
