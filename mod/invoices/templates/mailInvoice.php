<html>
<head>
<title>INVOICE #<?=$this->info['number']?></title>
<style>
body, table {
	color: #000;
	font-size: 13px;
}
.th {
	background-color: #CCC;
	font-weight: bold;
	vertical-align: middle;
	text-align: center;
}
tr {
	vertical-align: top;
}
.fontFront {
 	font-family: Arial, Helvetica, sans-serif;
	font-size: 13px;
	color: #000;
}
.mainTable {

}
.mainTablePaid {
	background-repeat: no-repeat;
	background-position: top center;

}
.termsTable {
	border-left: 1px solid #000;
	border-right: 1px solid #000;
	border-bottom: 1px solid #000;
}
.header {
	border-right: 1px solid #000;
	border-bottom: 1px solid #000;
}
.headerLast {
	border-bottom: 1px solid #000;
}
.line {
	border-bottom: 1px solid #000;
}
.doubleLine {
	border-top: 3px double #000;
}
.rightLine {
	border-right: 1px solid #000;
}
.boxHead {
	background-color: #ccc;
	border-right: 1px solid #000;
	border-bottom: 1px solid #000;
}
.box {
	border-bottom: 1px solid #000;
}
.boxBorderR {
	border-right: 1px solid #000;
}
.boxHeadLast {
	background-color: #ccc;
	border-right: 1px solid #000;
}
.dotted {
	border-bottom: 1px dotted #000;
}
.leftPad {
	padding-left: 7px;
}
.rightPad {
	padding-right: 10px;
}
.smallBox {
	border-top: 1px solid #000;
	border-left: 1px solid #000;
	border-right: 1px solid #000;
}
.topBg {
	background-repeat: no-repeat;
	background-position: top center;
	margin-top: 20px;
	font-size: 11px;
 	font-family: Arial, Helvetica, sans-serif;
	line-height: 14px;
}
.showTop {
	border: 1px solid #000;
	border-bottom: 0;
}
.showNotes {
	border-left: 1px solid #000;
	border-right: 1px solid #000;
}
.showTotals {
	border: 1px solid #000;
	border-top: 2px solid #000;
}
#mainTable tbody td{
padding:5px;
}
.notifyBox { font-family: Verdana, sans-serif; }
</style>
</head>
<body>
<table width="629" align="center" cellpadding="0" cellspacing="0" class="topBg">
  <tr>
    <td>

<table width="100%" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="256" height="125" class="fontFront">
	Makmal North America<br>
	85 West Wilmot street Unit 4 <br>
	Richmond Hill, Ontario<br>
	Canada, L4B1K7<br>
	Phone: 4168405849<br>
    </td>
    <td valign="top">
		  <div align="left" style="font-family:arial;font-size:21pt;color:#CCCCCC;font-weight:bold;">INVOICE</div>
	</td>
  </tr>
  <tr>
    <td height="100" valign="top" class="fontFront">
	<br><?=$this->info['name']?><br>	<br><br>
	<br>	<br>    </td>
    <td>
	  <table width="280" align="right" cellpadding="2" cellspacing="0" class="smallBox">
		<tr>
		  <td width="150" class="boxHead"><strong>Invoice #:</strong></td>
		  <td width="130" align="right" class="box"><?=$this->info['number']?></td>
		</tr>
		<tr>
		  <td class="boxHead"><strong>Date:</strong></td>
		  <td align="right" class="box"><?=$this->info['date']?></td>
		</tr>
		<tr>
		  <td class="boxHead"><strong>Amount Due USD:</strong></td>
		  <td align="right" class="box">$<?=$this->info['itog']?></td>
		</tr>
		      </table>
    </td>
  </tr>
   <tr>
    <td height="30" class="dotted" colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" colspan="2">&nbsp;</td>
  </tr>
  </table>

<div style=" min-height: 180px;" class="showTop mainTable">
<table width="99%" align="center" cellpadding="2" cellspacing="0" id="mainTable">
<thead>  
<tr class="th">
    <td width="95" height="25" class="header">Item</td>
    <td class="header">Description</td>
    <td width="80" class="header">Unit Cost ($)</td>
    <td width="50" class="header">Quantity</td>
    <td width="80" class="headerLast">Price ($)</td>
  </tr>
  </thead>
  <? foreach ($this->info['serv_list'] as $num=>$arr) {?>
  <tbody>
  <tr>
    <td width="95" height="25"><?=$arr['item']?></td>
    <td><?=$arr['description']?></td>
    <td width="80" align="right"><?=$arr['cost']?></td>
    <td width="50" align="center"><?=$arr['quantity']?></td>
    <td width="80" align="right"><?=$arr['summ']?></td>
  </tr>
  </tbody>
  <?}?>
</table>

</div>
</body>
</html>



<table width="100%" cellpadding="0" cellspacing="0" class="showTotals">
  <tr>
    <td height="30" colspan="5" style="padding: 0;">

<table width="100%" cellpadding="2" cellspacing="0">
  <tr align="right">
    <td width="314" align="left" valign="top" colspan="2" rowspan="9" class="rightLine" style="padding: 10px;">&nbsp;</td>
    <td width="235" height="10" colspan="2"><strong>Subtotal:</strong></td>
    <td width="80" class="rightPad"><?=$this->info['subitog']?></td>
  </tr>
  <tr align="right">
    <td height="10" colspan="2" style="border-top: 1px solid #000000;"><strong>Total:</strong></td>
    <td class="rightPad" style="border-top: 1px solid #000000;"><?=$this->info['subitog']?></td>
  </tr>
  <tr align="right">
    <td height="10" colspan="2"><strong>Amount Paid:</strong></td>
    <td style="padding-right: 10px;">-<?=$this->info['opl']?></td>
  </tr>
  <tr align="right">
    <td height="30" bgcolor="#BBBBBB" colspan="2" class="doubleLine"><strong>Balance Due USD:</strong></td>
    <td bgcolor="#BBBBBB" class="rightPad doubleLine">$<?=$this->info['itog']?></td>
  </tr>
</table>

    </td>
  </tr>
</table>


	<table width="100%" align="center" cellpadding="10" cellspacing="0" class="termsTable">
	  <tr>
		<td align="center"><strong>Wiring instructions:
<br />The Bank of Nova Scotia
<br />Institution code #002
<br />
<br />Richmond Hill Main
<br />10850 Yonge Street Unit #1
<br />Richmond Hill, ON L4C 3E4
<br />
<br />Swift code: NOSCCATT
<br />Routing or ABA #026002532
<br />
<br />Beneficiary name:
<br />MAKMAL NORTH AMERICA CO
<br />Beneficiary address: 92 Dovetail Dr Richmond Hill ON L4E5A7
<br />Account #308820139319
<!--<br>HSBC Bank Canada
<br>Richmond Hill Branch
<br>330 Highway #7 East, Unit 111
<br>Richmond Hill, Ontario L4B 3P8
<br>SWIFT Code: HKBC CATT
<br>Account Number: 10122-016-400399-070 (US Funds)-->
</strong>
                </td>
	  </tr>
	</table>
	
	</td>
  </tr>
</table>