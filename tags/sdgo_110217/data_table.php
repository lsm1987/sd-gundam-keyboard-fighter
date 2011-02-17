<?
include_once("header.php");
?>
<!--자료 테이블 페이지-->

<!--커스텀 초기화 비용 테이블-->
<h2>커스텀 초기화 비용</h2>
자료 출처: 춤추는리가지 님<br/>
<br/>
<?
//레벨이 순서대로 저장된 배열 만들기
$levelQuery="SELECT Level FROM level WHERE No BETWEEN '6' AND '14' ORDER BY No ASC";
$levelResult=mysql_query($levelQuery);
$arrLevel=array();
while($levelRow = mysql_fetch_array($levelResult)){
	array_push($arrLevel, $levelRow[Level]);
}
//기체랭크 배열
$arrRank=array("S","A","B","C");
 
//기체 랭크별 레벨별 초기화 비용 찾기
 ?>
<table class="example">
<thead>
<tr>
<th>랭크</th>
<?
foreach($arrLevel as $level){
	echo "<th>$level</th>";
}
?>
 </tr>
</thead>
<tbody>
<?
foreach($arrRank as $rank){
	echo "<tr>";
	echo "<td class='center'>$rank</td>";
	foreach($arrLevel as $level){
		$costQuery="SELECT InitCost FROM initcost WHERE Rank='$rank' AND Level='$level'";
		//echo $costQuery;
		$costResult=mysql_query($costQuery);
		$costRow = mysql_fetch_array($costResult);
		echo "<td class='right'>".$costRow[InitCost]."</td>";
	}
	echo "</tr>";
}
?>
</tbody>
</table>
<br/><br/>

<!--레벨별 경험치 구간 테이블-->
<h2>레벨별 경험치 구간</h2>
자료 출처: <a href="http://sdgowiki.com/wiki/Unit_EXP_Table" target="_blank">SDGO Wiki</a><br/>
<br/>
<?
//모든 레벨이 순서대로 저장된 배열 만들기
$levelQuery="SELECT Level FROM level ORDER BY No ASC";
$levelResult=mysql_query($levelQuery);
$arrLevel=array();
while($levelRow = mysql_fetch_array($levelResult)){
	array_push($arrLevel, $levelRow[Level]);
}
//print_r($arrLevel);

$rankPrefixQuery="SELECT RankPrefix FROM rankprefix ORDER BY No ASC";
$rankPrefixResult=mysql_query($rankPrefixQuery);
$arrRankPrefix=array();
while($row = mysql_fetch_array($rankPrefixResult)){
	array_push($arrRankPrefix, $row[RankPrefix]);
}
//print_r($arrRankPrefix);

$rankSuffixQuery="SELECT RankSuffix FROM ranksuffix ORDER BY No ASC";
$rankSuffixResult=mysql_query($rankSuffixQuery);
$arrRankSuffix=array();
while($row = mysql_fetch_array($rankSuffixResult)){
	array_push($arrRankSuffix, $row[RankSuffix]);
}
//print_r($arrRankSuffix);
?>
<table class="example">
<thead>
<tr>
<th>랭크</th>
<?
foreach($arrLevel as $level){
	echo "<th>$level</th>";
}
?>
<th>합계</th>
</tr>
</thead>
<tbody>
<?
foreach($arrRankPrefix as $rankPrefix){
	foreach($arrRankSuffix as $rankSuffix){
		//해당 랭크에 경험치 정보가 있는가?
		$expExistQuery="SELECT * FROM levelexp WHERE RankPrefix='$rankPrefix' AND RankSuffix='$rankSuffix'";
		$expExistResult=mysql_query($expExistQuery);
		if(0==mysql_num_rows($expExistResult)){
			continue; //정보가 없으면 다음 랭크로
		}
		
		//한줄 시작
		$totalExp=0;
		echo "<tr>";
		echo "<td class='center'>".GetRankFull($rankPrefix,$rankSuffix)."</td>";
		foreach($arrLevel as $level){
			$expQuery="SELECT Exp FROM levelexp WHERE RankPrefix='$rankPrefix' AND RankSuffix='$rankSuffix' AND Level='$level'";
			$expResult=mysql_query($expQuery);
			$expRow = mysql_fetch_array($expResult);
			if($expRow[Exp]!=""){
				$totalExp+=$expRow[Exp];//합계에 추가
				$exp=$expRow[Exp];
			}else{
				$exp="-";
			}
			echo "<td class='right'>".$exp."</td>";
		}
		echo "<td class='right'>".$totalExp."</td>";
		echo "</tr>";
	}
}
?>
</tbody>
</table>
<?
include_once("footer.php");
?>