<?php
require_once("db.class.php");

$DIR="/home/kennpin1/rss/backup";
$db=new DB();
if($handle=opendir($DIR)){
  while(false !==($file=readdir($handle))){
    $cnt=0;
    $fp=fopen($DIR."/".$file,"r");
    while(($data=fgetcsv($fp,0,",")) !==FALSE){
      print_r($data);
      if($cnt){
       if($file=="rss_rsspage.csv"){
        $db->updatecol=array(
                              "url"=>$data[0]
                             ,"part"=>$data[1]
                             ,"corpname"=>$data[2]
                             ,"status"=>$data[3]
                            );
        $db->from=TB_RSSPAGE;
       }
       elseif($file=="rss_rssitem.csv"){
        $db->updatecol=array(
                              "url"=>$data[0]
                             ,"itemurl"=>$data[1]
                             ,"lincode"=>$data[2]
                             ,"pagetitle"=>$data[3]
                             ,"status"=>$data[4]
                            );
        $db->from=TB_RSSITEM;
       }
       elseif($file=="rss_rssimg.csv"){
        $db->updatecol=array(
                              "itemurl"=>$data[0]
                             ,"imgurl"=>$data[1]
                             ,"status"=>$data[2]
                            );
        $db->from=TB_RSSIMG;
       }

       $db->where="id=0";
       $db->update();
      }
      $cnt++;
    }
    fclose($fp);
  }
  closedir($handle);
}
?>
