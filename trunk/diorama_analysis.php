<?
include_once("header.php");

//진열장 id 얻기
$rID="";
if( strlen($_GET["rID"])!=0 ){
	$rID=$_GET["rID"];
}
//$rID="43a1e700b6abe121b58cddf6e965d39c";
//$rID="2f0c26b11d605fc2e9dfc9ca114ddd45";

$arrUnit=array();
//진열장 id가 있다면
if($rID!=""){
	//1) 페이지별 유닛목록 가져오기
	$arrUUN=array();
	$url="http://gundam.netmarble.net/myroom/Diorama/mr_UnitListXml.asp?rid=".$rID."&Pnm=";//페이지 번호 아직 안 붙임
	$page=1;//1페이지는 항상 존재
	while(true){
		$html=GetHTML($url.$page);
		if($html==null || strrpos($html,"DOCTYPE")!=FALSE){
			break;
		}
		$xml=(array)simplexml_load_string($html);
		if($xml==FALSE){
			break;
		}
		
		//유닛이 1개일 때
		if(count($xml["xUnitLists"]) == 7){
			$UUN=$xml["xUnitLists"]->xUserUnitNum;
			array_push($arrUUN, $UUN);
		}
		//유닛이 2개 이상일 때
		else{
			foreach($xml["xUnitLists"] as $unit){
				$UUN=$unit->xUserUnitNum;
				array_push($arrUUN, $UUN);
			}
		}
		
		if($page==$xml["xTotpage"]){//마지막 페이지면
			//echo $page."page is end";
			break;
		}
		$page+=1;//다음 페이지
	}
	//print_r($arrUUN);
	
	//2) 유닛별 정보 가져오기
	foreach($arrUUN as $UUN){
		$url="http://gundam.netmarble.net/myroom/Diorama/mr_Unit3DinfoXml.asp?rid=".$rID."&rUUN=".$UUN;
		//echo $url."<br/>";
		$html=GetHTML($url);
		//echo $html."<br/>";
		if($html==null || strrpos($html,"DOCTYPE")!=FALSE){
			continue;
		}
		$xml=(array)simplexml_load_string($html);
		if($xml==FALSE){
			break;
		}
		array_push($arrUnit, (array)$xml["UnitDetail"]);
	}
}


?>
<!--유닛진열장 분석 페이지-->
<h2>유닛진열장 분석(베타)</h2>
유닛진열장의 현황과 유닛을 분석합니다. 유닛이 많을 수록 시간이 오래 걸립니다.<br/>
<br/>
유닛진열장 ID는 마이룸의 유닛진열장 이동 후 주소창의 rid항목을,<br/>
또는 마이룸에서 우클릭->소스보기->유닛진열장을 검색하여 나오는 링크 주소의 rid항목을 복사하시면됩니다.<br/>
예: http://gundam.netmarble.net/MyRoom/Diorama/mr_UnitView.asp?rid=<font color="red">d0c45dbe135abe922e45bb702af8fcbf</font>&menuId=2<br/>
<br/>

<form action="diorama_analysis.php" method="get">
	유닛진열장 ID: <input type="text" autocomplete="off" class="inputtext" style="width:300px"
	id="rID" name="rID" value="<?=$rID?>"/>
	<input type="submit" value="분석" />
</form>
<br/>

<?
if(count($arrUnit)==0){
	echo "유닛진열장 ID가 잘못되었거나 오류가 발생하였습니다.";
}else{
	//결과 있는 경우
?>
<strong>보유 유닛 수: <?=count($arrUnit)?>개</strong><br/>
<br/>

<!--보유 유닛 목록 테이블-->
<table class="example table-autosort">
<thead>
<tr>
	<th rowspan="2" class="table-sortable:numeric">보관함<br/>순서</th>
	<th colspan="4">유닛</th>
	<th colspan="5">전적</th>
	<th colspan="3">격추수</th>
	<th rowspan="2" class="table-sortable:numeric">랭킹</th>
</tr>
<tr>
	<th class="table-sortable:alphanumeric">유닛명</th>
	<th class="table-sortable:alphanumeric">랭크</th>
	<th class="table-sortable:alphanumeric">속성</th>
	<th>레벨</th>
	
	<th class="table-sortable:numeric">승률</th>
	<th class="table-sortable:numeric">전</th>
	<th class="table-sortable:numeric">승</th>
	<th class="table-sortable:numeric">패</th>
	<th class="table-sortable:numeric">무</th>
	
	<th class="table-sortable:numeric">킬</th>
	<th class="table-sortable:numeric">데스</th>
	<th class="table-sortable:numeric">비율</th>
</tr>
<tr>
	<th>필터</th>
	
	<th></th>
	<th>
		<select onchange="Table.filter(this,this)">
			<option value="function(){return true;}">All</option>
			<option value="function(val){var pattern=/^[S]/; return (pattern.test(val));}">S</option>
			<option value="function(val){var pattern=/^[A]/; return (pattern.test(val));}">A</option>
			<option value="function(val){var pattern=/^[B]/; return (pattern.test(val));}">B</option>
			<option value="function(val){var pattern=/^[C]/; return (pattern.test(val));}">C</option>
		</select>
	</th>
	<th>
		<select onchange="Table.filter(this,this)">
			<option value="function(){return true;}">All</option>
			<option value="function(val){return (val=='묵');}">묵</option>
			<option value="function(val){return (val=='찌');}">찌</option>
			<option value="function(val){return (val=='빠');}">빠</option>
		</select>
	</th>
	<th></th>
	
	<th>
		<select onchange="Table.filter(this,this)">
			<option value="function(){return true;}">All</option>
			<option value="function(val){return parseFloat(val.replace(/\%/,''))>50;}">&gt; 50%</option>
		</select>
	</th>
	<th>
		<select onchange="Table.filter(this,this)">
			<option value="function(){return true;}">All</option>
			<option value="function(val){return val>=100;}">&gt;= 100</option>
			<option value="function(val){return val>=500;}">&gt;= 500</option>
			<option value="function(val){return val>=1000;}">&gt;= 1000</option>
		</select>
	</th>
	<th></th>
	<th></th>
	<th></th>
	
	<th></th>
	<th></th>
	<th>
		<select onchange="Table.filter(this,this)">
			<option value="function(){return true;}">All</option>
			<option value="function(val){return parseFloat(val)>=1;}">&gt;= 1.00</option>
			<option value="function(val){return parseFloat(val)>=0.5;}">&gt;= 0.50</option>
		</select>
	</th>
	
	<th>
		<select onchange="Table.filter(this,this)">
			<option value="function(){return true;}">All</option>
			<option value="function(val){return (val)<=100;}">&lt;= 100</option>
		</select>
	</th>
</th>
</thead>
<tbody>
<?
for($i=0; $i<count($arrUnit); $i++){
	$unit=$arrUnit[$i];
	$property=NumToProperty($unit[Utype]);
	$unitrank=(($unit[xUnitRank]==0)?"-":$unit[xUnitRank]);
	$killrate=0;
	if($unit[UnitDead]!=0){
		$killrate=$unit[xUnitKill]/$unit[UnitDead];
	}
	$killrate=number_format(floor($killrate*100)/100,2,'.','');//소수점 2자리 이하 버림

	echo "<tr>
		<td class='center'>".($i+1)."</td>
		
		<td>$unit[ModelNm]</td>
		<td>$unit[UGrade]</td>
		<td class='center'>$property</td>
		<td class='center'>$unit[LevelNm]</td>
		
		<td class='right'>$unit[UnitWinPercent]</td>
		
		<td class='right'>$unit[UnitBattle]</td>
		<td class='right'>$unit[UnitWin]</td>
		<td class='right'>$unit[UnitLost]</td>
		<td class='right'>$unit[UnitTie]</td>
		
		<td class='right'>$unit[xUnitKill]</td>
		<td class='right'>$unit[UnitDead]</td>
		<td class='right'>".$killrate."</td>
		
		<td class='right'>$unitrank</td>
		</tr>";
}
?>
</tbody>
</table>
<?
}
include_once("footer.php");
?>