//공통적으로 쓰이는 자바스크립트

function GoMyroom(rid){
 var popWin;
 popWin = window.open("http://gundam.netmarble.net/myRoom/mr_index.asp?rid=" + rid + "&menuId=1" ,"popWin","width=947,height=648,left=300,top=200,resizable=no,scrollbars=no");
 popWin.focus();
 return;
}