<?php
require_once("php/rssfunction.php");
$pagename=$PAGE["addcorp.php"];
echo getHead($pagename);
echo getHeader($pagename);
?>
<h1>メーカー登録画面</h1>
<div id="corpbox">
  <h2>登録済みリスト</h2>
  <div id="corplist">
  </div><!-- corplist -->

</div><!-- corpbox -->

<div id="previewbox">
  <h2>プレビュー</h2>
  <div id="preview">
    test
  </div><!-- preview -->

</div><!-- preveiwbox -->
<div class="clr"></div>
<p>
【最新/未分類】
</p>
<div id="entrybox">
  <h2>エントリー</h2>
  <div id="entry">
    <dl>
      <dt>会社番号:</dt>
      <dd id="requestcorpid"></dd>
      <dt>URL:</dt>
      <dd><input type="text" value="" name="requesturl"></dd>
      <dt>Part:</dt>
      <dd><input type="text" value="" name="requestpart"></dd>
      <dt>会社名:</dt>
      <dd><input type="text" value="" name="requestcorpname"></dd>
      <dt>ふりがな:</dt>
      <dd><input type="text" value="" name="requestfcorpname"></dd>
      <dt></dt>
      <dd>
        <input type="button" value="登録"   name="btn_entry">
        <input type="button" value="クリア" name="btn_clear">
        <input type="button" value="削除"   name="btn_del">
    </dl>

  </div><!-- entry -->

<div><!-- entrybox -->
<?php
echo getFooter($pagename);
$html=<<<EOF
<script>
$(function(){
//「会社一覧」表示
  getCorpList();

//URL変更イベント
  $("input[name=requesturl]").change(function(){
   if(! $(this).val()){
     $("div#preview").empty();
     return false;
   }

   getPreview($(this).val(),null);

   $("input[name=requestpart]").val("");
  });

//Part変更イベント
  $("input[name=requestpart]").change(function(){
    var url=$("input[name=requesturl]").val();
    getPreview(url,$(this).val());
  });

//「登録」イベントセット
  $("input[name=btn_entry]").click(function(){
    setCorpName();
  });

//「クリア」イベント
  $("input[name=btn_clear]").click(function(){
   allClr();
  });

//削除イベント
  $("input[name=btn_del]").click(function(){
    delCorp();
  });

});//$(function(){

//削除
function delCorp(){
  var corpid=$("dd#requestcorpid").text();
  if(! corpid) return false;
  if(! confirm("削除しますか?")) return false;

  var phpfile="php/delCorp.php";

  console.log("削除会社番号:"+corpid);

  $.ajax({
    url:phpfile
    ,type:"get"
    ,data:{"requestcorpid":corpid}
    ,datatype:"html"
    ,cache:false
    ,async:false
    ,success:function(html){
      if(html.match(/^err/)){
        alert(html);
        return false;
      }
      alert("削除しました");
      allClr();
      getCorpList();
    }//success
    ,error:function(){
      $("div#preview").empty()
                      .append("データ受信に失敗しました");
    }
  });
}

//クリア
function allClr(){
  $("dd#requestcorpid").text("");
  $("input[name=requesturl]").val("");
  $("input[name=requestpart]").val("");
  $("input[name=requestcorpname]").val("");
  $("input[name=requestfcorpname]").val("");
  $("div#preview").empty();
  getCorpList();
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
      var phpfile="php/JsonGetCorpMas.php";
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
        ,datatype:"json"
        ,cache:false
        ,async:false
        ,success:function(json){
          getCorpMas(json);
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

//「会社マスタ」表示
function getCorpMas(json){
//ログ出力
  var d=new Date();
  console.log(d+" getCorpMas実行 ");

  var json=eval(json)[0];
  console.log(json);
  $("dd#requestcorpid").text(json["corpid"]);
  $("input[name=requesturl]").val(json["url"]);
  $("input[name=requestpart]").val(json["part"]);
  $("input[name=requestcorpname]").val(json["corpname"]);
  $("input[name=requestfcorpname]").val(json["fcorpname"]);

  getPreview(json["url"],json["part"]);
}

//プレビュー表示
function getPreview(url,part){
//ログ出力
  var d=new Date();
  console.log(d+" getPreview実行 ");
  console.log("url:"+url+" part:"+part);

  if(! url.length){
    console.log("URL空欄");
    return false;
  }
  if(! part) var part="";

//メッセージ表示
  $("div#preview").empty()
                  .append("データ受信中・・・");

  var phpfile="php/getDiv.php";
  $.ajax({
         url:phpfile
        ,async:false
        ,type:"get"
        ,data:{"requesturl":url,"requestpart":part}
        ,datatype:"html"
        ,cache:false
        ,success:function(html){
          $("div#preview").empty()
                          .append(html);

          //partクリックイベント
          $("div#preview h1").click(function(){
            var part=$(this).text();
            var url=$("input[name=requesturl]").val();
            $("input[name=requestpart]").val(part);
            getPreview(url,part);
          });
        }//success
        ,error:function(){
          $("div#preview").empty()
                          .append("データ受信に失敗しました");
        }
  });
}


//「登録」
function setCorpName(){
  var phpfile="php/setCorpName.php";
  var corpid=$("dd#requestcorpid").text();
  var url=$("input[name=requesturl]").val();
  var part=$("input[name=requestpart]").val();
  var corpname=$("input[name=requestcorpname]").val();
  var fcorpname=$("input[name=requestfcorpname]").val();
  var obj={"requestcorpid":corpid,"requesturl":url,"requestpart":part,"requestcorpname":corpname,"requestfcorpname":fcorpname};

//ログ出力
  var d=new Date();
  console.log(d+" setCorpName実行 ");
  console.log(obj);

  $.get(phpfile,obj,function(html){
    console.log(html);
    if(html.match(/err/)){
      alert(html);
      return false;
    }
    console.log(html);
    alert("登録しました");
    allClr();
  });
}
</script>
EOF;
echo $html;
echo getHtmlEnd($pagename);
?>
