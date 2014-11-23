<?php
require_once("db.class.php");
require_once("simple_html_dom.php");
require_once("make_uri.php");

define("HTMLPATTERN","/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/");

//---------------------------------------------------//
// 会社マスタ登録
//---------------------------------------------------//
function setCorpName($corpid=null,$url,$part=null,$corpname,$fcorpname=null){
  try{
//会社番号チェック
    if($corpid){
      if(! preg_match("/^[0-9]+$/",$corpid)){
        throw new exception("会社番号不正");
      }
    }

//URLチェック
	  if(! preg_match(HTMLPATTERN,$url)){
			throw new exception("URL不正");
	  }

//URLエンコード
    $url=escapeurl($url);

//会社名空欄チェック
    if(! $corpname){
      throw new exception("会社名空欄");
    }

    $db=new DB();
    $db->updatecol=array( "url"=>$url
                         ,"part"=>$part
                         ,"corpname"=>$corpname
                         ,"fcorpname"=>$fcorpname);
    $db->from=TB_RSSPAGE;
    if($corpid) $db->where="corpid=".$corpid;
    else        $db->where="corpid=0";
    $db->update();
    echo $corpname."を登録しました";

//配列を返す
    $db->select="corpid,url,part,corpname,fcorpname";
    $db->from=TB_RSSPAGE;
    $db->where =" url='".$url."'";
    $db->where.=" and part='".$part."'";
    $db->where.=" and corpname='".$corpname."'";
    $db->where.=" and fcorpname='".$fcorpname."'";
    $corpmas=$db->getArray();
    return $corpmas[0];
  }//try
  catch(Exception $e){
    echo "err(エラー):".$e->getMessage();
  }
}

function delCorp($corpid){
  try{
//会社番号チェック
    if(! preg_match("/^[0-9]+$/",$corpid)){
      throw new exception("会社番号不正");
    }

    echo $corpid;

    $db=new DB();

    //画像データ削除
    $db->select="itemid";
    $db->from=TB_RSSITEM;
    $db->where="corpid=".$corpid;
    $itemarray=$db->getArray();
    if(isset($itemarray)){
      foreach($itemarray as $key=>$val){
        $db->from=TB_RSSIMG;
        $db->where="itemid=".$val["itemid"];
        $db->delete();
      }
    }

    //アイテムデータ削除
    $db->from=TB_RSSITEM;
    $db->where="corpid=".$corpid;
    $db->delete();

    //ページデータ削除
    $db->from=TB_RSSPAGE;
    $db->where="corpid=".$corpid;
    $db->delete();
    echo "削除しました";

  }
  catch(Exception $e){
    echo "err(エラー):".$e->getMessage();
  }
}

//---------------------------------------------------//
// 会社一覧表示
//---------------------------------------------------//
function getCorpList($corpid=null){
  try{
    if($corpid){
      if(! preg_match("/^[0-9]+$/",$corpid)){
        throw new exception("会社番号不正");
      }
    }

    $db=New DB();
    $db->select="corpid,corpname,fcorpname,url,part";
    $db->from=TB_RSSPAGE;
    if($corpid) $db->where="corpid=".$corpid;
    $db->order="fcorpname,corpname";
    $corpary=$db->getArray();
    if(! isset($corpary)){
     throw new exception("会社未登録");
    }

    //URLデコード
    foreach($corpary as $key=>$val){
      $corpary[$key]["url"]=decodeurl($val["url"]);
    }

    //未分類アイテム
    $db->select="corpid,count(itemurl) as newitem";
    $db->from=TB_RSSITEM;
    $db->where =" status<>'対象外'";
    $db->where.=" and lincode=0";
    if($corpid) $db->where.=" and corpid=".$corpid;
    $db->group="corpid";
    $db->order=" corpid";
    $itemcnt=$db->getArray();

    foreach($corpary as $key=>$val){
      if(isset($itemcnt)){
        foreach($itemcnt as $key1=>$val1){
          if($val["corpid"]==$val1["corpid"]){
            $corpary[$key]["newitem"]=$val1["newitem"];
            break;
          }
        }
      }
    }

    //新しいニュース
    $db->select="corpid,count(itemurl) as newsflash";
    $db->from=TB_RSSITEM;
    $db->where=" idate>='".date("Y-m-d",strtotime("-".BEFOREDAY."days"))."'";
    $db->where.=" and status<>'対象外'";
    if($corpid) $db->where.=" and corpid=".$corpid;
    $db->where.=" and lincode>0";
    $db->group="corpid";
    $db->order=" corpid";
    $itemcnt=$db->getArray();

    foreach($corpary as $key=>$val){
      if(isset($itemcnt)){
        foreach($itemcnt as $key1=>$val1){
          if($val["corpid"]==$val1["corpid"]){
            $corpary[$key]["newsflash"]=$val1["newsflash"];
            break;
          }
        }
      }
    }

//対象ニュース数
    $db->select="corpid,count(itemurl) as newscount";
    $db->from=TB_RSSITEM;
    $db->where.=" status<>'対象外'";
    $db->where.=" and lincode>0";
    if($corpid) $db->where.=" and corpid=".$corpid;
    $db->group="corpid";
    $db->order=" corpid";
    $itemcnt=$db->getArray();

    foreach($corpary as $key=>$val){
      if(isset($itemcnt)){
        foreach($itemcnt as $key1=>$val1){
          if($val["corpid"]==$val1["corpid"]){
            $corpary[$key]["newscount"]=$val1["newscount"];
            break;
          }
        }
      }
    }

    return $corpary;
  }
  catch(Exception $e){
    echo "err(エラー):".$e->getMessage();
  }
}

//---------------------------------------------------//
// 指定されたURLからdiv名とelementを返す
//(プレビュー用)
//---------------------------------------------------//

function getDiv($url,$part=null){
	try{
		mb_language("Japanese");

//URLチェック
	  if(! preg_match(HTMLPATTERN,$url)){
	    throw new exception( "URL不正");
	  }

//XML判定
	  if($xml=@simplexml_load_file($url)){
			echo "<ul>";

			//RDF判定
      if($xml->channel->item){
       foreach($xml->channel->item as $val){
			 	echo "<li style='margin:10px;'>";
			 	echo "<a href='".(string)$val->href."' target='_blank'>";
			 	echo (string)$val->title;
			 	echo "</a>";
			 	echo "</li>";
			 }
			}

			//RDF判定
      if($xml->item){
			 foreach($xml->item as $val){
			 	echo "<li style='margin:10px;'>";
			 	echo "<a href='".(string)$val->href."' target='_blank'>";
			 	echo (string)$val->title;
			 	echo "</a>";
			 	echo "</li>";
			 }
			}

			echo "</ul>";
			return ;
	  }

//Partチェック
		if(! $part) $part="div div";

//指定されたURLページをゲットし配列へ格納
  	if(! $html=@file_get_html($url)){
  		throw new exception("HTML取得失敗");
  	}

//画像URLを絶対パスへ変換
		foreach($html->find("img") as $element){
			$href=make_uri($url,$element->src);
			$element->src=$href;
		}

//リンクURLを絶対パスへ変換
		foreach($html->find("a") as $element){
			$href=make_uri($url,$element->href);
			$element->href=$href;
		}

//CSSリンクを絶対パスへ変更して表示
    foreach($html->find("link") as $element){
			$href=make_uri($url,$element->href);
			$element->href=$href;
			//echo $element;
		}

//divを検索
		foreach($html->find($part) as $element){
			echo "<h1 style='width:100% !important;text-align:left;font-size:20px;'>";
			if($element->id){
				echo "div#".$element->id." ";
			}
			if($element->class){
				echo "div.".$element->class." ";
			}
			echo "</h1>";
			//echo $element;
			echo mb_convert_encoding($element,"UTF-8","auto");
			echo "<div style='clear:both'></div>";
		}

		$html->clear();
		unset($html);
	}
	catch(Exception $e){
		$html->clear();
		unset($html);
		echo "err:".$e->getMessage();
	}
}

//---------------------------------------------------//
//指定されたURL、Partを使ってリンク先配列を返す
//(絶対パスに変換、同一リンク削除)
//返り値
//array(,"corpid"   =>メーカーURL
//      ,"itemurl"  =>商品先URL)
//      ,"pagetitle"=>商品タイトル)
//---------------------------------------------------//
function getItemList($corpid){
	mb_language("Japanese");

//corpidチェック
  if(! preg_match("/^[0-9]+$/",$corpid)){
     echo "err:会社番号不正";
     return false;
  }
//URL Partゲット
  $corpmas=getCorpList($corpid);
  $url=$corpmas[0]["url"];
  $part=$corpmas[0]["part"];


//XML判定
	if($xml=@simplexml_load_file($url)){
	  $linkarray=array();

  	//RDF判定
    if($xml->channel->item){
     foreach($xml->channel->item as $val){
      $pagetitle=(string)$val->title;
			$pagetitle=mb_convert_encoding($pagetitle,"UTF-8","auto");
      $linkarray[]=array( "corpid"=>$corpid
		 	                  ,"itemurl"=>(string)$val->link
		 										,"pagetitle"=>$pagetitle);
		 }
		}

  	//RDF判定
    if($xml->item){
     foreach($xml->item as $val){
      $pagetitle=(string)$val->title;
			$pagetitle=mb_convert_encoding($pagetitle,"UTF-8","auto");
      $linkarray[]=array( "corpid"=>$corpid
		 	                  ,"itemurl"=>(string)$val->link
		 										,"pagetitle"=>$pagetitle);
		 }
		}

	  return $linkarray;
	}

//指定されたURLページをゲットし配列へ格納
	if(! $html=@file_get_html($url)){
		echo "HTML取得失敗";
		return false;
	}

//リンクを配列へ格納
	$i=0;
	$linkarray=array();
	foreach($html->find($part." a") as $element){
    //最大件数を超えれば終了
		if($i>MAXNEWSLIST) break;

  	//リンクを絶対パスに変換
		$href=make_uri($url,$element->href);

		//同一URLは登録しない
		$flg=0;
		foreach($linkarray as $key=>$val){
			if(isset($val["url"]) && $val["url"]==$href){
				$flg=1;
				break;
			}
		}//foreach $linkarray
		if(! $flg){
			$pagetitle=mb_convert_encoding($element->plaintext,"UTF-8","auto");
			$linkarray[]=array( "corpid"   =>$corpid
				                 ,"itemurl"  =>$href
				                 ,"pagetitle"=>$pagetitle);
		 $i++;
		}
	}//foreach $html->find
	$html->clear();
	unset($html);

  foreach($linkarray as $key=>$val){
    $linkarray[$key]["itemurl"]=escapeurl($val["itemurl"]);
  }
	return $linkarray;
}

//---------------------------------------------------//
// 渡された配列をDBへ登録(RSS_RSSITEM)
//---------------------------------------------------//
function setItemList($linkarray){
  if(!isset($linkarray)){
    echo "err:データがありません";
    return false;
  }

  $db=new DB();
  foreach($linkarray as $key=>$val){
    $db->updatecol=array( "corpid"=>$val["corpid"]
                         ,"itemurl"=>$val["itemurl"]
                         ,"pagetitle"=>$val["pagetitle"]
                         );
    $db->from=TB_RSSITEM;
    $db->where =" corpid=".$val["corpid"];
    $db->where.=" and itemurl='".$val["itemurl"]."'";
    $db->update();
  }//foreach

  foreach($linkarray as $key=>$val){
    $db->select="itemid";
    $db->from=TB_RSSITEM;
    $db->where =" corpid=".$val["corpid"];
    $db->where.=" and itemurl='".$val["itemurl"]."'";
    $item=$db->getArray();
    if(isset($item)){
      $linkarray[$key]["itemid"]=$item[0]["itemid"];
    }
  }
  return $linkarray;
}

//---------------------------------------------------//
//渡された配列からリンク先画像URLをつけて配列を返す
//(絶対パスに変換)
//引数
//array( "corpid"   =>会社番号
//      ,"itemid"   =>商品番号
//      ,"itemurl"  =>商品URL
//      ,"pagetitle"=>商品タイトル
//     )
//
//返り値
//array( "corpid"   =>会社番号
//      ,"itemurl"  =>商品URL
//      ,"pagetitle"=>商品タイトル
//      ,"img=>array("imgurl"=>画像URL),"status"=>"成功"))
//---------------------------------------------------//
function getLinkImg($linkarray){

	foreach($linkarray as $key=>$val){
   $itemurl=decodeurl($val["itemurl"]);
//URLチェック
 	  if(! preg_match(HTMLPATTERN,$itemurl)){
			$linkarray[$key]["status"]="商品URL不正";
			continue;
 	  }

//該当ページを配列($html)へ格納
		if(! $html=@file_get_html($itemurl)){
			$linkarray[$key]["status"]="商品HTML取得失敗";
			continue;
		}

//画像タグ取り出して配列へ格納
		foreach($html->find("img") as $element){

  	  //リンクを絶対パスに変換
		  $src=make_uri($itemurl,$element->src);

			$status="";

		  $linkarray[$key]["img"][]=array( "imgurl"=>escapeurl($src)
			                                ,"status"=>$status);
		}//foreach $html->find

		$linkarray[$key]["status"]="成功";

	  $html->clear();
    unset($html);
	}//foreach $linkarray
	return $linkarray;
}

//---------------------------------------------------//
// 渡された配列をDBへ登録(TB_RSSIMG)
//---------//
//引数・変数
//---------//
//返り値
//array( "url"      =>メーカーURL
//      ,"itemurl"  =>商品URL
//      ,"pagetitle"=>商品タイトル
//      ,"status=>[成功|URL不正|HTML取得失敗]
//      ,"img=>array("imgurl"=>画像URL),"status"=>"成功"))
//---------------------------------------------------//
function setImgList($linkarray){
	try{
		$db=new DB();
    foreach($linkarray as $key=>$val){
		 if(isset($val["img"])){
	    foreach($val["img"] as $key1=>$val1){
		    $db->updatecol=array( "itemid"=>$val["itemid"]
		   	                     ,"imgurl"=>$val1["imgurl"]
		                        );
		    $db->from=TB_RSSIMG;
		    $db->where =" itemid=".$val["itemid"];
		    $db->where.=" and imgurl='".$val1["imgurl"]."'";
		    $db->update();
		  }// foreach($val["img"] as $key1=>$val1){
		 }
	  }// foreach($linkarray as $key=>$val){
		return $linkarray;
	}
	catch(Exception $e){
		echo "err(エラー):".$e->getMessage();
		return false;
	}
}

//---------------------------------------------------//
// 渡された配列をDBへ登録(TB_RSSIMG)
//---------//
//引数・変数
//$corpid =>会社番号(空欄で全会社)
//---------//
//返り値
//$itemarray
//---------------------------------------------------//

function setRSS($corpid=null){
//会社マスタゲット
 $linkarray=getCorpList($corpid);

//HTMLを配列へ
 foreach($linkarray as $key=>$val){
   echo date("Y-m-d H:i:s")." ".$val["corpname"]." アイテムリンク作成\n";
   //アイテムリンクを配列へ格納
   $itemarray=getItemList($val["corpid"]);

   echo date("Y-m-d H:i:s")." ".$val["corpname"]." アイテムリンクDB登録\n";
   //アイテムリンクをDBへ登録
   $itemarray=setItemList($itemarray);

   echo date("Y-m-d H:i:s")." ".$val["corpname"]." 画像リンク作成\n";
   //画像リンクを配列へ
   $itemarray=getLinkImg($itemarray);

   echo date("Y-m-d H:i:s")." ".$val["corpname"]." 画像リンクDB登録\n";
   //画像リンクをDBへ登録
   $itemarray=setImgList($itemarray);

 }//foreach($linkarray as $key=>$val){
}

//---------------------------------------------------//
// DBに登録されているアイテム一覧を返す
//---------//
//引数・変数
//$corpid =>会社番号(空欄で全会社)
//---------//
//返り値
//配列
//---------------------------------------------------//
function getDBItemList($corpid){
	global $LINMAS;

//corpidチェック
  if(! preg_match("/^[0-9]+$/",$corpid)){
    echo "err(エラー):会社番号不正";
    return false;
  }
  $db=new DB();

  //アイテムリスト
  $db->select="itemid,corpid,itemurl,originalitemurl,pagetitle,originaltitle,itemcomment,lincode,saleday,storesaleday,status,idate,cdate";
  $db->from=TB_RSSITEM;
  $db->where =" corpid=".$corpid;
  $db->order =" idate desc";
  $itemarray=$db->getArray();
  if(isset($itemarray)){
    foreach($itemarray as $key=>$val){
      //部門名をセット
      $itemarray[$key]["linname"]=$LINMAS[$val["lincode"]];

      //itemurlをデコード
      $itemarray[$key]["itemurl"]=decodeurl($val["itemurl"]);

      //originalitemurlをデコード
      $itemarray[$key]["originalitemurl"]=decodeurl($val["originalitemurl"]);

      //画像URLを配列へセット
      $db->select="imgid,itemid,imgurl,status";
      $db->from=TB_RSSIMG;
      $db->where ="itemid=".$val["itemid"];
      $db->order ="imgid";
      $imgarray=$db->getArray();
      if(isset($imgarray)){
        foreach($imgarray as $key1=>$val1){
          $itemarray[$key]["img"][]=array( "imgid"=>$val1["imgid"]
                                          ,"imgurl"=>decodeurl($val1["imgurl"])
                                          ,"status"=>$val1["status"]
                                         );
        }
      }
    }
  }

  return $itemarray;
}

//---------------------------------------------------//
// DBに登録されているアイテム一覧を返す
//---------//
//引数・変数
//$lincode=>部門番号(空欄で全会社)
//---------//
//返り値
//配列
//---------------------------------------------------//
function getDBItemList2($lincode){
	global $LINMAS;

//corpidチェック
  if(! preg_match("/^[0-9]+$/",$lincode)){
    echo "err(エラー):bumon番号不正";
    return false;
  }
  $db=new DB();

  //アイテムリスト
  $db->select="itemid,corpid,itemurl,originalitemurl,pagetitle,originaltitle,itemcomment,lincode,saleday,storesaleday,status,idate,cdate";
  $db->from=TB_RSSITEM;
  $db->where =" lincode=".$lincode;
  $db->order =" idate desc";
  $itemarray=$db->getArray();
  if(isset($itemarray)){
    foreach($itemarray as $key=>$val){
      //部門名をセット
      $itemarray[$key]["linname"]=$LINMAS[$val["lincode"]];

      //itemurlをデコード
      $itemarray[$key]["itemurl"]=decodeurl($val["itemurl"]);

      //originalitemurlをデコード
      $itemarray[$key]["originalitemurl"]=decodeurl($val["originalitemurl"]);

      //画像URLを配列へセット
      $db->select="imgid,itemid,imgurl,status";
      $db->from=TB_RSSIMG;
      $db->where ="itemid=".$val["itemid"];
      $db->order ="imgid";
      $imgarray=$db->getArray();
      if(isset($imgarray)){
        foreach($imgarray as $key1=>$val1){
          $itemarray[$key]["img"][]=array( "imgid"=>$val1["imgid"]
                                          ,"imgurl"=>decodeurl($val1["imgurl"])
                                          ,"status"=>$val1["status"]
                                         );
        }
      }
    }
  }

  return $itemarray;
}

function setItem($itemid,$corpid,$itemurl,$pagetitle=null,$saleday=null,$storesaleday=null,$itemcomment=null,$lincode=null,$itemstatus=null,$imgurl=null){

  try{
//引数チェック
    if(! preg_match("/^[0-9]+$/",$itemid)){
      throw new exception("商品番号不正");
    }

    if(! preg_match("/^[0-9]+$/",$corpid)){
      throw new exception("会社番号不正");
    }

    if(! preg_match(HTMLPATTERN,$itemurl)){
      throw new exception("URL不正");
    }
    else{
      $itemurl=escapeurl($itemurl);
    }

    if($pagetitle==null) $pagetitle="";

    if($saleday && ! ISDATE($saleday)){
      throw new exception("発売日不正");
    }
    if($saleday==null) $saleday="1970/1/1";

    if($storesaleday && ! ISDATE($storesaleday)){
      throw new exception("店舗発売日不正");
    }
    if(! $storesaleday) $storesaleday="1970/1/1";

    if($itemcomment==null) $itemcomment="";

    if(! preg_match("/^[0-9]+$/",$lincode)){
      throw new exception("部門番号不正");
    }
    if(! $lincode) $lincode=0;

    if($itemstatus==null) $itemstatus="";
    else $itemstatus="対象外";

    if($imgurl && ! preg_match(HTMLPATTERN,$imgurl)){
      throw new exception("画像URL不正");
    }

    if($imgurl){
      $imgurl=escapeurl($imgurl);
    }

    $db=New DB();

//アイテムデータを更新
    $db->updatecol=array( "corpid"=>$corpid
                         ,"originalitemurl"=>$itemurl
                         ,"lincode"=>$lincode
                         ,"originaltitle"=>$pagetitle
                         ,"itemcomment"=>$itemcomment
                         ,"lincode"=>$lincode
                         ,"saleday"=>$saleday
                         ,"storesaleday"=>$storesaleday
                         ,"status"=>$itemstatus
                         );
    $db->from=TB_RSSITEM;
    $db->where="itemid=".$itemid;
    $db->update();

//画像データを更新
    //ステータスリセット
    $db->updatecol=array( "status"=>"");
    $db->from=TB_RSSIMG;
    $db->where="itemid=".$itemid;
    $db->update();

    //画像更新
    if($imgurl){
      $db->updatecol=array( "itemid"=>$itemid
                           ,"imgurl"=>$imgurl
                           ,"status"=>"選択"
                          );
      $db->from=TB_RSSIMG;
      $db->where =" itemid=".$itemid;
      $db->where.=" and imgurl='".$imgurl."'";
      $db->update();
    }
  }
  catch(Exception $e){
    echo "err(エラー):".$e->getMessage();
  }

}

function delItem($itemid){
  try{
    if(! preg_match("/^[0-9]+$/",$itemid)){
      throw new exception("err(エラー):商品番号(".$itemid.")が不正です。");
    }

    $db=new DB();

    //画像削除
    $db->from =TB_RSSIMG;
    $db->where ="itemid=".$itemid;
    $db->delete();

    //アイテム削除
    $db->from =TB_RSSITEM;
    $db->where ="itemid=".$itemid;
    $db->delete();

    echo "削除しました";
  }
  catch(Exception $e){
    echo "err:".$e->getMessage();
  }
}

function getItemURL($itemid){
  if(! preg_match("/^[0-9]+$/",$itemid)){
    echo "err(エラー):商品番号(".$itemid.")が不正です。";
    return false;
  }

  $db=new DB();
  $db->select =" t.itemid,t.itemurl,t.originalitemurl,t.lincode";
  $db->select.=",t.pagetitle,t.originaltitle,t.itemcomment";
  $db->select.=",t.saleday,t.storesaleday,t.status,t.corpid";
  $db->from =TB_RSSITEM." as t";
  $db->where ="t.itemid=".$itemid;
  $itemarray=$db->getArray();
  if(! isset($itemarray)){
    echo "err:データなし";
    return false;
  }

  foreach($itemarray as $key=>$val){
    if(isset($val["originalitemurl"])){
      $itemarray[$key]["originalitemurl"]=decodeurl($val["originalitemurl"]);
    }

    if(isset($val["itemurl"])){
      $itemarray[$key]["itemurl"]=decodeurl($val["itemurl"]);
    }

    $db->select="imgurl,status";
    $db->from =TB_RSSIMG;
    $db->where="itemid=".$val["itemid"];
    $imgarray=$db->getArray();
    if(isset($imgarray)){
      foreach($imgarray as $key1=>$val1){
        $imgarray[$key1]["imgurl"]=decodeurl($val1["imgurl"]);
      }
      if(isset($imgarray)){
        $itemarray[$key]["img"]=$imgarray;
      }
    }
  }

  return $itemarray;
}

function getItemListURL($lincode){
  if(! preg_match("/^[0-9]+$/",$lincode)){
    echo "err(エラー):部門番号(".$lincode.")が不正です。";
    return false;
  }

  $db=new DB();
  $db->select =" t.itemid,t.itemurl,t.originalitemurl,t.lincode";
  $db->select.=",t.pagetitle,t.originaltitle,t.itemcomment";
  $db->select.=",t.saleday,t.storesaleday,t.status,t.corpid";
  $db->from =TB_RSSITEM." as t";
  $db->where ="t.linocde=".$lincode;
  $db->order=" t.saleday desc";
  $itemarray=$db->getArray();
  if(! isset($itemarray)){
    echo "err:データなし";
    return false;
  }

  foreach($itemarray as $key=>$val){
    if(isset($val["originalitemurl"])){
      $itemarray[$key]["originalitemurl"]=decodeurl($val["originalitemurl"]);
    }

    if(isset($val["itemurl"])){
      $itemarray[$key]["itemurl"]=decodeurl($val["itemurl"]);
    }

    $db->select="imgurl,status";
    $db->from =TB_RSSIMG;
    $db->where="itemid=".$val["itemid"];
    $imgarray=$db->getArray();
    if(isset($imgarray)){
      foreach($imgarray as $key1=>$val1){
        $imgarray[$key1]["imgurl"]=decodeurl($val1["imgurl"]);
      }
      if(isset($imgarray)){
        $itemarray[$key]["img"]=$imgarray;
      }
    }
  }

  return $itemarray;
}

//---------------------------------------------------//
// 与えられた引数からRSS用XMLを出力する
// (getCorpListから出力される配列を使用)
//---------------------------------------------------//
function rssXML($corplist){
//XML生成
	$dom=new DomDocument('2.0','UTF-8');
	$dom->formatOutput=true;
	$rssNode=$dom->createElement("rss");
	$rssNode->setAttribute("version","2.0");
	$dom->appendChild($rssNode);

	foreach($corplist as $key=>$val){
		//メーカーごとにchannelを作成
		$channel=$dom->createElement("channel");
		$dom->appendChild($channel);

		//メーカー名
		$title=$dom->createElement("title");
		$text=$dom->createTextNode($val["corpname"]);
		$title->appendChild($text);
		$channel->appendChild($title);

		//メーカーURL
		$link=$dom->createElement("link");
		$text=$dom->createTextNode($val["url"]);
		$link->appendChild($text);
		$channel->appendChild($link);

		//詳細
		$description=$dom->createElement("description");
		$text=$dom->createTextNode($val["corpname"]);
		$description->appendChild($text);
		$channel->appendChild($description);

		//言語
		$language=$dom->createElement("language");
		$text=$dom->createTextNode("ja");
		$language->appendChild($text);
		$channel->appendChild($language);

	  //最終更新日
	  $lastBuildDate=$dom->createElement("lastBuildDate");
		$text=$dom->createTextNode(date("r",strtotime($val["pageidate"])));
		$lastBuildDate->appendChild($text);
		$channel->appendChild($lastBuildDate);

		//アイテム追加
		foreach($val["itemlist"] as $key1=>$val1){
		  //itemノード作成
      $item=$dom->createElement("item");

			//タイトル作成
		  $title=$dom->createElement("title");
		  $text=$dom->createTextNode($val1["pagetitle"]);
		  $title->appendChild($text);
		  $item->appendChild($title);

			//リンク作成
			$link=$dom->createElement("link");
			$text=$dom->createTextNode($val1["itemurl"]);
			$link->appendChild($text);
			$item->appendChild($link);

			//作成日時
			$pubDate=$dom->createElement("pubDate");
			$text=$dom->createTextNode(date("r",strtotime($val1["itemidate"])));
			$pubDate->appendChild($text);
			$item->appendChild($pubDate);

			//更新日時
			$lastPubDate=$dom->createElement("lastPubDate");
			$text=$dom->createTextNode(date("r",strtotime($val1["itemcdate"])));
			$lastPubDate->appendChild($text);
			$item->appendChild($lastPubDate);

			//カテゴリー追加
			$category=$dom->createElement("category");
			$text=$dom->createTextNode($val1["lincode"]." ".$val1["linname"]);
			$category->appendChild($text);
			$item->appendChild($category);

			foreach($val1["imglist"] as $key2=>$val2){
			 //画像の追加
       $img=$dom->createElement("photo:imgsrc");
			 $text=$dom->createTextNode($val2["imgurl"]);
			 $img->appendChild($text);
			 $item->appendChild($img);
			}

			//ノードに追加
		  $channel->appendChild($item);
		}// foreach($val["itemlist"] as $key1=>$val1){
	}// foreach($corplist as $key=>$val){
	echo  $dom->saveXML();
}

function getTopNews($lincode=null,$corpid=null){
  $itemarray=getHotNews($lincode,$corpid);

  $html="<div id='main_contents'>";
  $html.=getItemHtml($itemarray);
  $html.="</div><!-- main_contents -->";
  return $html;
}

//---------------------------------------------------//
// 最新ニュースの配列を返す
// 抽出条件　対象アイテム　分類済み　idateがBEFOREDAY以降
// 並び順    idate 降順
//---------------------------------------------------//
function getHotNews($lincode=null,$corpid=null){
  try{
    global $LINMAS;

    $db=new DB();

    //アイテムリスト作成
    $db->select ="t.itemid,t.corpid,t.originalitemurl,t.lincode,";
    $db->select.="t.originaltitle,t.itemcomment,t.saleday,t.storesaleday,";
    $db->select.="t1.corpname,t1.fcorpname";
    $db->from =TB_RSSITEM." as t";
    $db->from.=" inner join ".TB_RSSPAGE." as t1 on";
    $db->from.=" t.corpid=t1.corpid";
    if(! $lincode && ! $corpid) $db->where =" t.lincode>0";
    elseif($lincode)            $db->where =" t.lincode=".$lincode;
    elseif($corpid)             $db->where =" t.corpid=".$corpid." and t.lincode>0";


    if(! $lincode && ! $corpid){
      $db->where.=" and t.saleday >='".date("Y-m-d")."'";
    }
    $db->where.=" and t.status=''";
///   $db->order="t.idate desc ,t1.fcorpname";
    $db->order=" case when t.saleday='1970-1-1' then t.idate else t.saleday end desc,t.lincode";
    $itemlist=$db->getArray();

    if(isset($itemlist)){
      //画像リスト作成
      foreach($itemlist as $key=>$val){
        $db->select="t.imgid,t.itemid,t.imgurl,t.status";
        $db->from=TB_RSSIMG." as t";
        $db->where ="t.itemid=".$val["itemid"];
        $db->where.=" and t.status<>''";
        $imglist=$db->getArray();
        if(isset($imglist)){
          $itemlist[$key]["img"]=$imglist;
        }
      }

      //部門名
      foreach($itemlist as $key=>$val){
        $itemlist[$key]["linname"]=$LINMAS[$val["lincode"]];
      }
    }

    //URLデコード
    if(isset($itemlist)){
      //商品
      foreach($itemlist as $key=>$val){
        $itemlist[$key]["originalitemurl"]=decodeurl($val["originalitemurl"]);
        //画像
        if(isset($val["img"])){
          foreach($val["img"] as $key1=>$val1){
            $itemlist[$key]["img"][$key1]["imgurl"]=decodeurl($val1["imgurl"]);
          }
        }
      }
    }

    return $itemlist;

  }
  catch(Exception $e){
		echo "err(エラー):".$e->getMessage();
		return false;
  }
}


function getTimeLineNews(){
  $itemarray=getTimeLine();

  $html="<div id='main_contents'>";
  $html.=getItemHtml($itemarray);
  $html.="</div><!-- main_contents -->";
  return $html;
}

//---------------------------------------------------//
// 販売日降順の配列を返す
// 抽出条件　対象アイテム　分類済み
// 並び順    idate 降順
//---------------------------------------------------//
function getTimeLine(){
  try{
    global $LINMAS;

    $db=new DB();

    //アイテムリスト作成
    $db->select ="t.itemid,t.corpid,t.originalitemurl,t.lincode,";
    $db->select.="t.originaltitle,t.itemcomment,t.saleday,t.storesaleday,";
    $db->select.="t1.corpname,t1.fcorpname";
    $db->from =TB_RSSITEM." as t";
    $db->from.=" inner join ".TB_RSSPAGE." as t1 on";
    $db->from.=" t.corpid=t1.corpid";
    $db->where =" t.lincode>0";
    $db->where.=" and t.status=''";
    $db->order=" case when t.saleday='1970-1-1' then t.idate else t.saleday end desc,t.lincode";
    $itemlist=$db->getArray();

    if(isset($itemlist)){
      //画像リスト作成
      foreach($itemlist as $key=>$val){
        $db->select="t.imgid,t.itemid,t.imgurl,t.status";
        $db->from=TB_RSSIMG." as t";
        $db->where ="t.itemid=".$val["itemid"];
        $db->where.=" and t.status<>''";
        $imglist=$db->getArray();
        if(isset($imglist)){
          $itemlist[$key]["img"]=$imglist;
        }
      }

      //部門名
      foreach($itemlist as $key=>$val){
        $itemlist[$key]["linname"]=$LINMAS[$val["lincode"]];
      }
    }

    //URLデコード
    if(isset($itemlist)){
      //商品
      foreach($itemlist as $key=>$val){
        $itemlist[$key]["originalitemurl"]=decodeurl($val["originalitemurl"]);
        //画像
        if(isset($val["img"])){
          foreach($val["img"] as $key1=>$val1){
            $itemlist[$key]["img"][$key1]["imgurl"]=decodeurl($val1["imgurl"]);
          }
        }
      }
    }

    return $itemlist;

  }
  catch(Exception $e){
		echo "err(エラー):".$e->getMessage();
		return false;
  }
}


function getTanpinHtml($itemarray){
 $html ="<div id='divtanpin'>";
 $html.="<div id='tanpinimgbox'>";
 if(isset($itemarray["img"])){
   foreach($itemarray["img"] as $key=>$val){
     $html.="<a href='".$itemarray["originalitemurl"]."'>";
     $html.="<img src='".$val["imgurl"]."' id='".$val["imgid"]."'>";
     $html.="</a>";
   }
 }
 $html.="</div>";//tanpinimgbox
 $html.="<div id='tanpinitembox'>";
 $html.="<h2><a href='".$itemarray["originalitemurl"]."'>";
 $html.=$itemarray["originaltitle"];
 $html.="</a></h2>";
 $html.="<p class='tanpinitemcomment'>";
 $html.=$itemarray["itemcomment"];
 $html.="</p>";
 $html.="<p class='tanpinetcdata'>";
 $html.="<span class='spanmaker'>".$itemarray["corpname"]."</span>";
 $html.="<span class='spanlinname'>".$itemarray["linname"]."部門</span>";
 if(strtotime($itemarray["saleday"])!==strtotime("1970/1/1")){
   $html.="<span class='spansaleday'>発売日:".$itemarray["saleday"]."</span>";
 }
 if(strtotime($itemarray["storesaleday"])!==strtotime("1970/1/1")){
   $html.="<span class='spanstoresaleday'>店舗販売日:".$itemarray["storesaleday"]."</span>";
 }
 $html.="<div class='clr'></div>";
 $html.="</p>";
 $html.="</div>";//tanpinitembox
 $html.="<a href='".$itemarray["originalitemurl"]."' class='amore'>";
 $html.="メーカーホームページへ";
 $html.="</a>";
 $html.="</div>";//divtanpin
 return $html;
}

//---------------------------------------------------//
// アイテムHTML表示
//---------------------------------------------------//
function getItemHtml($itemarray){
  $html="";
  $html.="<ul id='ulitempreview'>";
  if(isset($itemarray)){
    $itemcnt=1;
    foreach($itemarray as $key=>$val){
      $html.="<li id='li_".$val["itemid"]."'>";
      $html.="<div class='itemlist'>";
      $html.="<div class='imgbox'>";
      if(isset($val["img"])){
        $html.="<a href='item_".$val["itemid"].".html'>";
        foreach($val["img"] as $key1=>$val1){
          $html.="<img src='".$val1["imgurl"]."' id='itemig_".$val1["imgid"]."'>";
        }
        $html.="</a>";
      }
      $html.="</div>";//imgbox
      $html.="<div class='itembox'>";
      $html.="<h2>";
      $html.="<a href='item_".$val["itemid"].".html'>";
      $html.=$val["originaltitle"];
      $html.="</a>";
      $html.="</h2>";
      $html.="<p class='itemcomment'>";
      $html.=$val["itemcomment"];
      $html.="</p>";
      $html.="<p class='etcdata'>";
      $html.="<span class='spanmaker'>".$val["corpname"]."</span>";
      $html.="<span class='spanlinname'>".$val["linname"]."部門</span>";
      if(strtotime($val["saleday"])!==strtotime("1970/1/1")){
        $html.="<span class='spansaleday'>発売日:".$val["saleday"]."</span>";
      }
      if(strtotime($val["storesaleday"])!==strtotime("1970/1/1")){
        $html.="<span class='spanstoresaleday'>店舗販売日:".$val["storesaleday"]."</span>";
      }
      $html.="<div class='clr'></div>";
      $html.="</p>";

  //    $html.="<dl id='dlitem_".$val["itemid"]."'>";
  //    $html.="<dt>";
  //    $html.="<a href='".$val["originalitemurl"]."' target='_blank'>";
  //    $html.=$val["originaltitle"];
  //    $html.="</a>";
  //    $html.="</dt>";
  //    $html.="<dd>".$val["itemcomment"]."</dd>";
  //    $html.="<dt>".$val["corpname"]."(".$val["linname"].")</dt>";
  //    if(strtotime($val["saleday"])!==strtotime("1970/1/1")){
  //      $html.="<dd>発売日:".$val["saleday"]."</dd>";
  //    }
  //    else{
  //      $html.="<dd></dd>";
  //    }
  //
  //    if(strtotime($val["storesaleday"])!==strtotime("1970/1/1")){
  //     $html.="<dt></dt>";
  //     $html.="<dd>店舗販売予定日:".$val["storesaleday"]."</dd>";
  //    }
  //    $html.="</dl>";
      $html.="</div>";//itembox
      $html.="<div class='clr'></div>";
      $html.="</div>";//itelist
      $html.="</li>";
      if($itemcnt % PAGEBREAK===0){
        $html.="<div style='page-break-after:always;width:0;height:0;'></div>";
        $html.="<div class='divcorpimg2'>";
        $html.=" <a href='http://www2.kita-grp.co.jp/hp/index.php'>";
        $html.=" <img src='http://www2.kita-grp.co.jp/hp/img/logo2.jpg'>";
        $html.=" </a>";
        $html.="</div>";
      }
      $itemcnt++;
    }
  }
  $html.="</ul>";
  return $html;
}

function escapeurl($url){
    $url=rawurldecode($url);
    return urlencode($url);
}

function decodeurl($url){
 // $url=html_entity_decode($url,ENT_QUOTES,"UTF-8");
  $url=rawurldecode($url);
  return $url;
}

function getHead($ary){
  $title=$ary["title"];
  $keyword=$ary["keyword"];
  $description=$ary["description"];
  $css=$ary["css"];
  $html=<<<EOF
<!DOCTYPE html>
<html lang="ja">
 <head>
	 <meta charset="utf-8">
	 <title>${title}</title>
   <meta name="keywords" content="${keyword}">
   <meta name="description" content="${description}">
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
	 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
   <link media="only screen and (max-device-width:480px)" href="smartphone.css" type="text/css" rel="stylesheet" />
   <link media="screen and (min-device-width:481px)" href="rss.css" type="text/css" rel="stylesheet" />
 </head>
   <link media="print" href="print.css" type="text/css" rel="stylesheet" />
EOF;
  return $html;
}

function getHeader($ary){
  $link1=$ary["link1"];
  $link2=$ary["link2"];
  $link3=$ary["link3"];
  $link4=$ary["link4"];
  $pagename1=$ary["pagename1"];
  $pagename2=$ary["pagename2"];
  $pagename3=$ary["pagename3"];
  $pagename4=$ary["pagename4"];

  $html=<<<EOF
  <body>
   <header>
     <div class='divcorpimg'>
      <a href="http://www2.kita-grp.co.jp/hp/index.php">
      <img src="http://www2.kita-grp.co.jp/hp/img/logo2.jpg">
      </a>
     </div>

     <ul>
      <li><a href="${link1}">${pagename1}</a></li>
      <li><a href="${link2}">${pagename2}</a></li>
      <li><a href="${link3}">${pagename3}</a></li>
      <li><a href="${link4}">${pagename4}</a></li>
     </ul>
   <div class="clr"></div>
  </header>
  <div id="wrapper">
EOF;

  return $html;
}

function getHeaderHTML(){
  $html=<<<EOF
  <body>
   <header>
     <div class='divcorpimg'>
      <a href="http://www2.kita-grp.co.jp/hp/index.php">
      <img src="http://www2.kita-grp.co.jp/hp/img/logo2.jpg">
      </a>
     </div>

     <div class="clr"></div>
  </header>
  <div id="wrapper">
EOF;

  return $html;
}

function getLinList($lincode=null){
  global $LINMAS;

  $db=new DB();
  $db->select=" t.lincode,count(itemid) as itemcnt";
  $db->from =TB_RSSITEM." as t";
  if($lincode) $db->where =" t.lincode=".$lincode;
  else $db->where =" t.lincode>0";
  $db->group ="t.lincode";
  $db->order ="t.lincode";
  $linarray=$db->getArray();
  if(isset($linarray)){
    foreach($linarray as $key=>$val){
      $linarray[$key]["linname"]=$LINMAS[$val["lincode"]];
    }
  }
  return $linarray;
}

function getLinMenu($lincode=null){
  $linarray=getLinList();
  $html ="<div id='linmenu'>";
  $html.="<ul>";
  $html.="<li>部門別一覧</li>";
  foreach($linarray as $key=>$val){
    $html.="<li>";
    if(! $lincode || $lincode!==$val["lincode"]){
      $html.="<a href='lincode_".$val["lincode"].".html'>";
    }

    $html.=$val["linname"];
    if(isset($val["itemcnt"])){
     $html.="(".$val["itemcnt"].")";
    }

    if(! $lincode || $lincode!==$val["lincode"]){
      $html.="</a>";
    }
    $html.="</li>";
  }
  $html.="</ul>";
  $html.="</div>";
  return $html;
//
}

function getLinMenuPage($lincode=null){
  $linarray=getLinList();
  $html ="<div id='mainlist'>";
  $html.="<ul>";
  $html.="<li>部門別一覧</li>";
  foreach($linarray as $key=>$val){
    $html.="<li>";
    if(! $lincode || $lincode!==$val["lincode"]){
      $html.="<a href='lincode_".$val["lincode"].".html'>";
    }

    $html.=$val["linname"];
    if(isset($val["itemcnt"])){
     $html.="(".$val["itemcnt"].")";
    }

    if(! $lincode || $lincode!==$val["lincode"]){
      $html.="</a>";
    }
    $html.="</li>";
  }
  $html.="</ul>";
  $html.="</div>";
  return $html;
//
}

function getCorpMenu($corpid=null){
 $corparray=getCorpList();
 $html ="<div id='corpmenu'>";
 $html.="<ul>";
 $html.="<li>会社別一覧</li>";
 foreach($corparray as $key=>$val){
   $html.="<li>";

   if(! $corpid || $corpid!==$val["corpid"]){
     $html.="<a href='corpid_".$val["corpid"].".html'>";
   }

   $html.=$val["corpname"];
   if(isset($val["newscount"])){
    $html.="(".$val["newscount"].")";
   }

   if(! $corpid || $corpid!==$val["corpid"]){
     $html.="</a>";
   }
   $html.="</li>";
 }
 $html.="</ul>";
 $html.="</div>";
  return $html;
}

function getCorpMenuPage($corpid=null){
 $corparray=getCorpList();
 $html ="<div id='mainlist'>";
 $html.="<ul>";
 $html.="<li>会社別一覧</li>";
 foreach($corparray as $key=>$val){
   $html.="<li>";

   if(! $corpid || $corpid!==$val["corpid"]){
     $html.="<a href='corpid_".$val["corpid"].".html'>";
   }

   $html.=$val["corpname"];
   if(isset($val["newscount"])){
    $html.="(".$val["newscount"].")";
   }

   if(! $corpid || $corpid!==$val["corpid"]){
     $html.="</a>";
   }
   $html.="</li>";
 }
 $html.="</ul>";
 $html.="</div>";
  return $html;
}


function getLeftMenuLin($lincode=null){
  $html="<div id='leftmenu'>";
  $html.=getLinMenu($lincode);
  $html.="</div>";
  return $html;
}

function getLeftMenu($lincode=null,$corpid=null){
  $html="<div id='leftmenu'>";
  $html.=getLinMenu($lincode);
  $html.=getCorpMenu($corpid);
  $html.="</div>";
  return $html;
}

function getEntryLeftMenu(){
  $html ="<div id='entryleft'>";
  $html.="<div id='corpselectbox'>";
  $html.=getSelectCorp();
  $html.="</div>";
  $html.="<div id='divitemlist'>";
  $html.="</div>";
  $html.="</div>";
  return $html;
}

function getSelectCorp(){
  $corparray=getCorpList();
  $html="<select id='corpselect'>";
  if(isset($corparray)){
    foreach($corparray as $key=>$val){
      $html.="<option value='".$val["corpid"]."'>";
      $html.=$val["corpname"];
      if(isset($val["newitem"])){
        $html.="【".$val["newitem"]."】";
      }
      $html.="</option>";
    }
  }
  $html.="</select>";
  $html.="<input type='button' value='新規' name='btnnew_".$val["itemid"]."'>";
  return $html;
}

function getFooter($ary){
  $html=<<<EOF
   <footer>
   </footer>
   </div><!-- div#wrapper -->
   </body>
EOF;
  return $html;
}

function getHtmlEnd($ary){
  $html="</html>";
  return $html;
}

function setHTML(){
  global $PAGE;

//TOPページ生成
  $pagename="newslist.html";
  $pagearray=$PAGE[$pagename];

  $html =getHead($pagearray);
  $html.=getHeader($pagearray);
  $html.=getLeftMenu();
  $html.=getTopNews();
  $html.=getFooter($pagename);
  $html.=getHtmlEnd($pagename);
  $html=preg_replace("/\r\n|\r|\n/","",$html);

  file_put_contents("/home/kennpin1/rss/html/newslist.html",$html);

//部門一覧ページ作成
  $pagename="linlist.html";
  $pagearray=$PAGE[$pagename];
  $html =getHead($pagearray);
  $html.=getHeader($pagearray);
  $html.=getLinMenuPage();
  $html.=getFooter($pagename);
  $html.=getHtmlEnd($pagename);
  $html=preg_replace("/\r\n|\r|\n/","",$html);
  file_put_contents("/home/kennpin1/rss/html/".$pagename,$html);

//メーカー一覧ページ作成
  $pagename="corplist.html";
  $pagearray=$PAGE[$pagename];
  $html =getHead($pagearray);
  $html.=getHeader($pagearray);
  $html.=getCorpMenuPage();
  $html.=getFooter($pagename);
  $html.=getHtmlEnd($pagename);
  $html=preg_replace("/\r\n|\r|\n/","",$html);
  file_put_contents("/home/kennpin1/rss/html/".$pagename,$html);

//発売日順ページ作成
  $pagename="timeline.html";
  $pagearray=$PAGE[$pagename];
  $html =getHead($pagearray);
  $html.=getHeader($pagearray);
  $html.=getTimeLineNews();
  $html.=getFooter($pagename);
  $html.=getHtmlEnd($pagename);
  $html=preg_replace("/\r\n|\r|\n/","",$html);
  file_put_contents("/home/kennpin1/rss/html/".$pagename,$html);



//部門別ページ作成
  $linarray=getLinList();
  if(isset($linarray)){
    foreach($linarray as $key=>$val){
      //初期化
      $html="";

      //ファイル名確定
      $filename="lincode_".$val["lincode"].".html";

      $html.=getHead($PAGE["lincode.html"]);
      $html.=getHeader($PAGE["lincode.html"]);
      $html.=getLeftMenu($val["lincode"],null);
      $html.=getTopNews($val["lincode"],null);
      $html.=getFooter($pagename);
      $html.=getHtmlEnd($pagename);
      $html=preg_replace("/\/\*linname\*\//",$val["linname"],$html);
      //改行コード削除
      $html=preg_replace("/\r\n|\r|\n/","",$html);
      file_put_contents("/home/kennpin1/rss/html/".$filename,$html);
    }
  }

//会社別ページ作成
  $corparray=getCorpList();
  if(isset($corparray)){
    foreach($corparray as $key=>$val){
      //初期化
      $html="";

      //ファイル名確定
      $filename="corpid_".$val["corpid"].".html";

      $html.=getHead($PAGE["corp.html"]);
      $html.=getHeader($PAGE["corp.html"]);
      $html.=getLeftMenu(null,$val["corpid"]);
      $html.=getTopNews(null,$val["corpid"]);
      $html.=getFooter($pagename);
      $html.=getHtmlEnd($pagename);
      $html=preg_replace("/\/\*corpname\*\//",$val["corpname"],$html);
      //改行コード削除
      $html=preg_replace("/\r\n|\r|\n/","",$html);
      file_put_contents("/home/kennpin1/rss/html/".$filename,$html);
    }
  }

//単品ページ作成
  $itemarray=getTimeLine();
  if(isset($itemarray)){
    foreach($itemarray as $key=>$val){
     //初期化
     $html="";

     //ファイル名確定
     $filename="item_".$val["itemid"].".html";

     $html.=getHead($PAGE["item.html"]);
     $html.=getHeader($PAGE["item.html"]);
     $html.=getLeftMenu();
     $html.=getTanpinHtml($val);
     $html.=getFooter($PAGE["item.html"]);
     $html.=getHtmlEnd($PAGE["item.html"]);
     $html=preg_replace("/\/\*originaltitle\*\//",$val["originaltitle"],$html);
     $html=preg_replace("/\/\*corpname\*\//",$val["corpname"],$html);
     $html=preg_replace("/\/\*saleday\*\//",$val["saleday"],$html);
     $html=preg_replace("/\/\*itemcomment\*\//",$val["itemcomment"],$html);
     //改行コード削除
     $html=preg_replace("/\r\n|\r|\n/","",$html);
     file_put_contents("/home/kennpin1/rss/html/".$filename,$html);
    }
  }

}
?>

