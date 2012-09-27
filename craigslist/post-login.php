<?php
$car_id=$_GET["car_id"];

if ($car_id<1) 
{
	echo "NO car_id";
	exit();
}
	

echo '
<form id="form1" name="form1" method="post" action="step1.php">
  <input type="hidden" name="car_id" value="'.$car_id.'">
  <label>
  <div align="center">
    <table width="200" border="0" cellpadding="5">
      <tr>
        <td colspan="2"><div align="center">Posting to Craigslist.Org</div></td>
      </tr>
      <tr>
        <td><span>Login: </span></td>
        <td><input name="login" type="text" id="textfield" /></td>
      </tr>
      <tr>
        <td><span>Password:</span></td>
        <td><input name="password" type="password" id="textfield2" /></td>
      </tr>
      <tr>
        <td><span>Phone:</span></td>
        <td><input name="phone" type="text" id="textfield3" /></td>
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