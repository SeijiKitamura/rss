<?php
require_once("./php/simple_html_dom.php");
require_once("./php/make_uri.php");

echo "success";
try{
//引数チェック
	$pattern="/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/";
	if(! preg_match($pattern,$_GET["requesturl"])){
		throw new exception("URLが不正です");
	}
	else $requesturl=$_GET["requesturl"];

//指定されたURLページをゲットし配列へ格納
	if(! $html=file_get_html($requesturl)){
		throw new exception("ページが取得できません");
	}	

	if(! $html=file_get_html($requesturl)){
		throw new exception("ページが取得できません");
	}	


	//divを検索して配列へ格納
	foreach($html->find("a") as $element){
		$element->href=make_uri($requesturl,$element->href);
	}

	foreach($html->find("img") as $element){
		$element->src=make_uri($requesturl,$element->src);
	}

	$cnt=0;
	foreach($html->find("div") as $element){
		if($cnt>0){
		 echo "<h1>div#".$element->attr["id"]." "."</h1>";
//		 echo $element->plaintext."<br>";
		 echo $element;
		}
		$cnt++;
	}
	$html->clear();
	unset($html);

}
catch(Exception $e){
	echo "err:".$e->getMessage();
}
?>
