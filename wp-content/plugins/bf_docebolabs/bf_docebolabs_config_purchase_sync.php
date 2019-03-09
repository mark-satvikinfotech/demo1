<?php
if(isset($cronrun) && $cronrun == true ){
	
	include('config.php');
	
	$config = json_decode(file_get_contents($purchase_config_log_file_name),true);
	
	$total_data = $config['total'] ;
	
	$last_updated_purchase = $config['last_updated'];
	
    $access_token = bf_docebolabs_fetch_token();
	$data = array(
			//'created_from' => date('Y-m-d H:i:s', $lastcronrun),
			'from' => '0',
			'count' => '1000',
			'show_items' => true
			);
	$authorization = "Authorization: Bearer ".$access_token['accessToken']; 
	$curlSession = curl_init("https://".$subdomain.".docebosaas.com/api/ecommerce/listTransactions"); 
			  
	curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
	curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curlSession, CURLOPT_TIMEOUT, 300);
	curl_setopt($curlSession, CURLOPT_POST, 1);
	curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($curlSession, CURLOPT_FOLLOWLOCATION, 1);
	$response = curl_exec($curlSession);
	
	curl_close($curlSession); 	
   
	$result = json_decode($response);
	
	$total_transcation = count($result->transactions); 
	if($total_transcation == $total_data)
	{
		exit;
	}
	
	
	/**** Start Functions ****/
   /* echo $_REQUEST['Course_data'];
   $lastcronrun = get_option('bf_docebolabs_cron_last_run');
	if($lastcronrun == null || $lastcronrun == ''){
		 $lastcronrun = strtotime('-10 years', current_time('timestamp', true));
		echo "<br/>";
	}
	echo $isTime = $lastcronrun - (5 * 3600); */
	// debug tool
	if(!function_exists('bf_debugOutput')){
		function bf_debugOutput($title, $value){
			global $debug;
			if($debug == true){
				$fp = fopen(dirname(__file__).'/debug_bf_docebolabs_purchase_cron.txt', 'a');
				fwrite($fp, $title." - ".print_r($value, true)."\r\n");
				fclose($fp);
			}
		}
	}

	// search Docebo array for key that holds matching id value
	if(!function_exists('bf_searchDoceboArray')){
		function bf_searchDoceboArray($input, $field, $search){
			foreach($input as $key => $value){
				if($value[$field] == $search){
					return "x".$key;
				}
			}
			return false;
		}
	}
	
	// Collect current Docebo Course data
	if(!function_exists('bf_getCurrentDoceboCourseData')){
		function bf_getCurrentDoceboCourseData(){
			global $subdomain, $access_token;
			$submiturl = "https://".$subdomain.".docebosaas.com/api/course/courses";
			$authorization = "Authorization: Bearer ".$access_token['accessToken'];
			$data = array();
			$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
			$httppost = wp_safe_remote_post($submiturl, $options);
			$result = json_decode($httppost['body'], true);
			
			if(!isset($result['success']) || $result['success'] != 1){
				$access_token = bf_docebolabs_fetch_token();

				$submiturl = "https://".$subdomain.".docebosaas.com/api/course/courses";
				$authorization = "Authorization: Bearer ".$access_token['accessToken'];
				$data = array();
				$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
				$httppost = wp_safe_remote_post($submiturl, $options);
				$result = json_decode($httppost['body'], true);
				
				if(!isset($result['success']) || $result['success'] != 1){
					bf_debugOutput('bf_getCurrentDoceboCourseData ERROR:', $result);
					die;
				}
			}
			//bf_debugOutput('bf_getCurrentDoceboCourseData', "Current Docebo Course Data");
			//bf_debugOutput('bf_getCurrentDoceboCourseData', $result['courses']);
			return $result['courses'];
		}
	}

	
	// Collect new purchases from Docebo
	if(!function_exists('bf_getNewPurchaseData')){
		function bf_getNewPurchaseData($coursesResult){
			global $subdomain, $access_token, $lastcronrun;
			$lastcronrun = get_option('bf_docebolabs_cron_last_run');
			//$lastcronrun = '1518415450';
			$submiturl = "https://".$subdomain.".docebosaas.com/api/ecommerce/listTransactions";
			$authorization = "Authorization: Bearer ".$access_token['accessToken'];
			$data = array(
				//'created_from' => date('Y-m-d H:i:s', $lastcronrun),
				'from' => '0',
				'count' => '500',
				'show_items' => true
			);
			$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
			$httppost = wp_safe_remote_post($submiturl, $options);
			$result = json_decode($httppost['body'], true);
			if(!isset($result['success']) || $result['success'] != 1){
				$access_token = bf_docebolabs_fetch_token();

				$submiturl = "https://".$subdomain.".docebosaas.com/api/ecommerce/listTransactions";
				$authorization = "Authorization: Bearer ".$access_token['accessToken'];
				$data = array(
					//'created_from' => date('Y-m-d H:i:s', $lastcronrun),
					'from' => '0',
					'count' => '500',
					'show_items' => true
				);
				$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
				$httppost = wp_safe_remote_post($submiturl, $options);
				$result = json_decode($httppost['body'], true);

				if(!isset($result['success']) ||  $result['transactions'] == null){
					bf_debugOutput('bf_getNewPurchaseData ERROR:', $result);
					die;
				}
			}
			//bf_debugOutput('bf_getNewPurchaseData raw', $result);
			$output = array();
			$x = '0';
			if(count($result['transactions']) >= '1'){
				foreach($result['transactions'] as $transactions){
					if($transactions['status'] == 'accepted'){
						foreach($transactions as $transactionKey => $transactionValue){
							if($transactionKey == 'items'){
								// prepare items
								if(count($transactions['items']) >= '1'){
									$y = '0';
									foreach($transactions['items'] as $transactionItem){
										// find course
										$courseKey = ltrim(bf_searchDoceboArray($coursesResult, 'course_id', $transactionItem['item_id']), 'x'); 
										//echo "blank";
										// build prepared output
										$output[$x]['items'][$y]['Sku'] = $coursesResult[$courseKey]['course_id'].'-'.$coursesResult[$courseKey]['code'];
										$output[$x]['items'][$y]['ProductName'] = $coursesResult[$courseKey]['course_name'].' - '.$coursesResult[$courseKey]['code'];
										$output[$x]['items'][$y]['ProductPrice'] = ($coursesResult[$courseKey]['price'] == null || $coursesResult[$courseKey]['price'] == '' ? '0.00' : $coursesResult[$courseKey]['price']);
										$output[$x]['items'][$y]['ShortDescription'] = $coursesResult[$courseKey]['course_name'].' - '.$coursesResult[$courseKey]['course_type'];
										$output[$x]['items'][$y]['Description'] = $coursesResult[$courseKey]['course_description'];
										$y++;
										
									}
								}
							} else {
								$output[$x][$transactionKey] = $transactionValue;
							}
						}
					}
					$x++;
				}
				
			}
			//bf_debugOutput('bf_getNewPurchaseData prepared', $output);
			return $output;
		}
	}

	// Add Purchase to Infusionsoft
	if(!function_exists('bf_addIsPurchases')){
		function bf_addIsPurchases($purchases){
			//print_r($purchases);
			//echo $total_purchase = count($purchases); 
			if(count($purchases) >= '1'){
				global $sitedomain, $syncfields, $tag_selected;
				$submiturl = plugin_dir_url(__file__)."docebolabs/bf_docebolabs_add_purchase.php";
				
				include('config.php');
				include('docebo_data.php');
				
				$docebo = new Docebo_data();
				
			    $config = json_decode(file_get_contents($purchase_config_log_file_name),true);
					 
				$start = $config['start'];
				$limit = $config['limit']+ $start;
				$last_updated_contact = $config['last_updated'];
				$total = $config['total'];
				$flag_var = $config['flag'];
				$purchase =  array_slice($purchases ,$start, $limit);
				
				//print_r($purchase);
				
				for($i=$start; $i<$limit; $i++)
				{
					 
				     $purch = $purchase[$i];
					 //echo "<pre>"; print_r($purch);
				    //foreach($purchases as $purchase){
					$data = array(
						'email' => esc_attr(get_option('bf_docebolabs_regemail')),
						'domain' => urlencode($sitedomain),
						'isappid' => esc_attr(get_option('bf_docebolabs_is_app_name')),
						'isapikey' => esc_attr(get_option('bf_docebolabs_is_api_key')),
						'plugin' => 'DoceboLabs',
						'addpurchase' => $purch,
						'syncfields' => $syncfields,
						'tag' => $tag_selected,
						'bf_docebo_taxable' => get_option('bf_docebo_taxable'),
						'bf_docebo_CountryTaxable' => get_option('bf_docebo_CountryTaxable'),
						'bf_docebo_StateTaxable' => get_option('bf_docebo_StateTaxable'),
						'bf_docebo_CityTaxable' => get_option('bf_docebo_CityTaxable')
					);
					//bf_debugOutput('bf_addIsPurchases', "Add order");
					bf_debugOutput('bf_addIsPurchases', $purch);
					$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1');
					$httppost = wp_safe_remote_post($submiturl, $options);
					sleep(1);
					
					$purchase_config = json_decode(file_get_contents($purchase_config_log_file_name),true);
					
					$st_data_value = $purchase_config['start'] + 1;
	                $docebo->update_LogFile("start", $st_data_value,$purchase_config_log_file_name);
					
					$last_data_value = $purchase_config['last_updated'] + 1;
	                $docebo->update_LogFile("last_updated", $last_data_value,$purchase_config_log_file_name);
					
				}
			}
		}
	}

	/**** End Functions ****/

	/**** Start Cron ****/

	
	// Collect Docebo Current Course Data
	$current_docebo_Courses = bf_getCurrentDoceboCourseData();


	// Check for new purchases in Docebo
	$purchase_Is_Updates = bf_getNewPurchaseData($current_docebo_Courses);

	// Process data
	bf_addIsPurchases($purchase_Is_Updates);

	$docebo = new Docebo_data();
	
	$purchase_config = json_decode(file_get_contents($purchase_config_log_file_name),true);
	
	$flag_data_value = $purchase_config['flag'] + 1;
	$docebo->update_LogFile("flag", $flag_data_value,$purchase_config_log_file_name);
	$total_data_value = $total_course;
	if($last_updated_purchase > $total_transcation)
	{
		$docebo->update_LogFile("total", $total_data_value,$purchase_config_log_file_name);
	}
    echo "Cron Run Successfully";
	/**** End Cron ****/
}
?>