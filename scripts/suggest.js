//요소의 절대좌표 구하기
//var curPos = findPos(document.getElementById("q"));
//alert(curPos[0] + " " + curPos[1]);
function findPos(obj) {
 var curleft = curtop = 0;
 if (obj.offsetParent) {
  do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
  } while (obj = obj.offsetParent);
 }
 /*
 var rect = obj.getBoundingClientRect();
 curleft=rect.left;
 curtop=rect.top;
 */
 
 //alert(curleft + " " + curtop);
 return [curleft,curtop];
}

//입력창 바로 아래에 서제스트 위치시키기
function setSuggestPos(inputObj, resultObj){
 var inputPos=findPos(inputObj);
 resultObj.style.left=inputPos[0];
 resultObj.style.top=inputPos[1]+inputObj.offsetHeight;
 //alert( inputObj.offsetHeight );
}