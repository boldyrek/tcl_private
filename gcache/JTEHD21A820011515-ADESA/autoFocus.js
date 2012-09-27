// Auto-focus form element on page load
// http://perishablepress.com/press/2006/12/04/auto-focus-form-elements-with-javascript/

function formfocus() {
	document.getElementById('userID').focus();
	}
function copyrightYear(){
       
    today=new Date();
    return today.getFullYear();
}
// The following is being implemented within the <body onload="formfocus(), Nifty('div.box','transparent')"> tag of each page
// window.onload = formfocus;