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
function CheckMe(){
    checkError = 0;

    $('span.required_marker').each(function(){

        myCaption = $(this).parent('td').html();
        myCaption = myCaption.replace('<SPAN class="required_marker">*</SPAN>', '');

        if ($(this).parent('td').next('td').children('input').val()==''){
            checkError = 1;
            alert('Поле '+myCaption+' не указано');
            return false;
        }

    });

    if (checkError == 1){
        return false;
    }
    return true;
    


}

</script>

<p><form id="webcars_form" onsubmit="return CheckMe();" action="?mod=cars&sw=repairsave&car_id=<?=$this->view->id?>" method="post">
		<div class="cont_car" style="width:960px;">
			<h3>Данные по авто для ремонтной базы</h3>
			<table width="945" border="0" cellpadding="3" cellspacing="0" class="list">
				<tr>
					<td colspan="2">
						<h3><a href="/?mod=cars&sw=form&car_id=<?=$this->view->id?>"><?=$this->view->year?> <?=$this->view->mark->name?> <?=$this->view->model->name?></a></h3>
						<input type="hidden" name="secret-key" value="<?=$this->view->authkey?>">
						<input type="hidden" value="miles" name="mileage_type" />
						<input type="hidden" value="<?=$this->view->id?>" name="source_id" />
						
						
						
					</td>
				</tr>
				<tr class="rowA title">
					<td colspan="2" class="title_division">Обязательные поля</td>
				</tr>
				<tr>
				<td colspan="2" style="padding-left:3%">
						<table class="form_group" cellspacing="0" width="90%">
						
						<tr class="rowA">
								<td class="title" align="right"><span class="required_marker">*</span>Марка:</td>
								<td>
									<input id="model" name="mark" size="30" type="text" value="<?=$this->view->mark->name?>" />
								</td>
					     </tr>
					     
					     <tr class="rowB">
								<td class="title" align="right"><span class="required_marker">*</span>Модель:</td>
								<td>
									<input id="model" name="model" size="30" type="text" value="<?=$this->view->model->name?>" />
								</td>
					     </tr>
						
					      <tr class="rowA">
								<td class="title" align="right"><span class="required_marker">*</span>VIN:</td>
								<td>
									<input id="vin" name="vin" size="30" type="text" value="<?=$this->view->frame?>" />
								</td>
					     </tr>
							
					    <tr class="rowB">
								<td class="title" align="right"><span class="required_marker">*</span>Год:</td>
								<td>
									<input id="year" name="year" size="30" type="text" value="<?=$this->view->year?>" />
								</td>
					     </tr>
					    
					     <tr class="rowA">
								<td class="title" align="right"><span class="required_marker">*</span>Объем двигателя:</td>
								<td>
									<input id="volume" name="volume" size="30" type="text" value="<?=$this->view->engine?>" />
								</td>
					     </tr>
					    
					     <tr class="rowB">
								<td class="title" align="right"><span class="required_marker">*</span>Пробег в милях:</td>
								<td>
									<input id="mileage" name="mileage" size="30" type="text" value="<?=$this->view->milage?>" />
								</td>
					     </tr>
					     
					     <tr class="rowA">
								<td class="title" align="right"><span class="required_marker">*</span>Цвет:</td>
								<td>
									<input id="color" name="color" size="30" type="text" value="" />
								</td>
					     </tr>
					     
					     <tr class="rowB">
								<td class="title" align="right"><span class="required_marker">*</span>Отделка салона:</td>
								<td>
									<select accept="true" id="salon_type" name="salon_type">
										<option value="leather">Кожанный</option>
										<option value="notleather">Не кожанный</option>
									</select>
								</td>
					     </tr>
					     
					      
					     <tr class="rowA">
								<td class="title" align="right"><span class="required_marker">*</span>Цвет салона:</td>
								<td>
									<input id="salon_color" name="salon_color" size="30" type="text" value="" />
								</td>
					     </tr>
					     
					     <tr class="rowB">
								<td class="title" align="right"><span class="required_marker">*</span>Тип привода:</td>
								<td>
									<select accept="true" id="gear_type" name="gear_type">
										<option value="4wd">4wd</option>
										<option value="2wd">2wd</option>
									</select>
								</td>
					     </tr>
					     
					     <tr class="rowA">
								<td class="title" align="right"><span class="required_marker">*</span>Тип руля:</td>
								<td>
									<select accept="true" id="rule_type" name="rule_type">
										<option value="left">Левый</option>
										<option value="right">Правый</option>
									</select>
								</td>
					     </tr>
					     
					      
					     <tr class="rowB">
								<td class="title" align="right"><span class="required_marker">*</span>Трансмиссия:</td>
								<td>
									<select accept="true" id="transmission" name="transmission">
										<option value="auto">Автомат</option>
										<option value="manual">Механика</option>
										<option value="gibrid">Гибрид</option>
									</select>
								</td>
					     </tr>
					     
					     
					     <tr class="rowA">
								<td class="title" align="right"><span class="required_marker">*</span>Тип топлива:</td>
								<td>
									<select accept="true" id="fuel_type" name="fuel_type">
										<option value="petrol">Бензин</option>
										<option value="diesel">Дизель</option>
									</select>
								</td>
					     </tr>
					     
					       
					     <tr class="rowB">
								<td class="title" align="right"><span class="required_marker">*</span>Начальная цена $:</td>
								<td>
									<input id="start_price" name="start_price" size="30" type="text" value="" />
								</td>
					     </tr>
					     
						
						<tr class="rowA">
							<td class="title" align="right">Дополнительная информация:</td>
							<td colspan="3"> 
								<textarea name="additional" cols="20" rows="10"></textarea>
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