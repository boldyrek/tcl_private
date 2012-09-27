<?

class oveForm extends Proto {

	private $car_info,$car_id;
	private $num_row_photo = 6;

	public function drawContent() {
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->carInfo();
			$this->getContent();
		}
		$this->page .= $this->templates['footer'];

		$this->errorsPublisher();
		$this->publish();
	}

	private function getContent() {
		if(intval($_GET['car_id'])>0)
		{
			$this->car_id = intval($_GET['car_id']);

			$data = @mysql_fetch_array($this->mysqlQuery("SELECT * FROM ccl_".ACCOUNT_SUFFIX."ove WHERE car_id='".$this->car_id."' LIMIT 1"));
			if($data)
			{
				$form = unserialize($data['serialize']);
			}else{
				require_once('default.ove.php');
			}
//arr_echo($form);
//exit;			
			$this->files = $this->mysqlQuery(
			"SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."cars_photos`
			WHERE `car` = '".$this->car_id."' ORDER BY `folder` DESC, `id` DESC");
			
			//фотографии автомобиля
			$photos = array();
			$num = @mysql_num_rows($this->files);
			if($num>0)
			{
				$j = 1;
				while($j<=$num)
				{
					$line = mysql_fetch_array($this->files);
					if($line['file']!='')$photos[] = $line['file'];
					$j++;
				}
				$photos = array_chunk($photos, $this->num_row_photo);

				$photo_list = '<table cellspacing="2" cellpadding="0" style="border-bottom:1px solid #ccc;">';
				$r_=0;
				foreach ($photos as $k=>$v) {
					$chk_list_row ='<tr class="row'.($r_%2? 'B':'A').'">';
					
					$photo_list_row = '<tr class="row'.($r_%2? 'B':'A').'">';
					for($i=0;$i<$this->num_row_photo;$i++)
					{
						$photo_td = '<td align="center" style="padding:0 10px">';
						if(isset($v[$i]))
						{
							$photo_td .= '<a href="'.$this->root_path.'photos/'.ACCOUNT_SUFFIX.$this->car_id.'/'.$v[$i].'" target="_blank"><img src="'.$this->root_path.'photos/'.ACCOUNT_SUFFIX.$this->car_id.'/thumb/'.$v[$i].'" border="0"></a>';
						}else{
							$photo_td .= '&nbsp;';
						}
						$photo_td .= '</td>';
						$photo_list_row .= $photo_td;
						
						$chk_td = '<td align="center" style="padding: 15px 10px 0 10px;border:none">';
						if(isset($v[$i]))
						{
							$value = $this->root_path.'photos/'.ACCOUNT_SUFFIX.$this->car_id.'/'.$v[$i];
						    
						    $chk_td .= '<input type="checkbox" '.(is_array($form['photos']) && in_array($value, $form['photos'])? "checked='checked'":"").' name="photos[]" value="'.$value.'">';
						}else{
							$chk_td .= '&nbsp;';
						}
						$chk_td .= '</td>';
						$chk_list_row .= $chk_td;
					}
					$photo_list_row .= '</tr>';
					$chk_list_row .='</tr>';
					$photo_list .= $chk_list_row.$photo_list_row;
					$r_++;
				}
				$photo_list .= '</table>';
			}
			else $photo_list = '';
			
			//arr_echo($form);
			
			$this->page.='
			<link href="'.$this->root_path.'css/form_validation.css" rel="stylesheet" type="text/css">
			<script>var myDateFormat = new Array("mm/dd/yyyy");</script>
			<script src="'.$this->root_path.'js/datepicker.js"></script>
			<script src="'.$this->root_path.'js/jquery.js"></script>
			<script src="'.$this->root_path.'js/validate.js"></script>
			<script src="'.$this->root_path.'js/jquery.dates.js"></script>
			<script>
			

			$(document).ready(function(){
			
				$("#ove_submit").click(function(){
				if ($("#ove_login").val()=="" || $("#ove_password").val()=="" || $("#ove_email").val()==""){
				alert ("Fill in Login, Password, Email ans save the form for automatic sending!");
				return false
				}
				
					var win_width = parseInt(screen.width);
					var win_height = parseInt(screen.height);
					var left = (win_width-602)/2;
					var top = (win_height-384)/2;
					var winattributes="width=602,height=384,resizable=no, status=no, scrollbars=no, location=no,left="+left+",top="+top;
					var w = window.open("/ove/insert.php?car_id='.$this->car_id.'","export", winattributes);
				});
				
				
				
				
				
				$("#offer_start_now").click(function(){
					if(!this.checked)
					{
						$("#specific_offer_start_time").show();
						$("#presenter_start_time_row").show();
					}else{
						$("#specific_offer_start_time").hide();
						$("#presenter_start_time_row").hide();
						$("#DatetimeToolbocksPresenterStartTimeDateInput").val(o2s.format(new Date (),"mm/dd/yyyy"));
						var new_time = o2s.format(new Date (),"h:i")
//						$("#presenter_start_time option").each(function(){
//							if(this.value==new_time)
//						});
					}
				});
				$("#presenter_offer_duration").change(function(){
					switch($(this).val())
					{
						case "specific":
						$("#presenter_end_time_row").show();
						$("#specific_offer_end_time_row").show();
						$("#offer_end_datetime").hide();
						break;
						
						default:
						$("#presenter_end_time_row").hide();
						$("#specific_offer_end_time_row").hide();
						$("#offer_end_datetime").show();
					}
				});
				$("#ove_form").formValidation({
					alias			: "msg"
					,required	: "accept"
					,err_list	:	true
					//,callback	: "callback"
				});
				$("#offer_start_time_zone_info_id").change(function(){
					$("#offer_end_datetime #time_zone").html($("option:selected",this).text());
				});
				
				$("#presenter_start_time_meridiem").change(function(){
					$("#offer_end_datetime #meridiem").html($(this).val());
				});
				
				$("#presenter_start_time").change(function(){
					var time = $(this).val().split(":");
					var hours = parseInt(time[0]);
					var minute = parseInt(time[1]);
					
					var datetime = $("#DatetimeToolbocksPresenterStartTimeDateInput").val().split("/");
					var month = parseInt(datetime[0]);
					var day = parseInt(datetime[1]);
					var year = parseInt(datetime[2]);
					
					var _h = hours;
					
					var mer = $("#presenter_start_time_meridiem").val();
					if(mer=="PM")_h=hours+12;
					
					var c_date = new Date ( year, (month-1), day, _h, minute, 0 );
					
					var mn = parseInt($("#presenter_offer_duration").val());
					var timestamp = c_date.getTime();
					mn = mn*60*60*1000;
					
					timestamp+=mn;
        			
        			var new_date = o2s.format(new Date (timestamp),"mm/dd/yyyy h:i A");
        			var arr_date = new_date.split(" ");
        			var span = $("#offer_end_datetime");
        			$("#date", span).html(arr_date[0]);
        			$("#time", span).html(arr_date[1]);
        			$("#meridiem", span).html(arr_date[2]);
				});
			});
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
			</script>
			<style>
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
	<form id="ove_form" action="?mod=cars&sw=saveove&car_id='.$this->car_id.'" method="post">
		<div class="cont_car" style="width:960px;">
			<h3>Auto Data for ove.com</h3>
			<table width="945" border="0" cellpadding="3" cellspacing="0" class="list">
				<tr>
					<td colspan="2">
						<h3><a href="/?mod=cars&sw=form&car_id='.$this->car_id.'">'.$this->car_info['model'].'</a> VIN: '.$this->car_info['frame'].'</h3>
						<input type="hidden" name="vin_code" value="'.$this->car_info['frame'].'">
					</td>
				</tr>
				
				<tr class="rowA title">
					<td colspan="2" class="title_division">Ove Authorization Information</td>
				</tr>
				<tr>
				<td colspan="2" style="padding-left:3%">
				
				<div class="notice" style="padding-right:80px;">
				<p><b>Warning! Storing password as open text is a potential risk.</b><br />
				If you understand this risk, but nevertheless wish to make an automatic sending of - fill in these fields. Otherwise, you can download the generated csv file and post it yourself if you do not want to fill in these fields.</p>
				
				</div>
				
						<table class="form_group" cellspacing="0" width="90%">
							<tr class="rowB">
								<td class="title" align="right">Login:</td>
								<td><input id="ove_login" name="ove_login" size="30" type="text" value="'.$form['ove_login'].'" /></td>
							</tr>
							<tr class="rowA">
								<td class="title" align="right">Password:</td>
								<td><input id="ove_password" name="ove_password" size="30" type="text" value="'.$form['ove_password'].'" /></td>
							</tr>
							
							<tr class="rowB">
								<td class="title" align="right">Result Email:</td>
								<td><input id="ove_email" name="ove_email" size="30" type="text" value="'.$form['ove_email'].'" /></td>
							</tr>
							
							</table>
							</td>
							</tr>
							
							
				<tr class="rowA title">
					<td colspan="2" class="title_division">Basic Vehicle Information</td>
				</tr>
				<tr>
				<td colspan="2" style="padding-left:3%">
						<table class="form_group" cellspacing="0" width="90%">
							<tr class="rowB">
								<td class="title" align="right"><span class="required_marker">*</span>VIN:</td>
								<td><input id="listing_manheim_vin" name="listing[vin]" size="30" type="text" value="'.((@$form['listing']['vin'])?$form['listing']['vin']:$this->car_info['frame']).'" /></td>
							</tr>
							
							<tr class="rowA">
								<td class="title" align="right"><span class="required_marker">*</span>YEAR:</td>
								<td><input id="listing_manheim_year" name="listing[year]" size="30" type="text" value="'.((@$form['listing']['year'])?$form['listing']['year']:$this->car_info['year']).'" /></td>
							</tr>
							
							<tr class="rowB">
								<td class="title" align="right"><span class="required_marker">*</span>Make:</td>
								<td><input id="listing_manheim_make" name="listing[make]" size="30" type="text" value="'.((@$form['listing']['make'])?$form['listing']['make']:$this->getMark()).'" /></td>
							</tr>
							
							<tr class="rowA">
								<td class="title" align="right"><span class="required_marker">*</span>Model:</td>
								<td><input id="listing_manheim_model" name="listing[model]" size="30" type="text" value="'.((@$form['listing']['model'])?$form['listing']['model']:$this->getModel()).'" /></td>
							</tr>
							
							
							
							<tr class="rowB">
								<td class="title" align="right">Trim:</td>
								<td><input id="listing_manheim_trim" name="listing[trim]" size="30" type="text" value="'.@$form['listing']['trim'].'" /></td>
							</tr>
							
							
		
							
							<tr class="rowA">
								<td class="title" align="right">Stock Number:</td>
								<td><input id="listing_manheim_stock_number" name="listing[manheim_stock_number]" size="30" type="text" value="'.$form['listing']['manheim_stock_number'].'" /></td>
							</tr>
							<tr class="rowB">
								<td class="title" align="right"><span class="required_marker">*</span>Vehicle Location:</td>
								<td>
									<select accept="true" id="listing_vehicle_location" name="listing[vehicle_location]" msg="Vehicle Location">
										<option value="" '.($form['listing']['vehicle_location']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="AU" '.($form['listing']['vehicle_location']=='AU'? "selected='selected'":"").'>At Auction</option>
										<option value="DL" '.($form['listing']['vehicle_location']=='DL'? "selected='selected'":"").'>At Dealership</option>
										
										<option value="DC" '.($form['listing']['vehicle_location']=='DC'? "selected='selected'":"").'>At Distribution Center</option>
										<option value="TR" '.($form['listing']['vehicle_location']=='TR'? "selected='selected'":"").'>In Transit</option>
									</select>
								</td>
							</tr>
							
							
							<tr class="rowA">
								<td class="title" align="right">Vehicle Location Code:</td>
								<td>
									<select id="listing_vehicle_location_id" name="listing[vehicle_location_id]">
										<option value="" '.($form['listing']['vehicle_location_id']==''? "selected='selected'":"").'>Not Specified</option>
										
										<option value="DRIV" '.($form['listing']['vehicle_location_id']=='DRIV'? "selected='selected'":"").'>DRIV - Manheim Drive Center</option>
										<option value="AGOL" '.($form['listing']['vehicle_location_id']=='AGOL'? "selected='selected'":"").'>AGOL - ADESA Golden Gate</option>
										<option value="RAA" '.($form['listing']['vehicle_location_id']=='RAA'? "selected='selected'":"").'>RAA - Manheim Riverside (Riverside AA)</option>
										<option value="WBPA" '.($form['listing']['vehicle_location_id']=='WBPA'? "selected='selected'":"").'>WBPA - Manheim Palm Beach (West Palm Beach AA)</option>
										<option value="NADE" '.($form['listing']['vehicle_location_id']=='NADE'? "selected='selected'":"").'>NADE - Manheim New Jersey (National Auto Dealers Exchange)</option>
										<option value="DFWA" '.($form['listing']['vehicle_location_id']=='DFWA'? "selected='selected'":"").'>DFWA - Manheim Dallas-Fort Worth (DFW AA)</option>
										<option value="MMAA" '.($form['listing']['vehicle_location_id']=='MMAA'? "selected='selected'":"").'>MMAA - Manheim Milwaukee (Metro Milwaukee)</option>
										<option value="ABOS" '.($form['listing']['vehicle_location_id']=='ABOS'? "selected='selected'":"").'>ABOS - ADESA Boston</option>
										<option value="TRFM" '.($form['listing']['vehicle_location_id']=='TRFM'? "selected='selected'":"").'>TRFM - Thief River Falls Minnensota (Arctic Cat)</option>
									</select>
								</td>
							</tr>
							
							
							<tr class="rowB">
								<td class="title" align="right"><span class="required_marker">*</span>Facilitation Location:</td>
								<td>
									<select accept="true" id="listing_facilitation_service_provider_id" name="listing[facilitation_service_provider_id]" msg="Facilitation Location">
										<option value="" '.($form['listing']['facilitation_service_provider_id']==''? "selected='selected'":"").'>Please select Facilitation Location</option>
										
										<optgroup label="------  United States  ------">
											<option value="OUTL" '.($form['listing']['facilitation_service_provider_id']=='OUTL'? "selected='selected'":"").'>AL - Manheim Birmingham</option>
											<option value="XCEN" '.($form['listing']['facilitation_service_provider_id']=='XCEN'? "selected='selected'":"").'>AR - Central Arkansas Auto Auction</option>
											<option value="XSWT" '.($form['listing']['facilitation_service_provider_id']=='XSWT'? "selected='selected'":"").'>AZ - DAA Southwest</option>
											<option value="AZAA" '.($form['listing']['facilitation_service_provider_id']=='AZAA'? "selected='selected'":"").'>AZ - Manheim Arizona</option>
											
											<option value="PXAA" '.($form['listing']['facilitation_service_provider_id']=='PXAA'? "selected='selected'":"").'>AZ - Manheim Phoenix</option>
											<option value="TCAA" '.($form['listing']['facilitation_service_provider_id']=='TCAA'? "selected='selected'":"").'>AZ - Manheim Tucson</option>
											<option value="ISAA" '.($form['listing']['facilitation_service_provider_id']=='ISAA'? "selected='selected'":"").'>CA - Brasher\'s Sacramento Auto Auction</option>
											<option value="CADE" '.($form['listing']['facilitation_service_provider_id']=='CADE'? "selected='selected'":"").'>CA - Manheim California</option>
											<option value="FADA" '.($form['listing']['facilitation_service_provider_id']=='FADA'? "selected='selected'":"").'>CA - Manheim Central California</option>
											<option value="LADA" '.($form['listing']['facilitation_service_provider_id']=='LADA'? "selected='selected'":"").'>CA - Manheim Los Angeles</option>
											<option value="RAA" '.($form['listing']['facilitation_service_provider_id']=='RAA'? "selected='selected'":"").'>CA - Manheim Riverside</option>
											<option value="SDAA" '.($form['listing']['facilitation_service_provider_id']=='SDAA'? "selected='selected'":"").'>CA - Manheim San Diego</option>
											<option value="BCAA" '.($form['listing']['facilitation_service_provider_id']=='BCAA'? "selected='selected'":"").'>CA - Manheim San Francisco Bay</option>
											
											<option value="SCAA" '.($form['listing']['facilitation_service_provider_id']=='SCAA'? "selected='selected'":"").'>CA - Manheim Southern California</option>
											<option value="XNOR" '.($form['listing']['facilitation_service_provider_id']=='XNOR'? "selected='selected'":"").'>CA - Norwalk Auto Auction</option>
											<option value="XROC" '.($form['listing']['facilitation_service_provider_id']=='XROC'? "selected='selected'":"").'>CO - DAA Rockies</option>
											<option value="CAA" '.($form['listing']['facilitation_service_provider_id']=='CAA'? "selected='selected'":"").'>CO - Manheim Colorado</option>
											<option value="DAA" '.($form['listing']['facilitation_service_provider_id']=='DAA'? "selected='selected'":"").'>CO - Manheim Denver</option>
											<option value="XCAE" '.($form['listing']['facilitation_service_provider_id']=='XCAE'? "selected='selected'":"").'>CT - Central Auto Auction</option>
											<option value="XHSA" '.($form['listing']['facilitation_service_provider_id']=='XHSA'? "selected='selected'":"").'>CT - Hartford Springfield Auction Co-op</option>
																			
											<option value="IGSA" '.($form['listing']['facilitation_service_provider_id']=='IGSA'? "selected='selected'":"").'>FL - Gulf States Auto Auction</option>
											<option value="GOAA" '.($form['listing']['facilitation_service_provider_id']=='GOAA'? "selected='selected'":"").'>FL - Manheim Central Florida</option>
											<option value="DADE" '.($form['listing']['facilitation_service_provider_id']=='DADE'? "selected='selected'":"").'>FL - Manheim Daytona Beach</option>
											<option value="LMAA" '.($form['listing']['facilitation_service_provider_id']=='LMAA'? "selected='selected'":"").'>FL - Manheim Fort Lauderdale</option>
											<option value="SWFL" '.($form['listing']['facilitation_service_provider_id']=='SWFL'? "selected='selected'":"").'>FL - Manheim Fort Myers</option>
											<option value="IAA" '.($form['listing']['facilitation_service_provider_id']=='IAA'? "selected='selected'":"").'>FL - Manheim Imperial Florida</option>
											<option value="LAA" '.($form['listing']['facilitation_service_provider_id']=='LAA'? "selected='selected'":"").'>FL - Manheim Lakeland</option>
											<option value="FAAO" '.($form['listing']['facilitation_service_provider_id']=='FAAO'? "selected='selected'":"").'>FL - Manheim Orlando</option>
											
											
											
											<option value="WPBA" '.($form['listing']['facilitation_service_provider_id']=='WPBA'? "selected='selected'":"").'>FL - Manheim Palm Beach</option>
											
											<option value="PCAA" '.($form['listing']['facilitation_service_provider_id']=='PCAA'? "selected='selected'":"").'>FL - Manheim Pensacola</option>
											<option value="SPAA" '.($form['listing']['facilitation_service_provider_id']=='SPAA'? "selected='selected'":"").'>FL - Manheim St. Pete</option>
											<option value="TBAA" '.($form['listing']['facilitation_service_provider_id']=='TBAA'? "selected='selected'":"").'>FL - Manheim Tampa</option>
											<option value="TALA" '.($form['listing']['facilitation_service_provider_id']=='TALA'? "selected='selected'":"").'>FL - Tallahassee Auto Auction</option>
											<option value="AAA" '.($form['listing']['facilitation_service_provider_id']=='AAA'? "selected='selected'":"").'>GA - Manheim Atlanta</option>
											<option value="AAAW" '.($form['listing']['facilitation_service_provider_id']=='AAAW'? "selected='selected'":"").'>GA - Manheim Georgia</option>
											<option value="BBAA" '.($form['listing']['facilitation_service_provider_id']=='BBAA'? "selected='selected'":"").'>GA - Manheim Metro Atlanta</option>
											
											<option value="PERR" '.($form['listing']['facilitation_service_provider_id']=='PERR'? "selected='selected'":"").'>GA - Perry\'s Auto Auction</option>
											<option value="XSGA" '.($form['listing']['facilitation_service_provider_id']=='XSGA'? "selected='selected'":"").'>GA - South Georgia Auto Auction</option>
											<option value="ALOH" '.($form['listing']['facilitation_service_provider_id']=='ALOH'? "selected='selected'":"").'>HI - Manheim Hawaii</option>
											<option value="XPLZ" '.($form['listing']['facilitation_service_provider_id']=='XPLZ'? "selected='selected'":"").'>IA - Plaza Auto Auction</option>
											<option value="AREN" '.($form['listing']['facilitation_service_provider_id']=='AREN'? "selected='selected'":"").'>IL - Manheim Arena Illinois</option>
											<option value="GCAA" '.($form['listing']['facilitation_service_provider_id']=='GCAA'? "selected='selected'":"").'>IL - Manheim Chicago</option>
											<option value="GAA" '.($form['listing']['facilitation_service_provider_id']=='GAA'? "selected='selected'":"").'>IL - Manheim Gateway St Louis</option>
											<option value="AWS" '.($form['listing']['facilitation_service_provider_id']=='AWS'? "selected='selected'":"").'>IL - Manheim Metro Chicago</option>
											
										
											<option value="XTCH" '.($form['listing']['facilitation_service_provider_id']=='XTCH'? "selected='selected'":"").'>IL - Tri-State AA of Chicago</option>
											<option value="XDYR" '.($form['listing']['facilitation_service_provider_id']=='XDYR'? "selected='selected'":"").'>IN - Dyer Auto Auction</option>
											<option value="XICE" '.($form['listing']['facilitation_service_provider_id']=='XICE'? "selected='selected'":"").'>IN - Indianapolis Car Exchange</option>
											<option value="FTWA" '.($form['listing']['facilitation_service_provider_id']=='FTWA'? "selected='selected'":"").'>IN - Manheim Fort Wayne</option>
											<option value="INDY" '.($form['listing']['facilitation_service_provider_id']=='INDY'? "selected='selected'":"").'>IN - Manheim Indianapolis</option>
											<option value="LOUA" '.($form['listing']['facilitation_service_provider_id']=='LOUA'? "selected='selected'":"").'>IN - Manheim Louisville</option>
											<option value="MIDA" '.($form['listing']['facilitation_service_provider_id']=='MIDA'? "selected='selected'":"").'>KY - Manheim Kentucky</option>
											
											<option value="LOUI" '.($form['listing']['facilitation_service_provider_id']=='LOUI'? "selected='selected'":"").'>LA - Louisiana\'s 1st Choice Auto Auction</option>
											<option value="LFAA" '.($form['listing']['facilitation_service_provider_id']=='LFAA'? "selected='selected'":"").'>LA - Manheim Lafayette</option>
											<option value="NOAA" '.($form['listing']['facilitation_service_provider_id']=='NOAA'? "selected='selected'":"").'>LA - Manheim New Orleans</option>
											<option value="XLWA" '.($form['listing']['facilitation_service_provider_id']=='XLWA'? "selected='selected'":"").'>MA - Lynnway Auto Auction</option>
											<option value="AAAI" '.($form['listing']['facilitation_service_provider_id']=='AAAI'? "selected='selected'":"").'>MA - Manheim New England</option>
											<option value="XQAA" '.($form['listing']['facilitation_service_provider_id']=='XQAA'? "selected='selected'":"").'>MA - Quincy Auto Auction</option>
											<option value="BELA" '.($form['listing']['facilitation_service_provider_id']=='BELA'? "selected='selected'":"").'>MD - Bel-Air Auto Auction</option>
											<option value="BWAE" '.($form['listing']['facilitation_service_provider_id']=='BWAE'? "selected='selected'":"").'>MD - Manheim Baltimore-Washington</option>
											<option value="BAY" '.($form['listing']['facilitation_service_provider_id']=='BAY'? "selected='selected'":"").'>MI - Bay Auto Auction</option>
											
											<option value="FLIN" '.($form['listing']['facilitation_service_provider_id']=='FLIN'? "selected='selected'":"").'>MI - Flint Auto Auction</option>
											<option value="XGRP" '.($form['listing']['facilitation_service_provider_id']=='XGRP'? "selected='selected'":"").'>MI - Grand Rapids Auto Auction</option>
											<option value="DETA" '.($form['listing']['facilitation_service_provider_id']=='DETA'? "selected='selected'":"").'>MI - Manheim Detroit</option>
											<option value="MIAA" '.($form['listing']['facilitation_service_provider_id']=='MIAA'? "selected='selected'":"").'>MI - Manheim Flint</option>
											<option value="GDTA" '.($form['listing']['facilitation_service_provider_id']=='GDTA'? "selected='selected'":"").'>MI - Manheim Metro Detroit</option>
											<option value="ABCM" '.($form['listing']['facilitation_service_provider_id']=='ABCM'? "selected='selected'":"").'>MI - West Michigan Auto Auction</option>
											
											<option value="MAAI" '.($form['listing']['facilitation_service_provider_id']=='MAAI'? "selected='selected'":"").'>MN - Manheim Minneapolis</option>
											<option value="NSAA" '.($form['listing']['facilitation_service_provider_id']=='NSAA'? "selected='selected'":"").'>MN - Manheim Northstar Minnesota</option>
											<option value="KCAA" '.($form['listing']['facilitation_service_provider_id']=='KCAA'? "selected='selected'":"").'>MO - Manheim Kansas City</option>
											<option value="MASC" '.($form['listing']['facilitation_service_provider_id']=='MASC'? "selected='selected'":"").'>MO - Manheim Missouri</option>
											<option value="SLAA" '.($form['listing']['facilitation_service_provider_id']=='SLAA'? "selected='selected'":"").'>MO - Manheim St. Louis</option>
											<option value="MISS" '.($form['listing']['facilitation_service_provider_id']=='MISS'? "selected='selected'":"").'>MS - Manheim Mississippi</option>
											<option value="RBMS" '.($form['listing']['facilitation_service_provider_id']=='RBMS'? "selected='selected'":"").'>MS - Rea Brothers Mid South</option>
											
											<option value="MONC" '.($form['listing']['facilitation_service_provider_id']=='MONC'? "selected='selected'":"").'>NB - Manheim Moncton</option>
											
											<option value="IGAA" '.($form['listing']['facilitation_service_provider_id']=='IGAA'? "selected='selected'":"").'>NC - Greensboro Auto Auction</option>
											<option value="AYCA" '.($form['listing']['facilitation_service_provider_id']=='AYCA'? "selected='selected'":"").'>NC - Manheim North Carolina</option>
											<option value="SVAA" '.($form['listing']['facilitation_service_provider_id']=='SVAA'? "selected='selected'":"").'>NC - Manheim Statesville</option>
											<option value="WILM" '.($form['listing']['facilitation_service_provider_id']=='WILM'? "selected='selected'":"").'>NC - Manheim Wilmington</option>
											<option value="XLIN" '.($form['listing']['facilitation_service_provider_id']=='XLIN'? "selected='selected'":"").'>NE - Lincoln Auto Auction</option>
											<option value="OMAA" '.($form['listing']['facilitation_service_provider_id']=='OMAA'? "selected='selected'":"").'>NE - Manheim Omaha</option>
											
											
											<option value="NADE" '.($form['listing']['facilitation_service_provider_id']=='NADE'? "selected='selected'":"").'>NJ - Manheim New Jersey</option>
											<option value="SKYA" '.($form['listing']['facilitation_service_provider_id']=='SKYA'? "selected='selected'":"").'>NJ - Manheim NY Metro Skyline</option>
											
											
											<option value="ALBA" '.($form['listing']['facilitation_service_provider_id']=='ALBA'? "selected='selected'":"").'>NM - Manheim New Mexico</option>
											
											
											<option value="LVAA" '.($form['listing']['facilitation_service_provider_id']=='LVAA'? "selected='selected'":"").'>NV - Manheim Las Vegas</option>
											<option value="NVAA" '.($form['listing']['facilitation_service_provider_id']=='NVAA'? "selected='selected'":"").'>NV - Manheim Nevada</option>
											<option value="NWE" '.($form['listing']['facilitation_service_provider_id']=='NWE'? "selected='selected'":"").'>NY - Manheim Albany</option>
											<option value="NAA" '.($form['listing']['facilitation_service_provider_id']=='NAA'? "selected='selected'":"").'>NY - Manheim New York</option>
											<option value="XAKR" '.($form['listing']['facilitation_service_provider_id']=='XAKR'? "selected='selected'":"").'>OH - Akron Auto Auction</option>
											<option value="XCOL" '.($form['listing']['facilitation_service_provider_id']=='XCOL'? "selected='selected'":"").'>OH - Columbus Fair Auto Auction</option>
											<option value="CINA" '.($form['listing']['facilitation_service_provider_id']=='CINA'? "selected='selected'":"").'>OH - Manheim Cincinnati</option>
											
											<option value="OAA" '.($form['listing']['facilitation_service_provider_id']=='OAA'? "selected='selected'":"").'>OH - Manheim Ohio</option>
											<option value="MONT" '.($form['listing']['facilitation_service_provider_id']=='MONT'? "selected='selected'":"").'>OH - Montpelier Auto Auction</option>
											<option value="DAOK" '.($form['listing']['facilitation_service_provider_id']=='DAOK'? "selected='selected'":"").'>OK - DAA Oklahoma City</option>
											<option value="OKLA" '.($form['listing']['facilitation_service_provider_id']=='OKLA'? "selected='selected'":"").'>OK - Manheim Oklahoma City</option>
											
											<option value="MODE" '.($form['listing']['facilitation_service_provider_id']=='MODE'? "selected='selected'":"").'>ON - Manheim Oshawa</option>
											<option value="TAA" '.($form['listing']['facilitation_service_provider_id']=='TAA'? "selected='selected'":"").'>ON - Manheim Toronto</option>
											
											<option value="PAA" '.($form['listing']['facilitation_service_provider_id']=='PAA'? "selected='selected'":"").'>OR - Manheim Portland</option>
											<option value="CORR" '.($form['listing']['facilitation_service_provider_id']=='CORR'? "selected='selected'":"").'>PA - Corry Auto Dealers Exchange</option>
											<option value="KEYA" '.($form['listing']['facilitation_service_provider_id']=='KEYA'? "selected='selected'":"").'>PA - Manheim Central Penn</option>
											
											<option value="MAA" '.($form['listing']['facilitation_service_provider_id']=='MAA'? "selected='selected'":"").'>PA - Manheim Pennsylvania</option>
											<option value="HATA" '.($form['listing']['facilitation_service_provider_id']=='HATA'? "selected='selected'":"").'>PA - Manheim Philadelphia</option>
											<option value="BAA" '.($form['listing']['facilitation_service_provider_id']=='BAA'? "selected='selected'":"").'>PA - Manheim Pittsburgh</option>
											<option value="PADE" '.($form['listing']['facilitation_service_provider_id']=='PADE'? "selected='selected'":"").'>PA - Pennsylvania Auto Dealers Exchange</option>
											<option value="XPIA" '.($form['listing']['facilitation_service_provider_id']=='XPIA'? "selected='selected'":"").'>PA - Pittsburgh Independent Auto Auction</option>
											
											<option value="MTRL" '.($form['listing']['facilitation_service_provider_id']=='MTRL'? "selected='selected'":"").'>QC - Manheim Montreal</option>
											
											<option value="XCAR" '.($form['listing']['facilitation_service_provider_id']=='XCAR'? "selected='selected'":"").'>SC - Carolina Auto Auction</option>
											<option value="CAAI" '.($form['listing']['facilitation_service_provider_id']=='CAAI'? "selected='selected'":"").'>SC - Manheim Darlington</option>
											<option value="XRAW" '.($form['listing']['facilitation_service_provider_id']=='XRAW'? "selected='selected'":"").'>SC - Rawls Auto Auction</option>
											<option value="XCHA" '.($form['listing']['facilitation_service_provider_id']=='XCHA'? "selected='selected'":"").'>TN - Chattanooga Auto Auction</option>
											
											<option value="TENN" '.($form['listing']['facilitation_service_provider_id']=='TENN'? "selected='selected'":"").'>TN - East Tennessee Auto Auction</option>
											<option value="NASH" '.($form['listing']['facilitation_service_provider_id']=='NASH'? "selected='selected'":"").'>TN - Manheim Nashville</option>
											<option value="TNAA" '.($form['listing']['facilitation_service_provider_id']=='TNAA'? "selected='selected'":"").'>TN - Manheim Tennessee</option>
											<option value="MEMP" '.($form['listing']['facilitation_service_provider_id']=='MEMP'? "selected='selected'":"").'>TN - Memphis Auto Auction</option>
											<option value="BIG" '.($form['listing']['facilitation_service_provider_id']=='BIG'? "selected='selected'":"").'>TX - Big State Auto Auction</option>
											<option value="DALA" '.($form['listing']['facilitation_service_provider_id']=='DALA'? "selected='selected'":"").'>TX - Manheim Dallas</option>
											<option value="DFWA" '.($form['listing']['facilitation_service_provider_id']=='DFWA'? "selected='selected'":"").'>TX - Manheim Dallas-Ft Worth</option>
											<option value="DAAE" '.($form['listing']['facilitation_service_provider_id']=='DAAE'? "selected='selected'":"").'>TX - Manheim El Paso</option>
											
											<option value="FWAA" '.($form['listing']['facilitation_service_provider_id']=='FWAA'? "selected='selected'":"").'>TX - Manheim Fort Worth</option>
											<option value="BIGH" '.($form['listing']['facilitation_service_provider_id']=='BIGH'? "selected='selected'":"").'>TX - Manheim Houston</option>
											<option value="DAAD" '.($form['listing']['facilitation_service_provider_id']=='DAAD'? "selected='selected'":"").'>TX - Manheim Metro Dallas</option>
											<option value="SAAA" '.($form['listing']['facilitation_service_provider_id']=='SAAA'? "selected='selected'":"").'>TX - Manheim San Antonio</option>
											<option value="THAA" '.($form['listing']['facilitation_service_provider_id']=='THAA'? "selected='selected'":"").'>TX - Manheim Texas Hobby</option>
											<option value="ASLC" '.($form['listing']['facilitation_service_provider_id']=='ASLC'? "selected='selected'":"").'>UT - Brasher\'s Salt Lake Auto Auction</option>
											<option value="UAA" '.($form['listing']['facilitation_service_provider_id']=='UAA'? "selected='selected'":"").'>UT - Manheim Utah</option>
											<option value="FAA" '.($form['listing']['facilitation_service_provider_id']=='FAA'? "selected='selected'":"").'>VA - Manheim Fredericksburg</option>
											<option value="HAA" '.($form['listing']['facilitation_service_provider_id']=='HAA'? "selected='selected'":"").'>VA - Manheim Harrisonburg</option>
											<option value="VVEA" '.($form['listing']['facilitation_service_provider_id']=='VVEA'? "selected='selected'":"").'>VA - Manheim Virginia</option>
											
											<option value="XNWT" '.($form['listing']['facilitation_service_provider_id']=='XNWT'? "selected='selected'":"").'>WA - DAA Northwest</option>
											<option value="SSAA" '.($form['listing']['facilitation_service_provider_id']=='SSAA'? "selected='selected'":"").'>WA - Manheim Seattle</option>
											
											<option value="MMAA" '.($form['listing']['facilitation_service_provider_id']=='MMAA'? "selected='selected'":"").'>WI - Manheim Milwaukee</option>
											<option value="WEST" '.($form['listing']['facilitation_service_provider_id']=='WEST'? "selected='selected'":"").'>WI - Western Wisconsin Auto Auction</option>
											<option value="WISC" '.($form['listing']['facilitation_service_provider_id']=='WISC'? "selected='selected'":"").'>WI - Wisconsin Auto Auction</option>
											<option value="XCCA" '.($form['listing']['facilitation_service_provider_id']=='XCCA'? "selected='selected'":"").'>WV - Capital City Auto Auction</option>
											<option value="XMTN" '.($form['listing']['facilitation_service_provider_id']=='XMTN'? "selected='selected'":"").'>WV - Mountain State Auto Auction</option>
										</optgroup>
										
										
									</select>
								</td>
							</tr>
							<tr class="rowA">
								<td class="title" align="right"><span class="required_marker">*</span>Title Status:</td>
								<td>
									<select accept="true" defval="" id="listing_title_status_id" name="listing[title_status_id]" msg="Title Status">
										<option value="" '.($form['listing']['title_status_id']=='7'? "selected='selected'":"").'>Not Specified</option>
										<option value="Title Present" '.($form['listing']['title_status_id']=='Title Present'? "selected='selected'":"").'>Title Present</option>
										<option value="Title Absent" '.($form['listing']['title_status_id']=='Title Absent'? "selected='selected'":"").'>Title Absent</option>
										
										<option value="Branded" '.($form['listing']['title_status_id']=='Branded'? "selected='selected'":"").'>Branded</option>
										<option value="MSO" '.($form['listing']['title_status_id']=='MSO'? "selected='selected'":"").'>MSO</option>
										<option value="No Title" '.($form['listing']['title_status_id']=='No Title'? "selected='selected'":"").'>No Title</option>
										<option value="Repo Affidavit" '.($form['listing']['title_status_id']=='Repo Affidavit'? "selected='selected'":"").'>Repo Affidavit</option>
										<option value="Salvage" '.($form['listing']['title_status_id']=='Salvage'? "selected='selected'":"").'>Salvage</option>
									</select> 
								</td>
							</tr>
							<tr class="rowB">
								<td class="title" align="right"><span class="required_marker">*</span>Titled In:</td>
								<td>
									<select accept="true" id="listing_state_id" name="listing[state_id]" msg="Titled In">
										<option value="" '.($form['listing']['state_id']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="AB" '.($form['listing']['state_id']=='AB'? "selected='selected'":"").'>AB</option>
										<option value="AK" '.($form['listing']['state_id']=='AK'? "selected='selected'":"").'>AK</option>
										<option value="AL" '.($form['listing']['state_id']=='AL'? "selected='selected'":"").'>AL</option>
										<option value="AR" '.($form['listing']['state_id']=='AR'? "selected='selected'":"").'>AR</option>
										<option value="AZ" '.($form['listing']['state_id']=='AZ'? "selected='selected'":"").'>AZ</option>
										<option value="BC" '.($form['listing']['state_id']=='BC'? "selected='selected'":"").'>BC</option>
										<option value="CA" '.($form['listing']['state_id']=='CA'? "selected='selected'":"").'>CA</option>
						
										<option value="CO" '.($form['listing']['state_id']=='CO'? "selected='selected'":"").'>CO</option>
										<option value="CT" '.($form['listing']['state_id']=='CT'? "selected='selected'":"").'>CT</option>
										<option value="DC" '.($form['listing']['state_id']=='DC'? "selected='selected'":"").'>DC</option>
										<option value="DE" '.($form['listing']['state_id']=='DE'? "selected='selected'":"").'>DE</option>
										<option value="FL" '.($form['listing']['state_id']=='FL'? "selected='selected'":"").'>FL</option>
										<option value="GA" '.($form['listing']['state_id']=='GA'? "selected='selected'":"").'>GA</option>
										<option value="HI" '.($form['listing']['state_id']=='HI'? "selected='selected'":"").'>HI</option>
										<option value="IA" '.($form['listing']['state_id']=='IA'? "selected='selected'":"").'>IA</option>
										<option value="ID" '.($form['listing']['state_id']=='ID'? "selected='selected'":"").'>ID</option>
										
										<option value="IL" '.($form['listing']['state_id']=='IL'? "selected='selected'":"").'>IL</option>
										<option value="IN" '.($form['listing']['state_id']=='IN'? "selected='selected'":"").'>IN</option>
										<option value="KS" '.($form['listing']['state_id']=='KS'? "selected='selected'":"").'>KS</option>
										<option value="KY" '.($form['listing']['state_id']=='KY'? "selected='selected'":"").'>KY</option>
										<option value="LA" '.($form['listing']['state_id']=='LA'? "selected='selected'":"").'>LA</option>
										<option value="MA" '.($form['listing']['state_id']=='MA'? "selected='selected'":"").'>MA</option>
										<option value="MB" '.($form['listing']['state_id']=='MB'? "selected='selected'":"").'>MB</option>
										<option value="MD" '.($form['listing']['state_id']=='MD'? "selected='selected'":"").'>MD</option>
										<option value="ME" '.($form['listing']['state_id']=='ME'? "selected='selected'":"").'>ME</option>
						
										<option value="MI" '.($form['listing']['state_id']=='MI'? "selected='selected'":"").'>MI</option>
										<option value="MN" '.($form['listing']['state_id']=='MN'? "selected='selected'":"").'>MN</option>
										<option value="MO" '.($form['listing']['state_id']=='MO'? "selected='selected'":"").'>MO</option>
										<option value="MS" '.($form['listing']['state_id']=='MS'? "selected='selected'":"").'>MS</option>
										<option value="MT" '.($form['listing']['state_id']=='MT'? "selected='selected'":"").'>MT</option>
										<option value="NB" '.($form['listing']['state_id']=='NB'? "selected='selected'":"").'>NB</option>
										<option value="NC" '.($form['listing']['state_id']=='NC'? "selected='selected'":"").'>NC</option>
										<option value="ND" '.($form['listing']['state_id']=='ND'? "selected='selected'":"").'>ND</option>
										<option value="NE" '.($form['listing']['state_id']=='NE'? "selected='selected'":"").'>NE</option>
										
										<option value="NH" '.($form['listing']['state_id']=='NH'? "selected='selected'":"").'>NH</option>
										<option value="NJ" '.($form['listing']['state_id']=='NJ'? "selected='selected'":"").'>NJ</option>
										<option value="NL" '.($form['listing']['state_id']=='NL'? "selected='selected'":"").'>NL</option>
										<option value="NM" '.($form['listing']['state_id']=='NM'? "selected='selected'":"").'>NM</option>
										<option value="NS" '.($form['listing']['state_id']=='NS'? "selected='selected'":"").'>NS</option>
										<option value="NT" '.($form['listing']['state_id']=='NT'? "selected='selected'":"").'>NT</option>
										<option value="NU" '.($form['listing']['state_id']=='NU'? "selected='selected'":"").'>NU</option>
										<option value="NV" '.($form['listing']['state_id']=='NV'? "selected='selected'":"").'>NV</option>
										<option value="NY" '.($form['listing']['state_id']=='NY'? "selected='selected'":"").'>NY</option>
						
										<option value="OH" '.($form['listing']['state_id']=='OH'? "selected='selected'":"").'>OH</option>
										<option value="OK" '.($form['listing']['state_id']=='OK'? "selected='selected'":"").'>OK</option>
										<option value="ON" '.($form['listing']['state_id']=='ON'? "selected='selected'":"").'>ON</option>
										<option value="OR" '.($form['listing']['state_id']=='OR'? "selected='selected'":"").'>OR</option>
										<option value="PA" '.($form['listing']['state_id']=='PA'? "selected='selected'":"").'>PA</option>
										<option value="PE" '.($form['listing']['state_id']=='PE'? "selected='selected'":"").'>PE</option>
										<option value="PR" '.($form['listing']['state_id']=='PR'? "selected='selected'":"").'>PR</option>
										<option value="QC" '.($form['listing']['state_id']=='QC'? "selected='selected'":"").'>QC</option>
										<option value="RI" '.($form['listing']['state_id']=='RI'? "selected='selected'":"").'>RI</option>
										
										<option value="SC" '.($form['listing']['state_id']=='SC'? "selected='selected'":"").'>SC</option>
										<option value="SD" '.($form['listing']['state_id']=='SD'? "selected='selected'":"").'>SD</option>
										<option value="SK" '.($form['listing']['state_id']=='SK'? "selected='selected'":"").'>SK</option>
										<option value="TN" '.($form['listing']['state_id']=='TN'? "selected='selected'":"").'>TN</option>
										<option value="TX" '.($form['listing']['state_id']=='TX'? "selected='selected'":"").'>TX</option>
										<option value="UT" '.($form['listing']['state_id']=='UT'? "selected='selected'":"").'>UT</option>
										<option value="VA" '.($form['listing']['state_id']=='VA'? "selected='selected'":"").'>VA</option>
										<option value="VT" '.($form['listing']['state_id']=='VT'? "selected='selected'":"").'>VT</option>
										<option value="WA" '.($form['listing']['state_id']=='WA'? "selected='selected'":"").'>WA</option>
										
										<option value="WI" '.($form['listing']['state_id']=='WI'? "selected='selected'":"").'>WI</option>
										<option value="WV" '.($form['listing']['state_id']=='WV'? "selected='selected'":"").'>WV</option>
										<option value="WY" '.($form['listing']['state_id']=='WY'? "selected='selected'":"").'>WY</option>
										<option value="YT" '.($form['listing']['state_id']=='YT'? "selected='selected'":"").'>YT</option>
									</select>
								</td>
							</tr>
							
							<tr class="rowA title">
								<td class="title" align="right"><span class="required_marker">*</span>Odometer:</td>
								<td>
									<input value="'.((@$form['listing']['odometer'])?$form['listing']['odometer']:$this->car_info['milage']).'" accept="true" mask="numeric" msg="Odometer" id="listing_odometer" name="listing[odometer]" size="12" type="text">
									<select accept="true" msg="Odometer type" id="listing_odometerunit" name="listing[odometerunit]" style="width:150px">
										<option value="kilometers" '.($form['listing']['odometerunit']=='kilometers'? "selected='selected'":"").'>kilometers</option>
										<option value="miles" '.($form['listing']['odometerunit']=='miles'? "selected='selected'":"").'>miles</option>
										<option value="hours" '.($form['listing']['odometerunit']=='hours'? "selected='selected'":"").'>hours</option>
									</select>
								</td>
							</tr>
							
						</table>
					</td>
				</tr>
				<tr class="rowA title">
					<td colspan="2" class="title_division">Vehicle Specs</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-left:3%">
						<table class="form_group" cellspacing="0" width="90%">
							<tr class="rowB">
								<td class="title" align="right">Engine Cylinders:</td>
								<td id="engine_selection">
									<select id="listing_engine_option" name="listing[engine_option]">
										<option value="" '.($form['listing']['engine_option']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="1 Cylinder" '.($form['listing']['engine_option']=='1 Cylinder'? "selected='selected'":"").'>1 Cylinder</option>
										<option value="10 Cylinder" '.($form['listing']['engine_option']=='10 Cylinder'? "selected='selected'":"").'>10 Cylinder</option>
										<option value="12 Cylinder" '.($form['listing']['engine_option']=='12 Cylinder'? "selected='selected'":"").'>12 Cylinder</option>
										<option value="2 Cylinder" '.($form['listing']['engine_option']=='2 Cylinder'? "selected='selected'":"").'>2 Cylinder</option>
										<option value="3 Cylinder" '.($form['listing']['engine_option']=='3 Cylinder'? "selected='selected'":"").'>3 Cylinder</option>
										<option value="4 Cylinder" '.($form['listing']['engine_option']=='4 Cylinder'? "selected='selected'":"").'>4 Cylinder</option>
										<option value="5 Cylinder" '.($form['listing']['engine_option']=='5 Cylinder'? "selected='selected'":"").'>5 Cylinder</option>
										<option value="6 Cylinder" '.($form['listing']['engine_option']=='6 Cylinder'? "selected='selected'":"").'>6 Cylinder</option>
										<option value="7 Cylinder" '.($form['listing']['engine_option']=='7 Cylinder'? "selected='selected'":"").'>7 Cylinder</option>
										<option value="8 Cylinder" '.($form['listing']['engine_option']=='5'? "selected='selected'":"").'>8 Cylinder</option>
										<option value="Rotary" '.($form['listing']['engine_option']=='Rotary'? "selected='selected'":"").'>Rotary</option>
									</select>
								</td>
							
								<td class="title" align="right">Transmission:</td>
								<td id="transmission_selection"> 
									<select id="listing_transmission_option" name="listing[transmission_option]">
										<option value="" '.($form['listing']['transmission_option']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="Automatic" '.($form['listing']['transmission_option']=='Automatic'? "selected='selected'":"").'>Automatic</option>
										<option value="Manual" '.($form['listing']['transmission_option']=='Manual'? "selected='selected'":"").'>Manual</option>
									</select>
								</td>
							</tr>
							<tr class="rowA">
								<td class="title" align="right">Drive Train:</td>
								<td id="drive_train_selection"> 
									<select id="listing_drive_train_option" name="listing[drive_train_option]">
										<option value="" '.($form['listing']['drive_train_option']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="2 Wheel Drive" '.($form['listing']['drive_train_option']=='2 Wheel Drive'? "selected='selected'":"").'>2 Wheel Drive</option>
										<option value="4 Wheel Drive" '.($form['listing']['drive_train_option']=='4 Wheel Drive'? "selected='selected'":"").'>4 Wheel Drive</option>
										<option value="All Wheel Drive" '.($form['listing']['drive_train_option']=='All Wheel Drive'? "selected='selected'":"").'>All Wheel Drive</option>
									</select>
								</td>
							
								<td class="title" align="right">Fuel Type:</td>
								<td id="fuel_selection"> 
									<select id="listing_fuel_option" name="listing[fuel_option]">
										<option value="" '.($form['listing']['fuel_option']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="Alternative/Hybrid" '.($form['listing']['fuel_option']=='Alternative/Hybrid'? "selected='selected'":"").'>Alternative/Hybrid</option>
										<option value="Diesel" '.($form['listing']['fuel_option']=='Diesel'? "selected='selected'":"").'>Diesel</option>
										<option value="Gasoline" '.($form['listing']['fuel_option']=='Gasoline'? "selected='selected'":"").'>Gasoline</option>
									</select>
								</td>
							</tr>
							<tr class="rowB">
								<td class="title" align="right">Audio:</td>
								<td id="audio_type_selection"> 
									<select id="listing_audio_type_option" name="listing[audio_type_option]">
										<option value="" '.($form['listing']['audio_type_option']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="AM" '.($form['listing']['audio_type_option']=='AM'? "selected='selected'":"").'>AM</option>
										<option value="AM/FM" '.($form['listing']['audio_type_option']=='AM/FM'? "selected='selected'":"").'>AM/FM</option>
										<option value="AM/FM/CD" '.($form['listing']['audio_type_option']=='AM/FM/CD'? "selected='selected'":"").'>AM/FM/CD</option>
										<option value="AM/FM/Cassette" '.($form['listing']['audio_type_option']=='AM/FM/Cassette'? "selected='selected'":"").'>AM/FM/Cassette</option>
										<option value="AM/FM/Cassette/CD" '.($form['listing']['audio_type_option']=='AM/FM/Cassette/CD'? "selected='selected'":"").'>AM/FM/Cassette/CD</option>
										<option value="AM/FM/Satelite/CD" '.($form['listing']['audio_type_option']=='AM/FM/Satelite/CD'? "selected='selected'":"").'>AM/FM/Satelite/CD</option>
										<option value="AM/FM/Satellite" '.($form['listing']['audio_type_option']=='AM/FM/Satellite'? "selected='selected'":"").'>AM/FM/Satellite</option>
										<option value="Premium" '.($form['listing']['audio_type_option']=='Premium'? "selected='selected'":"").'>Premium</option>
									</select>
								</td>
							
								<td class="title" align="right">Body Style:</td>
								<td id="body_type_selection"> 
									<select id="listing_body_type_option" name="listing[body_type_option]">
										<option value="" '.($form['listing']['body_type_option']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="Convertible" '.($form['listing']['body_type_option']=='Convertible'? "selected='selected'":"").'>Convertible</option>
										<option value="Coupe" '.($form['listing']['body_type_option']=='Coupe'? "selected='selected'":"").'>Coupe</option>
										<option value="Hatchback" '.($form['listing']['body_type_option']=='Hatchback'? "selected='selected'":"").'>Hatchback</option>
										<option value="SUV" '.($form['listing']['body_type_option']=='SUV'? "selected='selected'":"").'>SUV</option>
										<option value="Sedan" '.($form['listing']['body_type_option']=='Sedan'? "selected='selected'":"").'>Sedan</option>
										<option value="Truck" '.($form['listing']['body_type_option']=='Truck'? "selected='selected'":"").'>Truck</option>
										<option value="Van" '.($form['listing']['body_type_option']=='Van'? "selected='selected'":"").'>Van</option>
										<option value="Wagon" '.($form['listing']['body_type_option']=='Wagon'? "selected='selected'":"").'>Wagon</option>
									</select>
								</td>
							</tr>
							<tr class="rowA">
								<td class="title" align="right">Top Style:</td>
								<td id="top_type_selection"> 
									<select id="listing_top_type_option" name="listing[top_type_option]">
										<option value="" '.($form['listing']['top_type_option']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="Convertible" '.($form['listing']['top_type_option']=='Convertible'? "selected='selected'":"").'>Convertible</option>
										<option value="Hard Top" '.($form['listing']['top_type_option']=='Hard Top'? "selected='selected'":"").'>Hard Top</option>
										<option value="Moon Roof" '.($form['listing']['top_type_option']=='Moon Roof'? "selected='selected'":"").'>Moon Roof</option>
										<option value="T-Top" '.($form['listing']['top_type_option']=='T-Top'? "selected='selected'":"").'>T-Top</option>
										<option value="Vinyl Roof" '.($form['listing']['top_type_option']=='Vinyl Roof'? "selected='selected'":"").'>Vinyl Roof</option>
									</select>
								</td>

								<td class="title" align="right">Interior Type:</td>
								<td id="interior_type_selection"> 
									<select id="listing_interior_type_option" name="listing[interior_type_option]">
										<option value="" '.($form['listing']['interior_type_option']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="Cloth" '.($form['listing']['interior_type_option']=='Cloth'? "selected='selected'":"").'>Cloth</option>
										<option value="Leather" '.($form['listing']['interior_type_option']=='Leather'? "selected='selected'":"").'>Leather</option>
									
										<option value="Vinyl" '.($form['listing']['interior_type_option']=='Vinyl'? "selected='selected'":"").'>Vinyl</option>
									</select>
								</td>
							</tr>
							<tr class="rowB">
								<td class="title" align="right">Doors:</td>
								<td id="door_selection"> 
									<select id="listing_door_option" name="listing[door_option]">
										<option value="" '.($form['listing']['door_option']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="5" '.($form['listing']['door_option']=='5'? "selected='selected'":"").'>Five Doors</option>
										<option value="4" '.($form['listing']['door_option']=='4'? "selected='selected'":"").'>Four Doors</option>
										<option value="1" '.($form['listing']['door_option']=='1'? "selected='selected'":"").'>One Door</option>
										<option value="3" '.($form['listing']['door_option']=='3'? "selected='selected'":"").'>Three Doors</option>
										<option value="2" '.($form['listing']['door_option']=='2'? "selected='selected'":"").'>Two Doors</option>
									</select>
								</td>
								<td class="title" align="right">Exterior Color:</td>
								<td>
								<select id="listing_exterior_color" name="listing[exterior_color]">
										<option value="" '.($form['listing']['exterior_color']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="Beige" '.($form['listing']['exterior_color']=='Beige'? "selected='selected'":"").'>Beige</option>
										<option value="Black" '.($form['listing']['exterior_color']=='Black'? "selected='selected'":"").'>Black</option>
										<option value="Blue" '.($form['listing']['exterior_color']=='Blue'? "selected='selected'":"").'>Blue</option>
										<option value="Brown" '.($form['listing']['exterior_color']=='Brown'? "selected='selected'":"").'>Brown</option>
										<option value="Burgundy" '.($form['listing']['exterior_color']=='Burgundy'? "selected='selected'":"").'>Burgundy</option>
										<option value="Camouflage" '.($form['listing']['exterior_color']=='Camouflage'? "selected='selected'":"").'>Camouflage</option>
										<option value="Charcoal" '.($form['listing']['exterior_color']=='Charcoal'? "selected='selected'":"").'>Charcoal</option>
										<option value="Cream" '.($form['listing']['exterior_color']=='Cream'? "selected='selected'":"").'>Cream</option>
										<option value="Gold" '.($form['listing']['exterior_color']=='Gold'? "selected='selected'":"").'>Gold</option>
										<option value="Gray" '.($form['listing']['exterior_color']=='Gray'? "selected='selected'":"").'>Gray</option>
										<option value="Green" '.($form['listing']['exterior_color']=='Green'? "selected='selected'":"").'>Green</option>
										<option value="Lime" '.($form['listing']['exterior_color']=='Lime'? "selected='selected'":"").'>Lime</option>
										<option value="Off-white" '.($form['listing']['exterior_color']=='Off-white'? "selected='selected'":"").'>Off-white</option>
										<option value="Orange" '.($form['listing']['exterior_color']=='Orange'? "selected='selected'":"").'>Orange</option>
										<option value="Pink" '.($form['listing']['exterior_color']=='Pink'? "selected='selected'":"").'>Pink</option>
										<option value="Purple" '.($form['listing']['exterior_color']=='Purple'? "selected='selected'":"").'>Purple</option>
										<option value="Red" '.($form['listing']['exterior_color']=='Red'? "selected='selected'":"").'>Red</option>
										<option value="Silver" '.($form['listing']['exterior_color']=='Silver'? "selected='selected'":"").'>Silver</option>
										<option value="Turquoise" '.($form['listing']['exterior_color']=='Turquoise'? "selected='selected'":"").'>Turquoise</option>
										<option value="White" '.($form['listing']['exterior_color']=='White'? "selected='selected'":"").'>White</option>
										<option value="Yellow" '.($form['listing']['exterior_color']=='Yellow'? "selected='selected'":"").'>Yellow</option>
										<option value="Other" '.($form['listing']['exterior_color']=='Other'? "selected='selected'":"").'>Other</option>
										
									</select>
								
								</td>
							</tr>
							
							<tr class="rowA">
								<td class="title" align="right">Interior Color:</td>
								<td> 
									<select id="listing_interior_color" name="listing[interior_color]">
										<option value="" '.($form['listing']['interior_color']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="Beige" '.($form['listing']['interior_color']=='Beige'? "selected='selected'":"").'>Beige</option>
										<option value="Black" '.($form['listing']['interior_color']=='Black'? "selected='selected'":"").'>Black</option>
										<option value="Blue" '.($form['listing']['interior_color']=='Blue'? "selected='selected'":"").'>Blue</option>
										<option value="Brown" '.($form['listing']['interior_color']=='Brown'? "selected='selected'":"").'>Brown</option>
										<option value="Burgundy" '.($form['listing']['interior_color']=='Burgundy'? "selected='selected'":"").'>Burgundy</option>
										<option value="Camouflage" '.($form['listing']['interior_color']=='Camouflage'? "selected='selected'":"").'>Camouflage</option>
										<option value="Charcoal" '.($form['listing']['interior_color']=='Charcoal'? "selected='selected'":"").'>Charcoal</option>
										<option value="Cream" '.($form['listing']['interior_color']=='Cream'? "selected='selected'":"").'>Cream</option>
										<option value="Gold" '.($form['listing']['interior_color']=='Gold'? "selected='selected'":"").'>Gold</option>
										<option value="Gray" '.($form['listing']['interior_color']=='Gray'? "selected='selected'":"").'>Gray</option>
										<option value="Green" '.($form['listing']['interior_color']=='Green'? "selected='selected'":"").'>Green</option>
										<option value="Lime" '.($form['listing']['interior_color']=='Lime'? "selected='selected'":"").'>Lime</option>
										<option value="Off-white" '.($form['listing']['interior_color']=='Off-white'? "selected='selected'":"").'>Off-white</option>
										<option value="Orange" '.($form['listing']['interior_color']=='Orange'? "selected='selected'":"").'>Orange</option>
										<option value="Pink" '.($form['listing']['interior_color']=='Pink'? "selected='selected'":"").'>Pink</option>
										<option value="Purple" '.($form['listing']['interior_color']=='Purple'? "selected='selected'":"").'>Purple</option>
										<option value="Red" '.($form['listing']['interior_color']=='Red'? "selected='selected'":"").'>Red</option>
										<option value="Silver" '.($form['listing']['interior_color']=='Silver'? "selected='selected'":"").'>Silver</option>
										<option value="Turquoise" '.($form['listing']['interior_color']=='Turquoise'? "selected='selected'":"").'>Turquoise</option>
										<option value="White" '.($form['listing']['interior_color']=='White'? "selected='selected'":"").'>White</option>
										<option value="Yellow" '.($form['listing']['interior_color']=='Yellow'? "selected='selected'":"").'>Yellow</option>
										<option value="Other" '.($form['listing']['interior_color']=='Other'? "selected='selected'":"").'>Other</option>
										
									</select>
								</td>
								<td class="title" align="right">&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							
							
						</table>
					</td>
				</tr>
				<tr class="rowA title">
					<td colspan="2" class="title_division">Optional Vehicle Equipment</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-left:3%">
						<table class="optional_equipment_row" cellspacing="0" width="90%">
							<tr class="rowB">
								<td style="padding-left: 11%" colspan="4">
									<a onclick="javascript:toggleAllEquipment(true); return false;" href="#">Check All</a>&nbsp;&nbsp;
									<a onclick="javascript:toggleAllEquipment(false); return false;" href="#">Uncheck All</a>
								</td>
							</tr>
							<tr class="rowA">
								<td style="padding-left: 11%;padding-right:45px;" nowrap>
									<input id="listing_equipment_ids_9" '.(in_array('AirBagSideCurtain', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="AirBagSideCurtain" />
									<label for="listing_equipment_ids_9">Air Bag - Side Curtain</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_1" '.(in_array('AirConditioning', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="AirConditioning" />
									<label for="listing_equipment_ids_1">Air Conditioning</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_10" '.(in_array('Airbags', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="Airbags" />
									<label for="listing_equipment_ids_10">Airbags</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_13" '.(in_array('AlloyWheels', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="AlloyWheels" />
									<label for="listing_equipment_ids_13">Alloy Wheels</label>&nbsp;
								</td>
							</tr>
							<tr class="rowB">
								<td style="padding-left: 11%;padding-right:45px;" nowrap>
									<input id="listing_equipment_ids_15" '.(in_array('AntilockBrakes', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="AntilockBrakes" />
									<label for="listing_equipment_ids_15">Antilock Brakes</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_3" '.(in_array('ChildSeat', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="ChildSeat" />
									<label for="listing_equipment_ids_3">Child Seat</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_4" '.(in_array('ChildSeatAnchors', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="ChildSeatAnchors" />
									<label for="listing_equipment_ids_4">Child Seat Anchors</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_2" '.(in_array('CruiseControl', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="CruiseControl" />
									<label for="listing_equipment_ids_2">Cruise Control</label>&nbsp;
								</td>
							</tr>
							<tr class="rowA">
								<td style="padding-left: 11%;padding-right:45px;" nowrap>
									<input id="listing_equipment_ids_20" '.(in_array('DualClimateControl', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="DualClimateControl" />
									<label for="listing_equipment_ids_20">Dual Climate Control</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_18" '.(in_array('DVD', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="DVD" />
									<label for="listing_equipment_ids_18">DVD</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_19" '.(in_array('Navigation', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="Navigation" />
									<label for="listing_equipment_ids_19">GPS</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_21" '.(in_array('HeatedSeats', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="HeatedSeats" />
									<label for="listing_equipment_ids_21">Heated Seats</label>&nbsp;
								</td>
							</tr>
							<tr class="rowB">
								<td style="padding-left: 11%;padding-right:45px;" nowrap>
									<input id="listing_equipment_ids_14" '.(in_array('PowerBrakes', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="PowerBrakes" />
									<label for="listing_equipment_ids_14">Power Brakes</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_5" '.(in_array('PowerDoorLocks', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="PowerDoorLocks" />
									<label for="listing_equipment_ids_5">Power Door Locks</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_6" '.(in_array('PowerMirrors', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="PowerMirrors" />
									<label for="listing_equipment_ids_6">Power Mirrors</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_16" '.(in_array('PowerSeats', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="PowerSeats" />
									<label for="listing_equipment_ids_16">Power Seats</label>&nbsp;
								</td>
							</tr>
							<tr class="rowA">
								<td style="padding-left: 11%;padding-right:45px;" nowrap>
									<input id="listing_equipment_ids_17" '.(in_array('PowerSteering', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="PowerSteering" />
									<label for="listing_equipment_ids_17">Power Steering</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_7" '.(in_array('PowerWindows', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="PowerWindows" />
									<label for="listing_equipment_ids_7">Power Windows</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_8" '.(in_array('RearWindowDefroster', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="RearWindowDefroster" />
									<label for="listing_equipment_ids_8">Rear Window Defroster</label>&nbsp;
								</td>
								<td>
									<input id="listing_equipment_ids_11" '.(in_array('TiltTeleWheel', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="TiltTeleWheel" />
									<label for="listing_equipment_ids_11">Tilt/Telescope Wheel</label>&nbsp;
								</td>
							</tr>
							<tr class="rowB">
								<td style="padding-left: 11%;padding-right:45px;" nowrap colspan="4">
									<input id="listing_equipment_ids_12" '.(in_array('TintedGlass', $form['listing']['equipment_ids'])? 'checked="checked"':'').' name="listing[equipment_ids][]" type="checkbox" value="TintedGlass" />
									<label for="listing_equipment_ids_12">Tinted Glass</label>&nbsp;
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="rowA title">
					<td colspan="2" class="title_division">Vehicle Condition</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-left:3%">
						<table cellspacing=0 width="90%">
							<tr class="rowB">
								<td class="title" width="100" style="padding-left: 100px;"><span class="required_marker">*</span>Frame Damage:</td>
								<td id="frame_damage_cell" colspan="3">
									<input id="frame_damage_yes" '.($form['listing']['frame_damage']==1? 'checked="checked"':'').' name="listing[frame_damage]" onclick="$(\'#frame_damage_no\').get(0).checked = false" value="1" type="checkbox"> Yes 
									<input id="frame_damage_no" '.($form['listing']['frame_damage']==0? 'checked="checked"':'').' name="listing[frame_damage]" onclick="$(\'#frame_damage_yes\').get(0).checked = false" value="0" type="checkbox"> No
								</td>
								<td></td>
							</tr>
							<tr class="rowA">
								<td class="title" width="100" style="padding-left: 100px;"><span class="required_marker">*</span>Prior Paint:</td>  
								<td id="prior_paint_cell" colspan="3">
									<input id="prior_paint_yes" '.($form['listing']['prior_paint']==1? 'checked="checked"':'').' name="listing[prior_paint]" onclick="$(\'#prior_paint_no\').get(0).checked = false; $(\'#prior_paint_details\').toggle();" value="1" type="checkbox"> Yes 
									<input id="prior_paint_no" '.($form['listing']['prior_paint']==0? 'checked="checked"':'').' name="listing[prior_paint]" onclick="$(\'#prior_paint_yes\').get(0).checked = false; $(\'#prior_paint_details\').hide();" value="0" type="checkbox"> No    
								</td>
								<td></td>
							</tr>
							<tr class="rowB" id="prior_paint_details" style="display: '.($form['listing']['prior_paint']==1? '':'none').';">
								<td class="title" width="100" style="padding-left: 100px;">
									Details:
								</td>
								<td>
									<textarea id="prior_paint_details_text" name="listing[prior_paint_details]" onkeyup=\'if (this.value.length > 50) { this.value = this.value.truncate(50, ""); }\'>'.$form['listing']['prior_paint_details'].'</textarea>
								</td>
								<td></td>
							</tr>
						</table>
					</td>
				</tr>
				
				<tr class="rowA title">
					<td colspan="2" class="title_division">Exterior Condition
									<span class="checkbox_inside_h3">
										<input name="listing[no_exterior_damage]" value="0" type="hidden">
										<input id="listing_no_exterior_damage" '.($form['listing']['no_exterior_damage']==1? 'checked="checked"':'').' name="listing[no_exterior_damage]" value="1" type="checkbox">No Damage
									</span></td>
				</tr>
				<tr>
					<td colspan="2" style="padding-left:3%">
						<script>
						function addRow(type)
						{
							var table = $(\'#\'+type+\'_damages\');
							var rowClone = $(\'tr:eq(1)\', table).clone();
							$("option",rowClone).removeAttr("selected");
							$("tr:last",table).before(rowClone);
							return false;
						}
						</script>
						<table id="exterior_damages" width="90%" cellspacing=0 border=0>
							<tr class="rowB">
								<td style="width:90px">&nbsp;</td>
								<td><span class="title">Description</span></td>
								<td><span class="title">Condition</span></td>
								<td><span class="title">Severity</span></td>
							</tr>';
			
			$exterior_disclosed_damages_chunk = array_chunk($form['listing']['exterior_disclosed_damages'], 3);

			for($i=0;$i<count($exterior_disclosed_damages_chunk);$i++)
			{
				$damage_description_id = $exterior_disclosed_damages_chunk[$i][0]['damage_description_id'];
				$damage_condition_id = $exterior_disclosed_damages_chunk[$i][1]['damage_condition_id'];
				$damage_severity_id = $exterior_disclosed_damages_chunk[$i][2]['damage_severity_id'];
				$this->page.='<tr class="'.($i%2? 'rowB':'rowA').'">
								<td>&nbsp;</td>
								<td>
									<select class="damage_description" name="listing[exterior_disclosed_damages][][damage_description_id]">
										<option value="" '.($damage_description_id==''? "selected='selected'":"").'>Not Specified</option>
										<option value="Overall Vehicle" '.($damage_description_id=='Overall Vehicle'? "selected='selected'":"").'>Overall Vehicle</option>
										<option value="Grill" '.($damage_description_id=='Grill'? "selected='selected'":"").'>Grill</option>
										<option value="Front Bumper" '.($damage_description_id=='Front Bumper'? "selected='selected'":"").'>Front Bumper</option>
										<option value="Headlight" '.($damage_description_id=='Headlight'? "selected='selected'":"").'>Headlight</option>
										<option value="Hood" '.($damage_description_id=='Hood'? "selected='selected'":"").'>Hood</option>
										<option value="Windshield" '.($damage_description_id=='Windshield'? "selected='selected'":"").'>Windshield</option>
										<option value="Left Fender" '.($damage_description_id=='Left Fender'? "selected='selected'":"").'>Left Fender</option>
										<option value="Left Front Wheel" '.($damage_description_id=='Left Front Wheel'? "selected='selected'":"").'>Left Front Wheel</option>
										<option value="Left Front Door" '.($damage_description_id=='Left Front Door'? "selected='selected'":"").'>Left Front Door</option>
										<option value="Left Rear Door" '.($damage_description_id=='Left Rear Door'? "selected='selected'":"").'>Left Rear Door</option>
										<option value="Left Rocker Panel" '.($damage_description_id=='Left Rocker Panel'? "selected='selected'":"").'>Left Rocker Panel</option>
										<option value="Left Quarter Panel" '.($damage_description_id=='Left Quarter Panel'? "selected='selected'":"").'>Left Quarter Panel</option>
										<option value="Left Rear Wheel" '.($damage_description_id=='Left Rear Wheel'? "selected='selected'":"").'>Left Rear Wheel</option>
										<option value="Left Bed Side" '.($damage_description_id=='Left Bed Side'? "selected='selected'":"").'>Left Bed Side</option>
										<option value="Deck Lid" '.($damage_description_id=='Deck Lid'? "selected='selected'":"").'>Deck Lid</option>
										<option value="Tailgate" '.($damage_description_id=='Tailgate'? "selected='selected'":"").'>Tailgate</option>
										<option value="Lift Gate" '.($damage_description_id=='Lift Gate'? "selected='selected'":"").'>Lift Gate</option>
										<option value="Taillamp" '.($damage_description_id=='Taillamp'? "selected='selected'":"").'>Taillamp</option>
										<option value="Rear Bumper" '.($damage_description_id=='Rear Bumper'? "selected='selected'":"").'>Rear Bumper</option>
										<option value="Right Quarter Panel" '.($damage_description_id=='Right Quarter Panel'? "selected='selected'":"").'>Right Quarter Panel</option>
										<option value="Right Rear Wheel" '.($damage_description_id=='Right Rear Wheel'? "selected='selected'":"").'>Right Rear Wheel</option>
										<option value="Right Bed Side" '.($damage_description_id=='Right Bed Side'? "selected='selected'":"").'>Right Bed Side</option>
										<option value="Right Rear Door" '.($damage_description_id=='Right Rear Door'? "selected='selected'":"").'>Right Rear Door</option>
										<option value="Right Front Door" '.($damage_description_id=='Right Front Door'? "selected='selected'":"").'>Right Front Door</option>
										<option value="Right Rocker Panel" '.($damage_description_id=='Right Rocker Panel'? "selected='selected'":"").'>Right Rocker Panel</option>
										<option value="Right Fender" '.($damage_description_id=='Right Fender'? "selected='selected'":"").'>Right Fender</option>
										<option value="Right Front Wheel" '.($damage_description_id=='Right Front Wheel'? "selected='selected'":"").'>Right Front Wheel</option>
										<option value="Roof" '.($damage_description_id=='Roof'? "selected='selected'":"").'>Roof</option>
										<option value="Convertible Top" '.($damage_description_id=='Convertible Top'? "selected='selected'":"").'>Convertible Top</option>
									</select>
								</td>
								<td>
									<select class="damage_condition" name="listing[exterior_disclosed_damages][][damage_condition_id]">
										<option value="" '.($damage_condition_id==''? "selected='selected'":"").'>Not Specified</option>
										<option value="Collision Damage" '.($damage_condition_id=='Collision Damage'? "selected='selected'":"").'>Collision Damage</option>
										<option value="Bent" '.($damage_condition_id=='Bent'? "selected='selected'":"").'>Bent</option>
										<option value="Broken" '.($damage_condition_id=='Broken'? "selected='selected'":"").'>Broken</option>
										<option value="Burn" '.($damage_condition_id=='Burn'? "selected='selected'":"").'>Burn</option>
										<option value="Chipped" '.($damage_condition_id=='Chipped'? "selected='selected'":"").'>Chipped</option>
										<option value="Cracked" '.($damage_condition_id=='Cracked'? "selected='selected'":"").'>Cracked</option>
										<option value="Curbed" '.($damage_condition_id=='Curbed'? "selected='selected'":"").'>Curbed</option>
										<option value="Cut" '.($damage_condition_id=='Cut'? "selected='selected'":"").'>Cut</option>
										<option value="Dent/No Paint Dmg" '.($damage_condition_id=='Dent/No Paint Dmg'? "selected='selected'":"").'>Dent/No Paint Dmg</option>
										<option value="Dent/Paint Dmg" '.($damage_condition_id=='Dent/Paint Dmg'? "selected='selected'":"").'>Dent/Paint Dmg</option>
										<option value="Ding" '.($damage_condition_id=='Ding'? "selected='selected'":"").'>Ding</option>
										<option value="Faded" '.($damage_condition_id=='Faded'? "selected='selected'":"").'>Faded</option>
										<option value="Gouged" '.($damage_condition_id=='Gouged'? "selected='selected'":"").'>Gouged</option>
										<option value="Hail Damage" '.($damage_condition_id=='Hail Damage'? "selected='selected'":"").'>Hail Damage</option>
										<option value="Hole" '.($damage_condition_id=='Hole'? "selected='selected'":"").'>Hole</option>
										<option value="Missing" '.($damage_condition_id=='Missing'? "selected='selected'":"").'>Missing</option>
										<option value="Oxidized" '.($damage_condition_id=='Oxidized'? "selected='selected'":"").'>Oxidized</option>
										<option value="Peeling" '.($damage_condition_id=='Peeling'? "selected='selected'":"").'>Peeling</option>
										<option value="Pitted" '.($damage_condition_id=='Pitted'? "selected='selected'":"").'>Pitted</option>
										<option value="Poor Repair" '.($damage_condition_id=='Poor Repair'? "selected='selected'":"").'>Poor Repair</option>
										<option value="Prev Repair" '.($damage_condition_id=='Prev Repair'? "selected='selected'":"").'>Prev Repair</option>
										<option value="Primer" '.($damage_condition_id=='Primer'? "selected='selected'":"").'>Primer</option>
										<option value="Scratch Heavy" '.($damage_condition_id=='Scratch Heavy'? "selected='selected'":"").'>Scratch Heavy</option>
										<option value="Scratch Light" '.($damage_condition_id=='Scratch Light'? "selected='selected'":"").'>Scratch Light</option>
										<option value="Scuffed" '.($damage_condition_id=='Scuffed'? "selected='selected'":"").'>Scuffed</option>
										<option value="Water Damage" '.($damage_condition_id=='Water Damage'? "selected='selected'":"").'>Water Damage</option>
										<option value="Worn" '.($damage_condition_id=='Worn'? "selected='selected'":"").'>Worn</option>
									</select>
								</td>
								<td>
									<select class="damage_severity" name="listing[exterior_disclosed_damages][][damage_severity_id]">
										<option value="" '.($damage_severity_id==''? "selected='selected'":"").'>Not Specified</option>
										<option value="Less than 1`" '.($damage_severity_id=='Less than 1`'? "selected='selected'":"").'>Less than 1"</option>
										<option value="1` to 2`" '.($damage_severity_id=='1` to 2`'? "selected='selected'":"").'>1" to 2"</option>
										<option value="2` to 3`" '.($damage_severity_id=='2` to 3`'? "selected='selected'":"").'>2" to 3"</option>
										<option value="3` to 4`" '.($damage_severity_id=='3` to 4`'? "selected='selected'":"").'>3" to 4"</option>
										<option value="4` to 5`" '.($damage_severity_id=='4` to 5`'? "selected='selected'":"").'>4" to 5"</option>
										<option value="5` to 6`" '.($damage_severity_id=='5` to 6`'? "selected='selected'":"").'>5" to 6"</option>
										<option value="6` to 7`" '.($damage_severity_id=='6` to 7`'? "selected='selected'":"").'>6" to 7"</option>
										<option value="7` to 8`" '.($damage_severity_id=='7` to 8`'? "selected='selected'":"").'>7" to 8"</option>
										<option value="8` to 9`" '.($damage_severity_id=='8` to 9`'? "selected='selected'":"").'>8" to 9"</option>
										<option value="9` to 10`" '.($damage_severity_id=='9` to 10`'? "selected='selected'":"").'>9" to 10"</option>
										<option value="10` or More" '.($damage_severity_id=='10` or More'? "selected='selected'":"").'>10" or More</option>
										<option value="PDR/1" '.($damage_severity_id=='PDR/1'? "selected='selected'":"").'>PDR/1</option>
										<option value="PDR/2-3" '.($damage_severity_id=='PDR/2-3'? "selected='selected'":"").'>PDR/2-3</option>
										<option value="PDR/4-5" '.($damage_severity_id=='PDR/4-5'? "selected='selected'":"").'>PDR/4-5</option>
										<option value="PDR/6-7" '.($damage_severity_id=='PDR/6-7'? "selected='selected'":"").'>PDR/6-7</option>
										<option value="PDR/8-9" '.($damage_severity_id=='PDR/8-9'? "selected='selected'":"").'>PDR/8-9</option>
										<option value="PDR/10 or more" '.($damage_severity_id=='PDR/10 or more7'? "selected='selected'":"").'>PDR/10 or more</option>
										<option value="Multiple" '.($damage_severity_id=='Multiple'? "selected='selected'":"").'>Multiple</option>
										<option value="Substandard Repair" '.($damage_severity_id=='Substandard Repair'? "selected='selected'":"").'>Substandard Repair</option>
										<option value="Acceptable Repair" '.($damage_severity_id=='Acceptable Repair'? "selected='selected'":"").'>Acceptable Repair</option>
										<option value="Excellent Repair" '.($damage_severity_id=='Excellent Repair'? "selected='selected'":"").'>Excellent Repair</option>
										<option value="Replacement Required" '.($damage_severity_id=='Replacement Required'? "selected='selected'":"").'>Replacement Required</option>
									</select>
								</td>
							</tr>';
			}
			$this->page.='	<tr class="rowB">
								<td>&nbsp;</td>
								<td>
									<a href="javascript:void(0)" onclick="addRow(\'exterior\')" style="display: block;">Add Another Row</a>
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
					</table>
					</td>
				</tr>';

				$this->page.='
				<tr class="rowA title">
					<td colspan="2" class="title_division">Interior Condition
						<span class="checkbox_inside_h3">
							<input name="listing[no_interior_damage]" value="0" type="hidden">
							<input id="listing_no_interior_damage" '.($form['listing']['no_interior_damage']==1? 'checked="checked"':'').' name="listing[no_interior_damage]" value="1" type="checkbox">No Damage
						</span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-left:3%">
						<table id="interior_damages" width="90%" cellspacing=0 border=0>
							<tr class="rowB">
								<td style="width:90px">&nbsp;</td>
								<td><span class="title">Description</span></td>
								<td><span class="title">Condition</span></td>
								<td><span class="title">Severity</span></td>
							</tr>';
			
			$interior_disclosed_damages_chunk = array_chunk($form['listing']['interior_disclosed_damages'], 3);

			for($i=0;$i<count($interior_disclosed_damages_chunk);$i++)
			{
				$damage_description_id = $interior_disclosed_damages_chunk[$i][0]['damage_description_id'];
				$damage_condition_id = $interior_disclosed_damages_chunk[$i][1]['damage_condition_id'];
				$damage_severity_id = $interior_disclosed_damages_chunk[$i][2]['damage_severity_id'];
				$this->page.='<tr class="'.($i%2? 'rowB':'rowA').'">
								<td>&nbsp;</td>
								<td>
									<select class="damage_description" name="listing[interior_disclosed_damages][][damage_description_id]">
										<option value="" '.($damage_description_id==''? "selected='selected'":"").'>Not Specified</option>
										<option value="Overall Vehicle" '.($damage_description_id=='Overall Vehicle'? "selected='selected'":"").'>Overall Vehicle</option>
										<option value="Steering Wheel" '.($damage_description_id=='Steering Wheel'? "selected='selected'":"").'>Steering Wheel</option>
										<option value="Air Bag" '.($damage_description_id=='Air Bag'? "selected='selected'":"").'>Air Bag</option>
										<option value="Dash" '.($damage_description_id=='Dash'? "selected='selected'":"").'>Dash</option>
										<option value="Radio" '.($damage_description_id=='Radio'? "selected='selected'":"").'>Radio</option>
										<option value="Console" '.($damage_description_id=='Console'? "selected='selected'":"").'>Console</option>
										<option value="Shift Knob" '.($damage_description_id=='Shift Knob'? "selected='selected'":"").'>Shift Knob</option>
										<option value="Glove Box Door" '.($damage_description_id=='Glove Box Door'? "selected='selected'":"").'>Glove Box Door</option>
										<option value="Left Front Carpet" '.($damage_description_id=='Left Front Carpet'? "selected='selected'":"").'>Left Front Carpet</option>
										<option value="Left Front Door Panel" '.($damage_description_id=='Left Front Door Panel'? "selected='selected'":"").'>Left Front Door Panel</option>
										<option value="Left Front Seat" '.($damage_description_id=='Left Front Seat'? "selected='selected'":"").'>Left Front Seat</option>
										<option value="Left Rear Carpet" '.($damage_description_id=='Left Rear Carpet'? "selected='selected'":"").'>Left Rear Carpet</option>
										<option value="Left Rear Door Panel" '.($damage_description_id=='Left Rear Door Panel'? "selected='selected'":"").'>Left Rear Door Panel</option>
										<option value="Left Rear Seat" '.($damage_description_id=='Left Rear Seat'? "selected='selected'":"").'>Left Rear Seat</option>
										<option value="Right Front Carpet" '.($damage_description_id=='Right Front Carpet'? "selected='selected'":"").'>Right Front Carpet</option>
										<option value="Right Front Door Panel" '.($damage_description_id=='Right Front Door Panel'? "selected='selected'":"").'>Right Front Door Panel</option>
										<option value="Right Front Seat" '.($damage_description_id=='Right Front Seat'? "selected='selected'":"").'>Right Front Seat</option>
										<option value="Right Rear Carpet" '.($damage_description_id=='Right Rear Carpet'? "selected='selected'":"").'>Right Rear Carpet</option>
										<option value="Right Rear Door Panel" '.($damage_description_id=='Right Rear Door Panel'? "selected='selected'":"").'>Right Rear Door Panel</option>
										<option value="Right Rear Seat" '.($damage_description_id=='Right Rear Seat'? "selected='selected'":"").'>Right Rear Seat</option>
										<option value="3rd Row Seat" '.($damage_description_id=='3rd Row Seat'? "selected='selected'":"").'>3rd Row Seat</option>
										<option value="Headliner" '.($damage_description_id=='Headliner'? "selected='selected'":"").'>Headliner</option>
										<option value="Sunroof" '.($damage_description_id=='Sunroof'? "selected='selected'":"").'>Sunroof</option>
									</select>
								</td>
								<td>
									<select class="damage_condition" name="listing[interior_disclosed_damages][][damage_condition_id]">
										<option value="" '.($damage_condition_id==''? "selected='selected'":"").'>Not Specified</option>
										<option value="Collision Damage" '.($damage_condition_id=='Collision Damage'? "selected='selected'":"").'>Collision Damage</option>
										<option value="Bent" '.($damage_condition_id=='Bent'? "selected='selected'":"").'>Bent</option>
										<option value="Broken" '.($damage_condition_id=='Broken'? "selected='selected'":"").'>Broken</option>
										<option value="Burn" '.($damage_condition_id=='Burn'? "selected='selected'":"").'>Burn</option>
										<option value="Cracked" '.($damage_condition_id=='Cracked'? "selected='selected'":"").'>Cracked</option>
										<option value="Cut" '.($damage_condition_id=='Cut'? "selected='selected'":"").'>Cut</option>
										<option value="Faded" '.($damage_condition_id=='Faded'? "selected='selected'":"").'>Faded</option>
										<option value="Gouged" '.($damage_condition_id=='Gouged'? "selected='selected'":"").'>Gouged</option>
										<option value="Hole" '.($damage_condition_id=='Hole'? "selected='selected'":"").'>Hole</option>
										<option value="Missing" '.($damage_condition_id=='Missing'? "selected='selected'":"").'>Missing</option>
										<option value="Scratch Heavy" '.($damage_condition_id=='Scratch Heavy'? "selected='selected'":"").'>Scratch Heavy</option>
										<option value="Scratch Light" '.($damage_condition_id=='Scratch Light'? "selected='selected'":"").'>Scratch Light</option>
										<option value="Scuffed" '.($damage_condition_id=='Scuffed'? "selected='selected'":"").'>Scuffed</option>
										<option value="Singed" '.($damage_condition_id=='Singed'? "selected='selected'":"").'>Singed</option>
										<option value="Soiled" '.($damage_condition_id=='Soiled'? "selected='selected'":"").'>Soiled</option>
										<option value="Stained" '.($damage_condition_id=='Stained'? "selected='selected'":"").'>Stained</option>
										<option value="Water Damage" '.($damage_condition_id=='Water Damage'? "selected='selected'":"").'>Water Damage</option>
										<option value="Worn" '.($damage_condition_id=='Worn'? "selected='selected'":"").'>Worn</option>
										<option value="Inoperable" '.($damage_condition_id=='Inoperable'? "selected='selected'":"").'>Inoperable</option>
										<option value="Non-Factory" '.($damage_condition_id=='Non-Factory'? "selected='selected'":"").'>Non-Factory</option>
										<option value="Ripped" '.($damage_condition_id=='Ripped'? "selected='selected'":"").'>Ripped</option>
									</select>
								</td>
								<td>
									<select class="damage_severity" name="listing[interior_disclosed_damages][][damage_severity_id]">
										<option value="" '.($damage_severity_id==''? "selected='selected'":"").'>Not Specified</option>
										
										<option value="Less than 1/4`" '.($damage_severity_id=='Less than 1/4`'? "selected='selected'":"").'>Less than 1/4"</option>
										<option value="1/4` to 1/2`" '.($damage_severity_id=='1/4` to 1/2`'? "selected='selected'":"").'>1/4" to 1/2"</option>
										<option value="1/2` to 1`" '.($damage_severity_id=='1/2` to 1`'? "selected='selected'":"").'>1/2" to 1"</option>
										<option value="1` to 2`" '.($damage_severity_id=='1` to 2`'? "selected='selected'":"").'>1" to 2"</option>
										<option value="2` to 3`" '.($damage_severity_id=='2` to 3`'? "selected='selected'":"").'>2" to 3"</option>
										<option value="3` to 4`" '.($damage_severity_id=='3` to 4`'? "selected='selected'":"").'>3" to 4"</option>
										<option value="4` to 5`" '.($damage_severity_id=='4` to 5`'? "selected='selected'":"").'>4" to 5"</option>
										<option value="5` to 6`" '.($damage_severity_id=='5` to 6`'? "selected='selected'":"").'>5" to 6"</option>
										<option value="6` to 7`" '.($damage_severity_id=='6` to 7`'? "selected='selected'":"").'>6" to 7"</option>
										<option value="7` to 8`" '.($damage_severity_id=='7` to 8`'? "selected='selected'":"").'>7" to 8"</option>
										<option value="8` to 9`" '.($damage_severity_id=='8` to 9`'? "selected='selected'":"").'>8" to 9"</option>
										<option value="9` to 10`" '.($damage_severity_id=='9` to 10`'? "selected='selected'":"").'>9" to 10"</option>
										<option value="10` or More" '.($damage_severity_id=='10` or More'? "selected='selected'":"").'>10" or More</option>
										<option value="Multiple" '.($damage_severity_id=='Multiple'? "selected='selected'":"").'>Multiple</option>
										<option value="Needs Cleaning" '.($damage_severity_id=='Needs Cleaning'? "selected='selected'":"").'>Needs Cleaning</option>
										<option value="Permanent" '.($damage_severity_id=='Permanent'? "selected='selected'":"").'>Permanent</option>
										<option value="Repair Required" '.($damage_severity_id=='Repair Required'? "selected='selected'":"").'>Repair Required</option>
										<option value="Replacement Required" '.($damage_severity_id=='Replacement Required'? "selected='selected'":"").'>Replacement Required</option>
									</select>
								</td>
							</tr>';
}
			  $this->page.='<tr class="rowB">
								<td>&nbsp;</td>
								<td>
									<a href="javascript:void(0)" onclick="addRow(\'interior\')" style="display: block;">Add Another Row</a>
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="rowA title">
					<td colspan="2" class="title_division">Tire Condition
						
					</td>
				</tr>		
				<tr>
					<td colspan="2" style="padding-left:3%">
						<table width="90%" cellspacing=0 border=0>
							<tr class="rowB">
								<td style="width:90px">&nbsp;</td>
								<td style="width:90px"><b>Left Front:</b></td>
								<td>
									<select id="left_front_tire_condition" name="listing[left_front_tire_condition_id]">
										<option value="" '.($form['listing']['left_front_tire_condition_id']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="New" '.($form['listing']['left_front_tire_condition_id']=='New'? "selected='selected'":"").'>New</option>
										<option value="Less than 4/32`" '.($form['listing']['left_front_tire_condition_id']=='Less than 4/32`'? "selected='selected'":"").'>Less than 4/32"</option>
										<option value="4/32` to 7/32`" '.($form['listing']['left_front_tire_condition_id']=='4/32` to 7/32`'? "selected='selected'":"").'>4/32" to 7/32"</option>
										<option value="More than 7/32`" '.($form['listing']['left_front_tire_condition_id']=='More than 7/32`'? "selected='selected'":"").'>More than 7/32"</option>
										<option value="Bald" '.($form['listing']['left_front_tire_condition_id']=='Bald'? "selected='selected'":"").'>Bald</option>
										<option value="Flat" '.($form['listing']['left_front_tire_condition_id']=='Flat'? "selected='selected'":"").'>Flat</option>
										<option value="Missing" '.($form['listing']['left_front_tire_condition_id']=='Missing'? "selected='selected'":"").'>Missing</option>
									</select>
								</td>
							</tr>
							<tr class="rowA">
								<td>&nbsp;</td>
								<td><b>Right Front:</b></td>
								<td>
									<select id="right_front_tire_condition" name="listing[right_front_tire_condition_id]">
										<option value="" '.($form['listing']['right_front_tire_condition_id']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="New" '.($form['listing']['right_front_tire_condition_id']=='New'? "selected='selected'":"").'>New</option>
										<option value="Less than 4/32`" '.($form['listing']['right_front_tire_condition_id']=='Less than 4/32`'? "selected='selected'":"").'>Less than 4/32"</option>
										<option value="4/32` to 7/32`" '.($form['listing']['right_front_tire_condition_id']=='4/32` to 7/32`'? "selected='selected'":"").'>4/32" to 7/32"</option>
										<option value="More than 7/32`" '.($form['listing']['right_front_tire_condition_id']=='More than 7/32`'? "selected='selected'":"").'>More than 7/32"</option>
										<option value="Bald" '.($form['listing']['right_front_tire_condition_id']=='Bald'? "selected='selected'":"").'>Bald</option>
										<option value="Flat" '.($form['listing']['right_front_tire_condition_id']=='Flat'? "selected='selected'":"").'>Flat</option>
										<option value="Missing" '.($form['listing']['right_front_tire_condition_id']=='Missing'? "selected='selected'":"").'>Missing</option>
									</select>
								</td>
							</tr>
							<tr class="rowB">
								<td>&nbsp;</td>
								<td><b>Left Rear:</b></td>
								<td>
									<select id="left_rear_tire_condition" name="listing[left_rear_tire_condition_id]">
										<option value="" '.($form['listing']['left_rear_tire_condition_id']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="New" '.($form['listing']['left_rear_tire_condition_id']=='New'? "selected='selected'":"").'>New</option>
										<option value="Less than 4/32`" '.($form['listing']['left_rear_tire_condition_id']=='Less than 4/32`'? "selected='selected'":"").'>Less than 4/32"</option>
										<option value="4/32` to 7/32`" '.($form['listing']['left_rear_tire_condition_id']=='4/32` to 7/32`'? "selected='selected'":"").'>4/32" to 7/32"</option>
										<option value="More than 7/32`" '.($form['listing']['left_rear_tire_condition_id']=='More than 7/32`'? "selected='selected'":"").'>More than 7/32"</option>
										<option value="Bald" '.($form['listing']['left_rear_tire_condition_id']=='Bald'? "selected='selected'":"").'>Bald</option>
										<option value="Flat" '.($form['listing']['left_rear_tire_condition_id']=='Flat'? "selected='selected'":"").'>Flat</option>
										<option value="Missing" '.($form['listing']['left_rear_tire_condition_id']=='Missing'? "selected='selected'":"").'>Missing</option>
									</select>
								</td>
							</tr>
							<tr class="rowA">
								<td>&nbsp;</td>
								<td><b>Right Rear:</b></td>
								<td>
									<select id="right_rear_tire_condition" name="listing[right_rear_tire_condition_id]">
										<option value="" '.($form['listing']['right_rear_tire_condition_id']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="New" '.($form['listing']['right_rear_tire_condition_id']=='New'? "selected='selected'":"").'>New</option>
										<option value="Less than 4/32`" '.($form['listing']['right_rear_tire_condition_id']=='Less than 4/32`'? "selected='selected'":"").'>Less than 4/32"</option>
										<option value="4/32` to 7/32`" '.($form['listing']['right_rear_tire_condition_id']=='4/32` to 7/32`'? "selected='selected'":"").'>4/32" to 7/32"</option>
										<option value="More than 7/32`" '.($form['listing']['right_rear_tire_condition_id']=='More than 7/32`'? "selected='selected'":"").'>More than 7/32"</option>
										<option value="Bald" '.($form['listing']['right_rear_tire_condition_id']=='Bald'? "selected='selected'":"").'>Bald</option>
										<option value="Flat" '.($form['listing']['right_rear_tire_condition_id']=='Flat'? "selected='selected'":"").'>Flat</option>
										<option value="Missing" '.($form['listing']['right_rear_tire_condition_id']=='Missing'? "selected='selected'":"").'>Missing</option>
									</select>
								</td>
							</tr>
							<tr class="rowB">
								<td>&nbsp;</td>
								<td><b>Spare:</b></td>
								<td>
									<select id="spare_tire_condition" name="listing[spare_tire_condition_id]">
										<option value="" '.($form['listing']['spare_tire_condition_id']==''? "selected='selected'":"").'>Not Specified</option>
										<option value="New" '.($form['listing']['spare_tire_condition_id']=='New'? "selected='selected'":"").'>New</option>
										<option value="Less than 4/32`" '.($form['listing']['spare_tire_condition_id']=='Less than 4/32`'? "selected='selected'":"").'>Less than 4/32"</option>
										<option value="4/32` to 7/32`" '.($form['listing']['spare_tire_condition_id']=='4/32` to 7/32`'? "selected='selected'":"").'>4/32" to 7/32"</option>
										<option value="More than 7/32`" '.($form['listing']['spare_tire_condition_id']=='More than 7/32`'? "selected='selected'":"").'>More than 7/32"</option>
										<option value="Bald" '.($form['listing']['spare_tire_condition_id']=='Bald'? "selected='selected'":"").'>Bald</option>
										<option value="Flat" '.($form['listing']['spare_tire_condition_id']=='Flat'? "selected='selected'":"").'>Flat</option>
										<option value="Missing" '.($form['listing']['spare_tire_condition_id']=='Missing'? "selected='selected'":"").'>Missing</option>
									</select>
								</td>
							</tr>
							<tr class="rowA">
								<td>&nbsp;</td>
								<td><b>All Tires Match: <div>(Brand/Size)</div></b></td>
								<td>
									<input id="all_tires_match_yes" '.($form['listing']['all_tires_match']==1? 'checked="checked"':'').' name="listing[all_tires_match]" onclick="$(\'#all_tires_match_no\').get(0).checked = false" value="1" type="checkbox"> Yes
									<input id="all_tires_match_no" '.($form['listing']['all_tires_match']==0? 'checked="checked"':'').' name="listing[all_tires_match]" onclick="$(\'#all_tires_match_yes\').get(0).checked = false" value="0" type="checkbox"> No 
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="rowA title">
					<td colspan="2" class="title_division">Sell my vehicle using
					<input name="presenter[allow_dealers_to_bid]" value="0" type="hidden">	
					<input '.(@$form['presenter']['allow_dealers_to_bid']?'checked="checked"':'').'id="presenter_allow_dealers_to_bid" name="presenter[allow_dealers_to_bid]" value="1" type="checkbox">
						 <label for="presenter_allow_dealers_to_bid"> Bid</label>
						<input name="presenter[allow_dealers_to_buy_now]" value="0" type="hidden"> 
						 <input '.(@$form['presenter']['allow_dealers_to_buy_now']?'checked="checked"':'').' id="presenter_allow_dealers_to_buy_now" name="presenter[allow_dealers_to_buy_now]" value="1" type="checkbox">
						<label for="presenter_allow_dealers_to_buy_now">Buy Now</label>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-left:3%">
						<table width="90%" cellspacing=0 border=0>
							<tr class="rowB">
								<td style="width:90px">&nbsp;</td>
								<td style="width:90px"><span class="required_marker">*</span><b>Starting Bid</b></td>
								<td>$<input accept="true" mask="numeric" msg="Starting Bid" id="listing_starting_bid_price" name="listing[starting_bid_price]" size="10" type="text" value="'.$form['listing']['starting_bid_price'].'"></td>
							</tr>
							<tr class="rowA">
								<td style="width:90px">&nbsp;</td>
								<td><span class="required_marker">*</span><b>Bid Increment</b></td>
								<td>
									$<select accept="true" mask="numeric" msg="Bid Increment" id="listing_bid_increment" name="listing[bid_increment]">
										<option value="1" '.($form['listing']['bid_increment']=='1'? "selected='selected'":"").'>1</option>
										<option value="25" '.(($form['listing']['bid_increment']=='25' or $form['listing']['bid_increment']=='')? "selected='selected'":"").'>25</option>
										<option value="50" '.($form['listing']['bid_increment']=='50'? "selected='selected'":"").'>50</option>
										<option value="100" '.($form['listing']['bid_increment']=='100'? "selected='selected'":"").'>100</option>
										<option value="200" '.($form['listing']['bid_increment']=='200'? "selected='selected'":"").'>200</option>
										<option value="250" '.($form['listing']['bid_increment']=='250'? "selected='selected'":"").'>250</option>
										<option value="500" '.($form['listing']['bid_increment']=='500'? "selected='selected'":"").'>500</option>
										<option value="1000" '.($form['listing']['bid_increment']=='1000'? "selected='selected'":"").'>1000</option>
										<option value="1500" '.($form['listing']['bid_increment']=='1500'? "selected='selected'":"").'>1500</option>
										<option value="2000" '.($form['listing']['bid_increment']=='2000'? "selected='selected'":"").'>2000</option>
										<option value="2500" '.($form['listing']['bid_increment']=='2500'? "selected='selected'":"").'>2500</option>
										<option value="4000" '.($form['listing']['bid_increment']=='4000'? "selected='selected'":"").'>4000</option>
										<option value="5000" '.($form['listing']['bid_increment']=='5000'? "selected='selected'":"").'>5000</option>
										<option value="10000" '.($form['listing']['bid_increment']=='10000'? "selected='selected'":"").'>10000</option>
									</select>
								</td>
							</tr>
							<tr class="rowB">
								<td style="width:90px">&nbsp;</td>
								<td><span class="required_marker">*</span><b>Floor Price</b></td>
								<td>
									$<input accept="true" mask="numeric" msg="Floor Price" id="listing_floor_price" name="listing[floor_price]" size="10" type="text" value="'.$form['listing']['floor_price'].'">
								</td>
							</tr>
							<tr class="rowA">
								<td style="width:90px">&nbsp;</td>
								<td><span class="required_marker">*</span><b>Buy Now Price</b></td>
								<td>
									$<input accept="true" mask="numeric" msg="Buy Now Price" id="listing_buy_now_price" name="listing[buy_now_price]" size="10" type="text" value="'.$form['listing']['buy_now_price'].'">
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="rowA title">
					<td colspan="2" class="title_division">Offer</td>
				</tr>
				<tr class="rowA">
					<td colspan="2" style="padding-left:35px;border:none;color:#888">List this vehicle on OVE.com for: (we recommend 3-5 days, maximum 7 days)</td>
				</tr>
				<tr class="rowA">
					<td colspan="2" style="padding-left:3%">
						<table class="date_table" border="0">
							<tr>
								<td valign="top" style="padding: 6px 0 0 25px;">
								<span><b>Start</b></span>
								
								</td>
							
								<td valign="top" id="specific_offer_start_time" nowrap>
									
									<div id="DatetimeToolbocksPresenterStartTimeDateContainer">
										<nowrap><input value="'.(($form['presenter']['start_time_date'])?$form['presenter']['start_time_date']:date('m/d/Y')).'" id="DatetimeToolbocksPresenterStartTimeDateInput" name="presenter[start_time_date]" size="30" type="text">
										<img alt="Calendar" src="/img/ccl/cal.gif" border=0 onclick="show_calendar(\'DatetimeToolbocksPresenterStartTimeDateInput\', \'\', myDateFormat);" style="cursor: pointer;">

										</nowrap>
										<div class="DatetimeToolbocksMessage">
											<div id="DatetimeToolbocksPresenterStartTimeDateMsg">mm/dd/yyyy</div>
										</div>
									</div>
									
								</td>
								<td valign="top" id="presenter_start_time_row">
									<select class="live_input" id="presenter_start_time" name="presenter[start_time]">
										<option value="00:00" '.$this->check_time_option($form['presenter']['start_time'], '00:00').'>00:00</option>
										<option value="00:15" '.$this->check_time_option($form['presenter']['start_time'], '00:15').'>00:15</option>
										<option value="00:30" '.$this->check_time_option($form['presenter']['start_time'], '00:30').'>00:30</option>
										<option value="00:45" '.$this->check_time_option($form['presenter']['start_time'], '00:45').'>00:45</option>
										<option value="1:00" '.$this->check_time_option($form['presenter']['start_time'], '1:00').'>1:00</option>
										<option value="1:15" '.$this->check_time_option($form['presenter']['start_time'], '1:15').'>1:15</option>
										<option value="1:30" '.$this->check_time_option($form['presenter']['start_time'], '1:30').'>1:30</option>
										<option value="1:45" '.$this->check_time_option($form['presenter']['start_time'], '1:45').'>1:45</option>
										<option value="2:00" '.$this->check_time_option($form['presenter']['start_time'], '2:00').'>2:00</option>
										<option value="2:15" '.$this->check_time_option($form['presenter']['start_time'], '2:15').'>2:15</option>
										<option value="2:30" '.$this->check_time_option($form['presenter']['start_time'], '2:30').'>2:30</option>
										<option value="2:45" '.$this->check_time_option($form['presenter']['start_time'], '2:45').'>2:45</option>
										<option value="3:00" '.$this->check_time_option($form['presenter']['start_time'], '3:00').'>3:00</option>
										<option value="3:15" '.$this->check_time_option($form['presenter']['start_time'], '3:15').'>3:15</option>
										<option value="3:30" '.$this->check_time_option($form['presenter']['start_time'], '3:30').'>3:30</option>
										<option value="3:45" '.$this->check_time_option($form['presenter']['start_time'], '3:45').'>3:45</option>
										<option value="4:00" '.$this->check_time_option($form['presenter']['start_time'], '4:00').'>4:00</option>
										<option value="4:15" '.$this->check_time_option($form['presenter']['start_time'], '4:15').'>4:15</option>
										<option value="4:30" '.$this->check_time_option($form['presenter']['start_time'], '4:30').'>4:30</option>
										<option value="4:45" '.$this->check_time_option($form['presenter']['start_time'], '4:45').'>4:45</option>
										<option value="5:00" '.$this->check_time_option($form['presenter']['start_time'], '5:00').'>5:00</option>
										<option value="5:15" '.$this->check_time_option($form['presenter']['start_time'], '5:15').'>5:15</option>
										<option value="5:30" '.$this->check_time_option($form['presenter']['start_time'], '5:30').'>5:30</option>
										<option value="5:45" '.$this->check_time_option($form['presenter']['start_time'], '5:45').'>5:45</option>
										<option value="6:00" '.$this->check_time_option($form['presenter']['start_time'], '6:00').'>6:00</option>
										<option value="6:15" '.$this->check_time_option($form['presenter']['start_time'], '6:15').'>6:15</option>
										<option value="6:30" '.$this->check_time_option($form['presenter']['start_time'], '6:30').'>6:30</option>
										<option value="6:45" '.$this->check_time_option($form['presenter']['start_time'], '6:45').'>6:45</option>
										<option value="7:00" '.$this->check_time_option($form['presenter']['start_time'], '7:00').'>7:00</option>
										<option value="7:15" '.$this->check_time_option($form['presenter']['start_time'], '7:15').'>7:15</option>
										<option value="7:30" '.$this->check_time_option($form['presenter']['start_time'], '7:30').'>7:30</option>
										<option value="7:45" '.$this->check_time_option($form['presenter']['start_time'], '7:45').'>7:45</option>
										<option value="8:00" '.$this->check_time_option($form['presenter']['start_time'], '8:00').'>8:00</option>
										<option value="8:15" '.$this->check_time_option($form['presenter']['start_time'], '8:15').'>8:15</option>
										<option value="8:30" '.$this->check_time_option($form['presenter']['start_time'], '8:30').'>8:30</option>
										<option value="8:45" '.$this->check_time_option($form['presenter']['start_time'], '8:45').'>8:45</option>
										<option value="9:00" '.$this->check_time_option($form['presenter']['start_time'], '9:00').'>9:00</option>
										<option value="9:15" '.$this->check_time_option($form['presenter']['start_time'], '9:15').'>9:15</option>
										<option value="9:30" '.$this->check_time_option($form['presenter']['start_time'], '9:30').'>9:30</option>
										<option value="9:45" '.$this->check_time_option($form['presenter']['start_time'], '9:45').'>9:45</option>
										<option value="10:00" '.$this->check_time_option($form['presenter']['start_time'], '10:00').'>10:00</option>
										<option value="10:15" '.$this->check_time_option($form['presenter']['start_time'], '10:15').'>10:15</option>
										<option value="10:30" '.$this->check_time_option($form['presenter']['start_time'], '10:30').'>10:30</option>
										<option value="10:45" '.$this->check_time_option($form['presenter']['start_time'], '10:45').'>10:45</option>
										
										<option value="11:00" '.$this->check_time_option($form['presenter']['start_time'], '11:00').'>11:00</option>
										<option value="11:15" '.$this->check_time_option($form['presenter']['start_time'], '11:15').'>11:15</option>
										<option value="11:30" '.$this->check_time_option($form['presenter']['start_time'], '11:30').'>11:30</option>
										<option value="11:45" '.$this->check_time_option($form['presenter']['start_time'], '11:45').'>11:45</option>
										
										<option value="12:00" '.$this->check_time_option($form['presenter']['start_time'], '12:00').'>12:00</option>
										<option value="12:15" '.$this->check_time_option($form['presenter']['start_time'], '12:15').'>12:15</option>
										<option value="12:30" '.$this->check_time_option($form['presenter']['start_time'], '12:30').'>12:30</option>
										<option value="12:45" '.$this->check_time_option($form['presenter']['start_time'], '12:45').'>12:45</option>
										
										<option value="13:00" '.$this->check_time_option($form['presenter']['start_time'], '13:00').'>13:00</option>
										<option value="13:15" '.$this->check_time_option($form['presenter']['start_time'], '13:15').'>13:15</option>
										<option value="13:30" '.$this->check_time_option($form['presenter']['start_time'], '13:30').'>13:30</option>
										<option value="13:45" '.$this->check_time_option($form['presenter']['start_time'], '13:45').'>13:45</option>
										
										<option value="14:00" '.$this->check_time_option($form['presenter']['start_time'], '14:00').'>14:00</option>
										<option value="14:15" '.$this->check_time_option($form['presenter']['start_time'], '14:15').'>14:15</option>
										<option value="14:30" '.$this->check_time_option($form['presenter']['start_time'], '14:30').'>14:30</option>
										<option value="14:45" '.$this->check_time_option($form['presenter']['start_time'], '14:45').'>14:45</option>
										
										<option value="15:00" '.$this->check_time_option($form['presenter']['start_time'], '15:00').'>15:00</option>
										<option value="15:15" '.$this->check_time_option($form['presenter']['start_time'], '15:15').'>15:15</option>
										<option value="15:30" '.$this->check_time_option($form['presenter']['start_time'], '15:30').'>15:30</option>
										<option value="15:45" '.$this->check_time_option($form['presenter']['start_time'], '15:45').'>15:45</option>
										
										<option value="16:00" '.$this->check_time_option($form['presenter']['start_time'], '16:00').'>16:00</option>
										<option value="16:15" '.$this->check_time_option($form['presenter']['start_time'], '16:15').'>16:15</option>
										<option value="16:30" '.$this->check_time_option($form['presenter']['start_time'], '16:30').'>16:30</option>
										<option value="16:45" '.$this->check_time_option($form['presenter']['start_time'], '16:45').'>16:45</option>
										
										<option value="17:00" '.$this->check_time_option($form['presenter']['start_time'], '17:00').'>17:00</option>
										<option value="17:15" '.$this->check_time_option($form['presenter']['start_time'], '17:15').'>17:15</option>
										<option value="17:30" '.$this->check_time_option($form['presenter']['start_time'], '17:30').'>17:30</option>
										<option value="17:45" '.$this->check_time_option($form['presenter']['start_time'], '17:45').'>17:45</option>
										
										<option value="18:00" '.$this->check_time_option($form['presenter']['start_time'], '18:00').'>18:00</option>
										<option value="18:15" '.$this->check_time_option($form['presenter']['start_time'], '18:15').'>18:15</option>
										<option value="18:30" '.$this->check_time_option($form['presenter']['start_time'], '18:30').'>18:30</option>
										<option value="18:45" '.$this->check_time_option($form['presenter']['start_time'], '18:45').'>18:45</option>
										
										<option value="19:00" '.$this->check_time_option($form['presenter']['start_time'], '19:00').'>19:00</option>
										<option value="19:15" '.$this->check_time_option($form['presenter']['start_time'], '19:15').'>19:15</option>
										<option value="19:30" '.$this->check_time_option($form['presenter']['start_time'], '19:30').'>19:30</option>
										<option value="19:45" '.$this->check_time_option($form['presenter']['start_time'], '19:45').'>19:45</option>
										
										<option value="20:00" '.$this->check_time_option($form['presenter']['start_time'], '20:00').'>20:00</option>
										<option value="20:15" '.$this->check_time_option($form['presenter']['start_time'], '20:15').'>20:15</option>
										<option value="20:30" '.$this->check_time_option($form['presenter']['start_time'], '20:30').'>20:30</option>
										<option value="20:45" '.$this->check_time_option($form['presenter']['start_time'], '20:45').'>20:45</option>
										
										<option value="21:00" '.$this->check_time_option($form['presenter']['start_time'], '21:00').'>21:00</option>
										<option value="21:15" '.$this->check_time_option($form['presenter']['start_time'], '21:15').'>21:15</option>
										<option value="21:30" '.$this->check_time_option($form['presenter']['start_time'], '21:30').'>21:30</option>
										<option value="21:45" '.$this->check_time_option($form['presenter']['start_time'], '21:45').'>21:45</option>
										
										<option value="22:00" '.$this->check_time_option($form['presenter']['start_time'], '22:00').'>22:00</option>
										<option value="22:15" '.$this->check_time_option($form['presenter']['start_time'], '22:15').'>22:15</option>
										<option value="22:30" '.$this->check_time_option($form['presenter']['start_time'], '22:30').'>22:30</option>
										<option value="22:45" '.$this->check_time_option($form['presenter']['start_time'], '22:45').'>22:45</option>
										
										<option value="23:00" '.$this->check_time_option($form['presenter']['start_time'], '23:00').'>23:00</option>
										<option value="23:15" '.$this->check_time_option($form['presenter']['start_time'], '23:15').'>23:15</option>
										<option value="23:30" '.$this->check_time_option($form['presenter']['start_time'], '23:30').'>23:30</option>
										<option value="23:45" '.$this->check_time_option($form['presenter']['start_time'], '23:45').'>23:45</option>
										
										
									</select>
									
								</td>
								<td valign="top">
										
								</td>
							</tr>
							<tr>
								<td valign="top" style="padding: 6px 0 0 25px;">
										<span><b>End</b></span>
								</td>
								<td valign="top" id="specific_offer_end_time_row">
									<div>
											<input value="'.(($form['presenter']['end_time_date'])?$form['presenter']['end_time_date']:date('m/d/Y', time()+259200)).'" id="DatetimeToolbocksPresenterEndTimeDateInput" name="presenter[end_time_date]" size="30" type="text">
											<img alt="Calendar" src="/img/ccl/cal.gif" border=0 onclick="show_calendar(\'DatetimeToolbocksPresenterEndTimeDateInput\', \'\', myDateFormat);" style="cursor: pointer;">
									</div>
									<div class="DatetimeToolbocksMessage"><div id="DatetimeToolbocksPresenterEndTimeDateMsg">mm/dd/yyyy</div>
								</td>
								<td valign="top" id="presenter_end_time_row">
										<select class="live_input" id="presenter_end_time" name="presenter[end_time]">
											<option value="00:00" '.$this->check_time_option($form['presenter']['end_time'], '00:00').'>00:00</option>
											<option value="00:15" '.$this->check_time_option($form['presenter']['end_time'], '00:15').'>00:15</option>
											<option value="00:30" '.$this->check_time_option($form['presenter']['end_time'], '00:30').'>00:30</option>
											<option value="00:45" '.$this->check_time_option($form['presenter']['end_time'], '00:45').'>00:45</option>
											<option value="1:00" '.$this->check_time_option($form['presenter']['end_time'], '1:00').'>1:00</option>
											<option value="1:15" '.$this->check_time_option($form['presenter']['end_time'], '1:15').'>1:15</option>
											<option value="1:30" '.$this->check_time_option($form['presenter']['end_time'], '1:30').'>1:30</option>
											<option value="1:45" '.$this->check_time_option($form['presenter']['end_time'], '1:45').'>1:45</option>
											<option value="2:00" '.$this->check_time_option($form['presenter']['end_time'], '2:00').'>2:00</option>
											<option value="2:15" '.$this->check_time_option($form['presenter']['end_time'], '2:15').'>2:15</option>
											<option value="2:30" '.$this->check_time_option($form['presenter']['end_time'], '2:30').'>2:30</option>
											<option value="2:45" '.$this->check_time_option($form['presenter']['end_time'], '2:45').'>2:45</option>
											<option value="3:00" '.$this->check_time_option($form['presenter']['end_time'], '3:00').'>3:00</option>
											<option value="3:15" '.$this->check_time_option($form['presenter']['end_time'], '3:15').'>3:15</option>
											<option value="3:30" '.$this->check_time_option($form['presenter']['end_time'], '3:30').'>3:30</option>
											<option value="3:45" '.$this->check_time_option($form['presenter']['end_time'], '3:45').'>3:45</option>
											<option value="4:00" '.$this->check_time_option($form['presenter']['end_time'], '4:00').'>4:00</option>
											<option value="4:15" '.$this->check_time_option($form['presenter']['end_time'], '4:15').'>4:15</option>
											<option value="4:30" '.$this->check_time_option($form['presenter']['end_time'], '4:30').'>4:30</option>
											<option value="4:45" '.$this->check_time_option($form['presenter']['end_time'], '4:45').'>4:45</option>
											<option value="5:00" '.$this->check_time_option($form['presenter']['end_time'], '5:00').'>5:00</option>
											<option value="5:15" '.$this->check_time_option($form['presenter']['end_time'], '5:15').'>5:15</option>
											<option value="5:30" '.$this->check_time_option($form['presenter']['end_time'], '5:30').'>5:30</option>
											<option value="5:45" '.$this->check_time_option($form['presenter']['end_time'], '5:45').'>5:45</option>
											<option value="6:00" '.$this->check_time_option($form['presenter']['end_time'], '6:00').'>6:00</option>
											<option value="6:15" '.$this->check_time_option($form['presenter']['end_time'], '6:15').'>6:15</option>
											<option value="6:30" '.$this->check_time_option($form['presenter']['end_time'], '6:30').'>6:30</option>
											<option value="6:45" '.$this->check_time_option($form['presenter']['end_time'], '6:45').'>6:45</option>
											<option value="7:00" '.$this->check_time_option($form['presenter']['end_time'], '7:00').'>7:00</option>
											<option value="7:15" '.$this->check_time_option($form['presenter']['end_time'], '7:15').'>7:15</option>
											<option value="7:30" '.$this->check_time_option($form['presenter']['end_time'], '7:30').'>7:30</option>
											<option value="7:45" '.$this->check_time_option($form['presenter']['end_time'], '7:45').'>7:45</option>
											<option value="8:00" '.$this->check_time_option($form['presenter']['end_time'], '8:00').'>8:00</option>
											<option value="8:15" '.$this->check_time_option($form['presenter']['end_time'], '8:15').'>8:15</option>
											<option value="8:30" '.$this->check_time_option($form['presenter']['end_time'], '8:30').'>8:30</option>
											<option value="8:45" '.$this->check_time_option($form['presenter']['end_time'], '8:45').'>8:45</option>
											<option value="9:00" '.$this->check_time_option($form['presenter']['end_time'], '9:00').'>9:00</option>
											<option value="9:15" '.$this->check_time_option($form['presenter']['end_time'], '9:15').'>9:15</option>
											<option value="9:30" '.$this->check_time_option($form['presenter']['end_time'], '9:30').'>9:30</option>
											<option value="9:45" '.$this->check_time_option($form['presenter']['end_time'], '9:45').'>9:45</option>
											<option value="10:00" '.$this->check_time_option($form['presenter']['end_time'], '10:00').'>10:00</option>
											<option value="10:15" '.$this->check_time_option($form['presenter']['end_time'], '10:15').'>10:15</option>
											<option value="10:30" '.$this->check_time_option($form['presenter']['end_time'], '10:30').'>10:30</option>
											<option value="10:45" '.$this->check_time_option($form['presenter']['end_time'], '10:45').'>10:45</option>
											<option value="11:00" '.$this->check_time_option($form['presenter']['end_time'], '11:00').'>11:00</option>
											<option value="11:15" '.$this->check_time_option($form['presenter']['end_time'], '11:15').'>11:15</option>
											<option value="11:30" '.$this->check_time_option($form['presenter']['end_time'], '11:30').'>11:30</option>
											<option value="11:45" '.$this->check_time_option($form['presenter']['end_time'], '11:45').'>11:45</option>
											
											<option value="12:00" '.$this->check_time_option($form['presenter']['end_time'], '12:00').'>12:00</option>
											<option value="12:15" '.$this->check_time_option($form['presenter']['end_time'], '12:15').'>12:15</option>
											<option value="12:30" '.$this->check_time_option($form['presenter']['end_time'], '12:30').'>12:30</option>
											<option value="12:45" '.$this->check_time_option($form['presenter']['end_time'], '12:45').'>12:45</option>
											
											<option value="13:00" '.$this->check_time_option($form['presenter']['end_time'], '13:00').'>13:00</option>
											<option value="13:15" '.$this->check_time_option($form['presenter']['end_time'], '13:15').'>13:15</option>
											<option value="13:30" '.$this->check_time_option($form['presenter']['end_time'], '13:30').'>13:30</option>
											<option value="13:45" '.$this->check_time_option($form['presenter']['end_time'], '13:45').'>13:45</option>
											
											<option value="14:00" '.$this->check_time_option($form['presenter']['end_time'], '14:00').'>14:00</option>
											<option value="14:15" '.$this->check_time_option($form['presenter']['end_time'], '14:15').'>14:15</option>
											<option value="14:30" '.$this->check_time_option($form['presenter']['end_time'], '14:30').'>14:30</option>
											<option value="14:45" '.$this->check_time_option($form['presenter']['end_time'], '14:45').'>14:45</option>
											
											<option value="15:00" '.$this->check_time_option($form['presenter']['end_time'], '15:00').'>15:00</option>
											<option value="15:15" '.$this->check_time_option($form['presenter']['end_time'], '15:15').'>15:15</option>
											<option value="15:30" '.$this->check_time_option($form['presenter']['end_time'], '15:30').'>15:30</option>
											<option value="15:45" '.$this->check_time_option($form['presenter']['end_time'], '15:45').'>15:45</option>
											
											<option value="16:00" '.$this->check_time_option($form['presenter']['end_time'], '16:00').'>16:00</option>
											<option value="16:15" '.$this->check_time_option($form['presenter']['end_time'], '16:15').'>16:15</option>
											<option value="16:30" '.$this->check_time_option($form['presenter']['end_time'], '16:30').'>16:30</option>
											<option value="16:45" '.$this->check_time_option($form['presenter']['end_time'], '16:45').'>16:45</option>
											
											
											<option value="17:00" '.$this->check_time_option($form['presenter']['end_time'], '17:00').'>17:00</option>
											<option value="17:15" '.$this->check_time_option($form['presenter']['end_time'], '17:15').'>17:15</option>
											<option value="17:30" '.$this->check_time_option($form['presenter']['end_time'], '17:30').'>17:30</option>
											<option value="17:45" '.$this->check_time_option($form['presenter']['end_time'], '17:45').'>17:45</option>
											
											<option value="18:00" '.$this->check_time_option($form['presenter']['end_time'], '18:00').'>18:00</option>
											<option value="18:15" '.$this->check_time_option($form['presenter']['end_time'], '18:15').'>18:15</option>
											<option value="18:30" '.$this->check_time_option($form['presenter']['end_time'], '18:30').'>18:30</option>
											<option value="18:45" '.$this->check_time_option($form['presenter']['end_time'], '18:45').'>18:45</option>
											
											<option value="19:00" '.$this->check_time_option($form['presenter']['end_time'], '19:00').'>19:00</option>
											<option value="19:15" '.$this->check_time_option($form['presenter']['end_time'], '19:15').'>19:15</option>
											<option value="19:30" '.$this->check_time_option($form['presenter']['end_time'], '19:30').'>19:30</option>
											<option value="19:45" '.$this->check_time_option($form['presenter']['end_time'], '19:45').'>19:45</option>
											
											<option value="20:00" '.$this->check_time_option($form['presenter']['end_time'], '20:00').'>20:00</option>
											<option value="20:15" '.$this->check_time_option($form['presenter']['end_time'], '20:15').'>20:15</option>
											<option value="20:30" '.$this->check_time_option($form['presenter']['end_time'], '20:30').'>20:30</option>
											<option value="20:45" '.$this->check_time_option($form['presenter']['end_time'], '20:45').'>20:45</option>
											
											<option value="21:00" '.$this->check_time_option($form['presenter']['end_time'], '21:00').'>21:00</option>
											<option value="21:15" '.$this->check_time_option($form['presenter']['end_time'], '21:15').'>21:15</option>
											<option value="21:30" '.$this->check_time_option($form['presenter']['end_time'], '21:30').'>21:30</option>
											<option value="21:45" '.$this->check_time_option($form['presenter']['end_time'], '21:45').'>21:45</option>
											
											<option value="22:00" '.$this->check_time_option($form['presenter']['end_time'], '22:00').'>22:00</option>
											<option value="22:15" '.$this->check_time_option($form['presenter']['end_time'], '22:15').'>22:15</option>
											<option value="22:30" '.$this->check_time_option($form['presenter']['end_time'], '22:30').'>22:30</option>
											<option value="22:45" '.$this->check_time_option($form['presenter']['end_time'], '22:45').'>22:45</option>
											
											<option value="23:00" '.$this->check_time_option($form['presenter']['end_time'], '23:00').'>23:00</option>
											<option value="23:15" '.$this->check_time_option($form['presenter']['end_time'], '23:15').'>23:15</option>
											<option value="23:30" '.$this->check_time_option($form['presenter']['end_time'], '23:30').'>23:30</option>
											<option value="23:45" '.$this->check_time_option($form['presenter']['end_time'], '23:45').'>23:45</option>
											
											
										</select>
										
										<span id="offer_end_time_zone"></span>
										
								</td>
									
								<td valign="top">
								
								
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="rowA title">
					<td colspan="2" class="title_division">Photos</td>
				</tr>
				<tr>
					<td colspan="2">
						'.$photo_list.'
					</td>
				</tr>
				<tr>
					<td style="padding: 15px 0 35px 35px;">
						<input class="button" name="save" value="Save" type="submit"> &nbsp; &nbsp;
						'.(isset($data['serialize'])? '<span id="ove_csv" class="button" style="padding:1px 5px;margin:3px 0 0 55px;font-size:14px;cursor:pointer"><a href="/photos/ove/'.$data['csv'].'" target="_blank" style="text-decoration:none; color:#009933;">Download csv</a></span>':'').'
						'.(isset($data['serialize'])? '<span id="ove_submit" class="button" style="padding:1px 5px;margin:3px 0 0 55px;font-size:14px;cursor:pointer">Send to OVE.com</span>':'').'
						
						
					</td>
				</tr>
			</table>
	</form>';
		}
	}

	private function check_time_option($time, $opt_time)
	{
		$time_tmp = explode(':', $opt_time);
		$hour = preg_replace('/^0/','',$time_tmp[0]);
		$minute = preg_replace('/^0/','',$time_tmp[1]);
		
		$current_date = date('g:i',strtotime("now + 15 minutes"));
		$min_date = date('g:i',strtotime($hour.':'.$minute));
		$max_date = date('g:i',strtotime($hour.':'.$minute.'  + 15 minutes'));
		//echo "<br>".$min_date." ".$current_date." ".$current_date." ".$max_date." ".($min_date<$current_date && $current_date<$max_date);

		if($time!='' && $time==$opt_time)
		{
			return "selected='selected'";
		}elseif($min_date<$current_date && $current_date<$max_date)
		{
			return "selected='selected'";
		}else return "";
	}
	
	private function check_time_mer($mer, $opt_mer)
	{
		$m = date('A');
		if($mer=='' && $opt_mer==$m)
		{
			return "selected='selected'";
		}elseif($m==$opt_mer)
		{
			return "selected='selected'";
		}else return "";
	}
	
	private function carInfo() {
		if(intval($_GET['car_id'])!=0) {
			$this->car_info = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `id` = '".intval($_GET['car_id'])."'"));
		}
		else header('Location: /?mod=cars');
	}
	private function getModel(){
		$model = '';
		if (!empty($this->car_info)){
			$res = mysql_fetch_array($this->mysqlQuery("SELECT `name` FROM `ccl_".ACCOUNT_SUFFIX."model` WHERE `id` = '".$this->car_info['car_model']."'"));
			$model = $res['name'];
		}
		return $model;
	}
	
	private function getMark(){
		$mark = '';
		if (!empty($this->car_info)){
			$res = mysql_fetch_array($this->mysqlQuery("SELECT `name` FROM `ccl_".ACCOUNT_SUFFIX."marka` WHERE `id` = '".$this->car_info['car_marka']."'"));
			$mark = $res['name'];
		}
		return $mark;
	}


}
function arr_echo($arr)
{
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}
?>