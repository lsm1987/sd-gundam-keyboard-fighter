<?
//자주 쓰이는 PHP함수 모음

//숫자의 앞에 0을 붙여 지정한 자릿수의 문자열로 바꿈
function FillDigit($num, $digit){
	$result=$num;
	for($i=0; $i<($digit-strlen($num)); $i++){
		$result="0".$result;
	}
	return $result;
}

//입력받은 날짜로 한글 요일 구하기
function GetDayOfWeek($date){
 $week = array("일", "월", "화", "수", "목", "금", "토");
 return $week[date("w", $date)];
}

//URL의 html 소스 얻기
//php.ini에서 extension=php_curl.dll 허용
function GetHTML($url){
	$ch = curl_init();
	$timeout = 5; // set to zero for no timeout
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$file_contents = curl_exec($ch);
	curl_close($ch);
	return $file_contents;
}

//캡파관련 공통//////////////////////////////////////////////////////////////
//영어속성을 한글속성으로
function GetPropertyKor($property){
	if($property=="muk"){
		return "묵";
	}else if($property=="chi"){
		return "찌";
	}else{
		return "빠";
	}
}

//랭크 접두 접미 합치기
function GetRankFull($rankPrefix, $rankSuffix){
	if($rankSuffix=='-'){
		return $rankPrefix;
	}else{
		return $rankPrefix.$rankSuffix;
	}
}

//속성을 숫자로
function PropertyToNum($property){
	if($property=="muk"){
		return 0;
	}else if($property=="chi"){
		return 1;
	}else{
		return 2;
	}
}

//숫자를 속성으로
function NumToProperty($num){
	if($num==0){
		return "묵";
	}else if($num==1){
		return "찌";
	}else{
		return "빠";
	}
}

//속성과 랭크로 코스트 이미지 url 구하기
function GetCostImageUrl($property, $rankPrefix){
	$star=PropertyToNum($property);
	
	if($rankPrefix=="C"){
		$star=$star."2";
	}else if($rankPrefix=="B"){
		$star=$star."3";
	}else if($rankPrefix=="A"){
		$star=$star."4";
	}else if($rankPrefix=="S"){
		$star=$star."5";
	}else{
		$star=$star."1";
	}
	return "http://img.gundam.netmarble.net/img/unitinfo/grade/".$star.".gif";
}

//스탯, 속성으로 스탯 그래프 url 구하기
function GetStatGraphUrl($stat1, $stat2, $stat3, $stat4, $stat5, $stat6, $property){
	$param=$stat1.",".$stat2.",".$stat3.",".$stat4.",".$stat5.",".$stat6
		.",0,0,0,0,0,0,0,0,0,0,0,0,"
		.PropertyToNum($property)
		.",1";
	return "http://gundam.netmarble.net/unitinfo/2Dgraph.asp?val=".$param;
}

//스킬 번호로 스킬 이미지 url 구하기
function GetSkillImageUrl($no){
	return "http://img.gundam.netmarble.net/img/unitinfo/unitinfo_skill/icon_u_skill_".$no.".gif";
}

//무기 번호로 무기 이미지 url 구하기
function GetWeaponImageUrl($no){
	return "http://img.gundam.netmarble.net/img/unitinfo/weapon/weapon_".$no.".gif";
}

//쿼리문 관련 공통 함수//////////////////////////////////////////
//함수의 조건문을 만든다.
function GenerateQueryCondition($arrCondition){
	$isFirstCondition=true;	//첫번째 조건인가?
	$query_condition="";
	
	//각 조건들에 대해
	foreach($arrCondition as $condition){
		if($isFirstCondition){
			$isFirstCondition=false;
			$query_condition=$query_condition." WHERE";	//첫 조건이면 앞에 WHERE 붙임
		}else{
			$query_condition=$query_condition." AND";	//두번째 조건부터 앞에 AND 붙임
		}
		$query_condition=$query_condition." ".$condition;
	}
	return $query_condition;
}

//DB관련 함수. DB에 연결되어있다고 가정///////////////////////////////////////

//유닛명으로 유닛ID찾기. 없으면 null리턴
function GetUnitIDByModel($model){
	$query="SELECT UnitID FROM unit WHERE Model='$model'";
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);
	return $row['UnitID'];
}

//유닛ID로 유닛명 찾기
function GetModelByUnitID($unitID){
	$query="SELECT Model FROM unit WHERE UnitID='$unitID'";
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);
	return $row['Model'];
}
?>
