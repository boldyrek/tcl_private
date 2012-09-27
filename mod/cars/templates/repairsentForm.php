<p></p>
		<div class="cont_car" style="width:960px;">
			<h3>Данные по авто для ремонтной базы</h3>
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
								<td class="title" align="right"><span class="required_marker">*</span>Посмотреть запись:</td>
								<td> <a href="http://repair.web.weltkind.com/cars/index/edit/id/<?=$this->view->sent_id?>" target="_blank">http://repair.web.weltkind.com/cars/index/edit/id/<?=$this->view->sent_id?></a> <br /></td>
							</tr>
						
							<tr class="rowB">
								<td class="title" align="right">Если машина была продана, перейдите по этой ссылке, чтобы пометить ее как проданную в ремонтной базе:</td>
								<td>
								<?if ($this->view->sold !=1):?>
								<a href="/?mod=cars&sw=repairsave&mode=markassold&car_id=<?=$this->view->id?>" target="_blank">Пометить как проданную</a>
								<?else:?>
								<p>машина помечена как проданная</p>
								<?endif;?>
								
								</td>
							</tr>
							
							<tr class="rowA">
								<td class="title" align="right">Если машина была удалена из базы, удалите пометку об отправке:</td>
								<td><a href="/?mod=cars&sw=repairsave&mode=delete&car_id=<?=$this->view->id?>" target="_blank">Удалить</a></td>
							</tr>
					</table>
				</td>
				</tr>
			</table>
			</div>