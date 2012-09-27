<?php

class InvoicesTemplates extends Mainview {
	var $root_path;
	var $translate;
	var $fieldList;
	var $selectCar; //переменная для массива машин
	var $customersList;
	var $id;
	var $servArr;
	var $clientId;
	function InvoicesTemplates($root_path, $lang)
	{
		$this->root_path=$root_path;
		$this->translate = Zend_Registry::get('translation');
		$this->setFieldList();
	}
	function setFieldList()
	{
		$this->fieldList = array(
            $this->translate->_('Название услуги'),
            $this->translate->_('Описание услуги'),
            $this->translate->_('Количество'),
            $this->translate->_('Цена'),
            $this->translate->_('Сумма')
        );
			//array('Название услуги', 'Описание услуги', 'Количество', 'Цена', 'Сумма');
	}

	function top_services_link($id=0)
	{
		$text = '<div class="location">
		<table width="98%" border="0" cellspacing="0" cellpadding="0" class="location_tab">
		<tr><td><a href="'.$this->root_path.'?mod=invoices&sw=">'.$this->translate->_('Список инвойсов').'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="'.$this->root_path.'?mod=services">'.$this->translate->_('Список услуг').'</a> | <a href="'.$this->root_path.'?mod=services&sw=form">'.$this->translate->_('Добавить').'</a></td>
		<td width="400" align="right"></td></tr></table>
		</div>';
		return $text;
	}
	function getTop()
	{
		return "<div class='invoices'>";
	}
	function getBottom()
	{
		return "</div>";
	}
	function getTitle()
	{
		return '<h2>'.$this->translate->_('Добавление/редактирование инвойса').'</h2>';
	}
	function getError($text='')
	{
		return "<div class='mes'>$text</div>";

	}
	function getCarSelect($car_id=false)
	{
//		if($this->clientId!=0) {
//			if(count($this->selectCar)>0) {
				$text= "<select name='carId' id='carId' style=width:auto>";
				$text.='<option value=0>'.$this->translate->_('Выберите машину').'</option>';
				foreach ($this->selectCar as $num=>$arr)
				$text.="<option value='".$arr['id']."' ".($arr['id']==$car_id?"selected":"").">".$arr['model']." - ".$arr['frame']."</option>\n";
				$text.="</select>";
//			}
//			else $text = 'у этого клиента нет автомобилей';
//		}
//		else $text = '-(  Загружается список )- <script>switchCarsList();</script>';
		return '<div id="carsList">'.$text.'</div>';
	}
	function getJquery()
	{
		$text = "<script src=../js/jquery.js></script>";
		$text.='
		  <script>
		  var url="/ajax/carsInfo.php";
		  $(document).ready(function(){
		    var id=0;
		    $("#carId").change(function () {
		    id=$("#carId").val();
			jQuery.ajax({
					type: "GET",
					url: url,
					data: "id="+id,
					success: function(response){
						$("#carInfo").html(response);
					},
					error: function(response) {
						//alert("'.$this->translate->_('Ошибка соединения с сервером!').'");
					}
				});		    
		    })
		        .change();
		
		  });
		  function switchCarsList() 
		 {	
		 	var owner = 1;
			// document.getElementById("client").value;
		 	$.get("/?mod=invoices&sw=carsforclient", { client: owner },
		 	function(data) {
		 		document.getElementById("carsList").innerHTML = data;
		 	} );
		 	
		 }
		  </script>
		
		';
		return $text;
	}

	function getServArr()
	{
		return 'var serviceJSON='.$this->php2js($this->servArr);
	}
	function getJs()
	{

		$text = '
		<script language="javascript">
		'.$this->getServArr().'
		var count ;
		
		function insRow()
		{
			count= $("#services > tbody > tr").length
			$("#services > tbody").append("<tr><td>"+createOpt()+"</td><td>"+createTextarea(-1)+"</td><td>"+createQuantity(-1)+"</td><td>"+createPrice(-1)+"</td><td></td></tr>");
			calc();
		}
		function remRow()
		{
			$("#services > tbody > tr:last").remove();
			calc();
		}
		function calc()
		{
	
		ln= $("#services > tbody > tr").length;
			sum=0;	
			for (i=0; i<ln; i++)
			{
				quant=$("#services > tbody > tr:eq("+i+") > td:eq(2) > input").val();	
				cost=$("#services > tbody > tr:eq("+i+") > td:eq(3) > input").val();	
				s=parseInt(quant)*parseFloat(cost);
				$("#services > tbody > tr:eq("+i+") > td:eq(4)").html(s);
				sum+=s;
			}
			paid=$("#opl").val();
			itog=sum-parseFloat(paid);
			
			$("#subitog > div").html(sum);
			$("#subitog > input").val(sum);
			$("#itog").val(itog);
		} 
		
		function getOptNum(val)
		{
			for (i=0; i<serviceJSON.length; i++)
			{
				if (val==serviceJSON[i].id)
				return i;
			}
			return -1;
			
		}
		function ChangeVal(num)
		{
		val=($("#services > tbody > tr:eq("+num+") > td:eq(0) > select > option:selected").val());
		i=(getOptNum(val));
		if (i!=-1)
		{
			
			$("#services > tbody > tr:eq("+num+") > td:eq(1) > textarea").val(serviceJSON[i].description);
			$("#services > tbody > tr:eq("+num+") > td:eq(2) > input").val(serviceJSON[i].quantity);
			$("#services > tbody > tr:eq("+num+") > td:eq(3) > input").val(serviceJSON[i].cost);
		}
		
		else
		{
		$("#services > tbody > tr:eq("+num+") > td:eq(1) > textarea").val("");
		$("#services > tbody > tr:eq("+num+") > td:eq(2) > input").val(0);
		$("#services > tbody > tr:eq("+num+") > td:eq(3) > input").val(0);
		}
		
			calc();
		}
		function createNum(count)
		{
			return "<input type=\"hidden\" value=\""+count+"\" name=\"num\">";
		}
		function createOpt()
		{
			res="<select name=\'serv_opt["+count+"]\' id=\'serv_opt["+count+"]\' onchange=\"ChangeVal("+count+")\">";
			res+="<option value=\"-1\">'.$this->translate->_('Выбирите услугу').'</option>";
			for (i=0; i<serviceJSON.length; i++)
			res+="<option value=\""+serviceJSON[i].id+"\">"+serviceJSON[i].item+"</option>";
			res+="</select>";
			return res;
		}
		function createTextarea(i)
		{
			res="<textarea id=\"serv_soll["+count+"]\" name=\"serv_soll["+count+"]\">";
			res+=((i==-1)?"":serviceJSON[i].description);
			res+="</textarea>";
			return res;
		}
		function createQuantity(i)
		{
		return "<input type=\"text\" name=\"quantity["+count+"]\" id=\"quantity["+count+"]\" value=\""+((i==-1)?0:serviceJSON[i].quantity)+"\" size=\"6\" onchange=\"calc()\">";
		}
		function createPrice(i)
		{
		return "<input type=\"text\" name=\"cost["+count+"]\" id=\"cost["+count+"]\" value=\""+((i==-1)?0:serviceJSON[i].cost)+"\" size=\"6\" onchange=\"calc()\">";
		}
		</script>
		
		<script>var myDateFormat = new Array("yyyy-mm-dd");</script>
		<script src="'.$this->root_path.'js/datepicker.js"></script>
		';
		return $text;
	}
	function createOpt($count,$val)
	{
		$res="<select name='serv_opt[".$count."]' id='serv_opt[".$count."]' onchange=\"ChangeVal(".$count.")\">";
		$res.='<option value="-1">'.$this->translate->_('Выбирите услугу').'</option>';
		foreach ($this->servArr as $num=>$arr)
		$res.="<option value=\"".$arr['id']."\" ".(($arr['id']==$val)?"selected":"").">".stripslashes(stripcslashes($arr['item']))."</option>";
		$res.="</select>";
		return $res;
	}
	function createTextarea($count, $val)
	{
		$res="<textarea id=\"serv_soll[".$count."]\" name=\"serv_soll[".$count."]\">";
		$res.=br2nl($val);
		$res.="</textarea>";
		return $res;
	}
	function createQuantity($count, $val)
	{
		return "<input type=\"text\" name=\"quantity[".$count."]\" id=\"quantity[".$count."]\" value=\"".$val."\" size=\"6\" onchange=\"calc()\">";
	}
	function createPrice($count, $val)
	{
		return "<input type=\"text\" name=\"cost[".$count."]\" id=\"cost[".$count."]\" value=\"".$val."\" size=\"6\" onchange=\"calc()\">";
	}
	function getTable($arr=false)
	{
		$text=false;
		if ($arr!=false and !empty($arr['serv_list']))
		foreach ($arr['serv_list'] as $num=>$array)
		{
			$text.="<tr><td>".$this->createOpt($num, $array['item_id'])."</td><td>".$this->createTextarea($num, stripslashes($array['description']))."</td><td>".$this->createQuantity($num,$array['quantity'])."</td><td>".$this->createPrice($num, $array['cost'])."</td><td>".$array['summ']."</td></tr>";
		}
		else{
			for ($num=0; $num<5; $num++)
			$text.="<tr><td>".$this->createOpt($num,'')."</td><td>".$this->createTextarea($num, '')."</td><td>".$this->createQuantity($num, 0)."</td><td>".$this->createPrice($num, 0)."</td><td>".(0)."</td></tr>";
		}
		return $text;
	}
function createNum($num)
{
	return "<input type='hidden' name='num' value='".$num."'>";
}
	function getDefSumm()
	{
		return $this->servArr[0]['quantity']*$this->servArr[0]['cost']*5;
	}
	function getForm($action, $arr=false)
	{
		$text= $this->getJs();
		$text.="
		
	<form method='post' action='$action' onsubmit='calc()'>
	<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" bgcolor='#ffffff'>
  <tr>
    <td>
        {$this->makeCustomerSelect()}<br><br>
		<span style=\"cursor:hand; cursor:pointer; color:#0066CC\" onclick=\"switchCarsList()\">".$this->translate->_('Получить список авто без инвойса')."
                </span>
        {$this->getCarSelect((($arr!=false)?$arr['carid']:false))}<br>
        <input type='checkbox' name='clientaccess' id='clientaccess'".(($arr!=false)&&$arr['access']?" checked":false)."><label for='clientaccess' style='cursor:hand; cursor:pointer;'>{$this->translate->_('показывать клиенту')}</label>
    </td>
	<td align=\"center\">
	".(($arr!=false)?"<a target='_blank' href='{$this->root_path}?mod=invoices&sw=file&inv_id=".$this->id."'>".$this->translate->_('Печатная версия')."</a><br><br><a href='{$this->root_path}?mod=invoices&sw=mail&inv_id=".$this->id."'>".$this->translate->_('Отправить на почту клиенту')."</a><input type='hidden' name='id' value='".$arr['id']."'>":"")."
	</td>
  </tr>
  <tr>
    <td colspan=2>
 <table width='100%' border='0' cellspacing='0' cellpadding='3'>
  <tr>
    <td bgcolor='#F7F5F4'><div id='carInfo'></div></td>
    <td width=320 align=right>
    <table width=\"300\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" bgcolor='#F1E8E4'>
          <tr>
            <td width=\"130\">".$this->translate->_('Номер инвойса').": </td>
            <td width=\"170\"><input type=\"text\" name=\"number\" value='".(($arr!=false)?$arr['number']:false)."'></td>
          </tr>
          <tr>
            <td>{$this->translate->_('Дата')}:</td>
            <td><input type=\"text\" name=\"dat\" id='dat' value='".(($arr!=false)?$arr['date']:false)."'><img src='".$this->root_path."img/ccl/cal.gif' border=0 onclick=\"show_calendar('dat', '', myDateFormat);\" class='datePicker' style='margin:0px;margin-bottom:-3px;'></td>
          </tr>
      </table>
      </td>
  </tr>
</table>   

    </td>
  </tr>
  <tr>
    <td colspan=2>
    <table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\"   bgcolor='#F7F5F4' id='services'>
    <thead>
      <tr bgcolor='#F1E8E4'>
        <td  width=150 align=center>{$this->fieldList[0]}</td>
        <td width=200 align=center>{$this->fieldList[1]}</td>
        <td width=60 align=center>{$this->fieldList[2]}</td>
        <td width=60 align=center>{$this->fieldList[3]}</td>
        <td width=60 align=center>{$this->fieldList[4]}</td>
      </tr>
      </thead>
      <tbody>
      ";

	$text.=$this->getTable($arr);


		$text.="</tbody>
    </table>
  <br><input type='button' onclick=\"insRow();\" style='width: 172px' value='".$this->translate->_('Добавить услугу')."' class='button'/> <input type='button' onclick=\"remRow();\" style='width: 172px' value='".$this->translate->_('Удалить услугу')."' class='button'/><br>
    </td>
  </tr>
  <tr>
    <td colspan=2><div align=\"right\">
      <table width=\"230\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" bgcolor='#E1DAD6'>
          <tr>
            <td width=\"84\">".$this->translate->_('Под итого').": </td>
            <td width=\"151\" id='subitog'><div>".(($arr!=false)?$arr['subitog']:0)."</div><input type='hidden' name='subitog' value='".(($arr!=false)?$arr['subitog']:0)."'></td>
          </tr>
          <tr>
            <td width=\"84\">".$this->translate->_('Оплачены').":</td>
            <td><input type='text' id='opl' name='opl' onchange='calc();' value='".(($arr!=false)?$arr['opl']:0)."' /></td>
          </tr>
          <tr>
            <td width=\"84\">".$this->translate->_('Итого').":</td>
            <td><input type='text' id='itog' name='itog' value='".(($arr!=false)?$arr['itog']:0)."' readonly style='border:0px;'></td>
          </tr>
          </table>
    </div></td>
  </tr>
  <tr>
	<td>
	".(($arr!=false)?"<a onclick='return confirm(\"".$this->translate->_('Удалить инвойс?')."\")' href='{$this->root_path}?mod=invoices&sw=delete&id=".$this->id."'>".$this->translate->_('Удалить инвойс')."</a><input type='hidden' name='id' value='".$arr['id']."'>":"")."
	
	
	</td>  
    <td align=right><input type=\"submit\" value=\"".$this->translate->_('Сохранить')."\" class='button'></td>
  </tr>
</table>
</form>
	
	
	";	
		return $text;
	}

	
	function makeCustomerSelect() {		//  onChange="switchCarsList();"
		$out = '<select name="client" id="client">
		<option value="0"> - - '.$this->translate->_('не выбран').' - - </option>
		';
		while($line=mysql_fetch_array($this->customersList)) {
			$out .= '<option value="'.$line['id'].'"';
			if($this->clientId==$line['id']) $out .= ' selected="selected"';
			$out .='>'.$line['name'].'</option>
			';
		}
		$out .= '</select>';
		return $out;
	}
}


?>