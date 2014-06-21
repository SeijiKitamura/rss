<?php
function is_mobile () {
 $useragents = array(
 'iPhone', // Apple iPhone
 'iPod', // Apple iPod touch
 'Android', // 1.5+ Android
 'dream', // Pre 1.5 Android
 'CUPCAKE', // 1.5+ Android
 'blackberry9500', // Storm
 'blackberry9530', // Storm
 'blackberry9520', // Storm v2
 'blackberry9550', // Storm v2
 'blackberry9800', // Torch
 'webOS', // Palm Pre Experimental
 'incognito', // Other iPhone browser
 'webmate' // Other iPhone browser
 );
 $pattern = '/'.implode('|', $useragents).'/i';
 return preg_match($pattern, $_SERVER['HTTP_USER_AGENT']);
}

//-------------------------------------------//
// 日付チェック                              //
//-------------------------------------------//
function ISDATE($hiduke){
 $pattern="/(20[0-9]{2})[-\/]?([0-1]?[0-9]{1})[-\/]?([0-3]?[0-9]{1})$/";
 preg_match($pattern,$hiduke,$match);
 if(! $match) return false;
 $moto=mktime(0,0,0,$match[2],$match[3],$match[1]);
 if(date("Y",$moto)!=$match[1] ||
    date("m",$moto)!=$match[2] ||
    date("d",$moto)!=$match[3]) return false;
 return true;
}

//-------------------------------------------//
// 日付生成                                  //
//-------------------------------------------//
function JPNDATE($hiduke){
 global $YOUBI;
 if(! ISDATE($hiduke)) return false;
 $unixtime=strtotime($hiduke);
 $d=date("Y年n月j日",$unixtime);
 $w=date("w",$unixtime);
 $y=$YOUBI[$w];
 return $d."(".$y.")";
}

//-------------------------------------------//
// 日付生成(short)                           //
//-------------------------------------------//
function JPNDATESHORT($hiduke){
 global $YOUBI;
 if(! ISDATE($hiduke)) return false;
 $unixtime=strtotime($hiduke);
 $d=date("n月j日",$unixtime);
 $w=date("w",$unixtime);
 $y=$YOUBI[$w];
 return $d."(".$y.")";
}

//-------------------------------------------//
// CSVの値を配列へセット                     //
// CSVファイルを読み込んで配列を返す         //
// 注意:元データに[err]が付きます。          //
//-------------------------------------------//
function GETARRAYCSV($csvfilepath,$tablename){
 global $TABLES;
 global $CSVCOLUMNS;

 //テーブル列情報をセット
 $table=$TABLES[$tablename];
 if(! $table) throw new exception("テーブル列情報がありません");

 //CSV列情報をセット
 $csv=$CSVCOLUMNS[$tablename];
 if(! $csv) throw new exception("CSV列情報がありません");

 //ファイル読み込み
 if(($fl=fopen($csvfilepath,"r"))===FALSE) throw new exception("元データがありません");

 //配列へCSVデータを追加
 while($line=fgets($fl)){
  $line=str_replace("\n","",$line);
  $line=str_replace("\r","",$line);
  $line=mb_convert_encoding($line,"UTF-8","SJIS");
  $csvdata[]=explode(",",$line);
 }//while
 if(! $csvdata) throw new exception("CSVデータがありません");

 //列数を確認
 if(count($csvdata[0])!==count($csv)) throw new exception("CSVの列数が違います");

 //データをセット
 $flg=true;
 foreach($csvdata as $row =>$cols){
  foreach($csv as $colnum => $colname){
   //データセット
   $data[$row][$colname]=$cols[$colnum];

   //値チェック
   if(! CHKTYPE($table[$colname]["type"],$cols[$colnum])){
    $data[$row]["err"]=$table[$colname]["local"]."の値が不正です";
    $flg=false;
    continue 2;
   }//if
   else{
    $data[$row]["err"]="OK";
   }//else
  }//foreach
 }//foreach

 //列名をセット
 foreach($csv as $colnum=>$colname){
  $local[$colname]=$table[$colname]["local"];
 }//foreach
 $local["err"]="エラー内容";

 $items["data"]=$data;
 $items["status"]=$flg;
 $items["local"]=$local;

 return $items;
}
//-------------------------------------------//
// CSV 値チェック                            //
// CSVファイルを読み込んで配列を返す         //
// 注意:元データに[err]が付きます。          //
//-------------------------------------------//
function CHKDATA($csvfilepath,$table){
 global $TABLES;
 global $CSVCOLUMNS;

 //該当テーブルの列情報をゲット
 $col=$TABLES[$table];
 if(! $col) throw new exception("テーブル情報がありません");

 //該当CSVファイルの列情報をゲット
 $csv=$CSVCOLUMNS[$table];
 if(! $csv) throw new exception("CSV列情報がありません");

 //ファイル読み込み
 if(($fl=fopen($csvfilepath,"r"))===FALSE) throw new exception("元データがありません");

 //配列へCSVデータを追加
 while($line=fgets($fl)){
  $line=str_replace("\n","",$line);
  $csvdata[]=explode(",",$line);
 }//while
 if(! $csvdata) throw new exception("CSVデータがありません");

 //列数を確認
 if(count($csvdata[0])!==count($csv)) throw new exception("CSVの列数が違います");

 //データチェック
 $status=true;
 for($i=0;$i<count($csvdata);$i++){
  $flg=1; //エラーフラグ
  $msg="OK";
  for($j=0;$j<count($csvdata[$i]);$j++){
   //type、dataをゲット
   $type=$col[$csv[$j]]["type"];
   $data=$csvdata[$i][$j];

   //値チェック
   if(! CHKTYPE($type,$data)){
    $msg=$col[$csv[$j]]["local"]."の値が不正です";
    $flg=0;
    $status=false;
   }
  }//for $j

  //エラーメッセージ付加
  $csvdata[$i]["err"]=$msg;
 }//for $i

 //列名をセット
 for($i=0;$i<count($csv);$i++){
  $items["local"][]=$col[$csv[$i]]["local"];
 }//for

 $items["data"]  =$csvdata;
 $items["status"]=$status;
 $items["local"][]="エラー内容";

 //return $csvdata;
 return $items;
}
//-------------------------------------------//

//-------------------------------------------//
//       DB列 値チェック                     //
//-------------------------------------------//
function CHKTYPE($type,$data){
 //date型チェック
 if($type==="date"){
  if(! CHKDATE($data)) return false;
 }

 //int型チェック
 if($type==="int" || $type==="bigint"){
  $pattern="/^[0-9]+$/";
  preg_match($pattern,$data,$match);
  if(! $match) return false;
 }

 //varchar型
 //チェックしたい内容を記入

 //timestamp型
 //チェックしたい内容を記入

 return true;
}
//-------------------------------------------//

//-------------------------------------------//
//                 日付チェック              //
//-------------------------------------------//
function  CHKDATE($hiduke){
 //正規表現チェック
 $pattern="/^(20[0-9]{2})[-\/]?([0-1]?[0-9]{1})[-\/]?([0-3]?[0-9]{1})$/";
 preg_match($pattern,$hiduke,$match);
 if(! $match) return false;

 //日付の正当性をチェック
 $ts=strtotime($hiduke);
 if(date("Y",$ts)!=$match[1] || date("m",$ts)!=$match[2] || date("d",$ts)!=$match[3]) return false;
 return true;
}

//-------------------------------------------//
//        CSVアップロード                    //
//-------------------------------------------//
function UPLOAD($directory,$filename){

 $filepath=$directory.$filename.".csv";
 //アップロードファイル容量チェック

 //アップロードされたファイルを所定ディレクトリへコピー
 if(! move_uploaded_file($_FILES[$file]["tmp_name"],$filepath)){
  throw new exception("ファイルアップロードに失敗しました");
 }

 //ファイル読み込み
 if(! $data=file_get_contents($filepath)) throw new exception("ファイル読み込みに失敗しました");
 //文字コード変換
 if(! $data=mb_convert_encoding($data,"UTF-8","SJIS")) throw new exception("文字コード変換に失敗しました");

 //改行コード変換
 if(! $data=str_replace("\r\n","\n",$data)) throw new exception("改行コード変換に失敗しました");

 //ファイル保存
 if(! file_put_contents($filepath,$data)) throw new exception("ファイルの保存に失敗しました");

 //パーミッション変更確認
 if(! chmod($filepath,0666)) throw new exception("ファイル所有者変更に失敗しました");


 return true;
}//UPLOAD

//-------------------------------------------//
//        Imageアップロード                  //
// アップロードされたファイルを
// ファイル名$jcode 拡張子なしでとりあえず保存。
//-------------------------------------------//
function UPLOADIMAGE($jcode){

 //JANコードチェック
 //ここを追加すること！//
 //作業ディレクトリ
 $work=IMGDIR.$jcode;

 //アップロードされたファイルを所定ディレクトリへコピー
 if(! move_uploaded_file($_FILES["upload_".$jcode]["tmp_name"],$work)){
  throw new exception("ファイルアップロードに失敗しました");
 }

 //ファイル読み込み
 if(! $data=file_get_contents($work)) throw new exception("ファイル読み込みに失敗しました");

 //パーミッション変更確認
 if(! chmod($work,0666)) throw new exception("ファイル所有者変更に失敗しました");

 return $work;
}

//-------------------------------------------//
// インポート手順                            //
// 0.CHKKETA 桁チェック(1,5,7桁はインポートしない)
// 1.ADDZERO 桁を合わせる
// 2.CHKCD   チェックデジット計算
// 3.ADDCD   チェックデジット付加
//-------------------------------------------//
function GETJAN($jcode){
 $j=(string) $jcode;
 $j=str_replace(".0","",$j);
 if(CHKKETA($j)){
  $j=ADDZERO($j);
  if(! CHKCD($j)){
   $j=ADDCD($j);
  }
 }
 else return false;
 return $j;
}
//-------------------------------------------//
// 桁チェック                                //
// コードの桁が正しいか判断                  //
//-------------------------------------------//
function CHKKETA($jcode){
 $j=(string) $jcode;
 $l=strlen($j);

 //数字以外はエラー
 if(! preg_match("/^[0-9]+$/",$j)) return false;
 //1,5,7桁はエラー
 if($l===1 || $l===5 || $l===7) return false;

 //12桁で2で始まって最後が0以外はエラー(インストア)
 if($l===12 && preg_match("/^2[0-9]+[^0]$/",$j)) return false;

 return true;
}//CHKKETA

//-------------------------------------------//
// チェックデジット計算                      //
// チェックデジットが正しいか判断            //
//-------------------------------------------//
function CHKCD($jcode){
 $j=(string) $jcode;

 $l=strlen($j);

 //桁数チェック
 if($l!==8 && $l!==12 && $l!==13){
  return false;
 }

 //4桁1000番台はスルー
 if(preg_match("/^1[0-9]{3}$/",$j)) return true;

 //元CDゲット
 $c=substr($j,-1,1);

 //CD抜き
 $wc=substr($j,0,$l-1);
 $l=strlen($wc);
 //CD計算
 $chk=0;
 for($i=0;$i<$l;$i++){
  $keta=$i+2;
  $s=$l-$i-1;//全体桁数からループ回数を引くと右側の桁になる。
  if($keta%2) $chk+=substr($j,$s,1);  //偶数位置
  else        $chk+=substr($j,$s,1)*3;//奇数位置
 }
 $chk=10-$chk%10;
 if($chk===10) $chk=0;

 $chk=(string) $chk;
 if($c===$chk) return true;
 else          return false;
}


//-------------------------------------------//
// チェックデジット計算                      //
// チェックデジットを付加する                //
//-------------------------------------------//
function ADDCD($jcode){
 //正しければそのまま終了
 if(CHKCD($jcode)) return $jcode;

 $j=(string) $jcode;

 //CD抜き
 $l=0;
 $wc=substr($j,0,$l-1);
 $l=strlen($wc);

 //CD計算
 $chk=0;
 for($i=0;$i<$l;$i++){
  $keta=$i+2;
  $s=$l-$i-1;//全体桁数からループ回数を引くと右側の桁になる。
  if($keta%2) $chk+=substr($j,$s,1);  //偶数位置
  else        $chk+=substr($j,$s,1)*3;//奇数位置
 }
 $chk=10-$chk%10;
 if($chk===10) $chk=0;

 $chk=(string) $chk;

 $wc.=$chk;
 return $wc;
}

//----------------------------------------------//
// 前0付加                                      //
// 渡されたコードの文字数によって付加する0を変化//
// 1桁   無視
// 2-3桁 CD付き 13桁まで[前0]付加
// 4桁   CDなし 13桁まで[前0]付加
// 5桁   無視
// 6桁   CD付き 8桁まで[前0]付加 (UPC)
// 7桁   無視
// 8桁   無視
// 9桁   CDなし 11桁まで[前0]付加し[後0]を1つ付加する (UPC)
//10桁   CDなし 11桁まで[前0]付加し[後0]を1つ付加する (UPC)
//11桁   CDあり 12桁まで[前0]付加                     (UPC)
//12桁   CDあり 13桁まで[前0]付加                     (インストア)
//12桁   CDなし 13桁まで[前0]付加                     (インストア)
//13桁   無視
//----------------------------------------------//
function ADDZERO($jcode){
 $j=(string) $jcode;

 $l=strlen($j);
 $a=0;
 $b="";
 if($l===2 || $l===3 || $l===4){
  $a=13-$l;
 }
 if($l===6){
  $a=2; //付加する0の数
 }//if
 if($l===9 || $l===10){
  $a=11-$l;
  $b="0";
 }
 if($l===11 || $l===12){
  $a=1;
 }

 $zero="";
 for($i=0;$i<$a;$i++){
  $zero.="0";
 }//for

 return $zero.$j.$b;
}
//--------------------------------------------------------------//

function WriteLog($log,$data=null){
 //ファイル名セット
 $path=LOGDIR.date("Ymd").".log";
 if(! file_exists($path)){
  $fp=fopen($path,"w");
 }//if
 else{
  $fp=fopen($path,"a");
 }//else

 //ファイル名なし、データなし
 if(! $data){
  $log=date("Y-m-d H:i:s")." ".$log."\n";
  fwrite($fp,$log);
 }//if

 if($data){
  //エラーデータ抽出
  foreach($data as $rownum=>$rowdata){
   if($rowdata["status"]) continue;
   $l=$log." ".($rownum+1)."行 ".$rowdata["err"];
   fwrite($fp,$l);
  }//foreach
 }//if

 fclose($fp);
}

//---------------------------------------------------//
// 曜日
//---------------------------------------------------//
$YOUBI=array( 0=>"日"
             ,1=>"月"
             ,2=>"火"
             ,3=>"水"
             ,4=>"木"
             ,5=>"金"
             ,6=>"土"
            );


$ary=array("test"=>1,
           "last"=>3);

//ファンクション内で使用するグローバル関数
function TEST(){
 //echo "class". " ".CSSDIR;
 global $ary;
 print_r($ary);
}

//クラス内で使用するグローバル関数
class TEST2{
 public static function test(){
  print_r($GLOBALS["ary"]);
 }
}
//--------------------------------------------------------------//
?>
