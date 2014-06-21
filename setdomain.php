<?php
require_once("php/db.class.php");

try{
  $url=$_GET["requesturl"];
	$part=$_GET["requestpart"];
	$corpname=$_GET["requestcorpname"];

	//URLチェック
	$pattern="/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/";
	if(! preg_match($pattern,$url)){
	 throw new exception("URLが不正です");
	}

	//partチェック
	$pattern="/^div/";
	if(! preg_match($pattern,$part)){
	 throw new exception("PARTが不正です");
	}

	//corpnameチェック
	if(! $corpname){
	 throw new exception("会社名空欄です");
	}

 $db=new DB();
 $db->updatecol=array( "domain"=>$url
	                    ,"part"  =>$part
											,"corpname"=>$corpname);
 $db->from=TB_RSSPAGE;
 $db->where="domain='".$url."'";
 $db->update();
 echo "登録しました";

 
}
catch(Exception $e){
	echo "err(エラー):".$e->getMessage();
}
?>
