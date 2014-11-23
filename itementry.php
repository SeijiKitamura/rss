<?php
require_once("php/rssfunction.php");
$pagename="itementry.php";
$pagearray=$PAGE[$pagename];

echo getHead($pagearray);
echo getHeader($pagearray);
//echo getEntryLeftMenu();
?>
<div id="entrydiv">
 <div id="entrycorplist">
 </div>
 <div id="entryitemlist">
 </div>
</div>
<script>

$(function(){
  $("ul#mainlist li a").click(function(){
    console.log($(this).attr("href"));
  });

  getCorpList();
});

//会社一覧表示
function getCorpList(){
  var phpfile="php/getCorpList.php";
  $.get(phpfile,function(html){
    $("div#entrycorplist").empty()
                     .append(html);

    $("div#entrycorplist ul li").click(function(){
       var corpid=$(this).attr("id").split("_")[1];
       getItemList(corpid);
    });
  });
}
//アイテムリスト表示
function getItemList(corpid){
  var phpfile="php/getItemListCorp.php";
  var para={"requestcorpid":corpid};
  $.get(phpfile,para,function(html){
    $("div#entryitemlist").empty()
                          .append(html);
//「他の画像」イベント追加
    $("input[value='他の画像']").click(function(){
      var imglist=$(this).parent().parent().find("div.imglist");
      imglist.toggle();
    });

//画像クリックイベント
   $("div.imglist img").click(function(){
     var imgurl=$(this).attr("src");
     //ここから
   });

});
}

/*
//商品登録画面表示
function getHtml(itemid,newitem){
  var phpfile="php/getHtml.php";
  if(itemid){
    var para={"requestitemid":itemid};
  }
  if(newitem){
    var para={"newitem":1};
  }

  $.get(phpfile,para,function(html){
    console.log("getHtml取得");
    console.log(html);
    if(html.match(/^err/)){
      alert(html);
      return false;
    }

    $("div#entrypreview").empty()
                         .append(html);


    //対象外
    $("input[name^=btnout]").click(function(){
      var corpid=$(this).attr("name").split("_")[1];
      setItem("対象外");
    });

    //削除
    $("input[name^=btndel").click(function(){
      delItem();
    });

    //URL更新
    $("input[name^=entryoriginalitemurl]").change(function(){
      setItem();
    });

    //部門変更
    $("select[id^=selectlincode]").change(function(){
      setItem();
    });

    //商品名更新
    $("input[name^=entryoriginalpagetitle]").change(function(){
      setItem();
    });

    //コメント
    $("textarea[id^=entryitemcomment]").change(function(){
      setItem();
    });

    //発売日
    $("input[name^=entrysaleday]").change(function(){
      setItem();
    });

    //店舗発売日
    $("input[name^=entrystoresaleday]").change(function(){
      setItem();
    });

    //画像
    $("input[name^=entryimgurl]").change(function(){
      setItem();
      setImg();
    });
  });
}

//商品画像表示
function setImg(){
  var url=$("input[name^=entryimgurl]").val();
  url=encodeURI(url);

  console.log("imgurl変更:"+url);
  $("span.spanimg").empty()
                   .append($("<img>").attr("src",url));
}

//商品登録（対象外含む）
function setItem(itemstatus){
  var phpfile="php/setItem.php";

  var itemid=$("div#divitem").text();

  var corpid=$("select#corpselect").val();
  var itemurl=$("a#a_"+itemid).attr("href");
  var originalitemurl=$("input[name=entryoriginalitemurl_"+itemid).val();
  var lincode=$("select#selectlincode_"+itemid).val();
  var pagetitle=$("input[name=entryoriginalpagetitle_"+itemid).val();
  var itemcomment=$("textarea#entryitemcomment_"+itemid).val();
  var saleday=$("input[name=entrysaleday_"+itemid).val();
  var storesaleday=$("input[name=entrystoresaleday_"+itemid).val();
  var imgurl=$("input[name=entryimgurl_"+itemid).val();
  if(itemstatus) itemstatus="対象外";

  console.log(itemid);
  //URLチェック
  if(! itemurl &&! originalitemurl){
    alert("URLを入力してください");
    return false;
  }

  //新規用引数補正
  if(! originalitemurl) originalitemurl=itemurl;
  if(! itemurl) itemurl=originalitemurl;

  var para={ "requestcorpid":corpid
            ,"requestoriginalitemurl":encodeURI(originalitemurl)
            ,"requestitemid":itemid
            ,"requestitemurl":encodeURI(itemurl)
            ,"requestlincode":lincode
            ,"requestpagetitle":pagetitle
            ,"requestitemcomment":itemcomment
            ,"requestsaleday":saleday
            ,"requeststoresaleday":storesaleday
            ,"requestimgurl":encodeURI(imgurl)
            ,"requestitemstatus":itemstatus
           };

  console.log("setItemパラメータセット");
  console.log(para);
  $.get(phpfile,para,function(html){

    console.log("setItem.phpの返り値取得");
    console.log(html);

    //エラーチェック
    if(html.match(/err/)){
      alert(html);
      return false;
    }

//    alert("登録しました");
    var corpid=$("select#corpselect").val();
    getItemList(corpid);

  });
}

function clrPreview(){
  $("div#entrypreview").empty();
}

function delItem(){
  if(! confirm("削除しますか?")) return false;

  var phpfile="php/delitem.php";
  var itemid=$("div#divitem").text();
  var para={"requestitemid":itemid};

  console.log("delItemパラメータセット");
  console.log(para);

  $.get(phpfile,para,function(html){
    console.log("delItem返り値取得");
    console.log(html);

    if(html.match(/^err/)){
      alert(html);
      return false;
    }

    alert("削除しました");
  });
}
*/
</script>

