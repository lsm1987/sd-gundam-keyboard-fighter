//공통적으로 쓰이는 자바스크립트

function GoMyroom(rid){
	var popWin;
	popWin = window.open("http://gundam.netmarble.net/myRoom/mr_index.asp?rid=" + rid + "&menuId=1" ,"popWin","width=947,height=648,left=300,top=200,resizable=no,scrollbars=no");
	popWin.focus();
	return;
}

//초 단위의 시간으로부터 00시간 00분 00초의 문자열 얻기
function GetTimeStringFromSec(sec) {
	var hourSec = 60*60;
	var minSec = 60;

	var hour = Math.floor( sec/hourSec );
	sec -= hour*hourSec;
	
	var min = Math.floor( sec/minSec );
	sec -= min*minSec;
	
	var str = "";
	if(hour>0) str += hour+"시간 ";
	if(min>0) str += min+"분 ";
	str += sec+"초";
	return str;
}

function ConvLevelNameToNum(levelName) {
	if (levelName == "루키") {
		return 1;
	} else if (levelName == "솔져") {
		return 2;
	} else if (levelName == "베테랑") {
		return 3;
	} else if (levelName == "에이스") {
		return 4;
	} else if (levelName == "커스텀1") {
		return 5;
	} else if (levelName == "커스텀2") {
		return 6;
	} else if (levelName == "커스텀3") {
		return 7;
	} else if (levelName == "커스텀4") {
		return 8;
	} else if (levelName == "오버커스텀1") {
		return 9;
	} else if (levelName == "오버커스텀2") {
		return 10;
	} else if (levelName == "오버커스텀3") {
		return 11;
	} else if (levelName == "오버커스텀4") {
		return 12;
	} else if (levelName == "오버커스텀5") {
		return 13;
	} else if (levelName == "오버커스텀6") {
		return 14;
	} else if (levelName == "오버커스텀Ex") {
		return 15;
	}
	
	return 0;
}

function CompLevelName(a,b) {
	var na = ConvLevelNameToNum(a);
	var nb = ConvLevelNameToNum(b);
	return (na==nb)?0:(na<nb)?-1:1;
}