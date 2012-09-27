var whitespace = " \t\n\r\f\v";

var defaultEmptyOK = false;
var maxPort = 65535;
var minPort = 0;
var YMKMD=0;
var m_sURL = new String();
 var YearID ="";
 var RegionsID = "";
 var SelectedYear1="";
var timer = "";
var timerModel = "";
var timerTrim = "";

/************************
DATA VALIDATION ROUTINES
*************************/
function isIPPort(s){
    if (isInteger(s)){
        var iPort = parseInt(s);
        if (iPort >= minPort && iPort <= maxPort){
            return true;
        }
    }
    return false;
}

function isInteger (s)
{   var i;
    if (isEmpty(s)) 
       if (isInteger.arguments.length == 1) return defaultEmptyOK;
       else return (isInteger.arguments[1] == true);
    for (i = 0; i < s.length; i++)
    {   
        var c = s.charAt(i);
        if (!isDigit(c)) return false;
    }
    return true;
}

function isEmpty(s)
{   return ((s == null) || (s.length == 0));
}

function isDigit (c)
{   return ((c >= "0") && (c <= "9"));
}

function isNotJustWhitespace(s){
    return stripWhitespace(s).length != 0;
}
function stripWhitespace (s)
{   
    return stripCharsInBag (s, whitespace);
}
function stripCharsInBag (s, bag)

{   var i;
    var returnString = "";

    for (i = 0; i < s.length; i++)
    {   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }

    return returnString;
}

function isNonnegativeInteger (s)
{   var secondArg = defaultEmptyOK;

    if (isNonnegativeInteger.arguments.length > 1)
        secondArg = isNonnegativeInteger.arguments[1];

    return (isSignedInteger(s, secondArg)
         && ( (isEmpty(s) && secondArg)  || (parseInt (s) >= 0) ) );
}

function isInteger (s)

{   var i;

    if (isEmpty(s)) 
       if (isInteger.arguments.length == 1) return defaultEmptyOK;
       else return (isInteger.arguments[1] == true);

    for (i = 0; i < s.length; i++)
    {   
        var c = s.charAt(i);

        if (!isDigit(c)) return false;
    }

    return true;
}

function isSignedInteger (s)

{   if (isEmpty(s)) 
       if (isSignedInteger.arguments.length == 1) return defaultEmptyOK;
       else return (isSignedInteger.arguments[1] == true);

    else {
        var startPos = 0;
        var secondArg = defaultEmptyOK;

        if (isSignedInteger.arguments.length > 1)
            secondArg = isSignedInteger.arguments[1];

        // skip leading + or -
        if ( (s.charAt(0) == "-") || (s.charAt(0) == "+") )
           startPos = 1;    
        return (isInteger(s.substring(startPos, s.length), secondArg))
    }
}

function isPositiveInteger (s)
{   var secondArg = defaultEmptyOK;

    if (isPositiveInteger.arguments.length > 1)
        secondArg = isPositiveInteger.arguments[1];

    return (isSignedInteger(s, secondArg)
         && ( (isEmpty(s) && secondArg)  || (parseInt (s) > 0) ) );
}

function isDateMDY (dateEntry) {

  
  var datevalues=dateEntry.split("/");
  //if there is a leading 0 before day or month, strip it.
  if(datevalues.length != 3)return false;
  
  if(datevalues[0].charAt(0)=="0")datevalues[0]=datevalues[0].charAt(1);
  if(datevalues[1].charAt(0)=="0")datevalues[1]=datevalues[1].charAt(1);
  
  if(   !isPositiveInteger(datevalues[0]) ||
        !isPositiveInteger(datevalues[1]) ||
        !isPositiveInteger(datevalues[2])){return false;}
  
  if ( !isDate(datevalues[2],datevalues[0],datevalues[1]) ){
    return false;
  }
  return true;

}


var daysInMonth = new Array();
daysInMonth[1] = 31;
daysInMonth[2] = 29;   // must programmatically check this
daysInMonth[3] = 31;
daysInMonth[4] = 30;
daysInMonth[5] = 31;
daysInMonth[6] = 30;
daysInMonth[7] = 31;
daysInMonth[8] = 31;
daysInMonth[9] = 30;
daysInMonth[10] = 31;
daysInMonth[11] = 30;
daysInMonth[12] = 31;

function isDate (year, month, day)
{   // catch invalid years (not 2- or 4-digit) and invalid months and days.

//alert("isMonth: " + isMonth(month, false));

    if (! (isYear(year, false) && isMonth(month, false) && isDay(day, false))) return false;

    var intYear = parseInt(year);
    var intMonth = parseInt(month);
    var intDay = parseInt(day);

    if (intDay > daysInMonth[intMonth]) return false; 

    if ((intMonth == 2) && (intDay > daysInFebruary(intYear))) return false;

    return true;
}

function isYear (s)
{   if (isEmpty(s)) 
       if (isYear.arguments.length == 1) return defaultEmptyOK;
       else return (isYear.arguments[1] == true);
    if (!isNonnegativeInteger(s)) return false;
    return (s.length == 4);
}

function addSeparatorsNF(nStr, inD, outD, sep) {
    nStr += '';
    var dpos = nStr.indexOf(inD);
    var nStrEnd = '';
    if (dpos != -1) {
        nStrEnd = outD + nStr.substring(dpos + 1, nStr.length);
        nStr = nStr.substring(0, dpos);
    }
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(nStr)) {
        nStr = nStr.replace(rgx, '$1' + sep + '$2');
    }
    return nStr + nStrEnd;
}     


function isIntegerInRange (s, a, b){

	if (isEmpty(s)){
		if (isIntegerInRange.arguments.length == 1) return defaultEmptyOK;
	} else { 
		return (isIntegerInRange.arguments[1] == true);
	}

   if (!isInteger(s, false)){ 
	 	return false;
	}

   var num = parseInt (s);

   return ((num >= a) && (num <= b));
	
}

function isMonth (s){   
	if (isEmpty(s)) {
	      if (isMonth.arguments.length == 1){
			 	return defaultEmptyOK;
	      } else {
				return (isMonth.arguments[1] == true);
			}
	}
   return isIntegerInRange (s, 1, 12);
}


function isDay (s)
{   if (isEmpty(s)) 
       if (isDay.arguments.length == 1) return defaultEmptyOK;
       else return (isDay.arguments[1] == true);
    return isIntegerInRange (s, 1, 31);
}


function daysInFebruary (year)
{   // February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (  ((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0) ) ) ? 29 : 28 );
}

function isTime(sVal){
    s = stripWhitespace(sVal);
    switch (s.substring(s.length - 2, s.length).toLowerCase()){
        case "am": case "pm":
            break;
        default:
            return false;
    }
    var colPos = s.indexOf(":");
    if (colPos == -1) return false;
    var sPart = s.substring(0, colPos);
    if (! isPositiveInteger(sPart)) return false;
    var nPart = parseInt(sPart);
    if (nPart > 12 || nPart < 1) return false;
    sPart = s.substring(colPos + 1, colPos + 3);
    if (! isNonnegativeInteger(sPart)) return false;
    nPart = parseInt(sPart);
    if (nPart > 59) return false;
    if (s.length == colPos + 6 && s.substring(colPos + 4, colPos + 4) != " ") return false;
    return true;
}

function formatCurrency(intVal){
    if (intVal < 0) return "(negative value)";
    var sVal = intVal.toString();
    var sLength = sVal.length;
    var sReturn = "";
    for (i = 1; (i*3) < sLength; i++){
        sReturn = sReturn + "," + sVal.substr(sLength - (3 * i), 3);
    }
    var iPart = sLength % 3;
    if (iPart == 0) iPart = 3;
    sReturn = sVal.substr(0, iPart) + sReturn;
    return "$" + sReturn;
}




















function MonitorText(event, cmd){
    if (event.keyCode == 13) {
        document.getElementById(cmd).click();
        event.keyCode = 0;
    }
}



function CheckSendKey(e){
    if (e.keyCode == 13) {
        document.forms[1].submit();
        e.keyCode = 0;
    }
}


function sendUser(frm, varname){
    oUser = document.getElementById(varname);
    oHidden = document.getElementById( 'ctUser' );
    oUser.value = oHidden.value;
    document.forms[frm].submit();
}



/*CALENDAR*/
var vAuctions = new Array();
function ac(ord, a, b, c, d, e, f, g, h, i){
    var vAuction = new Array();
    vAuction[0] = a;    //EventID
    vAuction[1] = b;    //Date
    vAuction[2] = c;    //City
    vAuction[3] = d;    //Auction
    vAuction[4] = e;    //Opento
    vAuction[5] = f;    //OnlineLanes
    vAuction[6] = g;    //CarsConsigned
    vAuction[7] = h;    //EventName
    vAuction[8] = i;    //Consignors
    vAuctions[ord] = vAuction;
}

function ShowAuction(AuctionEvent){			
	var sEvent, sLocation, sDate, sOpenTo, sOnline, sConsigned, sConsignors, dvAuction;
	
	sEvent = vAuctions[AuctionEvent][7];
	sLocation = vAuctions[AuctionEvent][3];
	sDate = vAuctions[AuctionEvent][1];
	sOpenTo = vAuctions[AuctionEvent][4];
	sOnline = vAuctions[AuctionEvent][5];
	sConsigned = vAuctions[AuctionEvent][6];
	sConsignors = vAuctions[AuctionEvent][8];
	
	dvAuction = document.getElementById( 'DetailsPreview' );
	
	var sHTML = "";
	sHTML = sHTML + "<b>" + sLocation + "</b><br />";
	sHTML = sHTML + sDate + "<br /><br />";
	sHTML = sHTML + sEvent + "<br /><br />";
	if (sConsignors.length > 0){
	sHTML = sHTML + "<b>Featured Consignors:</b><br />";
	sHTML = sHTML + sConsignors + "<br /><br />";}
	switch (sOpenTo.toLowerCase()){
	    case "factory":
	        sHTML = sHTML + "<font color=red><b>Closed Factory</b></font><br />";
	        break;
	    case "public":
	        sHTML = sHTML + "<font color=darkgreen><b>Public Sale</b></font><br />";
	        break;
	    default:
	        sHTML = sHTML + "<font color=blue><b>Dealer Sale</b></font><br />";       
	}
	if (sOnline > 0) sHTML = sHTML + "<b>" + sOnline + " Lane(s) Online</b><br />";
	if (sConsigned > 0) sHTML = sHTML + sConsigned + " cars already consigned<br />";
	sHTML = sHTML + "</body></html>";
	
	dvAuction.innerHTML = sHTML;
}

function NavEvent(){
    window.alert('Not implemented. Will open inventory listing for online event.');
    return false;
}









/*CAR DETAILS*/
function NextImage(){
    var vIndex;
    vIndex = vImageIndex + 1;
    if (vIndex == vImages.length) {vIndex = 0;}
    SetImage(vIndex);
}
function PrevImage(){
    var vIndex;
    vIndex = vImageIndex - 1;
    if (vIndex == -1) {vIndex = vImages.length - 1;}
    SetImage(vIndex);
}
function SetImage(vIndex){
    vImageIndex = vIndex;
    var vSlide = document.getElementById("imgSlide");
    var vSlideLarge = $('#vehImageZoom');
    //var vSlideLargePath = vImages[vImageIndex].substring(vImages[vImageIndex].lastIndexOf('/')+1);
    var vSlideLargePath = vImagesBig[vImageIndex];
    if (vSlide.tagName == 'SPAN'){
        vSlide.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'" + vImages[vImageIndex] + "\', sizingMethod=\'scale\')"
        //window.alert(vSlide.onclick);
    }else{
        //vSlide.src = 'http://www.auctionpipeline.com/' + vImages[vImageIndex];
        vSlide.src = vImages[vImageIndex];
        //vSlideLarge.attr('image','http://www.auctionpipeline.com/img/veh/Large/' + vSlideLargePath);
        vSlideLarge.attr('image', vSlideLargePath);
        $(vSlide).attr('imageIndex',vImageIndex);
    }
    if(tgImageZoomOn) {
        $('#vehImageZoom').unbind().jqzoom(zoomOptions);
    }
}
function BigPic(vIndex){
    var imgBigPic = document.getElementById("imgBigPicture");
    var divBack = document.getElementById("dvBackToReport");
    var tblReport = document.getElementById("tblReport");
    var dvCert = document.getElementById("divCert");
    var imCert = document.getElementById("imgCert");
    
    tblReport.style.display = "none";
    divBack.style.display = "block";
    imgBigPic.style.display = "block";
    imgBigPic.src = vImagesBig[vIndex];
    //imgBigPic.src = 'http://www.auctionpipeline.com/' + vImagesBig[vIndex];
    
    if (vIndex == vCertIndex){
        imCert.src = vCertImage;
        dvCert.style.display = "block";
        correctPNG();
    }else{
        dvCert.style.display = "none";
    }
}
function BackToVehicleReport(){
    var imgBigPic = document.getElementById("imgBigPicture");
    var divBack = document.getElementById("dvBackToReport");
    var tblReport = document.getElementById("tblReport");
    var dvCert = document.getElementById("divCert");
    
    tblReport.style.display = "block";
    divBack.style.display = "none";
    imgBigPic.style.display = "none";
    dvCert.style.display = "none";
}

function CheckReport(){
    var iRpt = parseInt(document.getElementById("selReport").value);
    var dvDates = document.getElementById("divDates");
    if (iRpt <= 1){
        dvDates.style.display = 'block';
    }else{
        dvDates.style.display = 'none';
    }
}

function FetchReport(){
    var frm = document.forms[1];
    var iRpt = parseInt(document.getElementById("selReport").value);
    var iAuc = parseInt(document.getElementById("selAuction").value);  
    var sBeginDate = document.getElementById("BeginDate").value;
    var sEndDate = document.getElementById("EndDate").value;
    
    if (iRpt == 1){
        if (!isDateMDY(sBeginDate)){
            window.alert('Begin Date is not a valid date.');
            return;
        }
        if (!isDateMDY(sEndDate)){
            window.alert('End Date is not a valid date.');
            return;
        }
    }
    
    frm.action = vAuc[iAuc][iRpt];
    frm.submit();
}

vDlr = new Array();


var vAuc = new Array();
function inAuc(ord, URL1, URL2, URL3, URL4, URL5){
    var vA = new Array();
    vA[0] = URL1;
    vA[1] = URL2;
    vA[2] = URL3;
    vA[3] = URL4;
    vA[4] = URL5;
    vAuc[ord] = vA;
}

function equip(ord, key, desc){
    var vItem = new Array();
    vItem[0] = key;
    vItem[1] = desc;
    vEquip[ord] = vItem;
}


var vEquip = new Array();
equip(1, '4W', '4 Wheel Drive');
equip(2, 'AW', 'All Wheel Drive');
equip(3, 'A3', '3 Speed Automatic Transmission');
equip(4, '3S', '3 Speed Manual Transmission');
equip(5, 'A4', '4 Speed Automatic');
equip(6, '4S', '4 Speed Manual Transmission');
equip(7, 'A5', '5 Speed Automatic');
equip(8, '5S', '5 Speed Manual Transmission');
equip(9, 'A6', '6 Speed Automatic Transmission');
equip(10, '6S', '6 Speed Manual Transmission');
equip(11, 'AT', 'Automatic Transmission');
equip(12, '0D', '10 Cylinder Diesel');
equip(13, '0F', '10 Cylinder Flex Fuel');
equip(14, '0G', '10 Cylinder Gas');
equip(15, '2G', '2-Cylinder Gas');
equip(16, '3D', '3 Cylinder Diesel');
equip(17, '3F', '3 Cylinder Flex Fuel');
equip(18, '3G', '3-Cylinder Gas');
equip(19, '4D', '4-Cylinder Diesel');
equip(20, '4F', '4-Cylinder Flex Fuel');
equip(21, '4G', '4-Cylinder Gas');
equip(22, '5D', '5-Cylinder Diesel');
equip(23, '5F', '5-Cylinder Flex Fuel');
equip(24, '5G', '5-Cylinder Gas');
equip(25, '6D', '6-Cylinder Diesel');
equip(26, '6F', '6-Cylinder Flex Fuel');
equip(27, '6G', '6-Cylinder Gas');
equip(28, '8D', '8-Cylinder Diesel');
equip(29, '8F', '8-Cylinder Flex Fuel');
equip(30, '8G', '8-Cylinder Gas');
equip(31, 'AC', 'Air Conditioning');
equip(32, 'RA', 'Rear AC');
equip(33, 'RW', 'Rear Wiper');
equip(34, 'AL', 'ABS Brakes');
equip(35, 'WA', 'Alloy Wheels');
equip(36, 'CD', 'CD Player');
equip(37, 'SR', 'Sun Roof');
equip(38, 'FI', 'Fuel Injection');
equip(39, 'LU', 'Leather Upholstry');
equip(40, 'TB', 'Turbo Charged');

function equip(ord, key, desc){
    var vItem = new Array();
    vItem[0] = key;
    vItem[1] = desc;
    vEquip[ord] = vItem;
}

function ShowEquip(event){
    var aText;
    if (event.srcElement){aText = event.srcElement.innerHTML;}else{aText = event.target.innerHTML;}
    var sHTML = "<b>FEATURES</b><br><table border=0 cellspacing=0 cellpadding=0>";
    for(i=1;i<=40;i++){
        var sKey = vEquip[i][0];
        if (aText.indexOf(sKey, 0) > -1){
            sHTML = sHTML + "<tr><td><b>" + sKey + "</b></td><td nowrap>&nbsp;" + vEquip[i][1] + "</td></tr>";
        }
    }
    sHTML = sHTML + "</table>"
    ShowTip(event, sHTML);
}

var sSty = '<b>Body Style</b><br>';
sty(1, '2D', '2 Door');
sty(2, '4D', '4 Door');
sty(3, 'ATV', 'ATV');
sty(4, 'Boat', 'Boat');
sty(5, 'BUS', 'Bus');
sty(6, 'CV', 'Convertible');
sty(7, 'EQ', 'Equipment');
sty(8, 'JET', 'Jet Ski');
sty(9, 'MC', 'Motorcycle');
sty(10, 'PU', 'Pickup');
sty(11, 'RV', 'RV');
sty(12, 'SNOW', 'Snowmobile');
sty(13, 'SUV', 'SUV');
sty(14, 'TR', 'Truck');
sty(15, 'VAN', 'Van');
sty(16, 'WAG', 'Wagon');

function sty(ord, key, desc){
    sSty = sSty + '<b>' + key + '</b> - ' + desc + '<br>';
}

var sInt = '<b>Interior</b><br>';
sin(1, 'C', 'Cloth');
sin(1, 'L', 'Leather');
sin(1, 'V', 'Vinyl');
sin(1, 'O', 'Other');
function sin(ord, key, desc){
    sInt = sInt + '<b>' + key + '</b> - ' + desc + '<br>';
}

function ShowOdo(event){ShowTip(event, "<b>Odometer Status</b><br><b>E</b> - Exempt<br><b>A</b> - Actual<br><b>O</b> - Odometer Descrepency");}
function ShowI(event){ShowTip(event, sInt);}
function ShowSty(event){ShowTip(event, sSty);}
function ShowOLR(event){ShowTip(event, "Offered via Pipeline Simulcast");}
function ShowManCA(event){ShowTip(event, "Offered on Manheim CyberAuction");}
function ShowManCL(event){ShowTip(event, "Offered on Manheim CyberLot");}
function Sticker(event){ShowTip(event, "<b>Print Window Sticker</b><br>Available for Ford, Lincoln, and Mercury vehicles 4 years old and newer");}
function WLEmail(event){ShowTip(event, "Email me when this<br>vehicle changes");}
function WLDel(event){ShowTip(event, "Delete this vehicle<br>from my watch list");}
function VIHPDel(event){ShowTip(event, "Delete this vehicle from<br>vehicles I have purchased");}
function VIDNPDel(event){ShowTip(event, "Delete this vehicle from<br>vehicles I did not purchased");}
function SSDel(event){ShowTip(event, "Delete");}
function ShowQuic(event){ShowTip(event, "Offered on Ford Quic");}
function ShowCR(event){ShowTip(event, "Condition Report Available");}
function ShowCRHead(event){ShowTip(event, "Condition Report Available");}
function ShowOLHead(event){ShowTip(event, "Offered via Online Sale");}
function ShowTip(event, content){
    
    var divEquip = document.getElementById('dvTip2');
    if(document.all){

        divEquip.style.left = (event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft + 20) + 'px';
        divEquip.style.top = (event.clientY + document.documentElement.scrollTop + document.body.scrollTop - 8) + 'px';
    }else{

        divEquip.style.left = (event.pageX -150) + 'px';
        divEquip.style.top = (event.pageY - 115) + 'px';
    }
    divEquip.innerHTML = content;
    divEquip.style.display = 'block';
    
    //----------------------------------

//     var divnewEquip = document.getElementById('dvnewTip');
//    if(document.all){
//        divnewEquip.style.left = (event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft + 20) + 'px';
//        divnewEquip.style.top = (event.clientY + document.documentElement.scrollTop + document.body.scrollTop - 8) + 'px';
//    }else{
//        divnewEquip.style.left = (event.pageX + 20) + 'px';
//        divnewEquip.style.top = (event.pageY - 8) + 'px';
//    }
//    divnewEquip.innerHTML = content;
//    divnewEquip.style.display = 'block';
    
    //--------------------------------
    
}
function HideTip(){
    var divEquip = document.getElementById('dvTip2');
    divEquip.style.display = 'none';
    
    //-----------------
//     var divnewEquip = document.getElementById('dvnewTip');
//      divnewEquip.style.display = 'none';
    //-------------------
}
function z(){var b;}


/*Search*/
function SwitchTabs(oTab){
    var tbCars = document.getElementById("tbCars");
    var tbVehicles = document.getElementById("tbVehicles");
    if (oTab.id == "tbCars"){
        tbCars.className = "ActTab";
        tbVehicles.className = "InactTab";
    } else {
        tbCars.className = "InactTab";
        tbVehicles.className = "ActTab";
    }
}

function SwitchEventTabs(Tab){
    var ctLanes = document.getElementById(dvLanes);
    var ctConsign = document.getElementById(dvConsign);
    var ctModels = document.getElementById(dvModels);
    var ctTabConsignor = document.getElementById(tbConsignor);
    var ctTabLane = document.getElementById(tbLane);
    var ctTabModel = document.getElementById(tbModels);
	switch (Tab){
		case "Lane":
				ctTabLane.className = "CalActiveTab";
				ctTabConsignor.className = "CalInactiveTab";
				ctTabModel.className = "CalInactiveTab";
				ctLanes.style.display = "block";
				ctConsign.style.display = "none";
				ctModels.style.display = "none";
			break;
		case "Consignor":
				ctTabConsignor.className = "CalActiveTab";
				ctTabLane.className = "CalInactiveTab";
				ctTabModel.className = "CalInactiveTab";
				ctLanes.style.display = "none";
				ctConsign.style.display = "block";
				ctModels.style.display = "none";
			break;
		case "Summary":
		        if (!bModels){
		            window.location.replace(hRef);
		            return;
		        }
				ctTabConsignor.className = "CalInactiveTab";
				ctTabLane.className = "CalInactiveTab";
				ctTabModel.className = "CalActiveTab";
				ctLanes.style.display = "none";
				ctConsign.style.display = "none";
				ctModels.style.display = "block";
			break;
	}						
}

function SwitchEventTabs2(Tab){
    var ctLanes = document.getElementById(dvLanes);
    var ctLaneHead = document.getElementById(tblLaneHead);
    var ctConsign = document.getElementById(dvConsign);
    var ctConsignHead = document.getElementById(tblConsignHead);
    var ctTabConsignor = document.getElementById(tbConsignor);
    var ctTabModel = document.getElementById(tbModels);
    var ctModels = document.getElementById(dvModels);
	switch (Tab){
		case "Information":
				ctTabConsignor.className = "CalActiveTab";
				ctTabModel.className = "CalInactiveTab";
				ctLanes.style.display = "block";
				if (ctLaneHead) ctLaneHead.style.display = "block";
				if (ctConsign) ctConsign.style.display = "block";
				if (ctConsignHead) ctConsignHead.style.display = "block";
				ctModels.style.display = "none";
			break;
		case "Summary":
		        if (!bModels){
		            window.location.replace(hRef);
		            return;
		        }
				ctTabConsignor.className = "CalInactiveTab";
				ctTabModel.className = "CalActiveTab";
				ctLanes.style.display = "none";
				if (ctLaneHead) ctLaneHead.style.display = "none";
				if (ctConsign) ctConsign.style.display = "none";
				if (ctConsignHead) ctConsignHead.style.display = "none";
				ctModels.style.display = "block";
			break;
	}						
}

function CheckAuc(){
    var divAuc = document.getElementById('dvAuctions');
    var aAuc = document.getElementById('aAuctions');
    var ctStyle = document.getElementsByName('b');
    var ctSearches = document.getElementById('srchs');
    var ctInv = document.getElementById('inv');
    var ctSale = document.getElementById('sal');
    var ctConsign = document.getElementById('csn');
    
    divAuc.style.height = document.getElementById('tblSearch').offsetHeight;
    divAuc.style.display = "block";
    if (1==1){
        if (document.all){
            divAuc.style.left = (369 + (document.getElementById('tblBody').offsetWidth / 2) - divAuc.offsetWidth    ) + 'px';
        }else{
            divAuc.style.left = ((document.width / 2) + 369 - divAuc.offsetWidth) + 'px';
        }
    }else{
        if (document.all){
            divAuc.style.left = (403 + (divAuc.offsetWidth / 2)    ) + 'px';
        }else{
            divAuc.style.left = ((document.width / 2) + 217 - divAuc.offsetWidth) + 'px';
        }
    }
    
    if (ctSearches) ctSearches.style.display = 'none';
    if (ctInv) ctInv.style.display = 'none';
    if (ctSale) ctSale.style.display = 'none';
    if (ctConsign) ctConsign.style.display = 'none';
    if (ctStyle[0]) ctStyle[0].style.display = 'none';
}

function AuctionDone(){
    var spAuctions = document.getElementById('spSelAuctions');
    var spAuctions2 = document.getElementById('spSelAuctions2');
    var nlAuctions = document.getElementsByName("A");
    var ctStyle = document.getElementsByName('b');
    var ctSearches = document.getElementById('srchs');
    var ctInv = document.getElementById('inv');
    var ctSale = document.getElementById('sal');
    var ctConsign = document.getElementById('csn');
    var numAuctions = 0;
    var numSelected = 0;
    for(i=0;nlAuctions[i];i++){
        numAuctions++;
        if (nlAuctions[i].checked){numSelected++;}
    }
    spAuctions.innerHTML = numSelected.toString() + ' of ' + numAuctions.toString();
    if (spAuctions2)spAuctions2 .innerHTML = numSelected.toString() + ' of ' + numAuctions.toString();
    var divAuc = document.getElementById('dvAuctions');
    divAuc.style.display = 'none';
    
    if (ctSearches) ctSearches.style.display = 'block';
    if (ctInv) ctInv.style.display = 'block';
    if (ctSale) ctSale.style.display = 'block';
    if (ctConsign) ctConsign.style.display = 'block';
    if (ctStyle[0]) ctStyle[0].style.display = 'block';
}

function invc(ConsignID, AuctionID){
    window.open("Inventory.aspx?view=eventconsign&id=" + ConsignID + "&event=" + AuctionID, "_self");
}
function invel(LaneID){
    window.open("Inventory.aspx?view=lane&id=" + LaneID, "_self");
}

function HelpTab(){
    var mnHelp = document.getElementById("tbHelp");
    var lHelp = document.getElementById("aHelp");
    mnHelp.style.backgroundImage = "url(img/hpback.gif)";
    lHelp.style.color = 'black';
}
function HideHelp(){
    var mnHelp = document.getElementById("tbHelp");
    var lHelp = document.getElementById("aHelp");
    mnHelp.style.backgroundImage = "";
    lHelp.style.color = 'white';
}

function CheckLoginOK(){
    var ctUserName = document.getElementById("UserName");
    var ctPassword = document.getElementById("Password");
    
    if (ctUserName.value.length == 0){
        window.alert("Must provide a valid UserName to log in.");
        return false;
    }
    if (ctPassword.value.length == 0){
        window.alert("Must provide a valid Password to log in.");
        return false;
    }
    return true;
}




function ShowUpgrade(){
    document.getElementById('divUpgrade').style.display='block';
}


function CheckRegisterOK(){

    ctlDealerAuction = document.getElementById(ctDealerAuction);
    ctlDealer = document.getElementById(ctDealerName);
    ctlDealerCity = document.getElementById(ctDealerCity);
    ctlFirstName = document.getElementById(ctFirstName);
    ctlLastName = document.getElementById(ctLastName);
    ctlNickName = document.getElementById(ctNickName);
    ctlEmail = document.getElementById(ctEmail);
    ctlConfirmEmail = document.getElementById(ctConfirmEmail);
    ctlStreet = document.getElementById(ctStreet);
    ctlCity = document.getElementById(ctCity);
    ctlState = document.getElementById(ctState);
    ctlZip = document.getElementById(ctZip);
    ctlCountry = document.getElementById(ctCountry);
    ctlHomeArea = document.getElementById(ctHomeArea);
    ctlHomePrefix = document.getElementById(ctHomePrefix);
    ctlHomeNum = document.getElementById(ctHomeNum);
    ctlFaxArea = document.getElementById(ctFaxArea);
    ctlFaxPrefix = document.getElementById(ctFaxPrefix);
    ctlFaxNum = document.getElementById(ctFaxNum);
    ctlWorkArea = document.getElementById(ctWorkArea);
    ctlWorkPrefix = document.getElementById(ctWorkPrefix);
    ctlWorkNum = document.getElementById(ctWorkNum);
    ctlCellArea = document.getElementById(ctCellArea);
    ctlCellPrefix = document.getElementById(ctCellPrefix);
    ctlCellNum = document.getElementById(ctCellNum);
    ctlEmailFormat = document.getElementById(ctEmailFormat);
    if (ctlDealer){
        if (!isNotJustWhitespace(ctlDealer.value)){
            window.alert("Must provide 'Dealer Name' to register.");
            return false;
        }
    }
    if (ctlDealerAuction){
        if (!isNotJustWhitespace(ctlDealerAuction.value)){
            window.alert("Must provide 'Preferred Auction' to register.");
            return false;
        }
    }
    if (ctlDealerCity){
        if (!isNotJustWhitespace(ctlDealerCity.value)){
            window.alert("Must provide 'Dealer City' to register.");
            return false;
        }
    }
    if (!isNotJustWhitespace(ctlFirstName.value)){
        window.alert("Must provide 'First Name' to register.");
        return false;
    }
    if (!isNotJustWhitespace(ctlLastName.value)){
        window.alert("Must provide 'Last Name' to register.");
        return false;
    }
    if (!isNotJustWhitespace(ctlEmail.value)){
        window.alert("Must provide 'Email' to register.");
        return false;
    }
    if (!(ctlEmail.value == ctlConfirmEmail.value)){
        window.alert("'Email' and 'Confirm Email' do not match.");
        return false;
    }
    return true;
}


function CheckDefaults(){
    window.alert('test');
    var bChecked = false;
    for(i=0;vAuctions[i];i++){
        if(document.getElementById('chk' + vAuctions[i] + 'DEFAULT').checked)bChecked = true;
    }
    return bChecked;
}

function AuctionCheck(){
    var checked = $('#dvAuctions input').has(':checked');
    $('#dvAuctions input').attr('checked',true);
    return false;
}

function MoreOptions(){
    var tbQuickSearch = document.getElementById("tblQuickSearch");
    var tbOptions = document.getElementById("tblOptions");
    var sMoreOptions = document.getElementById("spMoreOptions");
    var sLessOptions = document.getElementById("spLessOptions");
    var submit = document.getElementById("sb");
    var bSearch = document.getElementById("bSearchType");
    
    tbOptions.style.display = 'block';
    tbQuickSearch.style.display = 'none';
    sMoreOptions.style.display = 'none';
    sLessOptions.style.display = 'block';
    submit.value = 'advanced';
    bSearch.innerHTML = "Advanced Search";
}

function LessOptions(){
    var tbQuickSearch = document.getElementById("tblQuickSearch");
    var tbOptions = document.getElementById("tblOptions");
    var sMoreOptions = document.getElementById("spMoreOptions");
    var sLessOptions = document.getElementById("spLessOptions");
    var submit = document.getElementById("sb");
    var bSearch = document.getElementById("bSearchType");
    
    tbOptions.style.display = 'none';
    tbQuickSearch.style.display = 'block';
    sMoreOptions.style.display = 'block';
    sLessOptions.style.display = 'none';
    submit.value = 'quick';
    bSearch.innerHTML = "Quick Search";
}


function CheckORPubEvents(){
    var nlLanes = document.getElementsByName("Lane");
    var bChecked = false;
    for(i=0;nlLanes[i];i++){
        if(nlLanes[i].checked == true) bChecked = true;
    }
    if(bChecked == false){window.alert("You must select a lane to continue.");}else{StatusSubmit();}
    return bChecked;
}


function CheckOREvents() {
    var nlLanes = document.getElementsByName("Lane");
    var bChecked = false;
    for(i=0;nlLanes[i];i++){
        if(nlLanes[i].checked == true) bChecked = true;
    }
    if(bChecked == false){window.alert("You must select at least one lane to continue.");}else{StatusSubmit();}
    return bChecked;
}


function CheckORDealers(){
    var nlLanes = document.getElementsByName("Deal");
    var bChecked = false;
    for(i=0;nlLanes[i];i++){
        if(nlLanes[i].checked == true) bChecked = true;
    }
    if(bChecked == false){window.alert("You must select at least one dealer to continue.");}else{StatusSubmit();}
    return bChecked;
}

/*
function MoreOptions(){
    var tblQuickSearch = document.getElementById("tblQuickSearch");
    var tblSearch = document.getElementById("tblSearch");
    var tblOptions = document.getElementById("tblOptions");
    var tblAdvancedSubmit = document.getElementById("tblAdvancedSubmit");
    var tblSimpleSubmit = document.getElementById("tblSimpleSubmit");
    var submit = document.getElementById("sb");
    var aOptions = document.getElementById("aOptions");
    if (tblAdvancedSubmit.style.display == "block"){
        tblOptions.style.display = "none";
        tblAdvancedSubmit.style.display = "none";
        tblSimpleSubmit.style.display = "block";
        submit.value = "simple";
    }else{
        tblOptions.style.display = "block";
        tblAdvancedSubmit.style.display = "block";
        tblSimpleSubmit.style.display = "none";
        submit.value = "advanced";
    }
    //window.alert(submit.value);
}
*/
function SwitchSearch(oTab){
    var tbSearch = document.getElementById("tbSearch");
    var tbQuickSearch = document.getElementById("tbQuickSearch");
    var tblQuickSearch = document.getElementById("tblQuickSearch");
    var tblSearch = document.getElementById("tblSearch");
    var tblOptions = document.getElementById("tblOptions");
    var tblQuickSubmit = document.getElementById("tblQuickSubmit");
    var tblAdvancedSubmit = document.getElementById("tblAdvancedSubmit");
    var submit = document.getElementById("sb");
    if (oTab.id == "tbSearch"){
        tbSearch.className = "ActTab";
        tbQuickSearch.className = "InactTab";
        tblQuickSearch.style.display = "none";
        tblQuickSubmit.style.display = "none";
        tblSearch.style.display = "block";
        tblAdvancedSubmit.style.display = "block";
        tblOptions.style.display = "block";
        submit.value = "advanced";
    } else {
        tbSearch.className = "InactTab";
        tbQuickSearch.className = "ActTab";
        tblQuickSearch.style.display = "block";
        tblQuickSubmit.style.display = "block";
        tblSearch.style.display = "none";
        tblAdvancedSubmit.style.display = "none";
        tblOptions.style.display = "none";
        submit.value = "quick";
    }
    //window.alert(submit.value);
}
function SwitchMarket(oTab){
    var tbMarketMain = document.getElementById("tbMarketMain");
    var tbMarketTrans = document.getElementById("tbMarketTrans");
    var tbMarketAuctions = document.getElementById("tbMarketAuctions");
    var tblMarketMain = document.getElementById(tableMarketMain);
    var tblMarketTrans = document.getElementById(tableMarketTrans);
    var tblMarketAuctions = document.getElementById(tableMarketAuctions);
    if (oTab.id == "tbMarketMain"){
        tbMarketMain.className = "ActTab";
        tbMarketTrans.className = "InactTab";
        tbMarketAuctions.className = "InactTab";
        if(tblMarketTrans){
            if(tblMarketMain)tblMarketMain.style.display = "block";
            tblMarketTrans.style.display = "none";
            tblMarketAuctions.style.display = "none";
        }
    }
    if (oTab.id == "tbMarketTrans"){
        tbMarketMain.className = "InactTab";
        tbMarketTrans.className = "ActTab";
        tbMarketAuctions.className = "InactTab";
        if(tblMarketTrans){
            if(tblMarketMain)tblMarketMain.style.display = "none";
            tblMarketTrans.style.display = "block";
            tblMarketAuctions.style.display = "none";
        }
    }
    if (oTab.id == "tbMarketAuctions"){
        tbMarketMain.className = "InactTab";
        tbMarketTrans.className = "InactTab";
        tbMarketAuctions.className = "ActTab";
        if(tblMarketTrans){
            if(tblMarketMain)tblMarketMain.style.display = "none";
            tblMarketTrans.style.display = "none";
            tblMarketAuctions.style.display = "block";
        }
    }
}

function CancelKey(){
    document.getElementById(ctFirstName).value = vFirstName;
    document.getElementById(ctLastName).value = vLastName;
    document.getElementById(ctEmail).value = vEmail;
    document.getElementById(ctHomeArea).value = vHomeArea;
    document.getElementById(ctHomePrefix).value = vHomePrefix;
    document.getElementById(ctHomeNum).value = vHomeNum;
    document.getElementById(ctCellArea).value = vCellArea;
    document.getElementById(ctCellPrefix).value = vCellPrefix;
    document.getElementById(ctCellNum).value = vCellNum;
}



function BidIncrement(dBidValue){
    if(dBidValue < 100)return 25;
    if(dBidValue < 500)return 50;
    return 100;
}

function BidValidate(dBidValue){
    return dBidValue - (dBidValue % BidIncrement(dBidValue));
}

function CheckOffer(){
    var txtOffer = document.getElementById(vOffer);
    var b = txtOffer.value;
    if(!isInteger(b)){
        window.alert("Offer amount must be a number without commas (i.e. 5500).");
        txtOffer.focus();
        return false;
    }
    return true;
}

function CheckBid() {
    var cboDealers = document.getElementById("ctDealer");
    var c = $('#ph1_chkDeleteBid').attr('checked');
    var d = $('#ph1_hidBidAction').attr('value');
   
    if (!c) {
        var b = txtMaxBid.value;

        if (d == 'change') {
            if (!isInteger(b)) {
                window.alert("Change amount must be a number without commas (i.e. 5500).");
                return false;
            }
            else if ((Number(b)) <  ($('#ph1_hidBidCurrent').attr('value'))) {
                window.alert("Max amount can't be less than the current bid.");
                return false;
            }
        }
        else {
            if (cboDealers) {
                if (cboDealers.value == "X") {
                    window.alert("You must choose a dealer before submitting a bid.");
                    return false;
                }
            }
            //var b = txtMaxBid.value;
            if (!isInteger(b)) {
                window.alert("Bid amount must be a number without commas (i.e. 5500).");
                return false;
            }
            var i = new Number(b);
            if (i < dNextBid) {
                window.alert("Bid amount must be at least " + dNextBid.toString() + ".");
                return false;
            }
            if (b != BidValidate(b)) {
                window.alert("Bid does not increment by an appropriate amount. Your bid has been changed to " + BidValidate(b).toString() + ".  You must resubmit your bid.");
                txtMaxBid.value = BidValidate(b).toString();
                return false;
            }
            if (txtPubSSN && txtPubSSN.value.length != 6) {
                window.alert("You must provide the last SIX digits of your social security number.");
                return false;
            }
        }
    }
    return true;
}

function OfferFocus(){
    var ctNotes = document.getElementById(vNotes);
    if (ctNotes.value == '(Put notes to customer service here)'){
        ctNotes.value = '';
    }
}

//function CheckOffer(){
//    var ctOffer = document.getElementById(vOffer);
//    if (!isInteger(ctOffer.value)){
//        window.alert('Offer amount must be a number without commas (i.e. 5500).');
//        return false;
//    }
//    return window.confirm('Are you sure want to send this offer?');
//}


function MarketChangeMiles(control, intercept, slope){
       //window.alert(control);
       //window.alert(intercept);
       //window.alert(slope);
    var divPrice = document.getElementById("divPrice");
    var txtMiles = document.getElementById(control);
    
    if (!isInteger(txtMiles.value)){
        divPrice.innerHTML = "--";
    } else {
        var NewPrice = intercept + (slope * parseInt(txtMiles.value)); 
        //window.alert(slope * parseInt(txtMiles.value));
        //window.alert(NewPrice);
        //divPrice.innerHTML = formatCurrency(Math.round(NewPrice));
        divPrice.innerHTML = formatCurrency(Math.round(NewPrice));
    }
}

function MarketLoadTrimYears(year_control, make_control, model_control, trim_control, target_control, dataset_num){ 
GetMake(year_control);

//    var frm = document.forms[1];
//    if (!frm) frm = document.forms[0];
//    switch (dataset_num) {
//        case 1 : //year has been changed

//            year = frm.elements[year_control].value;
//            make = "";
//            model = "";     
//            // Empty model, trim drop down box of any choices
//            document.getElementById(model_control).options.length = 0;
//            MarketSetDefaultText(document.getElementById(model_control), "Select Model", "");
//            document.getElementById(trim_control).options.length = 0;
//            MarketSetDefaultText(document.getElementById(trim_control), "Select Trim", "");            
//            break; 
//        case 2 : //make has been changed

//            year = frm.elements[year_control].value;
//            make = frm.elements[make_control].value;
//            model = "";       
//            //Empty trim drop down box of any choices
//            MarketSetDefaultText(document.getElementById(trim_control), "Select Trim", "");  
//            MarketSetDefaultText(document.getElementById(trim_control), "Select Trim", "");
//            break; 
//        case 3 : //model has been changed

//            year = document.getElementById(year_control).value;
//            make = document.getElementById(make_control).value;
//            model = document.getElementById(model_control).value;      
//            model = model.replace("&", "%26");
//            //window.alert(model);
//            break; 
//        case 4: //trim has been changed
//            //fire a page refresh to select the vehicle based on trimid - use querystring
//        default : 
//            year = "";
//            make = "";
//            model = ""; 
//    }  
//        var ctlMarket = document.getElementById(ctMarket);
//        ctlMarket.style.display = "none";
//        var sURL = "markettrim.ashx?year=" + year + "&make=" + make + "&model=" + model + "&selCtrl=" + target_control + "&dataset_num=" + dataset_num;
//        document.getElementById("frmLookup").src = sURL;
    }
    
function MarketSetDefaultText(control, innerText, value){    
        default_opt = new Option(innerText, value);
        control.options[0] = (default_opt);
        default_opt.value = value;           
        control.disabled = true;     
    }

function MarketSubmitTrimByRadio (trimid, vin) { 
        window.location = "market.aspx?trim=" + trimid + "&vin=" + vin; 
} 

function MarketSubmitTrimbyDropDown (trim_control) { 
        var ctlMarket = document.getElementById(ctMarket);
        ctlMarket.style.display = "none";
        var frm = document.forms[1];
        if (!frm) frm = document.forms[0];
        if (!vhview){
            window.location = "market.aspx?trim=" + frm.elements[trim_control].value; 
        }else{
            window.location = "market.aspx?view=cr&trim=" + frm.elements[trim_control].value; 
        }
} 

function CheckVINOK(){
    var ctlVIN = document.getElementById(ctVIN);
    if (ctlVIN.value.length == 8 || ctlVIN.value.length == 10){
        var ctlMarket = document.getElementById(ctMarket);
        ctlMarket.style.display = "none";
        window.location = "Market.aspx?vin=" + ctlVIN.value;
    }else{
        window.alert("VIN must be an 8 or 10 character value.");
    }
}
function ChangeVIN(event){
    if (event.keyCode == 13) {CheckVINOK()};
}




function SearchFocus(e){
    var ctl;
    if(document.all){ctl = e.srcElement}else{ctl = e.target}
    var text = ctl.value;
    if (text == 'Yr, Mk or Model' || text == 'Enter Year, Make, or Model'){ctl.value = '';}
}


function NotifyFocus(e){
    var ctl;
    if(document.all){ctl = e.srcElement}else{ctl = e.target}
    var text = ctl.value;
    if (text == 'Alternate Emails'){ctl.value = '';}
}


function ChooseHelp(HelpType){
    var dvCust = document.getElementById('divCust');
    var dvReg = document.getElementById('divReg');
    var dvTech = document.getElementById('divTech');
    var dvNoAuc = document.getElementById('divNoAuc');
    if (NoAuc){
        dvNoAuc.style.display = 'block';
        return;
    }
    switch (HelpType){
        case 'cust': 
            dvCust.style.display = 'block';
            dvReg.style.display = 'none';
            dvTech.style.display = 'none';
            break;
        case 'reg': 
            dvCust.style.display = 'none';
            dvReg.style.display = 'block';
            dvTech.style.display = 'none';
            break;
        case 'tech': 
            dvCust.style.display = 'none';
            dvReg.style.display = 'none';
            dvTech.style.display = 'block';
            break;
    }
}


function SelectModel(){
    var ctMake = document.getElementById('mk');
    var ctMod = document.getElementById('mod');
    var ctSMod = document.getElementById('smod');
    var vMake='';
    
    if(document.all){
        for(i=0;i<ctMake.options.length;i++){
            if (ctMake.options[i].selected && i>0)vMake = ctMake.options[i].text;
        }
    }else{vMake = ctMake.value;}
    if(vMake==''){
        ctMod.style.display = 'block';
        ctSMod.style.display = 'none';
    }else{
        vMake = stripWhitespace(vMake);
        ctSMod.style.display = 'block';
        ctMod.style.display = 'none';
        var i = 0;
        while(vModels[i]){
            //window.alert('*' + vMake + '*' + );
            if(stripWhitespace(vModels[i][0])==vMake){
                var j=1;
                ctSMod.options.length=0;
                ctSMod.options[0]=new Option('--Choose Model--', '');
                while(vModels[i][j]){
                    var op = new Option(vModels[i][j]);
                    ctSMod.options[j]=op;
                    j++;
                }
            }
            i++;
        }
    }
}


function CheckPostSearch(){
    var sd = document.getElementById('sdt');
    var ed = document.getElementById('edt');
    if (!isDateMDY(sd.value)){
        window.alert(sd.value);
        window.alert('Begin date is not a valid date.');
        return false;
    }
    if (!isDateMDY(ed.value)){
        window.alert('End date is not a valid date.');
        return false;
    }
    return true;
}


function OpenDetails(vid){
    window.open('CarReport.aspx?VID=' + vid + '&drill=postsale', '_blank');
}








function EventChanged(){
    var chkEventType = document.getElementById(idEventType);
    var dvNoFactory = document.getElementById("divNoFactory");
    switch (chkEventType.value){
        case "F":
            dvNoFactory.style.display = 'none';
            break;
        default:
            dvNoFactory.style.display = 'block';
    }
}

function EmailChecked(){
    var chkSaveEmail = document.getElementById(idSaveEmail);
    if (chkSaveEmail.checked) {
        document.getElementById('dvEmailMe').style.display = 'block';
    }else{
        document.getElementById('dvEmailMe').style.display = 'none';
    }
    PeriodChanged();
}

function PeriodChanged(){
    var radWeekly = document.getElementById(idWeekly);
    //var radDaily = document.getElementById(idDaily);
    var radRealTime = document.getElementById(idRealTime);
    var divDays = document.getElementById('dvDays');
    var divTime = document.getElementById('dvTime');
    var divRealTime = document.getElementById('dvRealTime');
    if (radWeekly.checked){
        divDays.style.display = 'block';
        divTime.style.display = 'block';
        divRealTime.style.display = 'none';
    }
    //if (radDaily.checked){
    //    divDays.style.display = 'none';
    //    divTime.style.display = 'block';
    //    divRealTime.style.display = 'none';
    //}
    if (radRealTime.checked){
        divDays.style.display = 'none';
        divTime.style.display = 'none';
        divRealTime.style.display = 'block';
    }
}

function CheckSSName(){
    var txtName = document.getElementById(idName);
    if(txtName.value.length == 0){
        window.alert('You must provide a name for this search before you can save it.');
        return false;
    }else{
        return true;
    }
}


function ssMonitorName(event){
    if (event.keyCode == 13) {
        event.keyCode = null;
        document.getElementById(idSubmit).click(); 
        }  
}

function CreateCellEmail(){
    var cCellArea = document.getElementById(ctCellArea);
    var cCellPrefix = document.getElementById(ctCellPrefix);
    var cCellNum = document.getElementById(ctCellNum);
    var cCellProvider = document.getElementById('selCellProvider');
    var cCellEmail = document.getElementById(ctCellEmail);
    
    if (cCellArea.value.length != 3 || cCellPrefix.value.length != 3 || cCellNum.value.length != 4){
        window.alert('You must provide a complete cell phone to determine your cell email address.');
        return;
    }
    if (cCellProvider.value.length == 0){
        window.alert('You must choose a cell phone provider to determine your cell email address.');
        return;
    }
    cCellEmail.value = cCellArea.value + cCellPrefix.value + cCellNum.value + cCellProvider.value;
}

function ExecSearch(index){
    if (index == 0) return;
    var URL = sSrch[index - 1];
    window.location = URL;
}


//This function supports opening vehicle details and allowing scrolling back and forth
function cd(vArrayPlace, DrillType){
    var iLen = vh.length; //vh is defined on the page as an array of vehicle ids
    var iOffsets = 5;
    var iStart = (vArrayPlace-iOffsets<0 ? 0 : (vArrayPlace-iOffsets));
    //window.alert('' + vArrayPlace + ' ' + iOffsets + ' ' + iLen);
    var iEnd = (vArrayPlace+iOffsets>iLen ? iLen : vArrayPlace+iOffsets);
    var isArrayPlace = vArrayPlace - iStart;
    var s = "";
    
    for(i=iStart;i<iEnd && i<iLen;i++){
        s += (i==iEnd-1 ? vh[i].toString() : vh[i].toString() + ",");
    }
    var sURL = "carreport.aspx?vhs=" + s + "&vhp=" + isArrayPlace + "&vhi=" + iStart + "&" + parms;
    //window.alert(sURL);
    //window.alert(sURL.length);
    window.open(sURL, "_blank");
}




//This function supports to navigate to selected page when user selects page number from search2 results  selected index   

//function Navigateto(URL, Noofpages)
//{
//     var sURL = new String(URL);
//     var str1 = new String();
//     var str2 = new String (); 
//        for(var index=1;index <= Noofpages;index++){
//            if($("selectpageno").value == index ){
//               var i = sURL.indexOf("page=");
//                if(i > 0 ){
//                 if(sURL.charAt(i+5) != "&" ){
//                     str1 = sURL.substring(0,i+5);
//                     str2 = sURL.substring(str1.length ,sURL.length);
//                     sURL = str2.substring (str2.indexOf ("&"),str2.length );
//                  }
//                 }
//              document.location.href=str1 +index+ sURL;
//             }    
//         }
//    
//}



function Navigateto(URL, Noofpages, pos)
{
     var str1,str2,sURL;
    sURL = URL;
        for(var index=1;index <= Noofpages;index++){
            var selectnav;
            if(pos == "top")
            {
                selectnav = $("selectpagenotop")
            }
            else
            {
                selectnav = $("selectpagenobottom")
            }
            if(selectnav.value == index ){
               var i = sURL.indexOf("page=");
                if(i > 0 ){
                     if(sURL.charAt(i+5) != "&" ){
                         str1 = sURL.substring(0,i+5);
                         str2 = sURL.substring(str1.length ,sURL.length);
                         if(str2.indexOf ("&") > 0)
                         {sURL = str2.substring (str2.indexOf ("&"),str2.length );}
                         else {sURL = "";}
                        }
                 document.location.href=str1 +index+sURL;
                 }
                else 
                 {document.location.href=sURL +"&page="+index;}
             }
                 
         }
    
}



// this function supports to display the sorted search results in Year make model

function SortYearMakeModel(){

    var URL = '';  
    URL = document.location.href;
    var ArrKeys = URL.toQueryParams();
    var sSort = ArrKeys["sort"];
    switch(sSort)
    {
        case "YMkMd":
            sSort ="MkYMd";
            break;
        case "MkYMd":
            sSort ="MkMdY";
            break;   
        case "MkMdY":
            sSort ="YMkMd";
            break;  
        default:
            sSort ="YMkMd";
            break;                                 
    }
        var str1 ,str2; 
        var index  = URL.indexOf("sort=");
        if (index >0)
        {
            if(URL.charAt(index +5) != "&" ){
             str1 = URL.substring(0,index +5);
             str2 = URL.substring(str1.length ,URL.length);
             if(str2.indexOf ("&") > 0){URL = str2.substring (str2.indexOf ("&"),str2.length );}
             else {URL = "";}
          }
            document.location.href=str1 + sSort + URL;
        } 
    
}


// this function supprots to sort the search results by mileage
function SortByMiles(){
var str1 ,str2,URL; 
       URL = document.location.href; 
        var index  = URL.indexOf("sort=");
        if (index >0)
        {
            if(URL.charAt(index +5) != "&" ){
             str1 = URL.substring(0,index +5);
             str2 = URL.substring(str1.length ,URL.length);
             if(str2.indexOf ("&") > 0){URL = str2.substring (str2.indexOf ("&"),str2.length );}
             else {URL = "";}
          }
            document.location.href=str1 + "Miles" + URL;
        } 
}

//This function supports to sort the search results by Interior and exterior colors

function SortByColor()
{
    var URL = '';  
    URL = document.location.href;
    var ArrKeys = URL.toQueryParams();
    var sSort = ArrKeys["sort"];
    switch(sSort)
    {
        case "IntColor"://Interior Color
            sSort ="Color";
            break;
        case "Color":
          sSort ="IntColor"//Interior Color  
            break;   
        default:
            sSort ="Color";
            break;                                 
    }
        var str1 ,str2; 
        var index  = URL.indexOf("sort=");
        if (index > 0)
        {
            if(URL.charAt(index +5) != "&" ){
             str1 = URL.substring(0,index +5);
             str2 = URL.substring(str1.length ,URL.length);
             if(str2.indexOf ("&") > 0)
               {URL = str2.substring (str2.indexOf ("&"),str2.length );}
             else {URL = "";}
          }
            document.location.href=str1 + sSort + URL;
        } 
}

// This function supports to sort the search result by auction city, date, lane, run
function SortByAuctionDateLaneLrun(){
    var URL = '';  
    URL = document.location.href;
    var ArrKeys = URL.toQueryParams();
    var sSort = ArrKeys["sort"];
    switch(sSort)
    {
        case "AtDtLnRn":
            sSort ="DtAtLnRn";
            break;
        case "DtAtLnRn":
            sSort ="AtDtLnRn";
            break;   
        default:
            sSort ="AtDtLnRn";
            break;                                 
    }
        var str1 ,str2; 
        var index  = URL.indexOf("sort=");
        if (index > 0)
        {
            if(URL.charAt(index +5) != "&" ){
             str1 = URL.substring(0,index +5);
             str2 = URL.substring(str1.length ,URL.length);
             if(str2.indexOf ("&") > 0)
              {URL = str2.substring (str2.indexOf ("&"),str2.length );}
             else {URL = "";}
          }
            document.location.href=str1 + sSort + URL;
        } 
}

//This function supports to sort the search results by Warranty
function SortByCert(){
var str1 ,str2,URL; 
       URL = document.location.href; 
        var index  = URL.indexOf("sort=");
        if (index >0)
        {
            if(URL.charAt(index +5) != "&" ){
             str1 = URL.substring(0,index +5);
             str2 = URL.substring(str1.length ,URL.length);
             if(str2.indexOf ("&") > 0)
              {URL = str2.substring (str2.indexOf ("&"),str2.length );}
             else {URL = "";}
          }
            document.location.href=str1 + "Cert" + URL;
        } 
}

//This function suppotr to sort the search results by Current Bid Time Left
function SortByBidTimeLeft(){
   var URL = '';  
    URL = document.location.href;
    var ArrKeys = URL.toQueryParams();
    var sSort = ArrKeys["sort"];
    switch(sSort)
    {
        case "BTAsc":
            sSort ="BTDes";
            break;
        case "BTDes":
            sSort ="BTAsc"; 
            break;   
        default:
            sSort ="BTAsc";
            break;                                 
    }
        var str1 ,str2; 
        var index  = URL.indexOf("sort=");
        if (index >0)
        {
            if(URL.charAt(index +5) != "&" ){
             str1 = URL.substring(0,index +5);
             str2 = URL.substring(str1.length ,URL.length);
             if(str2.indexOf ("&") > 0)
              {URL = str2.substring (str2.indexOf ("&"),str2.length );}
             else {URL = "";}
          }
            document.location.href=str1 + sSort + URL;
        }
 }       

//This  function supports to Get the ajax controls for Model and Trim 
    function GetYearMakeModel(YrMkMd)
      {

        if (YrMkMd == "Make")
            {
              var selectedMake =  $("selectMake").options[$("selectMake").selectedIndex].value
              //$("dvModel").innerHTML = "";
              //$("Model").hide();
              //$("dvModel").disabled = false;  
                         var selectedYear;
           if (YearID.length <= 0 )
              {selectedYear = SelectedYear1}
           else {selectedYear = document.getElementById(YearID).value; }
              var SURL = "YearMakeModel.aspx?Year="+selectedYear+ "&Make=" + selectedMake;
              new Ajax.Updater("dvModel",SURL, { 
              asynchronous:true, evalScripts:true,
              onFailure : function(resp) {  alert("Oops, there's been an error.");  } 
              });   
              timerModel = window.setTimeout("SelectedModel()", 1000);   
            }
        if (YrMkMd == "Model")
            {
              var selectedModel =  $("selectModel").options[$("selectModel").selectedIndex].value;
              var selectedMake =  $("selectMake").options[$("selectMake").selectedIndex].value;
              //$("dvTrim").innerHTML = "";
              //$("Trim").hide();
              //$("dvTrim").disabled = false;  
                         var selectedYear;
           if (YearID.length <= 0 )
              {selectedYear = SelectedYear1;}
           else {selectedYear = document.getElementById(YearID).value; }
              var SURL = "YearMakeModel.aspx?Year="+selectedYear+"&Make=" + selectedMake+ "&Model=" + selectedModel;
              new Ajax.Updater("dvTrim",SURL, { 
              asynchronous:true, evalScripts:true,
              onFailure : function(resp) {  alert("Oops, there's been an error.");  } 
              }); 
        timerTrim = window.setTimeout("SelectedTrim()", 1000);  
            }
        if (YrMkMd == "Trim")
            {
            var CtVinVal = "";
            if(document.getElementById(ctVIN).value.length > 0){CtVinVal = document.getElementById(ctVIN).value;}
            
               //document.location.href= "Market.aspx?Trim=" + $("selectTrim").options[$("selectTrim").selectedIndex].value + "&vin="+ CtVinVal ;
               
               if (!vhview)
                 { window.location = "market.aspx?trim="+$("selectTrim").options[$("selectTrim").selectedIndex].value+"&sYear="+SelectedYear1+"&sMake="+$("selectMake").options[$("selectMake").selectedIndex].value+"&sModel="+$("selectModel").options[$("selectModel").selectedIndex].value; }
               else
                 { window.location = "market.aspx?view=cr&trim="+$("selectTrim").options[$("selectTrim").selectedIndex].value+"&sYear="+SelectedYear1+"&sMake="+$("selectMake").options[$("selectMake").selectedIndex].value+"&sModel="+$("selectModel").options[$("selectModel").selectedIndex].value;  }
                       
            }
     }
     
// This function supports to get the ajax control for Make
    function GetMake(YearId)
    
      {
           YearID = YearId;
           // $("dvMakes").innerHTML = "";
	        //$("Make").hide();
            //$("dvMakes").disabled = false;  
	        var TrimYear = document.getElementById(YearId).value; 
	        SelectedYear1 = document.getElementById(YearId).value; 
	       GetMakeDropDown(TrimYear);   
      }
  //============================================    
      function GetMakeDropDown(TrimYear)
      {
      var selectedYear;
       if (YearID.length <= 0 )
          {selectedYear = TrimYear;}
       else {selectedYear = document.getElementById(YearID).value; }    
            var SURL = "YearMakeModel.aspx?Year=" + selectedYear;  
            new Ajax.Updater("dvMakes",SURL, { 
            asynchronous:true, evalScripts:true,
            onFailure : function(resp) {  alert("Oops, there's been an error.");  } 
              }); 
              timer = window.setTimeout("SelectedMake()", 1000);
              SelectedModel();
              SelectedTrim();
      }
      
      function SelectedMake()
      {
        URL = document.location.href;
        var ArrKeys = URL.toQueryParams();
        if(ArrKeys["sYear"] == SelectedYear1 || SelectedYear1 == "")
         {

          if($("selectMake"))
            {   
                if(timer)
                { window.clearTimeout(timer); }
                   
             
            
                var sMake = ArrKeys["sMake"];
               if(sMake)
                {
                    //selectedmodel = ArrKeys["smod"];
                    for(i=0; i < $("selectMake").options.length; i++)
                    {
                        if($("selectMake").options[i].value == sMake)
                        {
                            $("selectMake").options[i].selected = true;
                             $("selectMake").disabled = true;
                            GetYearMakeModel('Make')
                            break;
                        }
                    }
                }
            }    
        }
     
      }
      
    function SelectedModel()
     {
      URL = document.location.href;
      var ArrKeys = URL.toQueryParams();
      if(ArrKeys["sYear"] == SelectedYear1)
      {
      if($("selectModel"))
        {
            if(timerModel)
            {   window.clearTimeout(timerModel);}
                
                var sModel = ArrKeys["sModel"];
               if(slectedModel)
                {
                    for(i=0; i < $("selectModel").options.length; i++)
                    {
                        if($("selectModel").options[i].value == sModel)
                        {
                            $("selectModel").options[i].selected = true;
                            $("selectModel").disabled = true;
                            GetYearMakeModel('Model')
                            break;
                        }
                    }
                }
            }    
        }
     
      } 
     
     function SelectedTrim()
     {
        URL = document.location.href;
        var ArrKeys = URL.toQueryParams();
        if(ArrKeys["sYear"] == SelectedYear1)
        {
          if($("selectTrim"))
            {
                if(timerTrim)
                {   window.clearTimeout(timerTrim); }
                URL = document.location.href;
                var ArrKeys = URL.toQueryParams();
                var sTrim = ArrKeys["trim"];
               if(slectedTrim)
                {
                    for(i=0; i < $("selectTrim").options.length; i++)
                    { var Trim = $("selectTrim").options[i].value;
                  
                        if(Trim == sTrim)
                        {
                            $("selectTrim").options[i].selected = true;
                            $("selectTrim").disabled = true;
                            
                            break;
                        }
                    }
                }
             }   
        }
     
     
//alert($(RegionID).options.length)// = true;
     
      } 
      
      
//      function GetModelDropDown()
//      {
//           var selectedYear;
//           if (YearID.length <= 0 )
//              {selectedYear = TrimYear;}
//           else {selectedYear = document.getElementById(YearID).value; }  
//      
//      }
//      function GetTrimDropDown(TrimYear,TrimMake,TrimModel)
//      {}
      
    //==================================  
    function  HideAndShow()
      {
      if(!$("dvMakes").disabled && !$("dvModel").disabled && !$("dvTrim").disabled)
         {
               $("dvMakes").disabled = true;
               $("dvModel").disabled = true;
               $("dvTrim").disabled = true;
          }
        if(!$("Makes").disabled || !$("Model").disabled || !$("Trim").disabled)
          {
               $("Make").disabled = true; 
               $("Model").disabled = true; 
               $("Trim").disabled = true; 
          } 
      }
    
    
    // This function supports to Filter the result on specified Region   
     function OnRegion(RegionId)
           { 
             var URL = document.location.href;
             if (URL.indexOf("?") > 0 )
               {  
                    RegionsID = RegionId;
                    if (URL.indexOf("Region=") > 0)
                     {
                       var str1 ,str2; 
                       var index = URL.indexOf("Region=");
                      window.location =  URL.substring(0,index) +"Region=" +$(RegionId).options[$(RegionId).selectedIndex].innerHTML; 
                        
                     }
                     else
                     {
                       
                       window.location = URL +"&Region=" +$(RegionId).options[$(RegionId).selectedIndex].innerHTML; 
                     }
                              
               }
              else 
               {
                  alert("Please select Trim  First then select Region")
               }
           
           }
       
   function LoadRegion()
   {
     
    // alert($(RegionID).options.length);
   }    
           
   function ShowHideControls(Year)    
   {
          GetMakeDropDown(Year);
          SelectedYear1 = Year;
    
   }
        
