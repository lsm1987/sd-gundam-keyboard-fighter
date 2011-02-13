<?
include_once("header.php");

//인자 정리 및 쿼리 partial 생성///////////////////////////
$arrCondition=array();

//유닛명
$model="";
if( strlen($_GET["model_q"])!=0 ){
	$model=$_GET["model_q"];
}
$modelOption="";
if( strlen($_GET["modelOption_q"])!=0 ){
	$modelOption=$_GET["modelOption_q"];
}
//유닛명과 옵션이 지정되었다면 쿼리 생성
if($model!="" && $modelOption!=""){
	if($modelOption=="include"){
		$query_partial="Model LIKE '%$model%'";
		array_push($arrCondition, $query_partial);
	}else if($modelOption=="begin"){
		$query_partial="Model LIKE '$model%'";
		array_push($arrCondition, $query_partial);
	}else if($modelOption=="equal"){
		$query_partial="Model='$model'";
		array_push($arrCondition, $query_partial);
	}
}

//스킬 슬롯
$skillSlot="";
if( strlen($_GET["skillSlot_q"])!=0 ){
	$skillSlot=$_GET["skillSlot_q"];
}
//스킬명
$skillName="";
if( strlen($_GET["skillName_q"])!=0 ){
	$skillName=$_GET["skillName_q"];
	
	//스킬슬롯이 지정되어있지 않으면 All로 간주
	if($skillSlot==""){
		$query_partial="(Skill1Name='$skillName' OR Skill2Name='$skillName' OR Skill3Name='$skillName')";
	}else{
		$query_partial="Skill".$skillSlot."Name='$skillName'";
	}
	array_push($arrCondition, $query_partial);
}

//랭크 접두
$rankPrefix="";
if( strlen($_GET["rankPrefix_q"])!=0 ){
	$rankPrefix=$_GET["rankPrefix_q"];
	$query_partial="RankPrefix='$rankPrefix'";
	array_push($arrCondition, $query_partial);
}

//랭크 접미
$rankSuffix="";
if( strlen($_GET["rankSuffix_q"])!=0 ){
	$rankSuffix=$_GET["rankSuffix_q"];
	$query_partial="RankSuffix='$rankSuffix'";
	array_push($arrCondition, $query_partial);
}

//속성
$property="";
if( strlen($_GET["property_q"])!=0 ){
	$property=$_GET["property_q"];
	$query_partial="Property='$property'";
	array_push($arrCondition, $query_partial);
}

//지형
$landform="";
if( strlen($_GET["landform_q"])!=0 ){
	$landform=$_GET["landform_q"];
	$query_partial="Landform='$landform'";
	array_push($arrCondition, $query_partial);
}

//변형
$modification="";
if( strlen($_GET["modification_q"])!=0 ){
	$modification=$_GET["modification_q"];
	$query_partial="Modification='$modification'";
	array_push($arrCondition, $query_partial);
}

//거대
$oversize="";
if( strlen($_GET["oversize_q"])!=0 ){
	$oversize=$_GET["oversize_q"];
	$query_partial="Oversize='$oversize'";
	array_push($arrCondition, $query_partial);
}

//저격
$sniping="";
if( strlen($_GET["sniping_q"])!=0 ){
	$sniping=$_GET["sniping_q"];
	$query_partial="Sniping='$sniping'";
	array_push($arrCondition, $query_partial);
}

//쿼리 만들기////////////////////////////
$query="SELECT * FROM unit";
$query=$query.GenerateQueryCondition($arrCondition);
$query=$query." ORDER BY UnitID ASC";
//echo $query;
$result=mysql_query($query);
?>

<!--유닛 목록 페이지-->
<h2>유닛 목록</h2>
스탯 총합은 필살기 제외수치입니다. 가변/퍼지 후 바뀌는 필살기 정보는 제공되지 않습니다 OTL<br/>
<br/>

<!--검색조건-->
<form id="unitCondition_form" action="unit_list.php" method="get">
	유닛명: 	<select name="modelOption_q">
		<option value="include" <?if($modelOption=="include"){echo "selected='selected'"; }?> >입력값을 포함</option>
		<option value="begin" <?if($modelOption=="begin"){echo "selected='selected'"; }?> >입력값으로 시작</option>
		<option value="equal" <?if($modelOption=="equal"){echo "selected='selected'"; }?> >입력값과 일치</option>
	</select>
	&nbsp;
	<input type="text" autocomplete="off" id="model_q" name="model_q" class="suggest_input suggest_common" value="<?=$model?>"/>
	<div id='model_sug' class="suggest_wrap suggest_common">
		<iframe id="model_frame" src="ajax_suggest.php?prefix=model" frameborder="0" scrolling="no" class="suggest_frame suggest_common"></iframe>
	</div>
	&nbsp;
	스킬:	<select name="skillSlot_q">
		<option value="" <?if($skillSlot==""){echo "selected='selected'"; }?> >All</option>
		<option value="1" <?if($skillSlot=="1"){echo "selected='selected'"; }?> >1번 스킬</option>
		<option value="2" <?if($skillSlot=="2"){echo "selected='selected'"; }?> >2번 스킬</option>
		<option value="3" <?if($skillSlot=="3"){echo "selected='selected'"; }?> >3번 스킬</option>
	</select>
	&nbsp;
	<input type="text" autocomplete="off" id="skillName_q" name="skillName_q" class="suggest_input suggest_common" value="<?=$skillName?>"/>
	<div id='skillName_sug' class="suggest_wrap suggest_common">
		<iframe id="skillName_frame" src="ajax_suggest.php?prefix=skillName" frameborder="0" scrolling="no" class="suggest_frame suggest_common"></iframe>
	</div>
	<br/><br/>
	
	랭크 접두: <select name="rankPrefix_q">
		<option value="" <?if($rankPrefix==""){echo "selected='selected'"; }?> >All</option>
		<option value="S" <?if($rankPrefix=="S"){echo "selected='selected'"; }?> >S</option>
		<option value="A" <?if($rankPrefix=="A"){echo "selected='selected'"; }?> >A</option>
		<option value="B" <?if($rankPrefix=="B"){echo "selected='selected'"; }?> >B</option>
		<option value="C" <?if($rankPrefix=="C"){echo "selected='selected'"; }?> >C</option>
	</select>
	&nbsp;
	랭크 접미: <select name="rankSuffix_q">
		<option value="" <?if($rankSuffix==""){echo "selected='selected'"; }?> >All</option>
		<option value="-" <?if($rankSuffix=="-"){echo "selected='selected'"; }?> >-</option>
		<option value="R" <?if($rankSuffix=="R"){echo "selected='selected'"; }?> >R</option>
		<option value="S" <?if($rankSuffix=="S"){echo "selected='selected'"; }?> >S</option>
		<option value="U" <?if($rankSuffix=="U"){echo "selected='selected'"; }?> >U</option>
	</select>
	&nbsp;
	속성: <select name="property_q">
		<option value="" <?if($property==""){echo "selected='selected'"; }?> >All</option>
		<option value="muk" <?if($property=="muk"){echo "selected='selected'"; }?> >묵</option>
		<option value="chi" <?if($property=="chi"){echo "selected='selected'"; }?> >찌</option>
		<option value="ba" <?if($property=="ba"){echo "selected='selected'"; }?> >빠</option>
	</select>
	&nbsp;
	지형: <select name="landform_q">
		<option value="" <?if($landform==""){echo "selected='selected'"; }?> >All</option>
		<option value="수중" <?if($landform=="수중"){echo "selected='selected'"; }?> >수중</option>
		<option value="만능" <?if($landform=="만능"){echo "selected='selected'"; }?> >만능</option>
		<option value="우주" <?if($landform=="우주"){echo "selected='selected'"; }?> >우주</option>
		<option value="일반" <?if($landform=="일반"){echo "selected='selected'"; }?> >일반</option>
		<option value="지상" <?if($landform=="지상"){echo "selected='selected'"; }?> >지상</option>
	</select>
	&nbsp;
	변형: <select name="modification_q">
		<option value="" <?if($modification==""){echo "selected='selected'"; }?> >All</option>
		<option value="on" <?if($modification=="on"){echo "selected='selected'"; }?> >on</option>
		<option value="off" <?if($modification=="off"){echo "selected='selected'"; }?> >off</option>
	</select>
	&nbsp;
	거대: <select name="oversize_q">
		<option value="" <?if($oversize==""){echo "selected='selected'"; }?> >All</option>
		<option value="on" <?if($oversize=="on"){echo "selected='selected'"; }?> >on</option>
		<option value="off" <?if($oversize=="off"){echo "selected='selected'"; }?> >off</option>
	</select>
	&nbsp;
	저격: <select name="sniping_q">
		<option value="" <?if($sniping==""){echo "selected='selected'"; }?> >All</option>
		<option value="on" <?if($sniping=="on"){echo "selected='selected'"; }?> >on</option>
		<option value="off" <?if($sniping=="off"){echo "selected='selected'"; }?> >off</option>
	</select>
	&nbsp;
	<input type="submit" value="검색" />
	&nbsp;
	<input type="button" value="초기화" onclick="location.href='unit_list.php'"/>
</form>
<script type="text/JavaScript">
window.onload=function(){
	setSuggestPos(document.getElementById("model_q"), document.getElementById("model_sug"));
	setSuggestPos(document.getElementById("skillName_q"), document.getElementById("skillName_sug"));
}
</script>
<br/>
<strong>검색된 유닛 수: <?=mysql_num_rows($result)?>개</strong><br/>
<br/>

<!--유닛 목록 테이블-->
<table class="example table-autosort">
<thead>
<tr>
	<th class="table-sortable:numeric" rowspan="2">ID</th>
	<th rowspan="2">유닛명</th>
	<th colspan="2">랭크</th>
	<th class="table-sortable:alphanumeric" rowspan="2">속성</th>
	<th colspan="3">스킬</th>
	<th colspan="7">스탯</th>
</tr>
<tr>
	<th class="table-sortable:alphanumeric">접두</th>
	<th class="table-sortable:alphanumeric">접미</th>
	<th>스킬1</th>
	<th>스킬2</th>
	<th>스킬3</th>
	<th class="table-sortable:numeric">유닛HP</th>
	<th class="table-sortable:numeric">방어력</th>
	<th class="table-sortable:numeric">필살기</th>
	<th class="table-sortable:numeric">스피드</th>
	<th class="table-sortable:numeric">공격력</th>
	<th class="table-sortable:numeric">민첩성</th>
	<th class="table-sortable:numeric">필제외 총합</th>
</tr>
</thead>

<!--유닛 목록 테이블 본문-->
<tbody>
<?
while($row = mysql_fetch_array($result)){
	$property="";
	if($row['Property']=="muk"){
		$property="묵";
	}else if($row['Property']=="chi"){
		$property="찌";
	}else if($row['Property']=="ba"){
		$property="빠";
	}
	$stat_sum=$row[UnitStat1]+$row[UnitStat2]+$row[UnitStat4]+$row[UnitStat5]+$row[UnitStat6];
	echo "<tr>
		<td class='center'>$row[UnitID]</td>
		<td><a href='unit_info.php?unitID_q=$row[UnitID]'>$row[Model]</a></td>
		<td class='center'>$row[RankPrefix]</td>
		<td class='center'>$row[RankSuffix]</td>
		<td class='center'>$property</td>
		<td>$row[Skill1Name]</td>
		<td>$row[Skill2Name]</td>
		<td>$row[Skill3Name]</td>
		<td class='center'>$row[UnitStat1]</td>
		<td class='center'>$row[UnitStat2]</td>
		<td class='center'>$row[UnitStat3]</td>
		<td class='center'>$row[UnitStat4]</td>
		<td class='center'>$row[UnitStat5]</td>
		<td class='center'>$row[UnitStat6]</td>
		<td class='center'>$stat_sum</td>
		</tr>";
}
?>
</tbody>
</table>
<?
include_once("footer.php");
?>