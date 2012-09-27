// Row highlight based on the demo provided by Evertjan @ http://bytes.com/forum/thread97429.html
// Row linking based on the demo provided by Imar Spaanjaars @ http://imar.spaanjaars.com/QuickDocId.aspx?quickdoc=312
// Customized for ADESA.COM


// Row Highlight Function - Colors defined in CSS

function h(x) {
	x.className="rowH" 
}
function nA(x) {
	x.className="rowA" 
}
function nB(x) {
	x.className="rowB" 
}
function nW(x) {
	x.className="rowW" 
}
function nTH1(x) {
	x.className="rowTH1" 
}
function nTH2(x) {
	x.className="rowTH2" 
}

// Enables the linking of the entire table row

function DoNav(theUrl) {
	document.location.href = theUrl;
}
