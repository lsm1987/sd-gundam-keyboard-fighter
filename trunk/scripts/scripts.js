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