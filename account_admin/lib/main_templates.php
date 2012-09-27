<?
$header = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="'.$this->root_path.'css/style.css" rel="stylesheet" type="text/css">
<title>Makmal Auto - Cars&Clients</title>
</head>
<body>
';

//подвал				
$footer = '
</body></html>';

// форма авторизации

$login_form = '
<center>
	<br>
	<table border="0" cellspacing="0" cellpadding="0">
	<tr height="130">
	  <td valign="top"><img src="'.$this->root_path.'img/ccl/logo.jpg" width="298" /></td>
  </tr>
	<tr>
    <td valign="top"><div class="auth_form">
        <form action="'.$this->root_path.'" method="post" name="auth" id="auth">
          <table width="296" border="0" cellspacing="1" cellpadding="3">
            <tr>
              <td colspan="2" id="title" align="center">Здравствуйте!</td>
            </tr>
            <tr>
              <td colspan="2" height="10" class="notice"></td>
            </tr>
            <tr>
              <td width="100" align="right" class="caption">логин: </td>
              <td height="25"><input name="login" type="text" value="[%login%]" /></td>
            </tr>
            <tr>
              <td align="right" class="caption">пароль: </td>
              <td height="25"><input name="password" type="password" value="[%password%]" style="width:164px" ></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td height="25" class="caption"><input type="checkbox" name="save_login" id="store" style="border:0px; width:20px;" '.(($_COOKIE['muchachos']==1)?' checked="checked"':'').'> <label for="store" style="cursor:hand; cursor:pointer;">запомнить меня</login></td>
            </tr>
			<tr>
              <td>&nbsp;</td>
              <td height="25"><input type="submit" name="Submit" value="Войти" /></td>
            </tr>
			<tr>
              <td colspan="2" height="4" class="caption"></td>
            </tr>
          </table>
          <input type="hidden" name="auth" value="[%user_identificator%]" />
        </form>
    </div></td>
  </tr>
    <tr>
    <td align="center"><span style="color:#aaa; font-size:11px;">Cars&Clients v. '.$this->current_version.'<br>
    обновление: '.$this->last_updated.'</span></td>
  </tr>
</table>
<script language="javascript">
	if(document.auth.login.value==""){
		document.auth.login.focus();
	}
	else{
		document.auth.password.focus();
	}
</script>
</center>
';
?>