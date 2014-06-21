<?php
require_once("./php/db.class.php");
require_once("./php/simple_html_dom.php");
require_once("./php/make_uri.php");

try{

//引数チェック
	$pattern="/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/";
	if(! preg_match($pattern,$_GET["requesturl"])){
		throw new exception("URLが不正です");
	}
	else $requesturl=$_GET["requesturl"];

	if($_GET["requestpart"]) $part=$_GET["requestpart"];
	else $part="";

//指定されたURLページをゲットし配列へ格納
	if(! $html=file_get_html($requesturl)){
		throw new exception("ページが取得できません");
	}	

//まずはリンクだけゲット
	$tag=array();
	foreach($html->find($part." a") as $element){
		//リンクゲット
		$href=make_uri($requesturl,$element->href);

		//同一URLは登録しない
		$flg=0;
		foreach($tag as $key=>$val){
			if($val["url"]==$href){
			 	$flg=1;
				break;
			}
		}
		if(! $flg){
			$tag[]=array( "url"=>make_uri($requesturl,$element->href)
				           ,"text"=>$element->plaintext
			            );
		}
	}//foreach
	$html->clear();
	unset($html);


	//リンク先画像取得
	foreach($tag as $key=>$val){
	  if(! $html=file_get_html($val["url"])){
			continue;
	  }	
	 foreach($html->find("img") as $element){
		 $tag[$key]["imgurl"][]=make_uri($val["url"],$element->src);
	 }
	 $html->clear();
	}
	unset($html);

	//DB検索
	foreach($tag as $key=>$val){
   //ステータスフラグセット
	 $status=1;

	 $db=new DB();
	 $db->select="url,imgurl,status";
	 $db->from=TB_RSSITEM;
	 $db->where ="url='".$val["url"]."'";
	 $result=$db->getArray();
	 foreach($result as $key1=>$val1){
		 if($val["url"]==$val1["url"]){
		   //対象外なら配列を削除
			 if($val1["status"]==="対象外"){
				 unset($tag[$key]);
				 break;
			 }//if

		   //登録済みなら画像を書き換え
			 unset($tag[$key]["imgurl"]);
			 $tag[$key]["imgurl"][]=$val1["imgurl"];
		 }
	 }//foreach $result

	}//foreach $tag
	
	//html作成
	$html="<ul style='width:100%;'>";
	foreach($tag as $key=>$val){
   $html.="<li id='list_".$key."' style='width:100%;'>";
	 $html.="<h2>";
	 $html.="<a href='".$val["url"]."' id='a_".$key."' target='_blank' style='display:block;margin:15px;float:left;'>";
	 $html.=$val["text"];
	 $html.="</a>";
	 $html.="<input type='button' value='対象外' name='out_".$key."' style='margin:15px;float:left;'>";
	 $html.="<div style='clear:both;'></div>";
	 $html.="</h2>";
	 $html.="<div id='div_".$key."' style='width:100%;'>";
	 foreach($val["imgurl"] as $key1=>$val1){
		 $html.="<div style='width:17%;height:150px;float:left;border:1px solid #000000;margin:2px;'>";
		 $html.="<div id='imgdiv_".$key."_".$key1."'style='width:100%;height:100px;overflow:hidden;'>";
	   $html.="<a href='".$val1."' id='a_".$key."' target='_blank'>";
		 $html.="<img src='".$val1."' id='img_".$key."_".$key1."' style='width:90%;'>";
	   $html.="</a>";
		 $html.="</div>";
		 $html.="<input type='button' value='choice' name='btn_".$key."_".$key1."'>";
		 $html.="</div>";
	 }
   $html.="</div>\n";
   $html.="</li>\n";
	 $html.="<div style='clear:both;'></div>";
	}

	$html.="</ul>";
	echo $html;

	echo "<pre>";
	print_r($tag);
	echo "</pre>";

}
catch(Exception $e){
	echo "err:".$e->getMessage();
}

?>
