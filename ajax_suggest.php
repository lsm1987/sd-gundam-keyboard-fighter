<?
//부모컨트롤의 ID
$q=$_GET["prefix"]."_q";
$sug=$_GET["prefix"]."_sug";
$frame=$_GET["prefix"]."_frame";
//$form=$_GET["prefix"]."_form";
$url="ajax_query_".$_GET["prefix"].".php"; //쿼리 보낼 페이지
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
#querylist_border {
 width:100%;
 border:gray solid 1px;
 background:#ffffff;
 display:none;
}

.highlight {
 color:#EB550C;
}

.queryItem {
 padding:4px 0 0 6px;
 height:18px;
 cursor:pointer;
 font-size:12px;
 font-family:Dotum,돋움;
}

.queryLine {
 background-color:lightgray;
 height:1px;
 line-height:1px;
 font-size:1px;
}
</style>
<script type="text/javascript">
//전역변수/////////////////////////////////////////////////////////
var req; //쿼리 요청
var totalKeywordCount = 0; //요청 후 돌아온 응답의 서제스트 수
var curCursorPos = -1;
var buildListComplete = true; //추천목록이 완성되었는가?
var virtualValue = ""; //서제스트 중 위아래 움직여서 선택한 값?
var oldValue = ""; //바로 이전 키입력때 검색어
var revertQuery = ""; //XML과 함께 돌아온 검색어. 보낸것과 다를 수 있음
var qObj = getObject("<?=$q?>","parent"); //검색어 입력창

var SUG_URL = "<?=$url?>"; //서제스트 XML 요청할 곳
var isFirstBuild = false; //최초값이 true이면 값이 들어간 상태에서 페이지 불러올때 서제스트 안뜨도록
var onMouseOverColor = '#e0e8f5';
var onMouseOutColor  = '#ffffff';

//주요 함수////////////////////////////////////////////////
//초기화
function init() {
 if (navigator.userAgent.indexOf("MSIE") >= 0) {
	}else{
  var q_list = getObject("querylist_border","");
  q_list.style.width="248px"; //해결방법 모르겠음 ㄱ-
 }
 if(qObj.value != ""){ //검색어 값이 이미 들어와 있으면
  isFirstBuild=true;
 }
 qObj.onkeydown = moveFocusToSelect; //검색어 입력창에서 키입력 이벤트 발생시
 //qObj.onblur = moveFocusFromSelect; //검색어 입력창에서 포커스 잃을시
 checkChangeValue();
}

//검색어 입력창 갱신 체크
function checkChangeValue() {
	var newValue = qObj.value;
	if(newValue=="") { //새로 완성된 문자열이 빈문자열이면, 즉 입력창을 비웠다면
		oldValue = "";
		oldUserKeyword = "";
		setQueryDisplayOff();
	}
 //새 문자열이면
	if(newValue!=oldValue && newValue!=virtualValue){
  setQueryValue();
 }
	setTimeout("checkChangeValue()",100); //0.1초마다 재확인
}

//현재 검색어로 쿼리 실행
function setQueryValue() {
	var q = qObj.value;
	if(q == " "){
  return;
 }
	buildListComplete = false;
	loadXMLDoc(q);
	userKeyword = q;
	oldValue = q;
}

//XML결과 얻기
function loadXMLDoc(_str) {
	//var str = encodeURI(_str);
 //var str = escape(_str);
 var str = encodeURIComponent(_str);
	var url = SUG_URL +"?q="+ str;
	if (window.XMLHttpRequest) { //표준
		req = new XMLHttpRequest();
		try {
			req.onreadystatechange = processReqChange; //요청 상태 변화시 실행할 함수
			req.open("GET", url, true);
			req.send(null);
		} catch (e) {
			//alert(e);
		}
	} else if (window.ActiveXObject) {
		req = new ActiveXObject("Microsoft.XMLHTTP"); //IE용
		if (req) {
			req.onreadystatechange = processReqChange;
			req.open("GET", url, true);
			req.send();
		}
	}
}

//XML요청 후 상태 변화시마다 실행되는 함수
function processReqChange() {
	if (req.readyState == 4) { // only if req shows "loaded"
		if (req.status == 200) { // only if "OK"
			clearQueryList(); //기존 서제스트 목록 비움
			buildQueryList(); //서제스트 목록 생성
		} else {
			//alert(req.statusText);
		}
	}
}

//서제스트 목록 생성
function buildQueryList() {
 var q_list = getObject("querylist","");
 var xml=parse_xml(req);
 var items=xml.getElementsByTagName('items')[0];
 var item=items.getElementsByTagName("item");
 var ritem=items.getElementsByTagName("r_item");
 var _revertQuery = items.getElementsByTagName("rq");
 if(_revertQuery.length > 0){ //돌아온 키워드가 있으면
  revertQuery = _revertQuery[0].firstChild.nodeValue;
 }
 
 totalKeywordCount = item.length+ritem.length;
 if(totalKeywordCount < 1) {
		setQueryDisplayOff();
		return;
 }
 
 var str = "";
 //검색어로 시작단어
	for (var i=0;i<item.length;i++) {
  highlightVal = highlightCheck(item[i].firstChild.nodeValue);
  str += "<div class='queryItem' id='f"+i+"' onclick='onClickKeyword("+i+")' onmouseover='onMouseOverKeyword("+i+")' onmouseout='onMouseOutKeyword("+i+")'>" + highlightVal + "</div>";
	}
 //검색어로 시작단어가 있고 검색어 포함단어가 있으면 구분선 출력
 if(item.length>0 && ritem.length>0){
  str += "<div class='queryLine'><!-- --></div>";
 }
 //검색어 포함단어
 for (var i=0;i<ritem.length;i++) {
  highlightVal = highlightCheck2(ritem[i].firstChild.nodeValue);
  str += "<div class='queryItem' id='f"+(i+item.length)+"' onclick='onClickKeyword("+(i+item.length)+")' onmouseover='onMouseOverKeyword("+(i+item.length)+")' onmouseout='onMouseOutKeyword("+(i+item.length)+")'>" + highlightVal + "</div>";
	}
 
 if(!isFirstBuild) setQueryDisplayOn(); // 최초 검색시에는 화면 보여주지 않는다.
	isFirstBuild = false;
 //setQueryDisplayOn();
 q_list.innerHTML = str;
 curCursorPos = -1;
	buildListComplete = true;
}

//모든 키워드들을 마우스 아웃상태로
function clearCursorPos() {
	for(var i=0; i<totalKeywordCount; i++){
  getObject("f"+i,"").style.backgroundColor=onMouseOutColor;
 }
}

//마우스로 클릭시
function onClickKeyword(curCursorPos) {
 /*
 //클릭시 서브밋
	qObj.value = getInnerText(getObject("f"+curCursorPos,""));
	setQueryDisplayOff();
 formObj = getObject("<?=$form?>", "parent");
	if(formObj){
  formObj.submit();
 }
 */
 //curCursorPos = curCursorPos2;
 //alert("123");
 var pos = curCursorPos;
 qObj.value = getInnerText(getObject("f"+pos, ""));
 virtualValue = qObj.value;
 setQueryDisplayOff();
}

//마우스 오버시
function onMouseOverKeyword(curSorNum){
	clearCursorPos();
	curCursorPos = curSorNum;
	getObject("f"+curSorNum,"").style.backgroundColor = onMouseOverColor;
}

//마우스 아웃시
function onMouseOutKeyword(curSorNum){
	curCursorPos = curSorNum;
	if(getObject("f"+curCursorPos,"")) getObject("f"+curCursorPos,"").style.backgroundColor = onMouseOutColor;
}

//검색어 입력창에 키입력 발생시
function moveFocusToSelect(evt) {
 if("undefined"==typeof(evt)){
  evt = parent.window.event;
 } 
 var key = evt.keyCode;
 if(key==38) { //위로
  clearCursorPos();
  if(curCursorPos==-1 || curCursorPos==0) { //더 올라갈 곳이 없으면
			qObj.value = userKeyword; //입력창 원복
   curCursorPos=-1;
			return;
		} else {
			curCursorPos = curCursorPos - 1;
  }
  setTimeout("setCursorPos()",10);
 }else if(key==40) { //아래로
  if(curCursorPos!=(totalKeywordCount-1)) { //더 내려갈 곳이 있다면
			onMouseOutKeyword(curCursorPos); //현재것 해제
			curCursorPos = curCursorPos + 1;
  }
  setTimeout("setCursorPos()",10);
 }else if(key==9) { //탭
  setQueryDisplayOff();
 }
}

//지정한 번호에 커서 설정
function setCursorPos(num) {
	if(!buildListComplete) setTimeout("setCursorPos()",10); // 이벤트가 두번발생되는 경우
	
	var pos = curCursorPos;
	if(num==0) pos = 0;
	if(getObject("f"+pos,"")) {
		qObj.value = getInnerText(getObject("f"+pos, ""));
		getObject("f"+pos,"").style.backgroundColor=onMouseOverColor;
		virtualValue = qObj.value;
	}
	return;
}

//보조 함수////////////////////////////////////////
//서제스트 숨김
function setQueryDisplayOff() {
	getObject("querylist_border","").style.display = "none";
	getObject("<?=$frame?>","parent").style.display = "none";
	curCursorPos = -1;
}

//서제스트 보임
function setQueryDisplayOn() {
	if(totalKeywordCount > 0) {
		setResizeLayer();
		getObject("querylist_border","").style.display = "block";
		getObject("<?=$frame?>","parent").style.display = "block";
	}
}

//서제스트 목록 비움
function clearQueryList() {
	var q_list = getObject("querylist","");
	q_list.innerHTML = "";
}

//서제스트 크기 조정
//여기도 해결못함; 하드코딩 ㄱ-
function setResizeLayer() {
 var itemHeight = 0;
 var footerHeight=0;
 if(navigator.userAgent.indexOf("MSIE") >= 0){ //ie라면
  itemHeight=18;
  footerHeight=3;
 }else{
  itemHeight=22;
  footerHeight=12;
 }
 var q_list = getObject("querylist","");
 q_list.style.height = itemHeight * totalKeywordCount + 1;
 getObject("<?=$frame?>","parent").style.height = itemHeight*totalKeywordCount+footerHeight;
}

//서제스트 아이템 하이라이트 처리
function highlightCheck(str) {
	var rtStr = str;
	var _str = str.substring(0,revertQuery.length).toLowerCase();
	if(_str==revertQuery.toLowerCase()){
		rtStr = "<span class='highlight'>" + str.substring(0,revertQuery.length) + "</span>" + str.substring(revertQuery.length,str.length);
 }
	return rtStr;
}
function highlightCheck2(str) {
 var ori=str.substr(str.toLowerCase().indexOf(revertQuery.toLowerCase()), revertQuery.length); //서제스트 중 원래 검색어 해당부분
 var reg=new RegExp(revertQuery, "i"); //대소문자 무시, 처음 한번만 치환
	return str.replace(reg, "<span class='highlight'>"+ori+"</span>");
}

//기타 함수//////////////////////////////
function getObject(objectId,nodeObject) { // 객체 얻기 : checkW3C DOM, then MSIE 4, then NN 4.
	var doc = document;
	if(nodeObject!=""){
  doc = eval(nodeObject + ".document");
 }
	if(doc.getElementById && doc.getElementById(objectId)){
  return doc.getElementById(objectId); // 대부분의 브라우저 
 }else if (doc.all && doc.all(objectId)){
  return doc.all(objectId); // IE4와 5.0 
 }else if (doc.layers && doc.layers[objectId]){
  return doc.layers[objectId];  // Netscape 4.x 
 }else{
  return false;
 }
}

//req에서 xml얻기
// var xml = parse_xml(req); 식으로 사용하면 된다.
function parse_xml(_req) {
 try {
  return (new DOMParser()).parseFromString(_req.responseText, "text/xml");
 }
 catch(e) {
  return _req.responseXML;
 }
}

//FF에서 innerText가 안되므로 우회 함수
function getInnerText(obj){
  if (navigator.userAgent.indexOf("Firefox")>-1) {
   var ret = obj.innerHTML;
   ret = ret.replace(/&nbsp;/ig," ");
   ret = ret.replace(/<br>/ig,"\n");
   ret = ret.replace(/<br[^>]+>/ig,"\n");
   ret = ret.replace(/<[^>]+>/g,"");
   return ret;
  } else {
   return obj.innerText;
  }
}

</script>
</head>
<body style="margin:0;padding:0" onload="init()"> 
<div id="querylist_border">
 <div id="querylist"></div>
</div>
</body>
</html>