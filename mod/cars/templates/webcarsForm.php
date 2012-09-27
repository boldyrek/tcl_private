<script src="/js/jquery.js"></script>
<style type="text/css">
			.required_marker
			{
				color:#ff0000;
			}
			.title
			{
				font-weight: bold;
				width:25%;
			}
			td.title_division
			{
				padding:18px 0 5px 35px;
				background-color:#e9f0c4;
			}
			.live_input
			{
				width:auto;
			}
			.date_table td
			{
				border:none;
			}
			</style>
<script type="text/javascript">
function toggleAllEquipment(mode)
{
	var chkb = $(".optional_equipment_row :checkbox");
	if(mode)
		{
		chkb.attr("checked","checked");
		}else{
		chkb.removeAttr("checked");
		}
	return false;
}
			
function CheckMe(){
	if ($("#price").val()==''){
		alert ('Укажите цену!');
		return false;
	}
	
	if ($("#user_id").val()==''){
		alert ('Укажите ID пользователя!');
		return false;
	}
	
	
	return true;
}
			
</script>

<p><form id="webcars_form" onsubmit="return CheckMe();" action="?mod=cars&sw=webcarssave&car_id=<?=$this->view->id?>" method="post">
		<div class="cont_car" style="width:960px;">
			<h3>Данные по авто для webcars.kg</h3>
			<table width="945" border="0" cellpadding="3" cellspacing="0" class="list">
				<tr>
					<td colspan="2">
						<h3><a href="/?mod=cars&sw=form&car_id=<?=$this->view->id?>"><?=$this->view->year?> <?=$this->view->mark->name?> <?=$this->view->model->name?></a></h3>
						<input type="hidden" name="authkey" value="<?=$this->view->authkey?>">
						<input type="hidden" name="mark_id" value="<?=$this->view->mark->webcars_id?>">
						<input type="hidden" name="model_id" value="<?=$this->view->model->webcars_id?>">
					</td>
				</tr>
				<tr class="rowA title">
					<td colspan="2" class="title_division">Обязательные поля</td>
				</tr>
				<tr>
				<td colspan="2" style="padding-left:3%">
						<table class="form_group" cellspacing="0" width="90%">
						<tr class="rowA">
								<td class="title" align="right"><span class="required_marker">*</span>Руль:</td>
								<td>
									<select accept="true" id="ruletype_id" name="ruletype_id">
										<option value="1" >Левый</option>
										<option value="2" >Правый</option>
									</select>
								</td>
							</tr>
								
						<tr class="rowB">
								<td class="title" align="right"><span class="required_marker">*</span>ID владельца на сайте Webcars:</td>
								<td><input id="user_id" name="user_id" size="30" type="text" value="" /></td>
							</tr>
							
							<tr class="rowA">
								<td class="title" align="right">Тип кузова:</td>
								<td>
									<select id="carrosse_id" name="carrosse_id">
										<option value="1" selected='selected'>Седан</option>
										<option value="2" selected='selected'>Минивен</option>
										<option value="3" selected='selected'>Внедорожник</option>
										<option value="4" selected='selected'>Спорт</option>
										
									</select>
								</td>
							</tr>
							<tr class="rowB">
								<td class="title" align="right">Тип топлива:</td>
								<td>
									<select id="fuel_id" name="fuel_id">
										<option value="1">Безин</option>										
										<option value="2">Дизель</option>										
										<option value="3">Органика</option>										
										<option value="4">Газ</option>										
										<option value="5">Спирт</option>										
									</select>
								</td>
							</tr>
							<tr class="rowA">
								<td class="title" align="right">Коробка передач:</td>
								<td>
									<select id="kp_id" name="kp_id">
										<option value="1" >Автомат</option>
										<option value="2" >Механика</option>
									</select> 
								</td>
							</tr>
							<tr class="rowB">
								<td class="title" align="right">Привод:</td>
								<td>
									<select accept="true" id="privod_id" name="privod_id">
										<option value="1" >Передний</option>
										<option value="2" >Задний</option>
										<option value="3" >Полный</option>
									</select>
								</td>
							</tr>
							
							
						</table>
					</td>
				</tr>
				<tr class="rowA title">
					<td colspan="2" class="title_division">Поля</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-left:3%">
						<table class="form_group" cellspacing="0" width="90%">
							<tr class="rowA">
								<td class="title" align="right"><span class="required_marker">*</span>Цена:</td>
								<td>
									<input id="price" name="price" size="30" type="text" value="" />
								</td>
							
								<td class="title" align="right">Цвет:</td>
								<td> 
									<input id="color" name="color" size="30" type="text" value="" />
								</td>
							</tr>
							<tr class="rowB">
								<td class="title" align="right">Город:</td>
								<td>
									<select id="place_id" name="place_id">
										<option value="1" <?=(($this->view->place3=='5')?'selected':'')?>>Бишкек</option>
										<option value="2" <?=(($this->view->place3=='9')?'selected':'')?>>Алма-Ата</option>
									</select>
								</td>
							
								<td class="title" align="right">Год:</td>
								<td> 
									<input id="year" name="year" size="30" type="text" value="<?=$this->view->year?>" />
								</td>
							</tr>
							<tr class="rowA">
								<td class="title" align="right">V двигателя:</td>
								<td> 
									<input id="vengine" name="vengine" size="30" type="text" value="<?=$this->view->engine?>" />
								</td>
							
								<td class="title" align="right">Пробег:</td>
								<td> 
									<input id="trip" name="trip" size="30" type="text" value="<?=$this->view->milage?>" />
								</td>
							</tr>
							
							<tr class="rowB">
								<td class="title" align="right">Дополнительная информация:</td>
								<td colspan="3"> 
									<textarea name="additional" cols="20" rows="10"></textarea>
								</td>
							
							</tr>
							
							</table>
							</td>
				</tr>
				
				
				<tr class="rowA title">
					<td colspan="2" class="title_division">Опции:</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-left:3%">
						<table class="optional_equipment_row" cellspacing="0" width="90%">
							<tr class="rowB">
								<td style="padding-left: 11%" colspan="4">
									<a onclick="javascript:toggleAllEquipment(true); return false;" href="#">Выбрать все</a>&nbsp;&nbsp;
									<a onclick="javascript:toggleAllEquipment(false); return false;" href="#">Сбросить все</a>
								</td>
							</tr>
							<tr class="rowA">
								<td style="padding-left: 11%;padding-right:45px;" nowrap>
									<input name="options[]" id="listing_equipment_ids_12" type="checkbox" value="12" />
									<label for="listing_equipment_ids_12">CD Changer</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_1"  name="options[]" type="checkbox" value="1" />
									<label for="listing_equipment_ids_1">DVD</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_13"  name="options[]" type="checkbox" value="13" />
									<label for="listing_equipment_ids_13">Аэрбеги</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_8"  name="options[]" type="checkbox" value="8" />
									<label for="listing_equipment_ids_8">Камера заднего вида</label>&nbsp;
								</td>
							</tr>
							<tr class="rowB">
								<td style="padding-left: 11%;padding-right:45px;" nowrap>
									<input id="listing_equipment_ids_2"  name="options[]" type="checkbox" value="2" />
									<label for="listing_equipment_ids_2">Климат контроль</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_14"  name="options[]" type="checkbox" value="14" />
									<label for="listing_equipment_ids_14">Ксенон</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_7"  name="options[]" type="checkbox" value="7" />
									<label for="listing_equipment_ids_7">Кондиционер</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_6"  name="options[]" type="checkbox" value="6" />
									<label for="listing_equipment_ids_6">Монитор</label>&nbsp;
								</td>
							</tr>
							<tr class="rowA">
								<td style="padding-left: 11%;padding-right:45px;" nowrap>
									<input id="listing_equipment_ids_17"  name="options[]" type="checkbox" value="17" />
									<label for="listing_equipment_ids_17">Навигация</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_5"  name="options[]" type="checkbox" value="5" />
									<label for="listing_equipment_ids_5">Парктроники</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_4"  name="options[]" type="checkbox" value="4" />
									<label for="listing_equipment_ids_4">Подогрев  сидений</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_3"  name="options[]" type="checkbox" value="3" />
									<label for="listing_equipment_ids_3">Сигнализация</label>&nbsp;
								</td>
							</tr>
							<tr class="rowB">
								<td style="padding-left: 11%;padding-right:45px;" nowrap>
									<input id="listing_equipment_ids_10"  name="options[]" type="checkbox" value="10" />
									<label for="listing_equipment_ids_10">Центральный замок</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_16"  name="options[]" type="checkbox" value="16" />
									<label for="listing_equipment_ids_16">Электрические зеркала</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_15"  name="options[]" type="checkbox" value="15" />
									<label for="listing_equipment_ids_15">Электрические стеклоподъемники</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_11"  name="options[]" type="checkbox" value="11" />
									<label for="listing_equipment_ids_11">Электрический привод двери багажника</label>&nbsp;
								</td>
							</tr>
							<tr class="rowA">
								<td style="padding-left: 11%;padding-right:45px;" nowrap>
									<input id="listing_equipment_ids_9"  name="options[]" type="checkbox" value="9" />
									<label for="listing_equipment_ids_9">Электрический привод сидений</label>&nbsp;
								</td>
								<td>
									
								</td>
								<td>
									
								</td>
								<td>
									
								</td>
							</tr>
							<tr class="rowB">
								<td style="padding-left: 11%;padding-right:45px;" nowrap colspan="4">
									
								</td>
							</tr>
						</table>
					</td>
				</tr>
			
				<tr class="rowB title">
					<td colspan="2" class="title_division">Фотографии</td>
				</tr>
				<tr>
					<td colspan="2">
						<?=$this->view->photos?>
					</td>
				</tr>
				<tr>
					<td style="padding: 15px 0 35px 35px;">
						<input class="button" name="save" value="Отправить" type="submit"> &nbsp; &nbsp;
						
					</td>
				</tr>
			</table>
			</div>
	</form>