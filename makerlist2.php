<?php
require_once("php/rssfunction.php");

$itemlist=getPickUpNews();

//アイテムリストからメーカー別ニュース数と最新ニュース数をゲット
$maker=array();
$corpname="";
$news=0;
$hotnews=0;
$corpid=0;
$newslist=array();
foreach($itemlist as $key=>$val){
  if($key==true && $corpname!==$val["corpname"]){
    $maker[$corpname]=array( "news"=>$news
                            ,"hotnews"=>$hotnews
                            ,"corpid"=>$corpid
                            ,"newslist"=>$newslist);
    $news=0;
    $hotnews=0;
    $corpid=0;
    $newslist=array();
  }
  $corpname=$val["corpname"];
  $corpid=$val["corpid"];
  $news++;
  if(strtotime($val["idate"])>strtotime("-3days")){
   $hotnews++;
  }
  $newslist[]=$val;
}

foreach($maker as $key=>$val){
  $href="http://www2.kita-grp.co.jp/hp/rss/html/".$val["corpid"].".html";
  $corpname=$key;
  $html="";
  $html=<<<EOF
  <!DOCTYPE html>
  <html lang="ja">
   <head>
     <base href="http:www2.kita-grp.co.jp/hp/index.php" target="_self">
  	 <meta charset="utf-8">
  	 <title>${corpname}の最新ニュース</title>
     <meta name="keywords" content="スーパーキタムラ、RSS、新商品情報、入荷情報">
     <meta name="description" content="${corpname}の最新ニュースです。あわせて、当店入荷日も近日中に発表予定となっております。「毎日の食卓をワクワクに」を目標にいつも新しい商品が店頭にあるようにこころがけています">

     <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  	 <style type="text/css">
     </style>
  	 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
   <style type="text/css">
  /*html5 css リセット*/

  /* html5doctor.com Reset Stylesheetv1.6.1Last Updated: 2010-09-17Author: Richard Clark - http://richclarkdesign.com Twitter: @rich_clark*/html, body, div, span, object, iframe,h1, h2, h3, h4, h5, h6, p, blockquote, pre,abbr, address, cite, code,del, dfn, em, img, ins, kbd, q, samp,small, strong, sub, sup, var,b, i,dl, dt, dd, ol, ul, li,fieldset, form, label, legend,table, caption, tbody, tfoot, thead, tr, th, td,article, aside, canvas, details, figcaption, figure, footer, header, hgroup, menu, nav, section, summary,time, mark, audio, video {    margin:0;    padding:0;    border:0;    outline:0;    font-size:100%;    vertical-align:baseline;    background:transparent;}body {    line-height:1;}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section {   display:block;}nav ul {    list-style:none;}blockquote, q {    quotes:none;}blockquote:before, blockquote:after,q:before, q:after {    content:'';    content:none;}a {    margin:0;    padding:0;    font-size:100%;    vertical-align:baseline;    background:transparent;}/* change colours to suit your needs */ins {    background-color:#ff9;    color:#000;    text-decoration:none;}/* change colours to suit your needs */mark {    background-color:#ff9;    color:#000;     font-style:italic;    font-weight:bold;}del {    text-decoration: line-through;}abbr[title], dfn[title] {    border-bottom:1px dotted;    cursor:help;}table {    border-collapse:collapse;    border-spacing:0;}/* change border colour to suit your needs */hr {    display:block;    height:1px;    border:0;       border-top:1px solid #cccccc;    margin:1em 0;    padding:0;}input, select {    vertical-align:middle;}

  body,ul,h1{
    width:100%;
    margin:0;
    padding:0;
  }

  a{
    text-decoration:none;
  }

  div.clr{
   width  :0;
   height :0;
   clear  :both;
  }

  header {
    width:100%;
    background-color:#FFFFFF;
  }

  header div.divcorpimg{
    width:30%;
  }

  header div.divcorpimg img{
    width:100%;
  }

  header ul{
    list-style:none;
  }

  header ul li{
    float:left;
    width:30%;
  }


  header ul li a{
    display:block;
    border : 1px solid #D3D3D3;
    width  :100%;
    margin :5px 5px 5px 0;
    padding:20px 0;
    text-align :center;
  }

  div.divimg{
    width:80%;
    overflow:hidden;
  }

  div.divimg img{
    height:100%;
  }
  section ul{
    list-style:none;
  }
  section ul li{
  /*float:left;*/
  }

  div.divmakerlist{
    width:100%;
    border-top:1px solid #DCDCDC;
    padding:10px;
  }

  div.divcorp{
    width :100%;
  }

  div.divcorp h1{
    padding:5px;
  }


  div.divitemimg{
    width :50%;
    height:80%;
    overflow:hidden;
    float:left;
  }
  div.divitemimg img{
    width :90%;
  }

  div.topitem{
    width:45%;
    float:left;
  }

  div.divitemlist{
    float:left;
    width:100%;
  }

  div.divitemlist dl dt{
    width:25%;
    margin-top:30px;
    float:left;
    clear:both;
    border-top : 1px solid #D3D3D3;
  }

  div.itemlistimg{
    width:90%;
    height:80%;
    overflow:hidden;
  }

  div.itemlistimg img{
    width:90%;
  }
  div.divitemlist dl dd{
    width:70%;
    float:left;
    margin-top:30px;
    border-top : 1px solid #D3D3D3;
  }

   </style>
   </head>

   <body>
    <header>
     <div class='divcorpimg'>
      <a href="http://www2.kita-grp.co.jp/hp/index.php">
      <img src="http://www2.kita-grp.co.jp/hp/img/logo2.jpg">
      </a>
     </div>

     <ul>
      <li><a href="http://www2.kita-grp.co.jp/hp/rss/newslist.html">最新ニュース</a></li>
      <li><a href="http://www2.kita-grp.co.jp/hp/rss/makerlist.html">メーカー別</a></li>
      <li><a href="">部門別</a></li>
     </ul>
     <div class="clr"></div>
    </header>
    <div id="wrapper">
EOF;

  $html.="<section>";
  $html.="<div class='divcorp'>";
  $html.=$key;
  $html.=" New :".$val["hotnews"];
  $html.=" 合計:".$val["news"];
  $html.="</div>";

  $html.="<ul>";
  if(isset($val["newslist"])){
    foreach($val["newslist"] as $key1=>$val1){
      if($key1>2) break;
      $html.="<li>";
      $html.="<div class='divmakerlist'>";
      $html.="<div class='divitemimg'>";
      $html.="&nbsp;";
      if(isset($val1["imgurl"])){
        $html.="<a href='".$val1["itemurl"]."'>";
        $html.="<img src='".$val1["imgurl"]."'>";
        $html.="</a>";
      }// if(isset($val1["imgurl"])){
      $html.="</div>";//divitemimg

      $html.="<div class='topitem'>";
      $html.="<a href='".$val1["itemurl"]."'>";
      $html.="<h2>";
      $html.=date("Y-m-d",strtotime($val1["idate"]));
      $html.="</h2>";
      $html.="<p>";
      $html.=$val1["pagetitle"];
      $html.="</p>";
      $html.="</a>";
      $html.="</div>";//div.topitem

      $html.="<div class='clr'></div>";
      }// foreach($val["newslist"] as $key1=>$val1){
      $html.="</div>";//div.divmakerlist
      $html.="</li>";

    foreach($val["newslist"] as $key1=>$val1){
      if($key1<3) continue;
      $html.="<li>";
      $html.="<div class='divitemlist'>";
      $html.="<dl>";
      $html.="<dt>";
      $html.="<div class='itemlistimg'>";
      $html.="<img src='".$val1["imgurl"]."'>";
      $html.="</div>";
      $html.="</dt>";

      $html.="<dd>";
      $html.=date("Y-m-d",strtotime($val1["idate"]));
      $html.="<br>";
      $html.="<a href='".$val1["itemurl"]."'>";
      $html.=mb_substr(ltrim($val1["pagetitle"]),0,52,"UTF-8");
      $html.="</a>";
      $html.="<br>対象部門:".$LINMAS[$val1["lincode"]];
      $html.="</dd>";
      $html.="</dl>";
    }//foreach($val["newslist"]
      $html.="</li>";
  }// if(isset($val["newslist"])){

  $html.="</ul>";
  $html.="</section>";

  $html.=<<<EOF
    </div>

    <footer>
    </footer>

   </body>
  </html>
EOF;

  $html=str_replace("\n","",$html);
  file_put_contents("/home/kennpin1/rss/html/".$val["corpid"].".html",$html);
}
?>

