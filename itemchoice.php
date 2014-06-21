<?php
require_once("php/rssfunction.php");
$pagename=$PAGE["itemchoice.php"];
echo getHead($pagename);
echo getHeader($pagename);
?>
<h1>アイテム登録画面</h1>
<div id="corpbox_long">
  <h2>メーカー一覧</h2>
  <div id="corplist">
  </div><!-- corplist -->
</div><!-- corpbox_long -->

<div id="previewbox_long">
  <h2>アイテム一覧</h2>
  <div id="preview">
  </div><!-- preview -->
</div><!--previewbox_long-->
<div class="clr"></div>
<?php
echo getFooter($pagename);
$html=<<<EOF
<script>
$(function(){
//「会社一覧」表示
  getCorpList();
});

//カウント更新
function getCount(itemid){
  var corpid=$("#li_"+itemid).parent().siblings().filter("h2").attr("id").split("_")[1];

  var phpfile="php/getNewsCount.php";
  $.getJSON(phpfile,function(json){
    $(json).each(function(){
      if(corpid==this.corpid){
        if(this.newsflash && this.newitem){
          $("#newitem_"+corpid).text(this.newitem);
        }
        else{
          $("#newscount_"+corpid).slideUp();
        }
        return false;
      }
    });
  });
}

//全アイテム表示
function allItem(){
  $("#ulitemlist li").slideDown("slow");
}

//会社一覧
function getCorpList(){
  var phpfile="php/getCorpList.php";
//
//ログ出力
  var d=new Date();
  console.log(d+" getCorpList実行 ");

  $.get(phpfile,function(html){
    if(html.match(/err/)){
      alert(html);
      return false;
    }

//会社一覧をdivにセット
    $("div#corplist").empty()
                     .append(html);

//リスト反転表示
    $("div#corplist ul li").hover(function(){
      $(this).css("background-color","coral");
    }
    ,function(){
      $(this).css("background-color","white");
    });

//リストクリックイベント
    $("div#corplist ul li").click(function(){
      var phpfile="php/getItemList.php";
      var corpid=$(this).attr("id").split("_")[1];

      //反転アクション
//      $(this).fadeOut(100,function(){
//        $(this).fadeIn(100);
//      });
//

      //プレビュー初期化
      $("div#preview").empty()
                      .append("データ受信中・・・");

      //データゲット
      $.ajax({
        url:phpfile
        ,type:"get"
        ,data:{"requestcorpid":corpid}
        ,datatype:"html"
        ,cache:false
        ,async:false
        ,success:function(html){
          getItemList(html);
        }//success
        ,error:function(){
          $("div#preview").empty()
                          .append("データ受信に失敗しました");
        }
      });//$.ajax

      $(this).siblings().css("background-color","white");
      $(this).css("background-color","red");
      $(this).unbind("mouseenter").unbind("mouseleave");
    });
  });//$.get
}//function getCorpList

function getItemList(html){
  if(html.match(/err/)){
    $("div#preview").empty()
                    .append(html);
    return false;
  }

  $("div#preview").empty()
                  .append(html);
//「全データ」表示
  $("input[name=allItem]").click(function(){
    allItem();
  });

//「対象外」イベント
  $("input[name^=itemout]").click(function(){
    var itemid=$(this).attr("name").split("_")[1];
    setItem(itemid,1,0);
  });

//「タイトルリセット」イベント
  $("input[name^=title]").click(function(){
    var itemid=$(this).attr("name").split("_")[1];
    titleReset(itemid);
  });


//「画像リセット」イベント(未テスト)
  $("input[name^=img]").click(function(){
    var itemid=$(this).attr("name").split("_")[1];
    $("input[name=impurl_"+itemid+"]").val("");
    setItem(itemid,0,1);
  });

//オリジナルURLイベント
  $("input[name^=originalitemurl]").change(function(){
    var itemid=$(this).attr("name").split("_")[1];
    setItem(itemid);
  });

//[タイトル|コメント]イベント
  $("textarea").change(function(){
    var itemid=$(this).attr("id").split("_")[1];
    setItem(itemid);
  });

//発売日イベント
  $("input[name^=saleday]").change(function(){
    var itemid=$(this).attr("name").split("_")[1];
    setItem(itemid);
  });

//店舗取扱日イベント
  $("input[name^=storesaleday]").change(function(){
    var itemid=$(this).attr("name").split("_")[1];
    setItem(itemid);
  });

//部門イベント
  $("select").change(function(){
    var itemid=$(this).attr("id").split("_")[1];
    setItem(itemid);
  });

//画像URLイベント
  $("input[name^=impurl]").change(function(){
    var itemid=$(this).attr("name").split("_")[1];
    var texturl=$(this).val();
    if(texturl.length){
     setItem(itemid);
    }
    else{
     setItem(itemid,0,1);
    }
  });

//画像クリックイベント
  $("div.imgdiv img").click(function(){
    setImg($(this));
  });
}

//画像クリックイベント
function setImg(obj){
  var itemid=obj.attr("id").split("_")[1];
  $("input[name=impurl_"+itemid+"]").val(obj.attr("src"));
  setItem(itemid)
}

//タイトルリセット
function titleReset(itemid){
  var mototitle=$("dd#pagetitle_"+itemid).text();
  $("textarea#title_"+itemid).val(mototitle);
}

//アイテム登録
function setItem(itemid,itemswich,imgswich){
  var phpfile="php/setItem.php";

  var itemurl=$("input[name=originalitemurl_"+itemid).val();
  var pagetitle=$("textarea#title_"+itemid).val();
  var saleday=$("input[name=saleday_"+itemid+"]").val();
  var storesaleday=$("input[name=storesaleday_"+itemid+"]").val();
  var itemcomment=$("textarea#itemcomment_"+itemid).val();
  var lincode=$("select#lincode_"+itemid).val();
  var itemstatus=null;
  var imgurl=$("input[name=impurl_"+itemid+"]").val();

  //itemurl無効なら元のURLを代入
  if( !itemurl || ! itemurl.length) itemurl=$("#url_"+itemid).attr("href");

  //itemswich有効なら対象外に
  if(itemswich) itemstatus=1;

  //imgswich有効なら画像対象外に
  if(imgswich) imgurl="";

  var para={ "requestitemid":itemid
            ,"requestoriginalitemurl":itemurl
            ,"requestpagetitle":pagetitle
            ,"requestsaleday":saleday
            ,"requeststoresaleday":storesaleday
            ,"requestitemcomment":itemcomment
            ,"requestlincode":lincode
            ,"requestitemstatus":itemstatus
            ,"requestimgurl":imgurl
  };

  $.get(phpfile,para,function(html){
    //エラーチェック
    if(html.match(/err/)){
      alert(html);
      return false;
    }

    //引数をitemidに変更して作り直し　ここから
    getCount(itemid);

    //対象外(itemswich有効)なら非表示
    if(itemswich){
      $("#li_"+itemid).slideUp("slow");
      return false;
    }

    //画像全表示(imgswich有効)
    if(imgswich || ! imgurl){
      $("#dl_"+itemid+" dd div.imgdiv img").slideDown("slow");
      return false;
    }

    //選択画像のみ表示
    var eachflg=0;
    $("#dl_"+itemid+" dd div.imgdiv img").each(function(){
      console.log(imgurl);
      if($(this).attr("src")==imgurl){
        $(this).siblings().slideUp("slow");
        eachflg=1;
        return false;
      }
    });

    //選択画像が一覧にない場合、画像を追加
    if(! eachflg){
      $("#dl_"+itemid+" dd div.imgdiv img").slideUp("slow");
      $("#dl_"+itemid+" dd div.imgdiv").append("<img src='"+imgurl+"'>");
    }
  });
}
</script>

EOF;
echo $html;
echo getHtmlEnd($pagename);
?>

