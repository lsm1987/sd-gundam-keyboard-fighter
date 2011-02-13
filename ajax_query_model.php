<?
//UTF-8 (BOM없음)
ob_start(); //Cannot modify header information 워닝 막기
include_once("inc/dbconfig.php");
$conn=mysql_connect($server,$username,$password);
@mysql_select_db($database, $conn) or die( "Unable to select database");
mysql_query("set names utf8", $conn);

//XML 시작
header("Content-Type: application/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="utf-8"?> ';
echo "<items>";
echo "<rq>$_GET[q]</rq>";

//키워드로 시작하는 유닛명 구하기 (5개 오름차순)
//$query="SELECT Model FROM unit WHERE Model LIKE '$_GET[q]%' ORDER BY Model ASC LIMIT 5";
$query="SELECT Model FROM unit WHERE Model LIKE '$_GET[q]%' ORDER BY Model ASC";
$result=mysql_query($query);
while($row = mysql_fetch_array($result)){
 echo "<item>$row[Model]</item>";
}

//키워드를 포함하는 유닛명 구하기 (3개 오름차순)
//$query="SELECT Model FROM unit WHERE Model LIKE '%$_GET[q]%' AND Model Not In(SELECT Model FROM unit WHERE Model LIKE '$_GET[q]%') ORDER BY Model ASC LIMIT 3";
$query="SELECT Model FROM unit WHERE Model LIKE '%$_GET[q]%' AND Model Not In(SELECT Model FROM unit WHERE Model LIKE '$_GET[q]%') ORDER BY Model ASC";
$result=mysql_query($query);
while($row = mysql_fetch_array($result)){
 echo "<r_item>$row[Model]</r_item>";
}

//XML 끝
echo "</items>";
mysql_close($conn);
?>
