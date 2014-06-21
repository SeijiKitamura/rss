<?php
require_once("php/rssfunction.php");

//バックアップファイル読み込み
$fp=fopen("backup/rss_rsspage.csv","r");
$db=new DB();
$db->from=TB_RSSPAGE;
$db->where="corpid>0";
$db->delete();

echo "<pre>";
if($fp){
  while(!feof($fp)){
    $ary=array();
    $line=fgets($fp);
    echo $line."<br>";
    if(strlen($line)){
      $ary=preg_split("/,/",$line);
      print_r($ary);
      $db->updatecol=array("url"=>$ary[0]
                           ,"part"=>$ary[1]
                           ,"corpname"=>$ary[2]
                           );
      $db->from=TB_RSSPAGE;
      $db->where="corpid=0";
      $db->update();
    }

  }
}
?>
