<html>
<head>
<title>CraigsList.com posting form</title>
<link href="style.css" rel="stylesheet" type="text/css">
</head><body>
<?php
$car_id=$_GET["car_id"];

if ($car_id<1) 
{
	echo '<div align="center" class="error">NO <b>car_id</b> was specified!<br>
		Posible reason for this is that this vehicle was not set for sale.<br>
		<br><br>
		<a href="#" onclick="window.close()">Close window</a></div>';
	exit();
}
	

echo '
<form id="form1" name="form1" method="post" action="step1.php">
  <input type="hidden" name="car_id" value="'.$car_id.'">
  <label>
  <div align="center">
    <table width="250" border="0" cellpadding="5">
      <tr>
        <td colspan="2"><div align="center">Posting to Craigslist.Org</div></td>
      </tr>
      <tr>
        <td><span>E-mail: </span></td>
        <td><input name="email" type="text" id="textfield" value="dmitrii@makmalauto.com" /></td>
      </tr>
      <tr>
        <td><span>Phone:</span></td>
        <td><input name="phone" type="text" id="textfield3" value="6474355876" /></td>
      </tr>
      <tr>
        <td colspan="2"><div align="center">
          <input type="submit" name="button" id="button" value="Submit" />
        </div></td>
      </tr>
    </table>
    <br />
    <br />
    <br />
  </div>
  </label>
  <p>
    <label></label>
  </p>
  <p align="center">&nbsp; </p>
</form>

';	
?>
</body></html>