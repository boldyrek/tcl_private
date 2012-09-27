<?
//error_reporting(0);
set_time_limit(6000);
header("Expires: Tue, 1 Jul 2003 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=utf-8");
ob_implicit_flush(false);
session_start();


if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='8') header('Location: /adm_transporters');
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='9') header('Location: /adm_expeditors');

require_once($_SERVER['DOCUMENT_ROOT'].'/lib/class.Proto.php');

$car_id = (int)$_GET['car_id'];
class LoadOveForm extends Proto {

	public function drawContent($car_id) {
		if($this->checkAuth()) {
			$sql="SELECT * FROM `ccl_ove` WHERE car_id='".$car_id."' LIMIT 1";
			return mysql_fetch_array($this->mysqlQuery($sql));
		}else return false;
	}
}
$proto = new LoadOveForm;
$data = $proto->drawContent($car_id);

if($car_id>0 && $data && is_array($data) && isset($_SESSION['authorised']) && $_SESSION['authorised'])
{
	include_once("class.curl.php");
	$form = unserialize($data['serialize']);

//	$form['vin_code'] = 'JHMRA3863WC001017';
	
	$content = file_get_contents("page.html");
	echo $content;

	$hostname = 'www.ove.com';

	echo '<script>addContent("Соединяемся с сервером '.$hostname.'")</script>'."\n";

	$c = new curl("https://".$hostname."/authenticate") ;

	echo '<script>addContent("Соединение установлено.")</script>'."\n";

	$c->setopt(CURLOPT_HEADER, 1);
	$c->setopt(CURLOPT_NOBODY, 1);

	$c->setopt(CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3');
	$c->setopt(CURLOPT_REFERER, 'https://'.$hostname.'/login');
	$c->setopt(CURLOPT_HTTPHEADERS,array('Content-Type: application/x-www-form-urlencoded'));
	$c->setopt(CURLOPT_POST, true);

	$c->setopt(CURLOPT_COOKIESESSION, true);
	$c->setopt(CURLOPT_COOKIEFILE, 'cookie.txt');
	$c->setopt(CURLOPT_COOKIEJAR, 'cookie.txt');
	$c->setopt(CURLOPT_COOKIE, session_name() . '=' . session_id());
	$theFieldsLogin = array
	(
	'commit' => 'Login',
	'user' => array('password'=>'torus82', 'username'=>'makmal09')
	) ;

	$c->setopt(CURLOPT_POSTFIELDS, $c->asPostString($theFieldsLogin)) ;
	$c->setopt(CURLOPT_RETURNTRANSFER, 1);
	$c->setopt(CURLOPT_SSL_VERIFYPEER, 0);
	$c->setopt(CURLOPT_SSL_VERIFYHOST, 0);


	// это необходимо, чтобы cURL не высылал заголовок на ожидание
	$c->setopt(CURLOPT_HTTPHEADER, array('Expect:'));;


	$c->setopt(CURLOPT_VERBOSE, '1');

	$c->setopt(CURLOPT_FOLLOWLOCATION, 0);
	echo '<script>addContent("Запрос на авторизацию.")</script>'."\n";
	$result_auth = $c->exec() ;

	
	if ($theError = $c->hasError())
	{
		echo '<script>displayError("'.clean($theError).'")</script>'."\n";
		flush();
	}else{
		if(check_error_server($result_auth))die('<script>displayError("500 Internal Server Error!")</script>');
		flush();
		$result_auth = $c->follow_location($result_auth);

		if(preg_match('/(flash error)/',$result_auth))
		{
			preg_match_all('/flash error([^div]+)(.*)<\/div/sU', $result_auth,$out);
			$msg="Error autorization!";
			if(isset($out[2][0]))$msg=clean($out[2][0]);
			die('<script>displayError("'.$msg.'")</script>');
			flush();
		}else{
			echo '<script>addContent("Проверка авторизации прошла успешно.")</script>'."\n";
			flush();
			####################################################################################################################################
			$c->setopt(CURLOPT_URL, 'https://'.$hostname.'/listing_wizard/start');
			$c->setopt(CURLOPT_REFERER, 'https://'.$hostname.'/buy');
			
			$start = $c->exec();
			if(check_error_server($start))die('<script>displayError("500 Internal Server Error!")</script>');
			flush();

####################################################################################################################################
			echo '<script>addContent("Переходим к размещению VIN CODE.")</script>'."\n";
			$c->setopt(CURLOPT_URL, 'https://'.$hostname.'/listing_wizard/select_entry_method');
			$c->setopt(CURLOPT_REFERER, 'https://'.$hostname.'/listing_wizard/select_entry_method');
			$theFieldsAdd = array
			(
			'wizard_goto_step_2_button' => 'Go To Step 2',
			'presenter' => array('account_id'=>'228537', 'import_type'=>'vin', 'vehicle_type_id'=>'1', 'vin'=>$form['vin_code'],
			'auto_trader_login'=>array('auto_trader_password'=>'proof09', 'auto_trader_username'=>'makmal09', 'remember_username'=>false)
			)
			) ;
			$postdata = $c->asPostString($theFieldsAdd);

			$c->setopt(CURLOPT_POSTFIELDS, $postdata);
			$result_vin = $c->exec();
				
			if(check_error_server($result_vin))die('<script>displayError("500 Internal Server Error!")</script>');
			$result_vin = $c->follow_location($result_vin);

			####################################################################################################################################
			if ($theError = $c->hasError())
			{
				echo '<script>displayError("'.clean($theError).'")</script>'."\n";
				flush();
			}else{
				if(preg_match('/(error_vin)/',$result_vin))
				{
					preg_match_all('/errors([^div]+)(.*)<\/div/sU', $result_vin,$out);
					$msg="Error VIN CODE!";
					if(isset($out[2][0]))$msg=clean($out[2][0]);
					die('<script>displayError("'.$msg.'")</script>');
					flush();
				}else{
					echo '<script>addContent("VIN CODE - установлен.")</script>'."\n";
					echo '<script>addContent("Переходим к заполнению формы с данными о машине.")</script>'."\n";
					flush();
					$c->setopt(CURLOPT_URL, 'https://'.$hostname.'/listing_wizard/vehicle_information');
					$c->setopt(CURLOPT_REFERER, 'https://'.$hostname.'/listing_wizard/vehicle_information');

					$theFieldsStep2 = array
					(
					'listing' => Array(
					'trim_option'=>'',
					'odometer_reading'=>$form['listing']['odometer_reading'],
					'odometer_unit_of_measure'=>$form['listing']['odometer_unit_of_measure'],
					'exterior_color_id'=>'',
					'interior_color_id'=>'',
					'manheim_stock_number'=>$form['listing']['manheim_stock_number'],
					'vehicle_location_id'=>$form['listing']['vehicle_location_id'],
					'facilitation_service_provider_id'=>$form['listing']['facilitation_service_provider_id'],
					'title_status_id'=>$form['listing']['title_status_id'],
					'state_id'=>$form['listing']['state_id'],
					'engine_option'=>$form['listing']['engine_option'],
					'transmission_option'=>$form['listing']['transmission_option'],
					'drive_train_option'=>$form['listing']['drive_train_option'],
					'fuel_option'=>$form['listing']['fuel_option'],
					'audio_type_option'=>$form['listing']['audio_type_option'],
					'body_type_option'=>$form['listing']['body_type_option'],
					'top_type_option'=>$form['listing']['top_type_option'],
					'interior_type_option'=>$form['listing']['interior_type_option'],
					'door_option'=>$form['listing']['door_option'],
					'frame_damage'=>$form['listing']['frame_damage'],
					'prior_paint'=>$form['listing']['prior_paint'],
					'prior_paint_details'=>$form['listing']['prior_paint_details'],
					'salvage_vehicle'=>0,
					'sold_as_is'=>0,
					'no_exterior_damage'=>$form['listing']['no_exterior_damage'],
					'exterior_disclosed_damages'=>$form['listing']['exterior_disclosed_damages'],
					'no_interior_damage'=>$form['listing']['no_interior_damage'],
					'interior_disclosed_damages'=>$form['listing']['interior_disclosed_damages'],
					'all_new_tires'=>$form['listing']['all_new_tires'],
					'left_front_tire_condition_id'=>$form['listing']['left_front_tire_condition_id'],
					'right_front_tire_condition_id'=>$form['listing']['right_front_tire_condition_id'],
					'left_rear_tire_condition_id'=>$form['listing']['left_rear_tire_condition_id'],
					'right_rear_tire_condition_id'=>$form['listing']['right_rear_tire_condition_id'],
					'spare_tire_condition_id'=>$form['listing']['spare_tire_condition_id'],
					'seller_comments'=>'',
					'buyer_group_id'=>0,
					'account_id'=>'228537',
					'contact_name'=>'Valentina Shevchik',
					'contact_company_name'=>'MAKMAL NORTH AMERICA CO',
					'contact_phone'=>'4168541978',
					'contact_fax'=>'6474355876',
					'contact_email'=>'valentina@makmalauto.com',
					'contact_cc_email'=>''
					),
					'jump_to'=>'',
					'next' => 'Continue'
					);

					$postdata = $c->asPostString($theFieldsStep2);

					$postdata = preg_replace('/damages%5D%5B\d*%5D%5Bdamage/sU','damages%5D%5B%5D%5Bdamage', $postdata);

					$c->setopt(CURLOPT_POSTFIELDS, $postdata);
					$result_form = $c->exec(); // загружаем пост и если всё ок то переходим на страницу добавления фоток
					$result_form = $c->follow_location($result_form, 'https://'.$hostname.'/listing_wizard/vehicle_information');
					echo "#$result_form#";
					exit();
					if(check_error_server($result_form))die('<script>displayError("500 Internal Server Error!")</script>');
					flush();
					####################################################################################################################################

					if($theError = $c->hasError())
					{
						echo '<script>displayError("'.clean($theError).'")</script>'."\n";
						flush();
					}else{


						if(preg_match('/(errors)/',$result_form))
						{
							preg_match_all('/errors([^div]+)(.*)<\/div/sU', $result_form,$out);
							$msg="Error form!";
							if(isset($out[2][0]))$msg=clean($out[2][0]);
							die('<script>displayError("'.$msg.'")</script>');
							flush();
						}else{

							echo '<script>addContent("Данные успешно внесены.")</script>'."\n";
							flush();

							if(isset($form['photos']) && count($form['photos'])>0)
							{
								echo '<script>addContent("Переходим к размещению фотографий.")</script>'."\n";
								flush();
								preg_match_all('/vehicle_image_upload_iframe([^>]+)*>([^<]+)*<\/iframe/sUi',$result_form, $out);
								if(isset($out[0][0]) && !empty($out[0][0]))
								{
									$id_vihicle=preg_replace('/.*https:\/\/'.$hostname.'\/lising_images\/single_image_upload\/(\d+).*/','\1',$out[0][0]);

									$sql="UPDATE `ccl_ove` SET `ove_id` = '".$id_vihicle."' WHERE `car_id` =".$car_id." LIMIT 1 ;";
									$proto->mysqlQuery($sql);
									
									$c->setopt(CURLOPT_URL, 'https://'.$hostname.'/lising_images/single_image_upload/'.$id_vihicle);
									$c->setopt(CURLOPT_POST, false);
									$iframe_upload_html = $c->exec();
									
									preg_match_all('/.*listing_images\/'.$id_vihicle.'\/upload_one\/(\d+).*/si',$iframe_upload_html,$out2);

									if(isset($out2[1][0]) && !empty($out2[1][0]))
									{
										$id_pict = $out2[1][0];
										//echo '<script>addContent("id_pict->'.$id_pict.'")</script>';
										//фотографии автомобиля
										$_upload = array();

										$photo_path = $_SERVER['DOCUMENT_ROOT'].'/photos/'.$car_id.'/';
										
										echo '<script>addContent("Загружаем файл фотографии.")</script>'."\n";
										flush();
										
										foreach($form['photos'] as $k=>$photo)
										{
											$file = $photo_path.$photo;
											
											if(is_file($file))
											{
												$_upload['SourceFile_1'] = "@$file";
												$url = 'https://'.$hostname.'/listing_images/'.$id_vihicle.'/upload_one/'.$id_pict;
												$ref = 'https://'.$hostname.'/listing_images/single_image_upload/'.$id_vihicle;
												$c->setopt(CURLOPT_URL, $url);
												$c->setopt(CURLOPT_REFERER, $ref);
												$c->setopt(CURLOPT_RETURNTRANSFER, 1);
												$c->setopt(CURLOPT_POSTFIELDS, $_upload);

												$frame_uploaded_html = $c->exec();
												$frame_uploaded_html = $c->follow_location($frame_uploaded_html);
												if(check_error_server($frame_uploaded_html))die('<script>displayError("500 Internal Server Error!")</script>');
												flush();
												if($theError = $c->hasError())
												{
													echo '<script>displayError("'.clean($theError).'")</script>'."\n";
													flush();
												}else{
													echo '<script>addContent("Фотография №'.($k+1).' успешно загружена.")</script>'."\n";
													flush();
												}
											}
										}
									}
								}else{
									echo '<script>displayError("Не удалось определить ID номер машины!")</script>'."\n";
									flush();
								}

							}else{
								echo '<script>addContent("Вы не выбрали фотографии для размещения на сайте. Пропускаем операцию.")</script>'."\n";
								flush();
							}

							$c->setopt(CURLOPT_URL, 'https://'.$hostname.'/listing_wizard/add_pictures');
							$c->setopt(CURLOPT_REFERER, 'https://'.$hostname.'/listing_wizard/add_pictures');
							$theFieldsStep = array
							(
							'jump_to'=>'',
							'next' => 'Continue'
							);

							$postdata = $c->asPostString($theFieldsStep);
							$c->setopt(CURLOPT_POSTFIELDS, $postdata);

							$jump = $c->exec();
							$jump = $c->follow_location($jump, 'https://'.$hostname.'/listing_wizard/add_pictures');

							echo '<script>addContent("Переходим к форме заполнения цен.")</script>'."\n";
							flush();
							//							$c->setopt(CURLOPT_URL, 'https://'.$hostname.'/listing_wizard/add_price');
							//							$c->setopt(CURLOPT_REFERER, 'https://'.$hostname.'/listing_wizard/add_pictures');
							//							$add_price = $c->exec();
							//$add_price = $c->follow_location($add_price);

							//echo $jump;

							$c->setopt(CURLOPT_URL, 'https://'.$hostname.'/listing_wizard/add_price');
							$c->setopt(CURLOPT_REFERER, 'https://'.$hostname.'/listing_wizard/add_price');

							preg_match_all('/listing\[(above_mmr|average_mmr|below_mmr)\]"\s*type="hidden"\s*value="(\d+)"/sU', $jump, $out);

							$ove_above_mmr = $ove_average_mmr = $ove_below_mmr = '';
							if(count($out[2])>0)
							{
								foreach ($out[1] as $k=>$v)
								{
									${'ove_'.$v}=$out[2][$k];
								}
							}
							
							$postdata = 'next=true&listing%5Babove_mmr%5D='.$ove_above_mmr;
							$postdata.= '&listing%5Baverage_mmr%5D='.$ove_average_mmr;
							$postdata.= '&listing%5Bbelow_mmr%5D='.$ove_below_mmr;
							$postdata.= '&listing%5Bpriced_right_override_fee_accepted%5D=0&presenter%5Ballow_dealers_to_bid%5D=1&presenter%5Ballow_dealers_to_bid%5D=0&presenter%5Ballow_dealers_to_buy_now%5D=1&presenter%5Ballow_dealers_to_buy_now%5D=0';
							$postdata.= '&listing%5Bcurrency_id%5D=1&listing%5Bstarting_bid_price%5D='.$form['listing']['starting_bid_price'];
							$postdata.= '&listing%5Bbid_increment%5D='.$form['listing']['bid_increment'];
							$postdata.= '&listing%5Bfloor_price%5D='.$form['listing']['floor_price'];
							$postdata.= '&listing%5Bbuy_now_price%5D='.$form['listing']['buy_now_price'];
							$postdata.= '&presenter%5Boffer_start_now%5D=true&presenter%5Boffer_start_now%5D=0';
							$postdata.= '&presenter%5Bstart_time_date%5D='.urlencode($form['presenter']['start_time_date']);
							$postdata.= '&presenter%5Bstart_time%5D='.urlencode($form['presenter']['start_time']);
							$postdata.= '&presenter%5Bstart_time_meridiem%5D='.$form['presenter']['start_time_meridiem'];
							$postdata.= '&presenter%5Btime_zone_info_id%5D='.$form['presenter']['time_zone_info_id'];
							$postdata.= '&presenter%5Bend_time_date%5D='.urlencode($form['presenter']['end_time_date']);
							$postdata.= '&presenter%5Bend_time%5D='.urlencode($form['presenter']['end_time']);
							$postdata.= '&presenter%5Bend_time_meridiem%5D='.$form['presenter']['end_time_meridiem'];
							$postdata.= '&presenter%5Boffer_duration%5D='.$form['presenter']['offer_duration'];
							$postdata.= '&presenter%5Bauto_relist%5D=0&listing%5Bauto_relist_count%5D=1&jump_to=';
							
							$c->setopt(CURLOPT_POSTFIELDS, $postdata);
							//$c->setopt(CURLOPT_POSTFIELDS, 'next=true&listing%5Babove_mmr%5D='.$ove_above_mmr.'&listing%5Baverage_mmr%5D=8525&listing%5Bbelow_mmr%5D=7475&listing%5Bpriced_right_override_fee_accepted%5D=0&presenter%5Ballow_dealers_to_bid%5D=1&presenter%5Ballow_dealers_to_bid%5D=0&presenter%5Ballow_dealers_to_buy_now%5D=1&presenter%5Ballow_dealers_to_buy_now%5D=0&listing%5Bcurrency_id%5D=1&listing%5Bstarting_bid_price%5D=9600&listing%5Bbid_increment%5D=100&listing%5Bfloor_price%5D=9700&listing%5Bbuy_now_price%5D=9800&presenter%5Boffer_start_now%5D=true&presenter%5Boffer_start_now%5D=0&presenter%5Bstart_time_date%5D=03%2F27%2F2009&presenter%5Bstart_time%5D=1%3A15&presenter%5Bstart_time_meridiem%5D=AM&presenter%5Btime_zone_info_id%5D=1&presenter%5Bend_time_date%5D=03%2F30%2F2009&presenter%5Bend_time%5D=1%3A15&presenter%5Bend_time_meridiem%5D=AM&presenter%5Boffer_duration%5D=72.0&presenter%5Bauto_relist%5D=0&listing%5Bauto_relist_count%5D=1&jump_to=');
							//$c->setopt(CURLOPT_POSTFIELDS, 'next=true&listing%5Babove_mmr%5D=&listing%5Baverage_mmr%5D=&listing%5Bbelow_mmr%5D=&listing%5Bpriced_right_override_fee_accepted%5D=0&presenter%5Ballow_dealers_to_bid%5D=1&presenter%5Ballow_dealers_to_bid%5D=0&presenter%5Ballow_dealers_to_buy_now%5D=1&presenter%5Ballow_dealers_to_buy_now%5D=0&listing%5Bcurrency_id%5D=1&listing%5Bstarting_bid_price%5D=9600&listing%5Bbid_increment%5D=100&listing%5Bfloor_price%5D=9700&listing%5Bbuy_now_price%5D=9800&presenter%5Boffer_start_now%5D=true&presenter%5Boffer_start_now%5D=0&presenter%5Bstart_time_date%5D=03%2F27%2F2009&presenter%5Bstart_time%5D=1%3A15&presenter%5Bstart_time_meridiem%5D=AM&presenter%5Btime_zone_info_id%5D=1&presenter%5Bend_time_date%5D=03%2F30%2F2009&presenter%5Bend_time%5D=1%3A15&presenter%5Bend_time_meridiem%5D=AM&presenter%5Boffer_duration%5D=72.0&presenter%5Bauto_relist%5D=0&listing%5Bauto_relist_count%5D=1&jump_to=');
							$HTML = $c->exec();
							$HTML = $c->follow_location($HTML, 'https://'.$hostname.'/listing_wizard/add_price');
							if($theError = $c->hasError())
							{
								echo '<script>displayError("'.clean($theError).'")</script>'."\n";
								flush();
							}else{


								if(preg_match('/(flash error)/',$HTML))
								{
									preg_match_all('/flash error([^div]+)(.*)<\/div/sU', $HTML,$out);
									$msg="Error form!";
									if(isset($out[2][0]))$msg=clean($out[2][0]);
									die('<script>displayError("'.$msg.'")</script>');
									flush();
								}else{
									echo '<script>addContent("Данные по ценам и времени отображения объявления внесены успешно.")</script>'."\n";

									echo '<script>addContent("Переходим в завершающую стадию.")</script>'."\n";
									flush();
									$c->setopt(CURLOPT_URL, 'https://'.$hostname.'/listing_wizard/preview_listing');
									$c->setopt(CURLOPT_REFERER, 'https://'.$hostname.'/listing_wizard/preview_listing');

									$theFieldsStep4 = array
									(
									'jump_to' => '',
									'activate' => 'Activate'
									);
									$postdata = $c->asPostString($theFieldsStep4);
									$c->setopt(CURLOPT_POSTFIELDS, $postdata);
									$HTML2 = $c->exec();
									$HTML2 = $c->follow_location($HTML2, 'https://'.$hostname.'/listing_wizard/preview_listing');
									if($theError = $c->hasError())
									{
										echo '<script>displayError("'.clean($theError).'")</script>'."\n";
										flush();
									}else{
										echo '<script>addContent("Все операции выполнены успешно.")</script>'."\n";
										flush();
									}
								}
							}
						}
					}
				}
			}
		}
	}
	$c->close() ;
	echo '<script>stopProgress()</script>'."\n";
}else {
	echo "<html><body><script>window.close();</script></body></html>"."\n";
}

function clean($text)
{
	$text = strip_tags($text);
	$text = preg_replace('/\'|"/','',$text);
	$text = preg_replace('/(<div)|(<\/)(<p)|([<>])|(div)/','',$text);
	$text = trim($text);
	return $text;
}
function check_error_server($html)
{
	return preg_match('/(Internal Server Error)/i',$html);
}
?>