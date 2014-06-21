<?php
require_once("php/db.class.php");

try{
	$itemurl =$_GET["requestitemurl"];
	$itemtitle=$_GET["requestitemtitle"];
	$imgurl  =$_GET["requestimgurl"];
	//引数チェック
	$pattern="/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/";

	if(! preg_match($pattern,$itemurl)){
	 throw new exception("商品URLが不正です");
	}

	if(! preg_match($pattern,$imgurl)){
	 throw new exception("画像URLが不正です");
	}
  
  $db=new DB();
  $db->updatecol=array( "url"     =>$itemurl
		                   ,"pagetitle"=>$itemtitle
											 ,"imgurl"  =>$imgurl
											 ,"status"  =>""
										  );
  $db->from=TB_RSSITEM;
  $db->where="url='".$itemurl."'";
  $db->update();
  echo "登録しました";




}
catch(Exception $e){
	echo "err(エラー):".$e->getMessage();
}
?>
