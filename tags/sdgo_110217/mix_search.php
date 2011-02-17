<?
include_once("header.php");

$unitID="";
$model="";
//유닛ID가 넘어왔다면 그걸로 사용
if( strlen($_GET["unitID_q"])!=0 ){
	$unitID=$_GET["unitID_q"];
	$model=getModelByUnitID($unitID);
	
//유닛ID가 넘어오지 않고 유닛명만 넘어왔다면
}else if(strlen($_GET["model_q"])!=0){
	$model=$_GET["model_q"];
	$unitID=getUnitIDByModel($model);
	if(strlen($unitID)==0){ //유닛명에 해당하는 ID가 없으면
		$unitID=""; //공백으로
	}
}
//검색 범위
$searchRange="";
if( strlen($_GET["searchRange_q"])!=0 ){
	$searchRange=$_GET["searchRange_q"];
}

//쿼리
$query="SELECT
	r.Model AS ResultUnitName,	m.ResultUnitID,
	k.Model AS KeyUnitName, m.KeyUnitLevel, m.KeyUnitID,
	m1.Model AS MatUnit1Name, m.MatUnit1Level, m.MatUnit1ID,
	m2.Model AS MatUnit2Name, m.MatUnit2Level, m.MatUnit2ID,
	m3.Model AS MatUnit3Name, m.MatUnit3Level, m.MatUnit3ID,
	m4.Model AS MatUnit4Name, m.MatUnit4Level, m.MatUnit4ID,
	m.Acquisition
	FROM mix AS m
	LEFT JOIN unit AS r ON m.ResultUnitID=r.UnitID
	LEFT JOIN unit AS k ON m.KeyUnitID=k.UnitID
	LEFT JOIN unit AS m1 ON m.MatUnit1ID=m1.UnitID
	LEFT JOIN unit AS m2 ON m.MatUnit2ID=m2.UnitID
	LEFT JOIN unit AS m3 ON m.MatUnit3ID=m3.UnitID
	LEFT JOIN unit AS m4 ON m.MatUnit4ID=m4.UnitID";	//쿼리 본체
$query_order="ORDER BY ResultUnitName ASC";	//쿼리 정렬

//획득유닛 검색조건
$query_result="r.UnitID='$unitID'";
//재료유닛 검색조건
$query_mat=" (k.UnitID='$unitID' OR
	m1.UnitID='$unitID' OR
	m2.UnitID='$unitID' OR
	m3.UnitID='$unitID' OR
	m4.UnitID='$unitID')"; 
 //최종 쿼리
$query_final="";

//쿼리 조합
if($unitID==""){ //조건 없음
	$query_final=$query." ".$query_order;
}else if($searchRange=="result"){ //획득유닛만
	$query_final=$query." WHERE ".$query_result." ".$query_order;
}else if($searchRange=="mat"){ //재료유닛만
	$query_final=$query." WHERE ".$query_mat." ".$query_order;
}else{ //조건 둘 다
	$query_final=$query." WHERE ".$query_result." OR ".$query_mat." ".$query_order;
}
//echo $query_final."<br/><br/>";
$result=mysql_query($query_final);
?>

<!--조합식 검색 페이지-->
<h2>조합식 검색</h2>
조합식으로 얻어지는 유닛을 '획득유닛', 재료로 사용되는 유닛을 '재료유닛'으로 지칭합니다.<br/>
<br/>

<form action="mix_search.php" method="get">
	검색범위:<select name="searchRange_q">
		<option value="" <?if($searchRange==""){echo "selected='selected'"; }?> >All</option>
		<option value="result" <?if($searchRange=="result"){echo "selected='selected'"; }?> >획득유닛</option>
		<option value="mat" <?if($searchRange=="mat"){echo "selected='selected'"; }?> >재료유닛</option>
	</select>
	&nbsp;&nbsp;
	유닛명: <input type="text" autocomplete="off" id="model_q" name="model_q" class="suggest_input suggest_common" value="<?=$model?>"/>
	<!--서제스트 결과-->
	<div id='model_sug' class="suggest_wrap suggest_common">
		<iframe id="model_frame" src="ajax_suggest.php?prefix=model" frameborder="0" scrolling="no" class="suggest_frame suggest_common"></iframe>
	</div>
	&nbsp;&nbsp;
	<input type="submit" value="검색" />
</form>
<script type="text/JavaScript">
window.onload=function(){
	setSuggestPos(document.getElementById("model_q"), document.getElementById("model_sug"));
}
</script>
<br/>

<!--조합식 목록 테이블-->
<table class="example">
<thead>
<tr>
	<th rowspan="2">획득유닛</th>
	<th colspan="10">재료유닛</th>
	<th rowspan="2">획득경로</th>
</tr>
<tr>
	<th>키유닛</th>
	<th>레벨</th>
	<th>재료유닛1</th>
	<th>레벨</th>
	<th>재료유닛2</th>
	<th>레벨</th>
	<th>재료유닛3</th>
	<th>레벨</th>
	<th>재료유닛4</th>
	<th>레벨</th>
</tr>
</thead>

<!--조합식 목록 테이블 본문-->
<tbody>
<?
if(!mysql_num_rows($result)){ //검색 결과가 없을 때
	echo"<tr>
		<td colspan='12'><center><br/>검색 결과가 없습니다.<br/><br/></center></td>
		</tr>";
}else{ //검색 결과가 있을 때
	while($row = mysql_fetch_array($result)){
		echo "<tr>";
		$searched="";
		if(strlen($unitID)!=0 && $unitID==$row[ResultUnitID]){
			$searched="class='searched'";
		}
		echo "<td $searched><a href='unit_info.php?unitID_q=$row[ResultUnitID]'>$row[ResultUnitName]</a></td>";
		
		$searched="";
		if(strlen($unitID)!=0 && $unitID==$row[KeyUnitID]){
			$searched="class='searched'";
		}
		echo "<td $searched><a href='unit_info.php?unitID_q=$row[KeyUnitID]'>$row[KeyUnitName]</a></td>";
		echo "<td class='center'>$row[KeyUnitLevel]</td>";
		
		$searched="";
		if(strlen($unitID)!=0 && $unitID==$row[MatUnit1ID]){
			$searched="class='searched'";
		}
		echo "<td $searched><a href='unit_info.php?unitID_q=$row[MatUnit1ID]'>$row[MatUnit1Name]</a></td>";
		echo "<td class='center'>$row[MatUnit1Level]</td>";
		
		$searched="";
		if(strlen($unitID)!=0 && $unitID==$row[MatUnit2ID]){
			$searched="class='searched'";
		}
		echo "<td $searched><a href='unit_info.php?unitID_q=$row[MatUnit2ID]'>$row[MatUnit2Name]</a></td>";
		echo "<td class='center'>$row[MatUnit2Level]</td>";
		
		$searched="";
		if(strlen($unitID)!=0 && $unitID==$row[MatUnit3ID]){
			$searched="class='searched'";
		}
		echo "<td $searched><a href='unit_info.php?unitID_q=$row[MatUnit3ID]'>$row[MatUnit3Name]</a></td>";
		echo "<td class='center'>$row[MatUnit3Level]</td>";
		
		$searched="";
		if(strlen($unitID)!=0 && $unitID==$row[MatUnit4ID]){
			$searched="class='searched'";
		}		
		echo "<td $searched><a href='unit_info.php?unitID_q=$row[MatUnit4ID]'>$row[MatUnit4Name]</a></td>";
		echo "<td class='center'>$row[MatUnit4Level]</td>";
  
		echo "<td class='center'>$row[Acquisition]</td>";
		echo "</tr>";
	}
}
?>
</tbody>
</table>

<?
include_once("footer.php");
?>