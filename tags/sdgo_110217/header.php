<?
include_once("inc/dbconfig.php");
$conn=mysql_connect($server,$username,$password);
@mysql_select_db($database, $conn) or die( "Unable to select database");
mysql_query("set names utf8", $conn);

include_once("inc/function.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SD건담 키보드파이터</title>
<link rel="stylesheet" href="styles/styles.css" type="text/css" />
<link rel="stylesheet" href="styles/table.css" type="text/css" />
<link rel="stylesheet" href="styles/unitInfo.css" type="text/css" />
<link rel="stylesheet" href="styles/suggest.css" type="text/css" />
<script language="javascript" src="scripts/scripts.js"></script>
<script language="javascript" src="scripts/table.js"></script>
<script language="javascript" src="scripts/suggest.js"></script>
<?
include_once("../inc/analytics.php");
?>
</head>

<body>
<!--전체 시작-->
<table id="container">

<!--타이틀-->
<tr>
<td id="header">
	<h1><a href="index.php">SD건담<strong>키보드파이터</strong></a></h1>
	<h2>SD Gundam Keyboard Fighter</h2>
</td>
</tr>

<!--메뉴-->
<tr>
<td id="nav">
	<ul>
		<li><a href="index.php">메인페이지</a></li>
		<li><a href="unit_info.php">유닛 정보</a></li>
		<li><a href="unit_list.php">유닛 목록</a></li>
		<li><a href="mix_search.php">조합식 검색</a></li>
		<li><a href="capsule_search.php">캡슐머신 검색</a></li>
		<li><a href="user_rank.php">유저 계급표</a></li>
		<li><a href="skill_weapon_list.php">스킬/무기 목록</a></li>
		<li><a href="data_table.php">자료 테이블</a></li>
	</ul>
</td>
</tr>
 
 <!--본문 시작-->
 <tr>
 <td id="content">