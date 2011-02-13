<?
include_once("header.php");

$RecordDate="";
if( strlen($_GET["RecordDate"])!=0 ){
	$RecordDate=$_GET["RecordDate"];
}

$query="SELECT * FROM promotion AS p LEFT JOIN userrank AS ur ON p.UserRank=ur.UserRank";
if($RecordDate!=""){
	$query=$query." WHERE RecordDate='$RecordDate'"; //날짜 지정시
}else{
	$query=$query." WHERE RecordDate In (SELECT max(RecordDate) FROM promotion)"; //날짜 미지정시 최근기록
}
$query=$query." ORDER BY ur.No DESC";
?>

<!--유저 계급표 페이지-->
<h2>유저 계급표</h2>
주인장 기분 내킬때만 업데이트됩니다(...)<br/>
<br/>
<form action="user_rank.php" method="get">
정산일자:&nbsp;
<select name="RecordDate">
	<?
	$query_date="SELECT DISTINCT RecordDate FROM promotion ORDER BY RecordDate DESC";
	$result_date=mysql_query($query_date);
	while($row = mysql_fetch_array($result_date)){
		$selected="";
		if($RecordDate==$row['RecordDate']){
			$selected="selected='selected'";
		}
		$curDay=strtotime($row['RecordDate']);
		echo "<option value='$row[RecordDate]' $selected>".date("Y년 m월 d일", $curDay)." (".getDayOfWeek($curDay).")</option>";
	}
	?>
</select>
&nbsp;<input type="submit" value="조회" />
</form>
<br/>

<?
$result=mysql_query($query);
if(mysql_num_rows($result)==0){
	echo "해당 날짜의 기록이 없습니다.";
}else{
	//결과 있는 경우
?>
<table class="example">
<thead>
<tr>
	<th rowspan="2">계급</th>
	<th colspan="2">순위</th>
	<th colspan="2">경험치</th>
	<th rowspan="2">인원</th>
	<th rowspan="2">경험치<br/>구간</th>
	<th rowspan="2">1인당<br/>격차</th>
	<th colspan="2">비고</th>
</tr>
<tr>
	<th>최고</th>
	<th>최저</th>
	<th>최고</th>
	<th>최저</th>
	<th>필요 경험치</th>
	<th>추가 조건</th>
</tr>
</thead>
<tbody>
<?
	$prevRanking=0;	//순위 계산용 이전랭크까지 사람 수
	while($row = mysql_fetch_array($result)){ 
		echo "<tr>";
		//계급
		echo "<td><img src='http://img.gundam.netmarble.net/img/capaguide/class/".FillDigit($row[No],2).".gif' style='vertical-align:middle'/>&nbsp;$row[UserRank]</td>";
	  
		//순위
		$firstRanking=$prevRanking+1; //지난 랭킹 다음
		$lastRanking=$firstRanking+$row[Number]-1;
		if($prevRanking==0 && $row[Number]==0){ //첫 행이고 현재 계급에 사람이 없다면
			$firstRanking=0;
			$lastRanking=0;
		}
		echo "<td class='right'>".$firstRanking."위</td> ";
		echo "<td class='right'>".$lastRanking."위</td>";

		//경험치
		echo "<td class='right'>".number_format($row[ExpMax])."</td>";
		echo "<td class='right'>".number_format($row[ExpMin])."</td>";

		//인원
		echo "<td class='right'>$row[Number]명</td>";

		//구간
		echo "<td class='right'>".number_format($row[ExpMax]-$row[ExpMin])."</td>";

		//경험치 격차
		$gap=0;
		if($row[Number]>1){ //2명 이상일 때
			$gap=round( ($row[ExpMax]-$row[ExpMin])/($row[Number]-1) );
		}
		echo "<td class='right'>".number_format($gap)."</td>";
	  
		//비고
		echo "<td class='right'>".number_format($row[NeedExp])."</td>";
		echo "<td class='center'>".$row[Condition]."</td>";
		echo "</tr>";
	  
		$prevRanking+=$row[Number];
	}
?>
</tbody>
</table>
<?
}//결과 있을 경우
include_once("footer.php");
?>