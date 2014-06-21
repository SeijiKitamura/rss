<?php
require_once("rssfunction.php");
global $LINMAS;

$corpname=$_GET["requestcorpname"];
$opt=$_GET["requestoption"];// (0=>全件表示 1=>対象のみ表示)

$corplist=getNewsList($corpname);

$html="";
$html.="<dl id='newslist'>";
$html.="<dt class='shortdt'>日付</dt>";
$html.="<dd class='middledd'>タイトル</dd>";
$html.="<div class='clr'></div>";

foreach($corplist["itemlist"] as $key=>$val){
	$html.="<dt id='dt_".$key."' ";
	if($opt && $val["itemstatus"]==="対象外"){
		$html.=" style='display:none;'";
	}
	$html.=">";
	$html.=date("m月d日",strtotime($val["itemidate"]));
	$html.="</dt>";
	$html.="<dd id='dd_".$key."'";
	if($opt && $val["itemstatus"]==="対象外"){
		$html.=" style='display:none;'";
	}
	$html.=">";
	$html.="<h3><a id='a_".$key."' href='".$val["itemurl"]."' target='_blank'>";
	$html.=$val["pagetitle"]."</a>";
	$html.="<input type='button' value='対象外' name='btn_listout_".$key."'>";
	$html.="</h3>";
	$html.="画像URL:<input type='text' value='";
	foreach($val["imglist"] as $key1=>$val1){
		if($val1["imgstatus"]){
			$html.=$val1["imgurl"];
			break;
		}
	}
	$html.="' name='ipt_imgurl_".$key."'>";
	$html.="対象部門:<select id='selectlin_".$key."'>";
	foreach($LINMAS as $lin=>$linname){
		$html.="<option value='".$lin."' ";
		if($lin==$val["lincode"]){
			$html.=" selected";
		}
		$html.=">";
		$html.=$linname;
		$html.="</option>";
	}
	$html.="</select>";
	$html.="<input type='button' value='画像なし' name='btn_nothing_".$key."'>";
	$html.="<input type='button' value='画像全表示' name='btn_allshow_".$key."'>";

	$html.="<div class='clr'></div>";
	$html.="<div id='divimg_".$key."'>";

	//画像フラグ
	$imgflg=0;
	foreach($val["imglist"] as $key1=>$val1){
		if($val1["imgstatus"]){
			$imgflg=1;
			break;
		}	
	}

	//画像表示
	foreach($val["imglist"] as $key1=>$val1){
		$html.="<img src='".$val1["imgurl"]."'";
		//未選択は全件表示
		if($imgflg && ! $val1["imgstatus"]){
			$html.=" style='display:none'";
		}
		$html.=">";
	}
	$html.="</div>";
	$html.="</dd>";
	$html.="<div class='clr'></div>";
}
$html.="</dl>";
echo $html;
echo "<pre>";
print_r($corplist["itemlist"]);
echo "</pre>";
?>
