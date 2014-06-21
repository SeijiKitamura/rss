<?php
require_once("server.conf.php");

//---------------------------------------------------//
// デバックモード(true で「する」、falseで「しない」 //
//---------------------------------------------------//
define("DEBUG",true);
//---------------------------------------------------//

//---------------------------------------------------//
// 定数
//---------------------------------------------------//
define("MAXNEWSLIST",10); //取得する最大ニュース数
define("BEFOREDAY",3);  //最新ニュースを何日前からとするか
define("PAGEBREAK",4);    //改ページするアイテム数
define("TWITTER","<a href='https://twitter.com/share' class='twitter-share-button' data-lang='ja'>ツイート</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>");
////---------------------------------------------------//
//// Web系ディレクトリ系定数
////---------------------------------------------------//
//define("IMG" ,HOME."img/");             //画像ディレクトリ
//define("JS"  ,HOME."js/");              //JavaScript Jquery保存場所
//define("PHP" ,HOME."php/");             //PHP
//define("CSS" ,HOME."css/");             //CSS
//define("DATA",HOME."data/");            //データ
//define("JQNAME"         ,"jquery.js");  //jQueryファイル名
//
////---------------------------------------------------//
//// ファイル系定数(Web用)
////---------------------------------------------------//
//define("FAV"     ,IMG.FAVNAME);       //ファビコン
//define("LOGO"    ,IMG.LOGONAME);      //ロゴ
//define("JQ"      ,JS.JQNAME);         //jQueryファイル名
//
////---------------------------------------------------//
//// ファイルディレクトリ系定数(cron用)
////---------------------------------------------------//
//define("IMGDIR" ,ROOTDIR.IMG);  //画像保存場所
//define("JSDIR"  ,ROOTDIR.JS);   //JavaScript Jquery保存場所
//define("PHPDIR" ,ROOTDIR.PHP);  //PHP
//define("CSSDIR" ,ROOTDIR.CSS);  //css
//define("DATADIR",ROOTDIR.DATA); //更新データ
////---------------------------------------------------//

//----------------------------------------------------------//
// ファイル名定数(これがそのままテーブル名となる)
//----------------------------------------------------------//
define("RSSPAGE"  ,"rsspage");           //取得するページ
define("RSSITEM"  ,"rssitem");           //単品リスト
define("RSSIMG"   ,"rssimg" );           //画像リスト
define("RSSLIN"   ,"rsslin" );           //部門マスタ
//---------------------------------------------------//

//---------------------------------------------------//
// DB 接続系定数
//---------------------------------------------------//

//---------------------------------------------------//
// DB テーブル名定数
//---------------------------------------------------//
define("TB_RSSPAGE"     ,TABLE_PREFIX.RSSPAGE);     //ページマスタ
define("TB_RSSITEM"     ,TABLE_PREFIX.RSSITEM);     //単品リスト
define("TB_RSSIMG"      ,TABLE_PREFIX.RSSIMG );     //画像リスト
define("TB_RSSLIN"      ,TABLE_PREFIX.RSSLIN );     //部門マスタ

//---------------------------------------------------//
// DB テーブル列系定数
//---------------------------------------------------//
define("IDATE"   ,"idate"); //作成日時。各テーブルに必ずセットされる。
define("CDATE"   ,"cdate"); //更新日時。各テーブルに必ずセットされる。
define("IDATESQL"," ".IDATE." timestamp not null default current_timestamp");
define("CDATESQL"," ".CDATE." timestamp     null");
 //---------------------------------------------------//

//---------------------------------------------------//
// テーブル情報                                      //
//---------------------------------------------------//

$TABLES=array(
               TB_RSSPAGE=>array(
                             "corpid"  =>array( "type"   =>"int"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>"1"
                                               ,"default"=>"''"
                                               ,"primary"=>1
                                               ,"local"  =>"会社番号"
                                              )//corpid
                            ,"url"     =>array( "type"   =>"varchar(3000)"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>""
                                               ,"default"=>"''"
                                               ,"primary"=>0
                                               ,"local"  =>"メーカーURL"
                                              )//url
                           ,"part"    => array( "type"   =>"varchar(255)"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>""
                                               ,"default"=>"''"
                                               ,"primary"=>0
                                               ,"local"  =>"パート"
                                              )//part
                           ,"corpname"=> array( "type"   =>"varchar(255)"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>""
                                               ,"default"=>"''"
                                               ,"primary"=>0
                                               ,"local"  =>"会社名"
                                              )//corpname
                           ,"fcorpname"=> array( "type"   =>"varchar(255)"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>""
                                               ,"default"=>"''"
                                               ,"primary"=>0
                                               ,"local"  =>"ふりがな"
                                              )//corpname

                             ,"status"   =>array( "type"   =>"varchar(255)"
                                                 ,"null"   =>"not null"
                                                 ,"extra"  =>""
                                                 ,"default"=>"''"
                                                 ,"primary"=>""
                                                 ,"local"  =>"状態"
                                                )//status
                            )//TB_RSSPAGE
              ,TB_RSSITEM=>array(
                             "itemid"  =>array( "type"   =>"int"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>"1"
                                               ,"default"=>""
                                               ,"primary"=>1
                                               ,"local"  =>"アイテム番号"
                                              )//itemid
                            ,"corpid"  =>array( "type"   =>"int"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>""
                                               ,"default"=>"0"
                                               ,"primary"=>2
                                               ,"local"  =>"会社番号"
                                              )//corpid
                          ,"itemurl"   =>array( "type"   =>"varchar(3000)"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>""
                                               ,"default"=>"''"
                                               ,"primary"=>0
                                               ,"local"  =>"商品URL"
                                              )//itemurl
                  ,"originalitemurl"   =>array( "type"   =>"varchar(3000)"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>""
                                               ,"default"=>"''"
                                               ,"primary"=>0
                                               ,"local"  =>"商品URL"
                                              )//originalitemurl
                           ,"lincode"  =>array( "type"   =>"int"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>""
                                               ,"default"=>0
                                               ,"primary"=>0
                                               ,"local"  =>"部門番号"
                                              )//lincode
                           ,"pagetitle"=>array( "type"   =>"varchar(3000)"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>""
                                               ,"default"=>"''"
                                               ,"primary"=>""
                                               ,"local"  =>"タイトル"
                                              )//pagetitle
                       ,"originaltitle"=>array( "type"   =>"varchar(3000)"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>""
                                               ,"default"=>"''"
                                               ,"primary"=>""
                                               ,"local"  =>"オリジナルタイトル"
                                              )//originalpagetitle
                       ,"itemcomment"  =>array( "type"   =>"varchar(3000)"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>""
                                               ,"default"=>"''"
                                               ,"primary"=>""
                                               ,"local"  =>"コメント"
                                              )//itemcomment
                           ,"saleday"  =>array( "type"   =>"date"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>""
                                               ,"default"=>"'1970/1/1'"
                                               ,"primary"=>""
                                               ,"local"  =>"発売日"
                                              )//saleday
                      ,"storesaleday"  =>array( "type"   =>"date"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>""
                                               ,"default"=>"'1970/1/1'"
                                               ,"primary"=>""
                                               ,"local"  =>"店舗発売日"
                                              )//storesaleday

                           ,"status"   =>array( "type"   =>"varchar(255)"
                                                 ,"null"   =>"not null"
                                                 ,"extra"  =>""
                                                 ,"default"=>"''"
                                                 ,"primary"=>""
                                                 ,"local"  =>"状態"
                                                )//status
                            )//TB_RSSITEM
              ,TB_RSSIMG=>array(
                             "imgid"   =>array( "type"   =>"int"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>"1"
                                               ,"default"=>"''"
                                               ,"primary"=>1
                                               ,"local"  =>"画像番号"
                                              )//id
                            ,"itemid"  =>array( "type"   =>"int"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>""
                                               ,"default"=>"0"
                                               ,"primary"=>2
                                               ,"local"  =>"アイテム番号"
                                              )//itemid
                         ,"imgurl"     =>array( "type"   =>"varchar(1000)"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>""
                                               ,"default"=>"''"
                                               ,"primary"=>2
                                               ,"local"  =>"画像URL"
                                              )//imgurl
                           ,"status"   =>array( "type"   =>"varchar(255)"
                                                 ,"null"   =>"not null"
                                                 ,"extra"  =>""
                                                 ,"default"=>"''"
                                                 ,"primary"=>""
                                                 ,"local"  =>"状態"
                                              )//status
								               )//TB_RSSIMG
              ,TB_RSSLIN=>array(
                            "lincode"  =>array( "type"   =>"int"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>""
                                               ,"default"=>0
                                               ,"primary"=>1
                                               ,"local"  =>"部門番号"
                                              )//lincode
                           ,"linname"  =>array( "type"   =>"varchar(255)"
                                               ,"null"   =>"not null"
                                               ,"extra"  =>""
                                               ,"default"=>"''"
                                               ,"primary"=>""
                                               ,"local"  =>"部門名"
                                              )//linname

							                 )//TB_LINMAS
            );//TABLES

//---------------------------------------------------//

//---------------------------------------------------//
// ステータス配列
//---------------------------------------------------//
$RSSSTATUS=array(
	                0=>"対象外"
								, 1=>"未読"
								, 2=>"既読"
								, 3=>"リンク切れ"
							);//$RSSSTATUS



//---------------------------------------------------//
// 部門配列
//---------------------------------------------------//
$LINMAS=array(
	              0=>"未分類"
	             ,1=>"青果"
							 ,2=>"精肉"
							 ,3=>"鮮魚"
							 ,4=>"惣菜"
							 ,5=>"パン"
							 ,6=>"洋日配"
							 ,7=>"和日配"
							 ,8=>"加工食品"
							 ,9=>"飲料"
							 ,10=>"菓子"
							 ,11=>"酒"
							 ,12=>"その他"
						 );

//---------------------------------------------------//
// ページ配列
//---------------------------------------------------//
$PAGE["addcorp.php"]=array( "title"      =>"メーカー登録画面"
                           ,"keyword"    =>"スーパーキタムラ,rss,新商品情報"
                           ,"description"=>""
                           ,"css"        =>"rss.css"
                           ,"link1"      =>"addcorp.php"
                           ,"pagename1"  =>"メーカー登録"
                           ,"link2"      =>"itementry.php"
                           ,"pagename2"  =>"アイテム選択"
                           ,"link3"      =>""
                           ,"pagename3"  =>""
                           ,"link3"      =>"newslist.php"
                           ,"pagename3"  =>"プレビュー"
                          );

$PAGE["itementry.php"]=array( "title"      =>"商品登録画面"
                           ,"keyword"    =>"スーパーキタムラ,rss,新商品情報"
                           ,"description"=>""
                           ,"css"        =>"rss.css"
                           ,"link1"      =>"addcorp.php"
                           ,"pagename1"  =>"メーカー登録"
                           ,"link2"      =>"itementry.php"
                           ,"pagename2"  =>"アイテム選択"
                           ,"link3"      =>"newslist.php"
                           ,"pagename3"  =>"プレビュー"
                          );

$PAGE["itementry.php"]=array( "title"      =>"商品登録画面"
                           ,"keyword"    =>"スーパーキタムラ,rss,新商品情報"
                           ,"description"=>""
                           ,"css"        =>"rss.css"
                           ,"link1"      =>"addcorp.php"
                           ,"pagename1"  =>"メーカー登録"
                           ,"link2"      =>"itementry.php"
                           ,"pagename2"  =>"アイテム選択"
                           ,"link3"      =>"newslist.php"
                           ,"pagename3"  =>"プレビュー"
                          );


$PAGE["newslist.php"] =array( "title"      =>"プレビュー"
                           ,"keyword"    =>"スーパーキタムラ,rss,新商品情報"
                           ,"description"=>""
                           ,"css"        =>"rss.css"
                           ,"link1"      =>"addcorp.php"
                           ,"pagename1"  =>"メーカー登録"
                           ,"link2"      =>"itementry.php"
                           ,"pagename2"  =>"アイテム選択"
                           ,"link3"      =>"newslist.php"
                           ,"pagename3"  =>"プレビュー"
                          );

$PAGE["newslist.html"] =array( "title"   =>"メーカー新商品情報|スーパーキタムラ"
                           ,"keyword"    =>"スーパーキタムラ,rss,メーカー新商品情報"
                           ,"description"=>"メーカーから発表された最新の新商品情報をまとめました。スーパーキタムラ販売予定日も掲載中。独自のキュレーションシステム構築中〜！"
                           ,"css"        =>"rss.css"
                           ,"link1"      =>"newslist.html"
                           ,"pagename1"  =>"最新ニュース"
                           ,"link2"      =>"linlist.html"
                           ,"pagename2"  =>"部門一覧"
                           ,"link3"      =>"corplist.html"
                           ,"pagename3"  =>"メーカー一覧"
                           ,"link4"      =>"timeline.html"
                           ,"pagename4"  =>"発売日順"
                          );

$PAGE["linlist.html"] =array( "title"   =>"部門一覧 新商品情報|スーパーキタムラ"
                           ,"keyword"    =>"スーパーキタムラ,rss,メーカー新商品情報"
                           ,"description"=>"メーカーから発表された最新の新商品情報を部門別一覧にまとめました。スーパーキタムラ販売予定日も掲載中。独自のキュレーションシステム構築中〜！"
                           ,"css"        =>"rss.css"
                           ,"link1"      =>"newslist.html"
                           ,"pagename1"  =>"最新ニュース"
                           ,"link2"      =>"linlist.html"
                           ,"pagename2"  =>"部門一覧"
                           ,"link3"      =>"corplist.html"
                           ,"pagename3"  =>"メーカー一覧"
                           ,"link4"      =>"timeline.html"
                           ,"pagename4"  =>"発売日順"

                          );

$PAGE["corplist.html"] =array( "title"   =>"メーカー一覧 新商品情報|スーパーキタムラ"
                           ,"keyword"    =>"スーパーキタムラ,rss,メーカー新商品情報"
                           ,"description"=>"メーカーから発表された最新の新商品情報をメーカー別一覧にまとめました。スーパーキタムラ販売予定日も掲載中。独自のキュレーションシステム構築中〜！"
                           ,"css"        =>"rss.css"
                           ,"link1"      =>"newslist.html"
                           ,"pagename1"  =>"最新ニュース"
                           ,"link2"      =>"linlist.html"
                           ,"pagename2"  =>"部門一覧"
                           ,"link3"      =>"corplist.html"
                           ,"pagename3"  =>"メーカー一覧"
                           ,"link4"      =>"timeline.html"
                           ,"pagename4"  =>"発売日順"

                          );


$PAGE["corp.html"] =array( "title"   =>"/*corpname*/ 新商品情報|スーパーキタムラ"
                           ,"keyword"    =>"/*corpname*/,スーパーキタムラ,rss,メーカー新商品情報"
                           ,"description"=>"/*corpname*/から発表された最新の新商品情報をまとめました。スーパーキタムラ販売予定日も掲載中。独自のキュレーションシステム構築中〜！"
                           ,"css"        =>"rss.css"
                           ,"link1"      =>"newslist.html"
                           ,"pagename1"  =>"最新ニュース"
                           ,"link2"      =>"linlist.html"
                           ,"pagename2"  =>"部門一覧"
                           ,"link3"      =>"corplist.html"
                           ,"pagename3"  =>"メーカー一覧"
                           ,"link4"      =>"timeline.html"
                           ,"pagename4"  =>"発売日順"
                          );

$PAGE["lincode.html"] =array( "title"   =>"/*linname*/部門新商品情報|スーパーキタムラ"
                           ,"keyword"    =>"/*linname*/,スーパーキタムラ,rss,メーカー新商品情報"
                           ,"description"=>"メーカーから発表された/*linname*/部門の最新の新商品情報をまとめました。スーパーキタムラ販売予定日も掲載中。独自のキュレーションシステム構築中〜！"
                           ,"css"        =>"rss.css"
                           ,"link1"      =>"newslist.html"
                           ,"pagename1"  =>"最新ニュース"
                           ,"link2"      =>"linlist.html"
                           ,"pagename2"  =>"部門一覧"
                           ,"link3"      =>"corplist.html"
                           ,"pagename3"  =>"メーカー一覧"
                           ,"link4"      =>"timeline.html"
                           ,"pagename4"  =>"発売日順"

                          );

$PAGE["timeline.html"] =array( "title"   =>"発売日順新商品情報|スーパーキタムラ"
                           ,"keyword"    =>"スーパーキタムラ,rss,メーカー新商品情報"
                           ,"description"=>"メーカーから発表された最新の新商品情報を発売日降順でまとめました。スーパーキタムラ販売予定日も掲載中。独自のキュレーションシステム構築中〜！"
                           ,"css"        =>"rss.css"
                           ,"link1"      =>"newslist.html"
                           ,"pagename1"  =>"最新ニュース"
                           ,"link2"      =>"linlist.html"
                           ,"pagename2"  =>"部門一覧"
                           ,"link3"      =>"corplist.html"
                           ,"pagename3"  =>"メーカー一覧"
                           ,"link4"      =>"timeline.html"
                           ,"pagename4"  =>"発売日順"
                          );

$PAGE["item.html"] =array( "title"   =>"/*originaltitle*/新商品情報|スーパーキタムラ"
                           ,"keyword"    =>"スーパーキタムラ,rss,メーカー新商品情報"
                           ,"description"=>"/*saleday*/発売予定。/*itemcomment*//*corpname*/から発表された最新の新商品情報をまとめました。スーパーキタムラ販売予定日も掲載中。独自のキュレーションシステム構築中〜！"
                           ,"css"        =>"rss.css"
                           ,"link1"      =>"newslist.html"
                           ,"pagename1"  =>"最新ニュース"
                           ,"link2"      =>"linlist.html"
                           ,"pagename2"  =>"部門一覧"
                           ,"link3"      =>"corplist.html"
                           ,"pagename3"  =>"メーカー一覧"
                           ,"link4"      =>"timeline.html"
                           ,"pagename4"  =>"発売日順"
                          );

//---------------------------------------------------//
// CSV並び順配列
//---------------------------------------------------//
$CSVCOLUMNS=array(
                 );//CSVCOLUMNS
//---------------------------------------------------//


?>
