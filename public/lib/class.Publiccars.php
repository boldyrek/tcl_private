<?

include_once($_SERVER['DOCUMENT_ROOT'].'/mod/cars/class.Carscomment.php');

class PublicCars extends Proto {

    var $car_id = '';

    public function makePage()
    {
        $this->setCarId();
        $this->page .= $this->templates['header'];
        if ($this->checkAuth())
        {
            $this->page .= $this->makeTopMenu();
            $this->moduleContent();
        }
        else
        Proto::redirect('/');

        $this->page .= $this->templates['footer'];

        if ($this->page == '')
        $this->errorHandler('Пустая страница!', 0);
        $this->errorsPublisher();
        $this->publish();
    }

    private function setCarId()
    {
        if (isset($_GET['car_id']))
        $id = intval($_GET['car_id']);
        else
        $id = '';
        $this->car_id = $id;
    }

    private function moduleContent()
    {
        if (isset($_GET['sw']))
        $switch = $_GET['sw'];
        else
        $switch = '';
        if ($this->exists($switch))
        {
            switch ($switch)
            {
                case 'form':
                    $this->drawForm();
                    break;
                case 'save':
                    $this->save();
                    break;
                case 'comment':
                    $comment = new CarsComment();
                    $this->page .= $comment->getContent();
                    break;
                case 'umsave':
                    $this->saveUserMileage();
                    break;
                case 'umstatus':
                    $this->updateUserMileageStatus();
                    break;
                case 'photo':
                    $this->setMainPhoto();
                    break;
                case 'country':
                    $this->setCountry();
                    break;
                default:
                    $this->drawList();
                    break;
            }
        }
        else
        {
            $this->drawList();
        }
    }

    public function drawList()
    {

        $this->place[0] = '';
        /*
        $dealership = mysql_fetch_array($this->mysqlQuery("
        SELECT dealer
        FROM `ccl_".ACCOUNT_SUFFIX."".ACCOUNT_SUFFIX."customers`
        WHERE `id` = '".$_SESSION['user_id']."'"));
        */

        $addreq = '';
        if ($_SESSION['user_type'] == '11')
        $addreq = ' OR `place_id1` = 9';

        $addsold = " `ccl_".ACCOUNT_SUFFIX."cars`.sold != '1' AND ";
        if (isset($_SESSION['show_sold']) and $_SESSION['show_sold'] == 'show')
        $addsold = '';

        $request = "
		SELECT `ccl_".ACCOUNT_SUFFIX."cars`.*, `ccl_".ACCOUNT_SUFFIX."customers`.name as customer_name
		    FROM `ccl_".ACCOUNT_SUFFIX."cars`
		    LEFT JOIN `ccl_".ACCOUNT_SUFFIX."customers`
		        ON `ccl_".ACCOUNT_SUFFIX."cars`.buyer = `ccl_".ACCOUNT_SUFFIX."customers`.id
		    WHERE ".$addsold."
		        (`buyer`='".$_SESSION['user_id']."'
			
		        ".$addreq.")";

        //		if($dealership['dealer'] == '1') $request .= " or `dealer` = '".$_SESSION['user_id']."'";
        $request .= " ORDER BY `sold` ASC, `buy_date` DESC";

        $content = $this->mysqlQuery($request);

        $num = mysql_num_rows($content);
        if ($num > 0)
        {
            $class = "rowA rowB";

            $this->page .= '
			<script type="text/javascript" src="/js/jquery.js"></script>
			<script type="text/javascript">
            $(document).ready(function() {
                $("#legend").hide();
                $("span#legend-toggle").click(function() {
                    $("#legend").toggle("fast");
                    return false;
                });
            });
            function include_sold_cars(){
				var url = "/public/?mod=showsold";
				var state = $("#show_sold_cars").attr("checked")?1:0;
				jQuery.ajax({
					type: "GET",
					url: url,
					data: "set="+state,
					success: function(result){
						location.reload(true);
					},
					error: function(result){
						location.reload(true);
					}
				});
			}
			</script>
			<div class="location"><div style="width:300px;float:left;">'.$this->translate->_('Ваши автомобили').'</div>
				<input type="checkbox" id="show_sold_cars"'.((isset($_SESSION['show_sold']) and $_SESSION['show_sold'] == 'show') ? ' checked="yes"' : '').' onclick="include_sold_cars()"><label for="show_sold_cars">'.$this->translate->_('Отображать проданные автомобили').'</label>
                <span id="legend-toggle" style="cursor:pointer;color:blue;text-decoration:underline;padding-left:70px">'.$this->translate->_('Обозначение цветов').'</span>
                <div id="legend" style="position:absolute;left:600px;width:200px;">
                    <div><div class="greenTR" style="background-color:white;width:27px;height:20px;float:left;border: solid black 1px;"></div><div style="padding-top:7px;"> - Not yet delivered cars</div></div><br>
                    <div><div class="greenTR" style="width:27px;height:20px;float:left;border: solid black 1px;"></div><div style="padding-top:7px;"> - Delivered cars</div></div><br>
                    <div><div class="orangeTR" style="width:27px;height:20px;float:left;border: solid black 1px;"></div><div style="padding-top:7px;"> - Sold cars</div></div><br>
                    <div><div class="redTR" style="width:27px;height:20px;float:left;border: solid black 1px;"></div><div style="padding-top:7px;"> - Declined cars</div></div><br>
                </div>
			</div>
				<table width="970" border="0" cellspacing="0" cellpadding="0" class="list">
			 	 <tr class="title">
			    <td width="80">'.$this->translate->_('дата покупки').'</td>
			    <td>'.$this->translate->_('модель').'</td>
				<td width="110">'.$this->translate->_('клиент').'</td>
			    <td width="120">'.$this->translate->_('вин код').'</td>
			    <td width="90">'.$this->translate->_('всего<br>за автомобиль').'</td>
			    <td width="70">'.$this->translate->_('оплачено').'</td>
			    <td width="120">'.$this->translate->_('место нахождения').'</td>
				<td>&nbsp;</td>
			  </tr>';

            $i = 1;
            while ($i <= $num)
            {
                $line = mysql_fetch_array($content);
                //	getting paid summ for specified car
                $line2 = mysql_fetch_array(mysql_query("SELECT SUM(amount) as paid FROM ccl_".ACCOUNT_SUFFIX."accounting
						WHERE car = '".$line['id']."' AND type = 1"));
                if ($line['delivered'] == 1)
                $class = "greenTR"; // Green back if car has been delivered
                if ($line['type'] == 3)
                $class = "redTR";    // Red if type == 3 =)
                if ($line['sold'] == 1)
                $class = "orangeTR"; // Orange, if car has been sold
                $this->page .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'"  onclick="document.location=\''.$this->root_path.'public/?mod=cars&sw=form&car_id='.$line['id'].'\'">
					<td>'.$line['buy_date'].'&nbsp;</td>
					<td>'.$line['model'].'&nbsp;</td>
					<td>'.$line['customer_name'].'&nbsp;</td>
					<td>'.$line['frame'].'&nbsp;</td>
					<td>'.$line['total'].'&nbsp;</td>
					<td>'.$line2['paid'].'&nbsp;</td>
					<td>'.($line['total'] - $line2['paid']).'&nbsp;</td>
					<td>'.$line['year'].'&nbsp;</td>
					<td>'.$this->place[$line['place_id1']].'&nbsp;</td>
					<td>&nbsp;</td>
					</tr>';
                $i++;
                if ($class == "rowA")
                $class = "rowA rowB"; else
                $class="rowA";
            }

            $this->page .= '</table>';
        }
        else
        $this->page .= '<div class="notice">'.$this->translate->_('У вас пока не добавлено ни одного автомобиля!').'</div>';
    }

    public function drawForm()
    {
        if ($this->car_id != '0' and $this->car_id != '')
        {
            $content = mysql_fetch_array($this->mysqlQuery("
			SELECT ccl_".ACCOUNT_SUFFIX."cars.*,ccl_".ACCOUNT_SUFFIX."cars.sold as car_is_sold,
                           ccl_".ACCOUNT_SUFFIX."containers.loaddate, ccl_".ACCOUNT_SUFFIX."containers.sent, ccl_".ACCOUNT_SUFFIX."containers.portdate, ccl_".ACCOUNT_SUFFIX."containers.station, ccl_".ACCOUNT_SUFFIX."containers.rail, ccl_".ACCOUNT_SUFFIX."containers.number, ccl_".ACCOUNT_SUFFIX."containers.bishkek,
                           ccl_".ACCOUNT_SUFFIX."forsale.id as sell_id, ccl_".ACCOUNT_SUFFIX."forsale.sold as sell_sold, ccl_".ACCOUNT_SUFFIX."forsale.price as sell_price, ccl_".ACCOUNT_SUFFIX."forsale.comment as sell_comment, ccl_".ACCOUNT_SUFFIX."forsale.comment_admin as sell_comment_admin,
                           ccl_".ACCOUNT_SUFFIX."places.name as destination,
                           ccl_".ACCOUNT_SUFFIX."auctions.name as auction_name
			FROM `ccl_".ACCOUNT_SUFFIX."cars` 
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."containers` 
			ON ( ccl_".ACCOUNT_SUFFIX."containers.id = ccl_".ACCOUNT_SUFFIX."cars.container ) 
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."forsale`
			ON (ccl_".ACCOUNT_SUFFIX."forsale.car = ccl_".ACCOUNT_SUFFIX."cars.id)
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."auctions`
			ON (ccl_".ACCOUNT_SUFFIX."auctions.id = ccl_".ACCOUNT_SUFFIX."cars.auction)
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."places`
			ON (ccl_".ACCOUNT_SUFFIX."places.id = ccl_".ACCOUNT_SUFFIX."cars.place_id3)
			WHERE ccl_".ACCOUNT_SUFFIX."cars.id = '".$this->car_id."' LIMIT 1"));

            if ($this->validateOwnership($content))
            {
                $photos_sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars_photos` WHERE `car` = '".$this->car_id."' ORDER BY `folder` DESC";
                $photos_sql = $this->mysqlQuery($photos_sql);

                $auction = mysql_fetch_array($this->mysqlQuery("
				SELECT * 
				FROM `ccl_".ACCOUNT_SUFFIX."cars_cert` 
				WHERE `car` = '".$this->car_id."'"));

                $invoice = mysql_fetch_array($this->mysqlQuery("
				SELECT `id` 
				FROM `ccl_".ACCOUNT_SUFFIX."invoices` 
				WHERE `carid` 
				= '".$this->car_id."' 
				LIMIT 1"));

                //echo $invoice;
                //echo $sql;

                $export_certificate = mysql_fetch_array($this->mysqlQuery("
				SELECT * 
				FROM `ccl_".ACCOUNT_SUFFIX."certificates`
				WHERE `car` = '".$this->car_id."'"));
                if ($content['allow_inspection'] == 1)
                $post_sale = $this->mysqlQuery("
				SELECT * FROM `ccl_".ACCOUNT_SUFFIX."inspections`
				WHERE `car` = '".$this->car_id."'");

                $carfax_data = $this->mysqlQuery("
				SELECT * FROM ccl_".ACCOUNT_SUFFIX."autocheck
				WHERE `car` = ".$this->car_id);

                if ($content['allow_codocs'] == 1)
                {
                    $addocs = mysql_query("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."adddoc` WHERE `car` = '".$this->car_id."' ORDER BY `id` DESC");

                    // сопроводительные документы, если разрешили ;)
                    $codocs = '<b>'.$this->translate->_('Сопроводительные документы:').'</b><br>';
                    while ($line = mysql_fetch_array($addocs))
                    {
                        $codocs .= $this->wrapFile('
						<a href="'.$this->root_path.'photos/'.$this->car_id.'/'.$line['file'].'" target="_blank"><img src="'.$this->root_path.'img/ico/'.fileIco($line['file']).'" vspace="5" border="0"></a>
						');
                    }
                }
                else
                $codocs = '';

                $payments = mysql_fetch_array($this->mysqlQuery("SELECT SUM(amount) as total FROM `ccl_".ACCOUNT_SUFFIX."accounting` WHERE `car` = '".$this->car_id."' AND `type`=1"));

                $client_form = '';
                $form_end = '';

                $tmp_place = $this->place;
                $places1 = isset($tmp_place[$content["place_id1"]]) ? $tmp_place[$content["place_id1"]] : "не известно";
                $places2 = isset($tmp_place[$content["place_id2"]]) ? $tmp_place[$content["place_id2"]] : "не известно";

                $places3 = mysql_fetch_assoc($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."places` WHERE `id`='".$content["place_id3"]."'"));
                if (!isset($places3["name"]))
                $places3["name"] = $this->translate->_('не известно');

                $client = @mysql_fetch_array($this->mysqlQuery("
					SELECT `name` 
					FROM `ccl_".ACCOUNT_SUFFIX."customers` 
					WHERE `id` = '".$content['buyer']."'"));

                $reciever = @mysql_fetch_array($this->mysqlQuery("
 				SELECT `name`
 				FROM `ccl_".ACCOUNT_SUFFIX."recievers`
 				WHERE `id` = '".$content['reciever']."'"));

                //проверяем есть ли сохраненный инвойс
                if ($invoice['id'] != '')
                {

                    $show_invoice = '<a href="'.$this->root_path.'public/?mod=print&what=invoice_file&inv_id='.$invoice['id'].'" target="_blank">'.$this->translate->_('инвойс').'</a>';
                }
                else
                $show_invoice = '';

                if ($export_certificate['id'] != '')
                {
                    $export_cert_link = '<a href="'.$this->root_path.$this->user_folder.'?mod=export_certificate&id='.$this->car_id.'" target="_blank">'.$this->translate->_('экспортный сертификат').'</a>';
                }
                else
                $export_cert_link = '';

                //делаем список фотографий
                $photos = array();
                $photos_list = '';
                $num = mysql_num_rows($photos_sql);
                if ($num != 0)
                {
                    $j = 1;

                    while ($j <= $num)
                    {
                        $line = mysql_fetch_array($photos_sql);

                        $photos[$line['folder']] .= '
                  <div class="photo-wrapper" id="photo-wrapper-'.$line['id'].'" style="float:left; width:128px; padding:2px; border:1px solid #ddd;text-align:center; margin-right:4px; margin-bottom:5px;'.($line['id'] == $content['top_photo'] ? 'background-color:#d2f4d9' : '').'">
                     <input style="cursor:pointer" type="radio" name="photos" class="photos" id="photo-'.$line['id'].'" value="'.$line['id'].'" '.($line['id'] == $content['top_photo'] ? 'checked="checked"' : '').' />
                     <a href="'.$this->root_path.'photos/'.$this->car_id.'/'.$line['file'].'" target="_blank">
                        <img src="'.$this->root_path.'photos/'.$this->car_id.'/thumb/'.$line['file'].'" border="0" />
                     </a>
                     <br />
                     <a href="/?mod=cars&sw=delete&what=photo&name='.$line['file'].'&chk='.$this->car_id.'&from=client" class="delete" onclick="return confirm(\''.$this->translate->_('Действительно удалить?').'\')">'.$this->translate->_('удалить').'</a>
                  </div>';

                        $j++;
                    }

                    foreach ($this->photo_folders as $k => $v)
                    {

                        if ($photos[$k] != '')
                        {
                            $photos_list .= '<table width="100%" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #ccc; margin-bottom:10px;"><tr><td bgcolor="#eeeeee" align="center" class="rowB">'.$v.'</td>
							</tr>
							<tr><td>'.$photos[$k].'</td></tr></table>';
                        }
                    }
                }
                else
                $photos_list = '';

                //выводим Отчет о состоянии автомобиля или CR, если есть
                if ($auction['file'] != '')
                {
                    $auc_list = '<a href="'.$this->root_path.'photos/'.$this->car_id.'/'.$auction['file'].'" target="_blank"><img src="'.$this->root_path.'img/ccl/attached.gif" border="0"></a>';
                }
                else
                $auc_list = '';
                /*
                //выводим Отчет о состоянии автомобиля или CR, если есть
                if($auction['file']!='')
                {
                $auc_list = '<a href="'.$this->root_path.'photos/'.$this->car_id.'/'.$auction['file'].'" target="_blank"><img src="'.$this->root_path.'img/ccl/attached.gif" border="0"></a>';
                }
                else $auc_list = '';
                */
                if ($content['allow_inspection'] == 1)
                {
                    // после-продажная инспекция, если разрешили ;)
                    $ps_inspection = '';
                    while ($line = mysql_fetch_array($post_sale))
                    {
                        $ps_inspection .= $this->wrapFile('
						<a href="'.$this->root_path.'photos/'.$this->car_id.'/'.$line['file'].'" target="_blank"><img src="'.$this->root_path.'img/ico/'.fileIco($line['file']).'" vspace="5" border="0"></a>
						');
                    }
                }
                else
                $ps_inspection = '';

                $show_carfax = '';
                if ($content['allow_carfax'] == 1)
                {
                    // после-продажная инспекция, если разрешили ;)
                    $show_carfax = '';
                    while ($line = mysql_fetch_array($carfax_data))
                    {
                        $show_carfax .= $this->wrapFile('
						<a href="'.$this->root_path.'photos/'.$this->car_id.'/'.$line['file'].'" target="_blank"><img src="'.$this->root_path.'img/ico/'.fileIco($line['file']).'" vspace="5" border="0"></a>
						');
                    }
                }
                else
                $show_carfax = '';

                $umQuery = $this->mysqlQuery("SELECT * FROM `ccl_user_mileage` WHERE `car_id` = ".$this->car_id." LIMIT 1");
                $umResult = mysql_fetch_object($umQuery);

                $this->page .= '
		<script src="/js/jquery.js"></script>
		<script>
		function mark_car_as_sold(){
			var url = "/public/?mod=marksold&carid='.$this->car_id.'";
			var state = $("#car_is_sold").attr("checked")?1:0;
			jQuery.ajax({
				type: "GET",
				url: url,
				data: "set="+state,
				success: function(result){
					if(result=="reloadpage") { location.reload(true); }
					if(result=="marked") { $("#car_is_sold").attr("checked",true);}
					if(result=="unmarked") { $("#car_is_sold").attr("checked",false);}
				},
				error: function(result){
					location.reload(true);
				}
			});
		}
                $(function()
                {
                   um = $("#user-mileage");
                   ums = $("#user-mileage-status");
                   umsw = $("#user-mileage-status-wrapper");
                   
                   $("#save-user-mileage").click(function()
                   {
                      if ($.trim(um.val()) != "")
                      {
                         if (um.val() == 0)
                         {
                            um.val("");
                            ums.removeAttr("checked");
                            umsw.hide();
                         }
                         else
                         {
                            umsw.show();
                         }
                         
                         $.get("/public/?mod=cars&sw=umsave", {car_id:'.$this->car_id.',mileage:um.val()}, function(response)
                         {
                            if (response == "1")
                            {
                               $("#user-mileage-message")
                               .fadeIn()
                               .css("color","green")
                               .html("'.$this->translate->_('пробег успешно сохранен/удален').'")
                               .fadeOut();
                            }
                            else
                            {
                               $("#user-mileage-message")
                               .fadeIn()
                               .css("color","red")
                               .html("'.$this->translate->_('ошибка при сохранении/удалении').'")
                               .fadeOut();
                            }
                         });
                      }
                      else
                      {
                         alert("Empty value");
                         um.val("").focus();
                      }
                   });

                   ums.change(function()
                   {
                      $.get("/public/?mod=cars&sw=umstatus", {car_id:'.$this->car_id.', status:this.checked}, function(response)
                      {
                         
                      });
                   });

                   photos = $("input.photos");

                   photos.click(function()
                   {
                      var pid = this.value;

                      $.get("/public/?mod=cars&sw=photo", {id:'.$this->car_id.', photo_id:pid}, function(response)
                      {
                         if (response == 1)
                         {
                            $(".photo-wrapper").css("backgroundColor","#fff");
                            $("#photo-wrapper-"+pid).css("backgroundColor","#d2f4d9");
                            photos.removeAttr("checked");
                            $("#photo-"+pid).attr("checked","checked");
                         }
                      });
                   });

                   st = $("#sell-template");

                   st.change(function()
                   {
                      var commentBox = $("#sellComment");

                      if (this.value != "")
                      {
                         var params = this.value.split("-");
                         
                         $.get("/public/?mod=tpl&sw=view", {access:params[0],id:params[1]}, function(response)
                         {
                            commentBox.val(response);
                         });
                      }
                      else
                      {
                         commentBox.val($("#sellCommentOriginal").val());
                      }
                   });

                   $("#showAdminComment").click(function()
                   {
                      $("#sellComment").val(this.checked ? $("#sellCommentAdmin").val() : $("#sellCommentOriginal").val());
                      
                      if (! this.checked)
                      {
                         $(st.children(":first")).attr("selected", "selected");
                      }
                   });

                   $("#add-picture").click(function()
                   {
                      var lastId = $("#files :file:last").attr("name").split("-")[1];
                      var nextId = parseInt(lastId)+1;
                      var content = \'<input type="file" name="file-\'+nextId+\'" style="display:block" />\';
                      $(content).appendTo("#files");
                   });
                   
                   $("#country").change(function(){
                      $.get("/public", {mod:"cars",sw:"country",country_id:this.value,id:'.$this->car_id.'}, function(response){
                         alert("'.$this->translate->_('Страна успешно обновлена').'");
                      });
                   });
                   
                   $("#check-date").click(function(){
                      $("#check-date-response")
                        .show()
                        .html("проверка...")
                        .css({color:"black", fontWeight:"normal"});
                      $.getJSON("/cron/grabber/export/date", {vincode:$(this).attr("vincode")}, function(json){
                         $("#check-date-response")
                           .html(json.date_made)
                           .css({color:"green", fontWeight:"bold"})
                           .click(function(){
                              $(this).hide();
                           });
                      });
                   });
                });
		</script>
			<div class="cont_car">
			<h3>'.$this->translate->_('Автомобиль').'</h3>
			<table width="692" border="0" cellpadding="0" cellspacing="0" class="list">
			<tr>
				<td align="right" class="title rowB">'.$this->translate->_('покупатель').'</td>
				<td class="rowA rowB title">'.$client['name'].'</td>
				<td class="title rowB" align="right">'.$this->translate->_('аукцион').'</td>
				<td class="title rowB">'.stripslashes($content['auction_name']).'</td>
			</tr>
			  <tr>
				<td width="113" align="right" class="title">'.$this->translate->_('получатель').'</td>
				<td width="202" class="rowA title">'.$reciever['name'].'</td>
				<td align="right" class="title"></td>
				<td class="rowA title" width="200"></td>
			  </tr>
			  <tr>
				<td align="right" class="title rowB">'.$this->translate->_('название').'</td>
				<td class="rowA rowB title">'.$content['model'].'</td>
				<td align="right" class="title rowB">'.$this->translate->_('всего за автомобиль').'</td>
				<td class="rowA rowB title">'.$content['total'].'</td>
			  </tr>
			  <tr>
				<td align="right" class="title">'.$this->translate->_('вин код').'</td>
				<td class="rowA title">'.$content['frame'].'</td>
				<td align="right" class="title">'.$this->translate->_('оплачено').'</td>
				<td class="rowA title">'.$payments['total'].'</td>
			  </tr>
			  <tr>
				<td align="right" class="title rowB">'.$this->translate->_('год выпуска').'</td>
				<td class="rowA rowB title">'.$content['year'].'<span id="check-date" vincode="'.$content['frame'].'" style="margin-left:10px; color:#0066CC; border-bottom:1px dotted #0066CC; cursor:pointer">'.$this->translate->_('проверить').'</span><span id="check-date-response" style="margin-left:10px; display:none">'.$this->translate->_('проверка...').'</span></td>
				<td align="right" class="title rowB">'.$this->translate->_('баланс').'</td>
				<td class="rowA rowB title">'.($content['total'] - $payments['total']).'</td>
			  </tr>
			  <tr>
				<td align="right" class="title">&nbsp;</td>
				<td class="rowA title">&nbsp;</td>
				<td align="right" class="title">&nbsp;</td>
				<td class="rowA title">&nbsp;</td>
			  </tr>
			  <tr>
				<td align="right" class="title rowB">'.$this->translate->_('объем двигателя').'</td>
				<td class="rowA rowB title">'.$content['engine'].'</td>
				<td align="right" class="title rowB">'.$this->translate->_('пробег').'</td>
				<td class="rowA rowB title">
                                   <input type="text" name="user-mileage" id="user-mileage" value="'.(isset($umResult->mileage) ? (int) $umResult->mileage : '').'" size="10" />&nbsp;<span id="save-user-mileage" style="color:#0066CC; border-bottom:1px dotted #0066CC; cursor:pointer">'.$this->translate->_('сохранить').'</span>
                                   <div id="user-mileage-message" style="display:none"></div>
                                   <div id="user-mileage-status-wrapper" style="display:'.(isset($umResult->mileage) ? 'block' : 'none').'"><input type="checkbox" name="user-mileage-status" id="user-mileage-status" '.((int) $umResult->active ? 'checked="checked"' : '').' />&nbsp;<label for="user-mileage-status">'.$this->translate->_('показывать на сайте').'</label></div>
                                </td>
			  </tr>
			  <tr>
				<td align="right" class="title">&nbsp;</td>
				<td class="rowA title">&nbsp;</td>
				<td class="rowA title" align="right">'.$this->translate->_('статус').':</td>
				<td class="rowA title">';
                if ($content['delivered'] == '1')
                $this->page.='<span class="green">'.$this->translate->_('доставлена').'</span>';
                else
                $this->page .= $this->translate->_('в пути');

                $countries_sql = $this->mysqlQuery('SELECT * FROM `countries` ORDER BY id ASC');
                $countries = array();
                while ($row = mysql_fetch_object($countries_sql))
                {
                    // $countries[] = '<option '.((empty($this->content['country_id']) && $row->id == 121) ? 'selected="selected"' : ($this->content['country_id'] == $row->id ? 'selected="selected"' : '')).' value="'.$row->id.'">'.$row->country.'</option>';
                    $countries[] = '<option style="padding-left:18px; background:url(/img/flags/'.strtolower($row->iso2).'.gif) left no-repeat" '.($content['country_id'] == $row->id ? 'selected="selected"' : '').' value="'.$row->id.'">'.$row->country.'</option>';
                }
                $countries_select = '<select id="country">'.implode('', $countries).'</select>';

                $this->page.='</td>
			  </tr>
			  <tr>
				<td align="right" class="title">'.$this->translate->_('Отчет о состоянии автомобиля или CR:').'</td>
				<td class="rowA title" colspan="2">'.$auc_list.'&nbsp;</td>
				<td class="rowA title">'.$show_invoice.'&nbsp;<br>'.$export_cert_link.'</td>
			</tr>
			<tr>
				<td align="right" class="title rowB">'.$this->translate->_('После-продажная инспекция:').'</td>
				<td class="rowA rowB title">'.$ps_inspection.'&nbsp;</td>
                                <td align="right" class="rowA rowB title">'.$this->translate->_('Страна:').'</td>
                                <td class="rowA rowB title">'.$countries_select.'</td>
			</tr>
			<tr>
				<td align="right" class="title rowA">'.$this->translate->_('AutoCheck / Carfax:').'</td>
				<td class="rowA rowA title" colspan="3">'.$show_carfax.'&nbsp;</td>
			</tr>
			  <tr>
				<td align="right" class="rowB title">'.$this->translate->_('Место нахождение машины').'</td>
				<td class="rowB title">'.$places1.'&nbsp;</td>
				<td align="right" class="rowB title">'.$this->translate->_('Место назначения').'</td>
				<td class="rowB title">'.($content['destination'] == '' ? $this->translate->_('не указано') : $content['destination']).'</td>
			</tr>
			<tr>
				<td align="right" class="title rowA">&nbsp;</td>
				<td align="right" class="title rowA"><input type="checkbox" name="car_is_sold" id="car_is_sold" style="width:auto;"'.($content['car_is_sold'] == '1' ? ' checked="checked"' : '').' onclick="mark_car_as_sold()"><label for="car_is_sold">'.$this->translate->_('Продан').'</label></td>
				<td class="rowA rowA title" colspan="2">&nbsp;</td>
			</tr>
			  <tr>
				<td align="right" class="title rowA" valign="top"><b>'.$this->translate->_('Фотографии').'</b></td>
				<td class="rowA rowA title" colspan="3">'.$photos_list.'&nbsp;'.$codocs.'</td>
			  </tr>
                           <tr valign="top">
                              <td align="right" class="title rowA">
                                 <b>'.$this->translate->_('Загрузка фотографий').'</b>
                              </td>
                              <td class="title rowA" colspan="3">
                                 <form action="/?mod=multiupload" method="post" class="myForm" enctype="multipart/form-data">
                                    <select style="width:200px;margin-bottom:5px" name="folder">
                                       <option value="1">Pics at the place of purchase</option>
                                       <option value="2">Pics before loading into container</option>
                                    </select>
                                    <div id="files" style="margin-bottom:5px">
                                       <input type="file" name="file-0" style="display:block" />
                                    </div>
                                    <input type="button" id="add-picture" value="+" style="width:200px;margin-bottom:5px" />
                                    <br /><input type="submit" name="submit" value="'.$this->translate->_('Загрузить').'" id="save" style="width:200px" />
                                       
                                    <input type="hidden" name="foto_number" value="1">
                                    <input type="hidden" name="owner" value="'.$this->car_id.'">
                                    <input type="hidden" name="type" value="car">
                                    <input type="hidden" name="uploader" value="client">
                                 </form>
                                 <br />
                              </td>
                           </tr>
			</table>
			'.$form_end;

                //выставить автомобиль на продажу
                $this->page .= '<form style="margin:0px;" action="'.$this->root_path.'public/?mod=cars&sw=save&what=tosale&car_id='.$this->car_id.'" method="post">
			<table width="692" border="0" cellpadding="0" cellspacing="0" class="list">
			<tr class="rowB title">
				<td>
				<input type="checkbox" name="sell" id="sell" onClick="document.getElementById(\'sellOptions\').style.display=\'\'"'.($content['sell_id'] == '' ? '' : ' checked="checked"').'>
				<label for="sell">'.$this->translate->_('выставить на продажу').'</label>&nbsp;&nbsp;<a href="/public/?mod=tpl">Редактировать шаблоны</a></td></tr>
				<tr class="rowB title"><td align="right" valign="top">
				<div id="sellOptions"'.($content['sell_id'] == '' ? ' style="display:none"' : '').'>';

                $query = $this->mysqlQuery('SELECT * FROM `ccl_user_tpl` WHERE `user_id` = '.$_SESSION['user_id']);

                $this->page .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr valign="top">
                                       <td width="50%">
                                          <label for="sellPrice">'.$this->translate->_('шаблон').'</label>:
                                          <select id="sell-template">
                                             <option value="">'.$this->translate->_('Не указан').'</option>
                                             <optgroup label="'.$this->translate->_('Шаблоны пользователя').'">';
                if (mysql_num_rows($query))
                {
                    while ($row = mysql_fetch_object($query))
                    {
                        $this->page .= '<option value="user-'.$row->id.'">'.$row->name.'</option>';
                    }
                }

                $query = $this->mysqlQuery('SELECT * FROM `ccl_tpl`');

                $this->page .= '
                                             </optgroup>
                                             <optgroup label="'.$this->translate->_('Шаблоны администратора').'">';
                if (mysql_num_rows($query))
                {
                    while ($row = mysql_fetch_object($query))
                    {
                        $this->page .= '<option value="admin-'.$row->id.'">'.$row->name.'</option>';
                    }
                }
                $this->page .= '
                                             </optgroup>
                                          </select>
                                          <p>
                                             <input type="checkbox" id="showAdminComment" />&nbsp;<label for="showAdminComment">'.$this->translate->_('показать шаблона админа').'</label>
                                          </p>
                                       </td>
                                       <td>
                                          <label for="sellComment">'.$this->translate->_('комментарий').'</label>:
                                          <br /><textarea name="sellComment" id="sellComment" style="border:1px solid #bbb;width:400px;height:100px">'.$content['sell_comment'].'</textarea>
                                          <input type="hidden" id="sellCommentOriginal" value="'.$content['sell_comment'].'" />
                                          <input type="hidden" id="sellCommentAdmin" value="'.$content['sell_comment_admin'].'" />
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>
                                          <label for="sellPrice">'.$this->translate->_('цена').'</label>: <input type="text" name="sellPrice" id="sellPrice" size="5" style="border:1px solid #bbb;" value="'.$content['sell_price'].'">
                                          <input type="checkbox" name="sellSold" id="sellSold" '.($content['sell_sold'] ? 'checked="checked"' : '').' style="margin-left:10px">&nbsp;<label for="sellSold">'.$this->translate->_('продан').'</label>
                                       </td>
                                       <td><input name="submit" type="submit" value="'.$this->translate->_('сохранить').'"></td>
                                    </tr>
                                 </table>

				<input type="hidden" name="sell_id" value="'.($content['sell_id'] == '' ? 0 : $content['sell_id']).'">
				</div></td>
			</tr>
			</table></form> 
			';
                //################################

                if ($content['number'] != '')
                {
                    $this->page.='<h3>'.$this->translate->_('Идет в контейнере').':'.$content['number'].'</h3>
				<table width="692" border="0" cellpadding="0" cellspacing="0" class="list">
				  <tr>
				     <td width="25%" align="right" class="title"><strong>'.$this->translate->_('Дата погрузки в контейнер').':</strong></td>
				     <td width="25%" class="title">'.(strtotime($content['loaddate']) ? $content['loaddate'] : '-').'</td>
				  </tr>
                                  <tr>
                                     <td align="right" class="title"><strong>'.$this->translate->_('Отправкa из США').':</strong></td>
                                     <td class="title">'.(strtotime($content['sent']) ? $content['sent'] : '-').'</td>
                                  </tr>
                                  <tr>
                                     <td align="right" class="title"><strong>'.$this->translate->_('Приход в порт перегруза').':</strong></td>
                                     <td class="title">'.(strtotime($content['portdate']) ? $content['portdate'] : '-').'</td>
                                  </tr>
                                  <tr>
                                     <td align="right" class="title"><strong>'.$this->translate->_('Погрузка на Ж/Д платформу').':</strong></td>
                                     <td class="title">'.(strtotime($content['rail']) ? $content['rail'] : '-').'</td>
                                  </tr>
                                  <tr>
                                     <td align="right" class="title"><strong>'.$this->translate->_('Станция назначения - Бишкек').':</strong></td>
                                     <td class="title">'.(strtotime($content['bishkek']) ? $content['bishkek'] : '-').'</td>
                                  </tr>
				</table>';
                }


                $leftdays = $this->translate->_('Срок доставки автомобиля временно недоступен');

                $sql = mysql_query('SELECT left_days FROM `ccl_daysleft` WHERE `cid`='.$this->car_id.' LIMIT 1');

                if (mysql_num_rows($sql))
                {
                    $arr = mysql_fetch_assoc($sql);
                    $days = intval($arr['left_days']);
                    $daysLang = array(
                    '0' => $this->translate->_('дней'),
                    '1' => $this->translate->_('день'),
                    '2' => $this->translate->_('дня'),
                    '3' => $this->translate->_('дня'),
                    '4' => $this->translate->_('дня'),
                    '5' => $this->translate->_('дней'),
                    '6' => $this->translate->_('дней'),
                    '7' => $this->translate->_('дней'),
                    '8' => $this->translate->_('дней'),
                    '9' => $this->translate->_('дней'));



                    if ($days < 3)
                    {
                        if ($days==0){
                            $leftdays='';
                        }
                        else{
                            $leftdays = $this->translate->_('Автомобиль будет в Бишкеке в течении <b>трёх</b> дней');    
                        }
                        
                        
                    }
                    else
                    {
                        $leftdays = sprintf($this->translate->_('Автомобиль будет доставлен через <b>%d</b> %s'), $days, $daysLang[$days % 10]);
                    }
                }

                $this->page .= '<center class="title" style="margin-top:7px">'.$leftdays.'</center>';

                if ($content['carriage']){
                    $sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."carriage` WHERE `id`='".$content['carriage']."'";
                    $res = mysql_query($sql);
                    $carriage = mysql_fetch_assoc($res);


                    $this->page.='<h3>'.$this->translate->_('Идет в вагоне').'</h3>
				<table width="692" border="0" cellpadding="0" cellspacing="0" class="list">
				  <tr>
				     <td width="25%" align="right" class="title"><strong>'.$this->translate->_('Номер вагона').':</strong></td>
				     <td width="25%" class="title">'. $carriage['number'].'</td>
				  </tr>
                  <tr>
				     <td width="25%" align="right" class="title"><strong>'.$this->translate->_('Дата погрузки в вагон').':</strong></td>
				     <td width="25%" class="title">'. $carriage['loaddate'].'</td>
				  </tr>
				  
				  <tr>
                     <td align="right" class="title"><strong>'.$this->translate->_('Слежение').':</strong></td>
                     <td class="title">'.nl2br( $carriage['treking']).'</td>
                  </tr><br>

                  <tr>
                     <td align="right" class="title"><strong>'.$this->translate->_('Последнее слежение').':</strong></td>
                     <td class="title">'. $carriage['treking_date'].'</td>
                  </tr>
                                  
				</table>';
                }


                $this->page .= '</div>';


                $comment = new CarsComment();
                $comment->setCarOwnerId($content['buyer'], $content['reciever'], $content['dealer']);
                $this->page .= $comment->getContent();
            }
            else
            $this->redirect($this->root_path.'public/');
        }
        else
        $this->redirect($this->root_path.'public/');
    }

    private function validateOwnership($content)
    {
        if ($_SESSION['user_type'] != '11')
        {
            if ($content['buyer'] == $_SESSION['user_id'] or $content['dealer'] == $_SESSION['user_id'] or $content['reciever'] == $_SESSION['user_id'])
            return true;
            else
            return false;
        } else
        return true;
    }

    private function save()
    {
        if (isset($_POST['client']) and isset($_POST['ci']))
        {

            $this->mysqlQuery("
			UPDATE `ccl_".ACCOUNT_SUFFIX."cars` 
			SET `buyer` = '".intval($_POST['client'])."' 
			WHERE `id`= '".intval($_POST['ci'])."'");

            //обновляем баланс клиента
            require($_SERVER['DOCUMENT_ROOT'].$this->root_path.'bin/balance.php');
            if ($_SESSION['user_id'] == $_POST['buyer'])
            $dealer = 1;
            else
            $dealer = 0;
            updateBalance($_POST['buyer'], $dealer);

            $this->redirect($this->root_path.$this->user_folder.'/?mod=cars&sw=form&car_id='.intval($_POST['ci']));
        }
        elseif (isset($_GET['what']) and $_GET['what'] == 'tosale' and $this->car_id != '' and $this->car_id != 0)
        {
            if (isset($_POST['sell']) and $_POST['sell'] == 'on' and $_POST['sell_id'] == 0)
            {
                if ($this->ownershipCheck($this->car_id, 'car'))
                {
                    $sql = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."forsale`
                        (`id`, `car`, `price`, `comment`, `date`, `sold`)
                        VALUES (LAST_INSERT_ID(),
                        '".$this->car_id."',
                        '".intval($_POST['sellPrice'])."',
                        '".mysql_real_escape_string(strip_tags($_POST['sellComment']))."',
                        '".date('Y-m-d')."',
                        '".intval(isset($_POST['sellSold']))."')";
                }
                else
                $this->redirect($this->root_path.$this->user_folder);
            }

            elseif ($_POST['sell_id'] != 0 and $_POST['sell'] == 'on')
            {
                if ($this->ownershipCheck($_POST['sell_id'], 'sell_item'))
                {
                    $sql = "UPDATE `ccl_".ACCOUNT_SUFFIX."forsale` SET
                     `price`='".intval($_POST['sellPrice'])."',
                     `comment`='".mysql_real_escape_string(strip_tags($_POST['sellComment']))."',
                     `sold`='".intval(isset($_POST['sellSold']))."'
                      WHERE id='".intval($_POST['sell_id'])."'";
                }
                else
                $this->redirect($this->root_path.$this->user_folder);
            }
            else
            {
                if ($this->ownershipCheck($_POST['sell_id'], 'sell_item'))
                {
                    $sql = "DELETE FROM `ccl_".ACCOUNT_SUFFIX."forsale`
					WHERE id='".intval($_POST['sell_id'])."'";
                }
                else
                $this->redirect($this->root_path.$this->user_folder);
            }

            $this->mysqlQuery($sql);
            $this->redirect($this->root_path.$this->user_folder.'/?mod=cars&sw=form&car_id='.$this->car_id);
        }

        else
        {
            die('Not allowed!');
        }
    }

    public function saveUserMileage()
    {
        if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            $mileage = abs((int) $_GET['mileage']);
            $id = (int) $_GET['car_id'];

            $query = $this->mysqlQuery("SELECT COUNT(*) AS `total` FROM `ccl_user_mileage` WHERE `car_id` = ".$id);
            $result = mysql_fetch_object($query);

            if ((int) $result->total)
            {
                if ($mileage != 0)
                {
                    $query = "UPDATE `ccl_user_mileage` SET `mileage` = ".$mileage." WHERE `car_id` = ".$id;
                }
                else
                {
                    $query = "DELETE FROM `ccl_user_mileage` WHERE `car_id` = ".$id;
                }
            }
            else
            {
                if ($mileage != 0)
                {
                    $query = "INSERT INTO `ccl_user_mileage` VALUES('', '".$id."', '0', '".$mileage."')";
                }
            }

            $this->templates['footer'] = '';
            $this->page = $this->mysqlQuery($query);
        }
    }

    public function updateUserMileageStatus()
    {
        if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            $this->templates['footer'] = '';
            $this->page = (int) $this->mysqlQuery("UPDATE `ccl_user_mileage` SET `active` = ".($_GET['status'] == 'true' ? 1 : 0)." WHERE `car_id` = ".$_GET['car_id']);
        }
    }

    public function setMainPhoto()
    {
        if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            $this->templates['footer'] = '';
            $sql = "UPDATE `ccl_cars` SET `top_photo` = ".intval($_GET['photo_id'])." WHERE `id` = ".intval($_GET['id']);
            $this->page = (int) $this->mysqlQuery($sql);
            // $this->page = $sql;
        }
    }

    public function setCountry()
    {
        $this->templates['footer'] = '';
        $sql = "UPDATE `ccl_cars` SET `country_id` = ".intval($_GET['country_id'])." WHERE `id` = ".intval($_GET['id']);
        $this->mysqlQuery($sql);
        $this->page = $sql;
    }

    private function ownershipCheck($item, $type)
    {
        $found = 0;
        if ($type == 'car')
        {
            if ($this->checkByCar(
            $this->mysqlQuery("
			SELECT id 
			FROM `ccl_".ACCOUNT_SUFFIX."cars` 
			WHERE `buyer` = '".$_SESSION['user_id']."' 
			OR `reciever` = '".$_SESSION['user_id']."'"), $item))
            return true;
            else
            return false;
        }

        elseif ($type = 'sell_item')
        {
            if ($this->checkByPost($this->mysqlQuery("
			SELECT car 
			FROM `ccl_".ACCOUNT_SUFFIX."forsale` 
			WHERE `id`='".$item."'"), $item))
            return true;
            else
            return false;
        }
    }

    private function checkByCar($content, $item)
    {
        $num = mysql_num_rows($content);
        if ($num > 0)
        {
            $i = 0;
            $out = array();
            while ($i < $num)
            {
                $line = mysql_fetch_array($content);
                array_push($out, $line['id']);
                $i++;
            }
            foreach ($out as $k => $v)
            {
                if (intval($item) == $v)
                $found = '1';
            }
            if ($found == '1')
            return true;
            else
            return false;
        }
        else
        return false;
    }

    private function checkByPost($content, $item)
    {
        $post = mysql_fetch_array($content);
        if ($this->checkByCar($this->mysqlQuery("
		SELECT id 
		FROM `ccl_".ACCOUNT_SUFFIX."cars` 
		WHERE `buyer` = '".$_SESSION['user_id']."' 
		OR `reciever` = '".$_SESSION['user_id']."'"), $post['car']))
        return true;
        else
        return false;
    }

    function wrapFile($in)
    {
        return '<div style="float:left; width:128px; padding:2px; border:1px solid #ddd;text-align:center; margin-right:4px; margin-bottom:5px;">
		'.$in.'</div>';
    }

}

?>