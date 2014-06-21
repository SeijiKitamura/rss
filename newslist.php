<?php
require_once("php/rssfunction.php");
$pagename=$PAGE["newslist.php"];
echo getHead($pagename);
echo getHeader($pagename);
echo getLeftMenu();
echo getTopNews();
echo getFooter($pagename);
echo getHtmlEnd($pagename);
?>

