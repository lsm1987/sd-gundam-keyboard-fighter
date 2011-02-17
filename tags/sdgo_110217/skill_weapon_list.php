<?
include_once("header.php");

//쿼리 만들기////////////////////////////
$query="SELECT * FROM skill ORDER BY No ASC";
//echo $query;
$result=mysql_query($query);
?>

<!--스킬/무기 목록 페이지-->
<h2>스킬 목록</h2>
스킬 이름을 클릭하면 해당 스킬을 보유한 유닛 목록을 검색합니다.<br/>
<br/>

<!--스킬 목록 테이블-->
<table class="example table-autosort">
<thead>
<tr>
	<th class="table-sortable:numeric">번호</th>
	<th>스킬 이미지</th>
	<th class="table-sortable:alphanumeric">이름</th>
	<th>설명</th>
</tr>
</thead>

<!--스킬 목록 테이블 본문-->
<tbody>
<?
while($row = mysql_fetch_array($result)){
	echo "<tr>";
	echo "<td class='center'>$row[No]</td>";
	echo "<td class='center'><img src='".GetSkillImageUrl($row[No])."' /></td>";
	echo "<td><a href='unit_list.php?skillName_q=".urlencode($row[Name])."'>$row[Name]</a></td>";
	echo "<td>$row[Description]</td>";
	echo "</tr>";
}
?>
</tbody>
</table>
<br/>
<br/>

<?
$query="SELECT * FROM weapon ORDER BY No ASC";
//echo $query;
$result=mysql_query($query);
?>

<h2>무기 목록</h2>
목록 보여주기 외의 특별한 기능은 아직 없습니다(....).<br/>
<br/>

<!--무기 목록 테이블-->
<table class="example table-autosort">
<thead>
<tr>
	<th class="table-sortable:numeric">번호</th>
	<th>무기 이미지</th>
	<th class="table-sortable:alphanumeric">이름</th>
</tr>
</thead>

<!--스킬 목록 테이블 본문-->
<tbody>
<?
while($row = mysql_fetch_array($result)){
	echo "<tr>";
	echo "<td class='center'>$row[No]</td>";
	echo "<td class='center'><img src='".GetWeaponImageUrl($row[No])."' /></td>";
	echo "<td>$row[Name]</td>";
	echo "</tr>";
}
?>
</tbody>
</table>
<br/>
<br/>

<?
include_once("footer.php");
?>