<p></p>
		<div class="cont_car" style="width:960px;">
			<h3>Данные по авто для webcars.kg</h3>
			<table width="945" border="0" cellpadding="3" cellspacing="0" class="list">
				<tr>
					<td colspan="2">
						<h3><a href="/?mod=cars&sw=form&car_id=<?=$this->view->id?>"><?=$this->view->year?> <?=$this->view->mark->name?> <?=$this->view->model->name?></a></h3>
					</td>
				</tr>
				<tr class="rowA title">
					<td colspan="2" class="title_division">Ваша машина уже отправлена</td>
				</tr>
				<tr>
				
				<td colspan="2" style="padding-left:3%">
						<table class="form_group" cellspacing="0" width="90%">
						<tr class="rowA">
								<td class="title" align="right"><span class="required_marker">*</span>Посмотреть объявление:</td>
								<td> <a href="http://webcars.kg/catalog/full/<?=$this->view->sent_id?>.html" target="_blank">http://webcars.kg/catalog/full/<?=$this->view->sent_id?>.html</a> <br /></td>
							</tr>
								
						<tr class="rowB">
								<td class="title" align="right">Продлить активацию:</td>
								<td><a href="http://webcars.kg/api/prolong/?authkey=<?=$this->view->authkey?>&id=<?=$this->view->sent_id?>" target="_blank">http://webcars.kg/api/prolog/</a></td>
							</tr>
							
							<tr class="rowA">
								<td class="title" align="right">Если объявление было удалено на сайте webcars, удалите пометку об отпарвке:</td>
								<td><a href="/?mod=cars&sw=webcarssave&mode=delete&car_id=<?=$this->view->id?>" target="_blank">Удалить</a></td>
							</tr>
					</table>
				</td>
				</tr>
			</table>
			</div>