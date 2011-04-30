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

$infoRow=null;
//인자가 있을 때
if($unitID!=""){
	$infoQuery="SELECT * FROM unit WHERE UnitID=$unitID";
	$infoResult=mysql_query($infoQuery);
	if(mysql_num_rows($infoResult)){ //검색 결과가 있을 때
		$infoRow = mysql_fetch_array($infoResult);
	}
}

//유닛 레벨
$levelQuery="SELECT * FROM level";
$levelResult=mysql_query($levelQuery);

//유닛 경험치
$expQuery="SELECT Exp
	FROM levelexp AS le
	LEFT JOIN level AS l ON le.Level=l.Level
	WHERE le.RankPrefix='$infoRow[RankPrefix]' AND le.RankSuffix='$infoRow[RankSuffix]'
	ORDER BY l.No ASC";
$expResult=mysql_query($expQuery);
?>

<!--유닛 시뮬레이터 페이지-->
<h2>유닛 시뮬레이터(베타)</h2>
지정한 유닛의 성장을 시뮬레이트합니다. 스탯공식은 이에르님의 능력치표를 참고하였습니다.<br/>
모든 유닛은 커스텀 슬롯이 4개 있다고 가정합니다.<br/>
<br/>
<form action="unit_simulator.php" method="get">
	유닛명: <input type="text" autocomplete="off" id="model_q" name="model_q" class="inputtext suggest_common" value="<?=$model?>"/>
	<div id='model_sug' class="suggest_wrap suggest_common">
		<iframe id="model_frame" src="ajax_suggest.php?prefix=model" frameborder="0" scrolling="no" class="suggest_frame suggest_common"></iframe>
	</div>
	<input type="submit" value="검색" />
</form>
<script type="text/JavaScript">
window.onload=function(){
	setSuggestPos(document.getElementById("model_q"), document.getElementById("model_sug"));
}
</script>
<br/>

<?
if($unitID==""){
	echo "선택된 유닛이 없거나 검색결과가 없습니다.";
}else if($infoRow==null){
	echo "검색결과가 없습니다.";
}else{
	//결과 있는 경우
?>
<script language="javascript" src="scripts/unitSimulator.js"></script>

<!--기본정보-->
<table id="simulatorConfig" class="sdgoTable unitSimulator">
<tr>
<th>선택 유닛</th>
<td><?=$infoRow[Model]?></td>
<th>랭크</th>
<td class="rank center"><?=GetRankFull($infoRow[RankPrefix], $infoRow[RankSuffix])?></td>
<th>속성</th>
<td class="property center"><?=GetPropertyKor($infoRow[Property])?></td>
</tr>
<tr>
<th>레벨</th>
<td>
	<select class="level" onchange="javascript:simulatorLevelChange()">
	<?
	$levelCount=mysql_num_rows($levelResult);
	$i=0;
	while($levelRow = mysql_fetch_array($levelResult)){
		$strText="<option value='".$levelRow[No]."' ";
		if ($i==$levelCount-1){
			$strText .= "selected='selected' ";
		}
		$strText .= ">".$levelRow[Level]."</option>";
		echo $strText;
		$i++;
	}
	?>	
	</select>
</td>
<th>커스텀 포인트</th>
<td class="point_custom center">0</td>
<th>오버커스텀 포인트</th>
<td class="point_overcustom center">0</td>
</table>
<br/>

<!--스탯-->
<table id="simulatorStat" class="sdgoTable unitSimulator">
<thead>
<tr>
<th rowspan="2">스탯</th>
<th rowspan="2">기본 성능</th>
<th colspan="3">커스텀</th>
<th colspan="3">오버커스텀</th>
<th rowspan="2">최종 성능</th>
<th rowspan="2">성능 그래프</th>	
</tr>
<tr>
<th>증가량</th>
<th>포인트</th>
<th class="change">변경</th>
<th>증가량</th>
<th>포인트</th>
<th class="change">변경</th>
</tr>
</thead>

<tbody>
<?
$statName = array("유닛HP", "방어력", "필살기", "스피드", "공격력", "민첩성", "총합");
for($i=0; $i<7; $i++){
?>
	<tr>
	<th><?=$statName[$i]?></th>
	<td class="right"></td>

	<!--증가량, 포인트, 변경-->
	<?
	for($j=0; $j<2; $j++){
	?>
		<td class="right"></td>
		<td class="right"></td>
		<td class="center">
			<?if($i!=6){?>
				<input type="button" value="-" onclick="javascript:simulatorPointChange(<?=$j?>,<?=$i?>,-1);" />
				<input type="button" value="+" onclick="javascript:simulatorPointChange(<?=$j?>,<?=$i?>,+1);" />
			<?}?>
		</td>
	<?
	}
	?>

	<!--최종 성능-->
	<td class="right finalStat"></td>
	
	<!--그래프-->
	<?if($i==0){?>
	<td rowspan="7">
		<iframe class="frameGraph"
		src=""
		border="0" frameborder="0" scrolling="no" style="width:210px;height:140px">
		</iframe>
	</td>
	<?}?>
	</tr>
<?
}
?>
<tr>
<th>비고</th>
<td colspan="9" class="note">&nbsp;</td>
</tr>
</tbody>
</table>
<br/>

<!--기타-->
<table id="simulatorEtc" class="sdgoTable unitSimulator">
<thead>
<tr>
<th></th>
<th class="header">기타 육성정보</th>
</tr>
</thead>
<tbody>
<tr>
<th>유닛 경험치</th>
<td class="exp"></td>
</tr>
</tbody>
</table>


<script language="javascript">
SimulatorInit(
	//기본 스탯
	new Array(<?=$infoRow[UnitStat1]?>, <?=$infoRow[UnitStat2]?>, <?=$infoRow[UnitStat3]?>, <?=$infoRow[UnitStat4]?>, <?=$infoRow[UnitStat5]?>, <?=$infoRow[UnitStat6]?>),
	//스킬
	new Array("<?=$infoRow[Skill1Name]?>", "<?=$infoRow[Skill2Name]?>", "<?=$infoRow[Skill3Name]?>"),
	//속성
	"<?=PropertyToNum($infoRow[Property])?>",
	//경험치
	new Array(
		<?
		$expCount=mysql_num_rows($expResult);
		$i=0;
		while($expRow = mysql_fetch_array($expResult)){
			$exp=0;
			if( $expRow[Exp] != null ) $exp=$expRow[Exp];
			echo $exp;
			if ($i<$expCount-1) echo ", ";
			$i++;
		}
		?>
	)
);
</script>
<?
}
include_once("footer.php");
?>