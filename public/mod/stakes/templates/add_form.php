<form method="post" action="/public/?mod=stakes&sw=add" name="addStakeFrom" class="myForm">
    <table width="500" border="0" cellspacing="0" cellpadding="2" bgcolor="#ffffff" class="list">
      <tr class="rowB">
        <td width="30">№ лота</td>
        <td width="250">аукцион</td>
        <td>ставка (тысяч йен)</td>
      </tr>
      <tr>
        <td><input name="lot" type="text" id="lot" size="6" value="[%lot_value%]" style="width:50px;"/></td>
        <td>[%auctions%]</td>
        <td><input name="sum" type="text" id="sum" size="4" maxlength="4" value="[%sum_value%]" style="width:40px;" />,000</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr class="rowB">
        <td>день</td>
        <td>месяц</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><input name="lot_date[d]" type="text" maxlength="2" id="lot_date" size="2" value="[%day_value%]"  style="width:30px;"/></td>
        <td>[%month%]</td>
        <td></td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>
      <tr class="rowB">
        <td colspan="3">марка, модель</td>
      </tr>
      <tr>
        <td colspan="3"><input name="model" type="text" id="model" value="[%model_value%]"/></td>
      </tr>
	  <tr class="rowB">
        <td colspan="3" align="center"> <p>Пожалуйста, заполните все поля
  </p></td>
      </tr>
      <tr>
        <td height="40" colspan="3" align="center"><input type="button" value="Добавить ставку" onClick="checkForm();" id="save"></td>
      </tr>
  </table>
</form>
<p class="notice">В случае ошибки, вы можете отменить ставку. Это можно сделать до того, как оператор ставку принял. </p>

