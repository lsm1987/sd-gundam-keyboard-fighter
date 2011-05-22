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

//쿼리
$query="SELECT
	c.Name AS Name, c.Cost AS Cost,
	c.Unit1ID, u1.Model AS Unit1Name, u1.RankPrefix As Unit1RankPrefix, u1.RankSuffix As Unit1RankSuffix, u1.Property As Unit1Property,
	c.Unit2ID, u2.Model AS Unit2Name, u2.RankPrefix As Unit2RankPrefix, u2.RankSuffix As Unit2RankSuffix, u2.Property As Unit2Property,
	c.Unit3ID, u3.Model AS Unit3Name, u3.RankPrefix As Unit3RankPrefix, u3.RankSuffix As Unit3RankSuffix, u3.Property As Unit3Property,
	c.Unit4ID, u4.Model AS Unit4Name, u4.RankPrefix As Unit4RankPrefix, u4.RankSuffix As Unit4RankSuffix, u4.Property As Unit4Property,
	c.Unit5ID, u5.Model AS Unit5Name, u5.RankPrefix As Unit5RankPrefix, u5.RankSuffix As Unit5RankSuffix, u5.Property As Unit5Property,
	c.Unit6ID, u6.Model AS Unit6Name, u6.RankPrefix As Unit6RankPrefix, u6.RankSuffix As Unit6RankSuffix, u6.Property As Unit6Property
	FROM capsule AS c
	LEFT JOIN unit AS u1 ON c.Unit1ID=u1.UnitID
	LEFT JOIN unit AS u2 ON c.Unit2ID=u2.UnitID
	LEFT JOIN unit AS u3 ON c.Unit3ID=u3.UnitID
	LEFT JOIN unit AS u4 ON c.Unit4ID=u4.UnitID
	LEFT JOIN unit AS u5 ON c.Unit5ID=u5.UnitID
	LEFT JOIN unit AS u6 ON c.Unit6ID=u6.UnitID"; //쿼리 본체
$query_order="ORDER BY c.No ASC";	//쿼리 정렬

//포함유닛 검색조건
$query_included="c.Unit1ID=$unitID OR
	c.Unit2ID=$unitID OR
	c.Unit3ID=$unitID OR
	c.Unit4ID=$unitID OR
	c.Unit5ID=$unitID OR
	c.Unit6ID=$unitID";
 //최종 쿼리
$query_final="";

//쿼리 조합
if(strlen($unitID)==0){ //조건 없을 때
	$query_final=$query." ".$query_order;
}else{
	$query_final=$query." WHERE ".$query_included." ".$query_order;
}
//echo $query_final."<br/><br/>";
?>

<!--캡슐머신 검색 페이지-->
<h2>캡슐머신 검색</h2>
손으로 옮겨적느라 오타의 우려가 있습니다 =_=;;;<br/>
<br/>

<form id="unitName_form" action="capsule_search.php" method="get">
	<!--검색어-->
	유닛명: <input type="text" autocomplete="off" id="model_q" name="model_q" class="inputtext suggest_common" value="<?=$model?>"/>
	<input type="submit" value="검색" />
	<!--서제스트 결과-->
	<div id='model_sug' class="suggest_wrap suggest_common">
		<iframe id="model_frame" src="ajax_suggest.php?prefix=model" frameborder="0" scrolling="no" class="suggest_frame suggest_common"></iframe>
	</div>
</form>
<script type="text/JavaScript">
window.onload=function(){
	setSuggestPos(document.getElementById("model_q"), document.getElementById("model_sug"));
}
</script>
<br/>

<!--캡슐머신 목록 테이블-->
<table class="example table-autosort">
<thead>
<tr>
	<th>캡슐머신명</th>
	<th class="table-sortable:numeric">가격</th>
	<th>유닛1</th>
	<th>유닛2</th>
	<th>유닛3</th>
	<th>유닛4</th>
	<th>유닛5</th>
	<th>유닛6</th>
</tr>
</thead>

<!--캡슐머신 목록 테이블 본문-->
<tbody>
<?
//쿼리 실행
$result=mysql_query($query_final);
if(mysql_num_rows($result)==0){ //검색 결과가 없을 때
	echo"<tr>
		<td colspan='8'><center>검색 결과가 없습니다.</center></td>
		</tr>";
}else{ //검색 결과가 있을 때
	while($row = mysql_fetch_array($result)){
		echo "<tr>";
		echo "<td>$row[Name]</td>";
		echo "<td class='center'>$row[Cost]</td>";
		for($i=1; $i<=6; $i++){
			$curUnitName=$row["Unit"."$i"."Name"];
			$curUnitID=$row["Unit"."$i"."ID"];
			$curUnitRank=GetRankFull($row["Unit"."$i"."RankPrefix"], $row["Unit"."$i"."RankSuffix"]);
			$curUnitProperty=GetPropertyKor($row["Unit"."$i"."Property"]);
			$searched="";
			if($curUnitID==$unitID){
				$searched="class='searched'";
			}
			if( $curUnitID != null ) {
				echo "<td $searched><span class='subscript'>".$curUnitRank."랭크 $curUnitProperty</span><br/><a href='unit_info.php?unitID_q=$curUnitID'>$curUnitName</a></td>";
			}else{
				echo "<td></td>";
			}
		}
		echo "</tr>";
	}
}
?>
</tbody>
</table>

<?
include_once("footer.php");
?>