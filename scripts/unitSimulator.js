//유닛시뮬레이터 페이지에서 사용되는 스크립트

//커스텀 포인트로 인한 스탯 상승률
var arrStatIncrease = new Array();
//0: 일반 기체
arrStatIncrease[0] = new Array();
arrStatIncrease[0][0] = new Array(0.6, 0.9, 2.4, 1.8, 0.9, 1.8);
arrStatIncrease[0][1] = new Array(0.3, 0.9, 2.4, 1.2, 0.9, 1.2);
//1: 정밀 보유기체
arrStatIncrease[1] = new Array();
arrStatIncrease[1][0] = new Array(0.6, 0.9, 2.4, 1.8, 0.9, 1.8);
arrStatIncrease[1][1] = new Array(0.3, 0.9, 3.0, 1.2, 0.6, 2.4);
//2: 철벽 보유기체
arrStatIncrease[2] = new Array();
arrStatIncrease[2][0] = new Array(0.6, 0.9, 2.4, 2.4, 0.9, 2.4);
arrStatIncrease[2][1] = new Array(0.3, 0.6, 2.4, 1.8, 0.9, 1.8);

var statIncreseType = 0;
var statNote = "";

//스탯값
var arrStat = new Array(0, 0, 0, 0, 0, 0); //기본
//투자 커스텀 포인트
var arrPoint = new Array();
arrPoint[0] = new Array(0, 0, 0, 0, 0, 0); //커스텀
arrPoint[1] = new Array(0, 0, 0, 0, 0, 0); //오버커스텀

//속성
var numProperty=0;

//스탯 테이블의 지정한 셀에 값 셋팅
function SetStatValue(row, col, val, fixed){
	$("#simulatorStat tbody tr:eq("+row+") td:eq("+col+")").text(val.toFixed(fixed));
}
function GetStatValue(row, col){
	return parseFloat($("#simulatorStat tbody tr:eq("+row+") td:eq("+col+")").text());
}

//스탯, 속성으로 스탯 그래프 url 구하기
function GetStatGraphUrl(basicStat, customStat, overCustomStat, property){
	var url="http://gundam.netmarble.net/unitinfo/2Dgraph.asp?val=";
	for(i=0; i<6; i++){
		url += basicStat[i] + ",";
	}
	for(i=0; i<6; i++){
		url += (basicStat[i]+customStat[i]) + ",";
	}
	for(i=0; i<6; i++){
		url += (basicStat[i]+customStat[i]+overCustomStat[i]) + ",";
	}
	url += property + ",";
	url += "1";

	return url;
}

//관련 변수 초기화
function SimulatorInit(arrStatBasic, arrSkill, property){
	//기본스탯 저장
	arrStat=arrStatBasic;
	
	//스탯 상승률 타입 검사
	statIncreseType=0;
	statNote = "커스텀 패널티 없음";
	for(i=0; i<3; i++){
		if(arrSkill[i].indexOf("정밀")!=-1){
			statIncreseType=1;
			statNote = "정밀저격 커스텀 패널티 적용";
			break;
		}
		if(arrSkill[i].indexOf("철벽")!=-1){
			statIncreseType=2;
			statNote = "철벽 커스텀 패널티 적용";
			break;
		}		
	}
	$("#simulatorStat #note").text(statNote);
	
	//속성
	numProperty=property;
	
	//갱신
	SimulatorUpdate();
}

//포인트 변경
function simulatorPointChange(type, stat, val){
	//기본커스텀
	var pointCol=2;
	var maxPoint=4;
	//오버커스텀
	if(type==1){
		/*
		if( GetStatValue(6, pointCol)==0 ){
			alert("기본커스텀을 먼저 설정해 주세요.");
			return;
		}
		*/
		pointCol=5;
		maxPoint=9;
	}	

	if(val<0 && (GetStatValue(stat, pointCol)<=0 || GetStatValue(6, pointCol)<=0)){
		alert("0 미만의 포인트를 줄 수 없습니다.");
		return;
	}
	if(val>0 && GetStatValue(6, pointCol)>=maxPoint){
		alert(maxPoint+" 초과의 포인트를 줄 수 없습니다.");
		return;
	}
	if(val>0){
		var statTotal = GetStatValue(stat, 7) + arrStatIncrease[statIncreseType][type][stat];
		//alert(statTotal);
		if (statTotal>30){
			alert("스탯 최대치는 30.0입니다.");
			return;
		}
	}
	
	arrPoint[type][stat] += val;
	SimulatorUpdate();
}

//테이블 업데이트
function SimulatorUpdate(){
	
	var basicStatTotal=0;
	
	var customStat = new Array(0, 0, 0, 0, 0, 0);
	var customStatTotal=0;
	var customPointTotal=0;
	
	var overCustomStat = new Array(0, 0, 0, 0, 0, 0);
	var overCustomStatTotal=0;
	var overCustomPointTotal=0;
	
	var finalStat = new Array(0, 0, 0, 0, 0, 0);
	var finalStatTotal=0;
	
	for(i=0; i<6; i++){
		SetStatValue(i, 0, arrStat[i], 1);
		basicStatTotal+=arrStat[i];
		
		customStat[i] = Math.floor( arrStatIncrease[statIncreseType][0][i]*arrPoint[0][i]*10 ) / 10 ;
		SetStatValue(i, 1, customStat[i], 1);
		customStatTotal += customStat[i];
		SetStatValue(i, 2, arrPoint[0][i], 0);
		customPointTotal += arrPoint[0][i];
		
		overCustomStat[i] = Math.floor( arrStatIncrease[statIncreseType][1][i]*arrPoint[1][i]*10 ) / 10;
		SetStatValue(i, 4, overCustomStat[i], 1);
		overCustomStatTotal += overCustomStat[i];
		SetStatValue(i, 5, arrPoint[1][i], 0);
		overCustomPointTotal += arrPoint[1][i];
		
		finalStat[i] = arrStat[i]+customStat[i]+overCustomStat[i];
		SetStatValue(i, 7, finalStat[i], 1);
		finalStatTotal += finalStat[i];
	}
	
	SetStatValue(6, 0, basicStatTotal, 1);
	
	SetStatValue(6, 1, customStatTotal, 1);
	SetStatValue(6, 2, customPointTotal, 0);
	
	SetStatValue(6, 4, overCustomStatTotal, 1);
	SetStatValue(6, 5, overCustomPointTotal, 0);
	
	SetStatValue(6, 7, finalStatTotal, 1);
	
	$("#simulatorStat #frameGraph").attr("src", GetStatGraphUrl(arrStat, customStat, overCustomStat, numProperty));
}