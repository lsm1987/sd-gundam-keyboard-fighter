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

//키워드로 시작하는 스킬명 구하기
$query="SELECT Name FROM skill WHERE Name LIKE '$_GET[q]%' ORDER BY Name ASC";
$result=mysql_query($query);
while($row = mysql_fetch_array($result)){
 echo "<item>$row[Name]</item>";
}

//키워드를 포함하는 스킬명 구하기
$query="SELECT Name FROM skill WHERE Name LIKE '%$_GET[q]%' AND Name NOT IN (SELECT Name FROM skill WHERE Name LIKE '$_GET[q]%') ORDER BY Name ASC";
$result=mysql_query($query);
while($row = mysql_fetch_array($result)){
 echo "<r_item>$row[Name]</r_item>";
}

//XML 끝
echo "</items>";
mysql_close($conn);
?>
