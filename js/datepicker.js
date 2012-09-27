
var directory = "";
var today = new Date();
var arrDays = new Array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');
var arrVDays = new Array('1st','2nd','3rd','4th','5th','6th','7th','8th','9th','10th','11th','12th','13th','14th','15th','16th','17th','18th','19th','20th','21st','22nd','23rd','24th','25th','26th','27th','28th','29th','30th','31st');
var arrMons = new Array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
var arrMonths = new Array('January','February','March','April','May','June','July','August','September','October','November','December');
//var arrMonths = new Array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');

var monthLength = new Array(31,28,31,30,31,30,31,31,30,31,30,31);
var hiddenArray = new Array(42);

var currentyear = today.getYear();
var currentmonth = today.getMonth();



if (currentyear < 1000) {currentyear += 1900;}
var dp_dir = "";
var newWin_doc = null;
var newWin = null;
var txtboxObj = null;

var DateFormatArray = new Array("yyyy-mm-dd");



//Variables: d,dd,m,mm,yy,yyyy,mon,month,ith
function formatdate(dd,mm,yy){
    var strf = newWin_doc.getElementById('dateformat').value;
    var temp = "";
    strf = strf.replace(/dd/ig, arrDays[dd-1]);
    strf = strf.replace(/d/ig, dd);
    strf = strf.replace(/ith/ig, arrVDays[dd-1]);

    strf = strf.replace(/yyyy/ig, yy);
    var temp = yy % 100;
    if (temp < 10) temp = "0" + temp;
    strf = strf.replace(/yy/ig, temp);

    temp = strf;
    strf = strf.replace(/month/ig, arrMonths[mm-1]);
    if (temp == strf)
    {
        strf = strf.replace(/mon/ig, arrMons[mm-1]);
        if (temp == strf) {
            strf = strf.replace(/mm/ig, arrDays[mm-1]);
            strf = strf.replace(/m/ig, mm);
        }
    }

    return strf;
}

function returndate(indx){
          if(hiddenArray[indx] > 0) {
        if (txtboxObj) {
            var d = hiddenArray[indx];
            var m = newWin_doc.getElementById('months').options.selectedIndex+1;
            var y = parseInt(newWin_doc.getElementById('years').value);
            document.getElementById(txtboxObj).value = formatdate(d,m,y);
        }
        newWin.close();
    }

}
function updateoutput() {
    var ml = newWin_doc.getElementById('months').options.selectedIndex;
    var sday = new Date(parseInt(newWin_doc.getElementById('years').value),ml,01)
    var startindex = sday.getDay();
    var numberOfDays = monthLength[ml];
    if (startindex==0) {startindex = 7;}

    if (ml==1 && (parseInt(newWin_doc.getElementById('years').value)%4) == 0){numberOfDays = 29;}

    for (var n=0; n<42; n++) {
        var str = "c"+n;
        newWin_doc.getElementById(str).innerHTML = "&nbsp;";
        hiddenArray[n] = 0;
    }

    for (var m=0; m<numberOfDays; m++)
    {
        var str = "c"+(startindex+m-1);
        newWin_doc.getElementById(str).innerHTML = (m+1);
        hiddenArray[(startindex+m-1)] = (m+1);
    }
}

function updateyears()
{
    var tempyear = newWin_doc.getElementById('years').value;
    var styear = tempyear - 10;
    for (var i=0; i <= 20; i++) {
        var s = "yopt"+i;
        newWin_doc.getElementById(s).text = (styear+i);
        newWin_doc.getElementById(s).value = (styear+i);
    }
    for (var i = 0; i < newWin_doc.getElementById('years').options.length; i++ )
    {
        if (newWin_doc.getElementById('years').options[i].value == ""+tempyear)
        {
              newWin_doc.getElementById('years').options.selectedIndex = i;
            break;
        }
    }
}

function twoDigitNumber(intVal)
{
    if (intVal >= 0 && intVal <=9) return "0" + intVal;
    return intVal;
}

function show_calendar(textfieldname, dir, dateformatlist) {
    var df_array = new Array();
    if (dateformatlist) df_array = dateformatlist;
    else df_array = DateFormatArray;
    if (dir) {dp_dir = dir;}
    newWin = window.open("", "Calendar", "width=260,height=250,status=no,resizable=no,scrollbars=no,top=200,left=300");
    newWin.opener = self;
    newWin_doc = newWin.document;
    txtboxObj = textfieldname;


    newWin_doc.writeln('<html><head><title>Choose date</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>');
    newWin_doc.writeln('<style type="text/css">body,div,td,select {font:menu;}');
    newWin_doc.writeln(' .tdHeader {height: 20px; color:#ffffff;font-weight:bold}');
    newWin_doc.writeln(' .tdCell {width: 20px; height: 26px; font-size:12px; cursor:pointer;cursor:hand;}');
    newWin_doc.writeln('</style>');
    newWin_doc.writeln('<body  style="margin: 3px;background-color:#eee;" scroll=no  onBlur="self.focus()">');
    newWin_doc.writeln('<div align=\"center\"><fieldset><legend>Date</legend><table style="#D4D6D9" bgcolor="#FAFCFF" width="234" cellpadding="0" cellspacing="0"><tr><td align="center" height="35">');
    newWin_doc.writeln('<select id="months" onchange="window.opener.updateoutput();">');
    for(var i=1;i<13;i++){
        newWin_doc.writeln('<option value="'+i+'">'+arrMonths[(i-1)]+'</option>');
    }
    newWin_doc.writeln('</select>&nbsp;');

    newWin_doc.getElementById('months').options.selectedIndex = parseInt(currentmonth);

    newWin_doc.writeln('<select id="years" onchange="window.opener.updateoutput();">');
            var styear = currentyear-1;
            for (var i=0; i <= 5; i++) {
                newWin_doc.write("<option id=\"yopt"+ i +"\" value=\""+ (styear+i) +"\">" + (styear+i) +"</option>\n");
            }
    newWin_doc.writeln('</select>&nbsp;');

    for (var i = 0; i < newWin_doc.getElementById('years').options.length; i++ )
    {
        if (newWin_doc.getElementById('years').options[i].value == ""+currentyear)
        {
              newWin_doc.getElementById('years').options.selectedIndex = i;
            break;
        }
    }

    newWin_doc.writeln('<input type=hidden id=\"including\" value="yes">');
    newWin_doc.writeln('</td></tr>');
    newWin_doc.writeln('<tr><td>');

        newWin_doc.writeln('<TABLE width="100%" cellpadding="1px" cellspacing="1px" bgcolor="#efefef">');
        newWin_doc.writeln('<TR>');
        newWin_doc.writeln('<TD align="center" class="tdHeader" bgcolor="#0056ca">Mo</TD>');
        newWin_doc.writeln('<TD align="center" class="tdHeader" bgcolor="#0056ca">Tu</TD>');
        newWin_doc.writeln('<TD align="center" class="tdHeader" bgcolor="#0056ca">We</TD>');
        newWin_doc.writeln('<TD align="center" class="tdHeader" bgcolor="#0056ca">Th</TD>');
        newWin_doc.writeln('<TD align="center" class="tdHeader" bgcolor="#0056ca">Fr</TD>');
        newWin_doc.writeln('<TD align="center" class="tdHeader" bgcolor="#0056ca">St</TD>');
        newWin_doc.writeln('<TD align="center" class="tdHeader" bgcolor="#0056ca">Su</TD>');
        newWin_doc.writeln('</TR>');
            newWin_doc.writeln("<tr>"); 
            for (var j=1;j<=42;j++){
                if (j==6 || j==7 || j==13 || j==14 || j==20 || j==21 || j==27 || j==28 || j==34 || j==35 || j==41 || j==42) {
                    newWin_doc.writeln("<td class='tdCell' align=\"center\" valign=\"middle\" bgcolor='#F5F5EF' onclick=\"window.opener.returndate("+(j-1)+");\" onmouseover=\"this.bgColor='#ffeeba';\" onmouseout=\"this.bgColor='#F5F5EF'\"><div id=\"c"+(j-1)+"\"></div></td>");
                } else {
                    newWin_doc.writeln("<td class='tdCell' align=\"center\" valign=\"middle\" bgcolor='#ffffff' onclick='window.opener.returndate("+(j-1)+");\' onmouseover=\"this.bgColor='#ffeeba';\" onmouseout=\"this.bgColor='#ffffff'\"><div id=\"c"+(j-1)+"\"></div></td>");
                }
                if ((j % 7) == 0) {newWin_doc.writeln("</tr>");}
            }
            newWin_doc.writeln("</tr>");
            newWin_doc.writeln('<input type=hidden id=\"dateformat\" value="'+df_array[0]+'">');
        newWin_doc.writeln('</TABLE>');

    newWin_doc.writeln('</td></tr></table></fieldset></div>');

    updateoutput();
    newWin_doc.writeln('</body></html>');


    if(navigator.appName != "Microsoft Internet Explorer")
      {
        newWin_doc.close();
      }
}