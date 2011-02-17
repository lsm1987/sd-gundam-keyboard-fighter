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
	$infoQuery="SELECT u.*,
		s1.No AS Skill1No, s1.Description AS Skill1Desc,
		s2.No AS Skill2No, s2.Description AS Skill2Desc,
		s3.No AS Skill3No, s3.Description AS Skill3Desc
		FROM unit AS u
		LEFT JOIN skill AS s1 ON u.Skill1Name=s1.Name
		LEFT JOIN skill AS s2 ON u.Skill2Name=s2.Name
		LEFT JOIN skill AS s3 ON u.Skill3Name=s3.Name
		WHERE UnitID=$unitID";
	//echo $infoQuery;
	$infoResult=mysql_query($infoQuery);
	if(mysql_num_rows($infoResult)){ //검색 결과가 있을 때
		$infoRow = mysql_fetch_array($infoResult);
	}
}
?>

<!--유닛 정보 페이지-->
<h2>유닛 정보</h2>
지정한 유닛의 상세정보를 조회합니다.<br/>
<br/>
<form action="unit_info.php" method="get">
	유닛명: <input type="text" autocomplete="off" id="model_q" name="model_q" class="suggest_input suggest_common" value="<?=$model?>"/>
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

<!--전체 감싸는 테이블-->
<table id="unitInfoTotal">
<tr>
<td style="width:380px; text-align:center;">
	<!--유닛 이미지-->
	<img src="http://img.gundam.netmarble.net/img/unitinfo/unit/<?=$infoRow[UnitID]?>.png" />
	&nbsp;&nbsp;
	<iframe src="http://gundam.netmarble.net/unitinfo/3dunit.asp?unitid=<?=$infoRow[UnitID]?>" id="frameUnit" border="0" frameborder="0" scrolling="no" style="width:200px;height:141px"></iframe>
</td>
<td>

	<!--유닛 기본정보-->
	<table class="unitInfo">
	<tr>
	<th>ID</th>
	<td colspan="4"><?=$infoRow[UnitID]?></td>
	</tr>
	<tr>
	<th>No</th>
	<td colspan="4"><?=$infoRow[UnitNo]?></td>
	</tr>
	<tr>
	<th>이름</th>
	<td colspan="4"><?=$infoRow[Model]?></td>
	</tr>
	<tr>
	<th>랭크</th>
	<td class="rank" colspan="4">
		<?=GetRankFull($infoRow[RankPrefix], $infoRow[RankSuffix])?> rank
		&nbsp;
		<img src="<?=GetCostImageUrl($infoRow[Property], $infoRow[RankPrefix])?>"/></td>
	</tr>
	
	<tr>
	<th>지형</th>
	<th>가변</th>
	<th>수리</th>
	<th>대형</th>
	<th>저격</th>
	</tr>
	<tr>
	<td class="center"><?=$infoRow[Landform]?></td>
	<td class="center"><?=$infoRow[Modification]?></td>
	<td class="center"><?=$infoRow[Repair]?></td>
	<td class="center"><?=$infoRow[Oversize]?></td>
	<td class="center"><?=$infoRow[Sniping]?></td>
	</tr>
	</table>
 
</td>
</tr>
<tr>
<td colspan="2">

	<!--유닛 설명-->
	<table class="unitInfo">
	<tr>
	<th>유닛 설명</th>
	</tr>
	<tr>
	<td><?=$infoRow[Comment]?></td>
	</tr>
	</table>

	<!--무기-->
	<table class="unitInfo">
	<tr>
	<th colspan="3">무기</th>
	</tr>
	<tr>
	<td class="weaponNum">무기1</td>
	<td class="weaponNum">무기2</td>
	<td class="weaponNum">무기3</td>
	</tr>
	<tr>
	<td class="weaponImg"><img src="<?=GetWeaponImageUrl($infoRow[Weapon1No])?>" /></td>
	<td class="weaponImg"><img src="<?=GetWeaponImageUrl($infoRow[Weapon2No])?>" /></td>
	<td class="weaponImg"><img src="<?=GetWeaponImageUrl($infoRow[Weapon3No])?>" /></td>
	</tr>
	<tr>
	<td class="weaponName"><?=$infoRow[Weapon1Name]?></td>
	<td class="weaponName"><?=$infoRow[Weapon2Name]?></td>
	<td class="weaponName"><?=$infoRow[Weapon3Name]?></td>
	</tr>
	</table>
 
<?if($infoRow[Modification]=="on"){ //가변기만 출력 ?>
	<!--가변 후 무기-->
	<table class="unitInfo">
	<tr>
	<th colspan="3">가변 후 무기</th>
	</tr>
	<tr>
	<td class="weaponNum">가변 후 무기1</td>
	<td class="weaponNum">가변 후 무기2</td>
	<td class="weaponNum">가변 후 무기3</td>
	</tr>
	<tr>
	<td class="weaponImg"><?if($infoRow[Weapon4Name]!="Empty"){?><img src="<?=GetWeaponImageUrl($infoRow[Weapon4No])?>" /><?}?></td>
	<td class="weaponImg"><?if($infoRow[Weapon5Name]!="Empty"){?><img src="<?=GetWeaponImageUrl($infoRow[Weapon5No])?>" /><?}?></td>
	<td class="weaponImg"><?if($infoRow[Weapon6Name]!="Empty"){?><img src="<?=GetWeaponImageUrl($infoRow[Weapon6No])?>" /><?}?></td>
	</tr>
	<tr>
	<td class="weaponName"><?if($infoRow[Weapon4Name]!="Empty"){?><?=$infoRow[Weapon4Name]?><?}?></td>
	<td class="weaponName"><?if($infoRow[Weapon5Name]!="Empty"){?><?=$infoRow[Weapon5Name]?><?}?></td>
	<td class="weaponName"><?if($infoRow[Weapon6Name]!="Empty"){?><?=$infoRow[Weapon6Name]?><?}?></td>
	</tr>
	</table>
<?}?>
 
	<!--스킬-->
	<table class="unitInfo">
	<tr>
	<th colspan="3">스킬</th>
	</tr>
	<tr>
	<td class="weaponNum">스킬1</td>
	<td class="weaponNum">스킬2</td>
	<td class="weaponNum">스킬3</td>
	</tr>
	<tr>
	<td class="weaponImg"><img src="<?=GetSkillImageUrl($infoRow[Skill1No])?>" /></td>
	<td class="weaponImg"><img src="<?=GetSkillImageUrl($infoRow[Skill2No])?>" /></td>
	<td class="weaponImg"><img src="<?=GetSkillImageUrl($infoRow[Skill3No])?>" /></td>
	</tr>
	<tr>
	<td class="skillName"><a href="unit_list.php?skillName_q=<?=urlencode($infoRow[Skill1Name])?>"><?=$infoRow[Skill1Name]?></a></td>
	<td class="skillName"><a href="unit_list.php?skillName_q=<?=urlencode($infoRow[Skill2Name])?>"><?=$infoRow[Skill2Name]?></a></td>
	<td class="skillName"><a href="unit_list.php?skillName_q=<?=urlencode($infoRow[Skill3Name])?>"><?=$infoRow[Skill3Name]?></a></td>
	</tr>
	<tr>
	<td class="skillDesc"><?=$infoRow[Skill1Desc]?></td>
	<td class="skillDesc"><?=$infoRow[Skill2Desc]?></td>
	<td class="skillDesc"><?=$infoRow[Skill3Desc]?></td>
	</tr>
	</table>
 
	<!--성능-->
	<table class="unitInfo">
	<tr>
	<th colspan="3">성능</th>
	</tr>
	<tr>
	<th class="leftHeader">유닛HP</th>
	<td style="width:135px"><?=$infoRow[UnitStat1]?></td> 
	<td rowspan="7" style="text-align:center">
	<iframe id="frameGraph"
		src="<?=GetStatGraphUrl($infoRow[UnitStat1],$infoRow[UnitStat2],$infoRow[UnitStat3],$infoRow[UnitStat4],$infoRow[UnitStat5],$infoRow[UnitStat6],$infoRow[Property])?>"
		border="0" frameborder="0" scrolling="no" style="width:210px;height:140px">
	</iframe>
	</td>
	</tr>
	<tr>
	<th>방어력</th>
	<td><?=$infoRow[UnitStat2]?></td> 
	</tr>
	<tr>
	<th>필살기</th>
	<td><?=$infoRow[UnitStat3]?></td> 
	</tr>
	<tr>
	<th>스피드</th>
	<td><?=$infoRow[UnitStat4]?></td> 
	</tr>
	<tr>
	<th>공격력</th>
	<td><?=$infoRow[UnitStat5]?></td> 
	</tr>
	<tr>
	<th>민첩성</th>
	<td><?=$infoRow[UnitStat6]?></td> 
	</tr>
	<tr>
	<th>필제외 총합</th>
	<td><?=$infoRow[UnitStat1]+$infoRow[UnitStat2]+$infoRow[UnitStat3]+$infoRow[UnitStat4]+$infoRow[UnitStat5]?></td> 
	</tr>
	</table>
 
	<!--획득방법-->
	<table class="unitInfo">
	<tr>
	<th colspan="2">획득 방법</th>
	</tr>
	<tr>
	<th class="leftHeader"><a href="capsule_search.php?unitID_q=<?=$unitID?>" target=_self>캡슐머신</a></th>
	<td>
<?
$query="SELECT * FROM capsule
	WHERE Unit1ID=$unitID OR Unit2ID=$unitID OR Unit3ID=$unitID OR Unit4ID=$unitID OR Unit5ID=$unitID OR Unit6ID=$unitID
	ORDER BY No ASC";
$result=mysql_query($query);
if(mysql_num_rows($result)==0){
	echo "없음";
}else{
	$num=0;
	while($row = mysql_fetch_array($result)){
		$num++;
		if($num!=1){ echo ", "; }
		echo "$row[Name]($row[Cost])";
	}
}
?>
	</td> 
	<tr>
	<th><a href="mix_search.php?searchRange_q=result&unitID_q=<?=$unitID?>" target=_self>조합식</a></th>
	<td>
<?
$query="SELECT
	r.Model AS ResultUnitName,
	m.ResultUnitID,
	k.Model AS KeyUnitName,
	m.KeyUnitLevel,
	m.KeyUnitID,
	m1.Model AS MatUnit1Name,
	m.MatUnit1Level,
	m.MatUnit1ID,
	m2.Model AS MatUnit2Name,
	m.MatUnit2Level,
	m.MatUnit2ID,
	m3.Model AS MatUnit3Name,
	m.MatUnit3Level,
	m.MatUnit3ID,
	m4.Model AS MatUnit4Name,
	m.MatUnit4Level,
	m.MatUnit4ID,
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
//최종 쿼리. 획득유닛만
$query_final=$query." WHERE ".$query_result." ".$query_order;

$result=mysql_query($query_final);
$num=0;
while($row = mysql_fetch_array($result)){
$num++;
if($num!=1){ echo "<br/>"; }
echo "[$row[Acquisition]] <a href='unit_info.php?unitID_q=$row[KeyUnitID]'>$row[KeyUnitName]</a>($row[KeyUnitLevel])";
if($row[MatUnit1Name]!=""){ echo " + <a href='unit_info.php?unitID_q=$row[MatUnit1ID]'>$row[MatUnit1Name]</a>($row[MatUnit1Level])"; }
if($row[MatUnit2Name]!=""){ echo " + <a href='unit_info.php?unitID_q=$row[MatUnit2ID]'>$row[MatUnit2Name]</a>($row[MatUnit2Level])"; }
if($row[MatUnit3Name]!=""){ echo " + <a href='unit_info.php?unitID_q=$row[MatUnit3ID]'>$row[MatUnit3Name]</a>($row[MatUnit3Level])"; }
if($row[MatUnit4Name]!=""){ echo " + <a href='unit_info.php?unitID_q=$row[MatUnit4ID]'>$row[MatUnit4Name]</a>($row[MatUnit4Level])"; }
}
if($num==0){ echo "없음"; }
?>
	</td> 
	</tr>
	</table>
 
	<!--기타 정보-->
	<table class="unitInfo">
	<tr>
	<th colspan="2">기타 정보</th>
	</tr>
	<tr>
	<th class="leftHeader"><a href="mix_search.php?searchRange_q=mat&unitID_q=<?=$unitID?>" target=_self>사용 조합식</a></th>
	<td>
<?
//최종 쿼리. 재료유닛만
$query_final=$query." WHERE ".$query_mat." ".$query_order;
$result=mysql_query($query_final);
$num=0;
while($row = mysql_fetch_array($result)){
	$num++;
	if($num!=1){ echo "<br/>"; }

	$searched_start="<span style='color:blue;'>";
	$searched_end="</span>";
	echo "[$row[Acquisition]] ";
	echo "<strong><a href='unit_info.php?unitID_q=$row[ResultUnitID]'>$row[ResultUnitName]</a></strong> = ";

	echo "<a href='unit_info.php?unitID_q=$row[KeyUnitID]'>";
	if($row[KeyUnitID]==$unitID){echo $searched_start;}
	echo "$row[KeyUnitName]";
	if($row[KeyUnitID]==$unitID){echo $searched_end;}
	echo "</a>";
	if($row[KeyUnitID]==$unitID){echo $searched_start;}
	echo "($row[KeyUnitLevel])";
	if($row[KeyUnitID]==$unitID){echo $searched_end;}
	
	for($i=1; $i<=4; $i++){
		$matID=$row["MatUnit".$i."ID"];
		$matName=$row["MatUnit".$i."Name"];
		$matLevel=$row["MatUnit".$i."Level"];
		if($matID==""){
			break;
		}
		
		echo " + ";
		echo "<a href='unit_info.php?unitID_q=$matID'>";
		if($matID==$unitID){echo $searched_start;}
		echo "$matName";
		if($matID==$unitID){echo $searched_end;}
		echo "</a>";
		if($matID==$unitID){echo $searched_start;}
		echo "($matLevel)";
		if($matID==$unitID){echo $searched_end;}
	}
}
if($num==0){ echo "없음"; }
?>
	</td>
	</tr>
	<tr>
	<th class="leftHeader">검색 자료</th>
	<td>
<?
$model_euc = iconv("UTF-8", "EUC-KR", $infoRow[Model]);
?>
		<a href="http://gundam.netmarble.net/Community/Bbs/list.asp?menu=5_2_1&tbName=Tip&searchType=f_subject&searchString=<?=urlencode($model_euc)?>" target="_blank">공홈 공략게시판</a>,
		<a href="http://www.google.co.kr/cse?cx=partner-pub-1667443081589695:6777996725&ie=UTF-8&q=<?=urlencode($infoRow[Model])?>&sa=%EA%B2%80%EC%83%89&siteurl=mirror.enha.kr/wiki/FrontPage" target="_blank">엔하위키</a>
	</td>
	</table>
  
</td>
</tr>
</table>
<!--전체 감싸는 테이블 끝-->
<?
}
include_once("footer.php");
?>