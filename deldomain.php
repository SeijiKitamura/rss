<?php
require_once("php/db.class.php");
try{
  $url=$_GET["requesturl"];

	//URLチェック
	$pattern="/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/";
	if(! preg_match($pattern,$url)){
	 throw new exception("URLが不正です");
	}

  $db=new DB();
  $db->from=TB_RSSPAGE;
  $db->where="domain='".$url."'";
  $db->delete();
  echo "削除しました";

}
catch(Exception $e){
  echo "err(エラー):".$e->getMessage();
}
?>
