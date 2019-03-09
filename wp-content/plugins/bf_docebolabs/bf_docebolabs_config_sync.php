<?php
if(isset($cronrun) && $cronrun == true){
	
	include('config.php');
	
	$config = json_decode(file_get_contents($config_log_file_name),true);
	
	$total_data = $config['total'] ;
	
    $access_token = bf_docebolabs_fetch_token();
	$data = array("from" => 0,"count" => "default");
	$authorization = "Authorization: Bearer ".$access_token['accessToken']; 
	$curlSession = curl_init("https://".$subdomain.".docebosaas.com/api/user/listUsers"); 
			  
	curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
	curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curlSession, CURLOPT_TIMEOUT, 300);
	curl_setopt($curlSession, CURLOPT_POST, 1);
	curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($curlSession, CURLOPT_FOLLOWLOCATION, 1);
	$response = curl_exec($curlSession);
	
	curl_close($curlSession); 			
	$result = json_decode($response);
	$total_users = count($result->users); 
	
			
	if($total_users == $total_data)
	{
		exit;
		
	}
	
	

	/**** Start Functions ****/

	// debug tool
	if(!function_exists('bf_debugOutput')){
		function bf_debugOutput($title, $value){
			global $debug;
			if($debug == true){
				$fp = fopen(dirname(__file__).'/debug_bf_docebolabs_cron.txt', 'a');
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


	// Add Infusionsoft User, apply tag if option set
	if(!function_exists('bf_addIsUserAndTag')){
		function bf_addIsUserAndTag($users){
			if(count($users) >= '1'){
				global $sitedomain, $tag_selected;
				$submiturl = plugin_dir_url(__file__)."docebolabs/bf_docebolabs_add_user.php";
				foreach($users as $user){
					$data = array(
						'email' => esc_attr(get_option('bf_docebolabs_regemail')),
						'domain' => urlencode($sitedomain),
						'isappid' => esc_attr(get_option('bf_docebolabs_is_app_name')),
						'isapikey' => esc_attr(get_option('bf_docebolabs_is_api_key')),
						'plugin' => 'DoceboLabs',
						'userdata' => $user,
						'tag' => $tag_selected
					);
					bf_debugOutput('bf_addDoceboIsUser', $data);
					$options = array('timeout' => 200, 'body' => $data, 'httpversion' => '1.1');
					$httppost = wp_safe_remote_post($submiturl, $options);
					sleep(1);
				}
			}
		}
	}

	// Update Infusionsoft User
	if(!function_exists('bf_updateIsUser')){
		function bf_updateIsUser($users){
			if(count($users) >= '1'){
				
				global $sitedomain, $syncfields;
				$submiturl = plugin_dir_url(__file__)."docebolabs/bf_docebolabs_add_user.php";
				foreach($users as $userkey => $user){
					$data = array(
						'email' => esc_attr(get_option('bf_docebolabs_regemail')),
						'domain' => urlencode($sitedomain),
						'isappid' => esc_attr(get_option('bf_docebolabs_is_app_name')),
						'isapikey' => esc_attr(get_option('bf_docebolabs_is_api_key')),
						'plugin' => 'DoceboLabs',
						'id_user' => $userkey,
						'updateuserdata' => $user,
						'id_user_field' => $syncfields['id_user']
					);
					$options = array('timeout' => 200, 'body' => $data, 'httpversion' => '1.1');
					$httppost = wp_safe_remote_post($submiturl, $options);
					sleep(1);
				}
			}	
		}
	}

	// Collect cached Docebo data from db
	if(!function_exists('bf_getCachedDoceboData')){
		function bf_getCachedDoceboData(){
			global $wpdb, $docebodb;
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "SELECT * FROM $docebodb";
			$pre_cached_results = $wpdb->get_results($sql);
			$cached_results = json_decode(json_encode($pre_cached_results), true);
			unset($pre_cached_results);
			bf_debugOutput('bf_getCachedDoceboData', $cached_results);
			$output = array();
			if(isset($cached_results) && count($cached_results) >= '1'){
				echo "Get CAchedDoceboData";
				$x = '0';
				foreach($cached_results as $cached_result){
					foreach($cached_result as $key => $fieldvalue){
						if($key == 'extraFields'){
							$extraFields = json_decode($fieldvalue, true);
							foreach($extraFields as $extraKey => $extraFieldValue){
								$output[$x][$extraKey] = addslashes($extraFieldValue);
							}
						} else {
							$output[$x][$key] = addslashes($fieldvalue);
						}
					}
					$x++;
				}
			}
			unset($cached_results);
			return $output;
		}
	}

	// Collect current Docebo data
	if(!function_exists('bf_getCurrentDoceboData')){
		function bf_getCurrentDoceboData(){			
			include('config.php');
			include('docebo_data.php');
			$docebo = new Docebo_data();
			$config = json_decode(file_get_contents($config_log_file_name),true);			
			$start = $config['start'];
			$limit = $config['limit'];
			$last_updated_contact = $config['last_updated'];
			$total = $config['total'];
			$flag_var = $config['flag'];
			
			
			global $subdomain, $access_token;
			$data = array("from" => $start,"count" => $limit);
			$authorization = "Authorization: Bearer ".$access_token['accessToken']; 
			$curlSession = curl_init("https://".$subdomain.".docebosaas.com/api/user/listUsers"); 
		  
			curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
			curl_setopt($curlSession, CURLOPT_TIMEOUT, 300);
			curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curlSession, CURLOPT_POST, 1);
			curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($data));
			curl_setopt($curlSession, CURLOPT_FOLLOWLOCATION, 1);
			$response = curl_exec($curlSession);
			//file_put_contents(COMP_COURSEDATA, print_r($response,true), FILE_APPEND);
			curl_close($curlSession); 			
			$result = json_decode($response);
			/*$submiturl = "https://".$subdomain.".docebosaas.com/api/user/listUsers";
			$authorization = "Authorization: Bearer ".$access_token['accessToken'];
			$data = array();
			$options = array('timeout' => 200, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
			$httppost = wp_safe_remote_post($submiturl, $options);
			
			$result = json_decode($httppost['body'], true);*/
			if(!isset($result->success) || $result->success != 1){
				$access_token = bf_docebolabs_fetch_token();
				$data = array("from" => $start,"count" => $limit);
				$authorization = "Authorization: Bearer ".$access_token['accessToken']; 
				$curlSession = curl_init("https://".$subdomain.".docebosaas.com/api/user/listUsers"); 
			  
				curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
				curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($curlSession, CURLOPT_TIMEOUT, 300);
				curl_setopt($curlSession, CURLOPT_POST, 1);
				curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($data));
				curl_setopt($curlSession, CURLOPT_FOLLOWLOCATION, 1);
				$response = curl_exec($curlSession);
				file_put_contents(DEVELOPER_MCNT, print_r($response,true), FILE_APPEND);
				
				curl_close($curlSession); 			
				$result = json_decode($response);
				/*$submiturl = "https://".$subdomain.".docebosaas.com/api/user/listUsers";
				$authorization = "Authorization: Bearer ".$access_token['accessToken'];
				$data = array();
				$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
				$httppost = wp_safe_remote_post($submiturl, $options);
				$result = json_decode($httppost['body'], true);*/

				if(!isset($result->success) || $result->success != 1){
					bf_debugOutput('bf_getCurrentDoceboData ERROR:', $result);
					die;
				}
			}
			
			$output = array();
			$x = '0';

			$skipcustom = get_option('bf_docebo_skipcustomfieldsincron');
			if(isset($skipcustom) && ($skipcustom == 1 || $skipcustom == true)){
				// get standard data for each user
				foreach($result->users as $user){
					foreach($user as $userResultKey => $userResultValue){
						$output[$x][$userResultKey] = $userResultValue;
					}
					$x++;
				}
				bf_debugOutput('bf_getCurrentDoceboData (skipped custom fields)', $output);
			} else {
				// get extended data for each user
				foreach($result->users as $user){
					
					$data = array('id_user' => $user->id_user);
					$authorization = "Authorization: Bearer ".$access_token['accessToken']; 
					$curlSession = curl_init("https://".$subdomain.".docebosaas.com/api/user/profile"); 
				  
					curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
					curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($curlSession, CURLOPT_TIMEOUT, 300);
					curl_setopt($curlSession, CURLOPT_POST, 1);
					curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($data));
					curl_setopt($curlSession, CURLOPT_FOLLOWLOCATION, 1);
					$response1 = curl_exec($curlSession);
					
					curl_close($curlSession); 			
					$userResult = json_decode($response1);
					//echo "<pre>";print_r($userResult);
					/*$submiturl = "https://".$subdomain.".docebosaas.com/api/user/profile";
					$authorization = "Authorization: Bearer ".$access_token['accessToken'];
					$data = array('id_user' => $user['id_user']);
					$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
					$httppost = wp_safe_remote_post($submiturl, $options);
					if(!is_wp_error($httppost['body'])) {
					$userResult = json_decode($httppost['body'], true);*/
					if(!isset($userResult->success) || $userResult->success != 1){
						$access_token = bf_docebolabs_fetch_token();
						
						$authorization = "Authorization: Bearer ".$access_token['accessToken']; 
						$curlSession = curl_init("https://".$subdomain.".docebosaas.com/api/user/profile"); 
					  
						curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
						curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
						curl_setopt($curlSession, CURLOPT_TIMEOUT, 300);
						curl_setopt($curlSession, CURLOPT_POST, 1);
						curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($data));
						curl_setopt($curlSession, CURLOPT_FOLLOWLOCATION, 1);
						$response1 = curl_exec($curlSession);
						
						curl_close($curlSession); 			
						$userResult = json_decode($response1);
						
						/*$submiturl = "https://".$subdomain.".docebosaas.com/api/user/profile";
						$authorization = "Authorization: Bearer ".$access_token['accessToken'];
						$data = array('id_user' => $user['id_user']);
						$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
						$httppost = wp_safe_remote_post($submiturl, $options);
						$userResult = json_decode($httppost['body'], true);*/

						if(!isset($userResult->success) || $userResult->success != 1){
							bf_debugOutput('bf_getCurrentDoceboData ERROR:', $userResult);
							die;
						}
					}
					foreach($userResult as $userResultKey => $userResultValue){
						if($userResultKey == 'fields'){
							if(count($userResultValue) >= '1'){
								foreach($userResultValue as $userResultValueExtra){
									//echo "<pre>"; print_r($userResultValueExtra);
									 $output[$x][$userResultValueExtra->name.'['.$userResultValueExtra->id.']'] = $userResultValueExtra->value;
								}
							}
						} else {
							$output[$x][$userResultKey] = $userResultValue;
						}
					}
					usleep(200000);
					$x++;
					
					$config = json_decode(file_get_contents($config_log_file_name),true);
					
					$st_data_value = $config['start'] + 1;
	                $docebo->update_LogFile("start", $st_data_value,$config_log_file_name);
					
					$last_data_value = $config['last_updated'] + 1;
	                $docebo->update_LogFile("last_updated", $last_data_value,$config_log_file_name);	
				   
				}
				//bf_debugOutput('bf_getCurrentDoceboData', $output);
			}

			return $output;
		}
	}

	// Update Docebo Cache data
	if(!function_exists('bf_updateCachedDoceboUserData')){
		function bf_updateCachedDoceboUserData($newData){
			global $wpdb, $docebodb, $access_token, $subdomain;
			$charset_collate = $wpdb->get_charset_collate();
			$delete = $wpdb->query("TRUNCATE TABLE $docebodb");
			//$submiturl = "https://".$subdomain.".docebosaas.com/api/user/listUsers";
			//$authorization = "Authorization: Bearer ".$access_token['accessToken'];
			//$data = array();
			//$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
			//$httppost = wp_safe_remote_post($submiturl, $options);
			//$result = json_decode($httppost['body'], true);
			if(count($newData) >= '1'){
				foreach($newData as $contact){
					$conDat = array(
						'id' => $contact['id_user'],
						'userid' => $contact['userid'],
						'firstname' => $contact['firstname'],
						'lastname' => $contact['lastname'],
						'email' => $contact['email']
					);
					$extraFields = array();
					foreach($contact as $key => $value){
						if(strpos($key, '[') !== false && strpos($key, ']') !== false){ 
							$extraFields[$key] = $value;
							
						}
					}
					$conDat['PTIN'] = $extraFields['PTIN ( if none enter N/A)[1]'];
					$conDat['extraFields'] = json_encode($extraFields);
					bf_debugOutput('bf_updateCachedDoceboUserData conDat:', $conDat);
					
					$wpdb->insert($docebodb, $conDat);
				}
			}
		}
	}

	// Check for changes in Docebo (current vs cached)
	if(!function_exists('bf_compareDoceboData')){
		function bf_compareDoceboData($cached, $current){
			$update = array();
			$newUsers = array();
			if(count($current) >= '1'){
				
				foreach($current as $contact){
					$arrayKey = bf_searchDoceboArray($cached, 'id', $contact['id_user']);
					if($arrayKey != false){
						$arrayKey = ltrim($arrayKey, 'x');
						// user already in cache now check for changes
						if($contact['userid'] != $cached[$arrayKey]['userid']){
							$update[$contact['id_user']]['userid'] = $contact['userid'];
						}
						if($contact['firstname'] != $cached[$arrayKey]['firstname']){
							$update[$contact['id_user']]['firstname'] = $contact['firstname'];
						}
						if($contact['lastname'] != $cached[$arrayKey]['lastname']){
							$update[$contact['id_user']]['lastname'] = $contact['lastname'];
						}
						if($contact['email'] != $cached[$arrayKey]['email']){
							$update[$contact['id_user']]['email'] = $contact['email'];
						}
						if($contact['password'] != $cached[$arrayKey]['password']){
							$update[$contact['id_user']]['password'] = $contact['password'];
						}
						if($contact['PTIN'] != $cached[$arrayKey]['PTIN']){
							$update[$contact['id_user']]['PTIN'] = $contact['PTIN'];
						}
						
					} else {
						$newUsers[] = $contact;
					}
				}
			}
			$output = array('updateUsers' => $update, 'newUsers' => $newUsers);
			unset($update, $newUsers);
			//bf_debugOutput('bf_compareDoceboData', $output);
			return $output;
		}
	}

	// Collect Infusionsoft contacts updated since last cron run
	if(!function_exists('bf_getUpdatedIsContacts')){
		function bf_getUpdatedIsContacts(){
			global $syncfields, $sitedomain, $lastcronrun, $isTime;
			$syncfieldlist = array();
			foreach($syncfields as $value){
				$syncfieldlist[] = $value;
			}
			$syncfieldlist = array_unique($syncfieldlist);
			$syncfieldlist = array_values($syncfieldlist);
			$submiturl = plugin_dir_url(__file__)."docebolabs/bf_docebolabs_get_users.php";
			$data = array(
				'email' => esc_attr(get_option('bf_docebolabs_regemail')),
				'domain' => urlencode($sitedomain),
				'isappid' => esc_attr(get_option('bf_docebolabs_is_app_name')),
				'isapikey' => esc_attr(get_option('bf_docebolabs_is_api_key')),
				'plugin' => 'DoceboLabs',
				'lastupdated' => date('Y-m-d H:i:s', $isTime),
				'syncfields' => $syncfieldlist
			);
			$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1');
			$httppost = wp_safe_remote_post($submiturl, $options);
			$contacts = json_decode($httppost['body'], true);
			bf_debugOutput('bf_getUpdatedIsContacts', $contacts);
			return $contacts;
		}
	}

	// Compare New Infusionsoft data with New Docebo Data
	if(!function_exists('bf_compareUserData')){
		function bf_compareUserData($is_Updates, $docebo_Updates, $docebo_Current){
			global $syncfields;
			$output = array();
			if(count($is_Updates) >= '1'){
				// check latest Is updates
				foreach($is_Updates as $isContact){
					if(isset($isContact[$syncfields['id_user']]) && $isContact[$syncfields['id_user']] != null && $isContact[$syncfields['id_user']] != ''){
						// has docebo id_user, check updates
						if(isset($docebo_Updates['updateUsers'][$isContact[$syncfields['id_user']]]) && $docebo_Updates['updateUsers'][$isContact[$syncfields['id_user']]] != null && $docebo_Updates['updateUsers'][$isContact[$syncfields['id_user']]] != ''){
							// both docebo and IS have new data compare fields
							foreach($syncfields as $doceboField => $IsField){
								if(strpos($doceboField, '[') !== false && strpos($doceboField, ']') !== false){
									// is a custom field so use database column for custom fields

								bf_debugOutput('doceboField custom', $IsField);




								} else {
									if($docebo_Updates['updateUsers'][$isContact[$syncfields['id_user']]][$doceboField] == $isContact[$IsField]){
										// Update values match do nothing
									} elseif(!isset($isContact[$IsField]) || $isContact[$IsField] == null || $isContact[$IsField] == ''){
										// IS field null update from docebo
										$output['updateIs'][$isContact[$syncfields['id_user']]][$IsField] = $docebo_Updates['updateUsers'][$isContact[$syncfields['id_user']]][$doceboField];
										//bf_debugOutput('IS field null update from docebo', $docebo_Updates['updateUsers'][$isContact[$syncfields['id_user']]][$doceboField]);
									} elseif(!isset($docebo_Updates['updateUsers'][$isContact[$syncfields['id_user']]][$doceboField]) || $docebo_Updates['updateUsers'][$isContact[$syncfields['id_user']]][$doceboField] == null || $docebo_Updates['updateUsers'][$isContact[$syncfields['id_user']]][$doceboField] == ''){
										// Docebo field null update from IS check current
										$docebo_Current_Key = ltrim(bf_searchDoceboArray($docebo_Current, 'id_user', $isContact[$syncfields['id_user']]), 'x');
										if(!isset($docebo_Current[$docebo_Current_Key][$doceboField]) || $docebo_Current[$docebo_Current_Key][$doceboField] != $isContact[$IsField]){
											$output['updateDocebo'][$isContact[$syncfields['id_user']]][$doceboField] = $isContact[$IsField];
											//bf_debugOutput('Docebo field null update from IS', $isContact[$IsField]);
										}
									}
								}
							}
						} else {
							// Only IS has update info, check whats different and update Docebo with it
							$docebo_Current_Key = ltrim(bf_searchDoceboArray($docebo_Current, 'id_user', $isContact[$syncfields['id_user']]), 'x');
							foreach($syncfields as $doceboField => $IsField){
								if($docebo_Current[$docebo_Current_Key][$doceboField] != $isContact[$IsField]){
									$output['updateDocebo'][$isContact[$syncfields['id_user']]][$doceboField] = $isContact[$IsField];
								}
							}
						}
					} elseif(isset($isContact[$syncfields['email']]) && $isContact[$syncfields['email']] != null && $isContact[$syncfields['email']] != ''){
						// has email use this to find docebo contact
						
					}
				}
			}
			// check docebo updates
			//$output['updateIs'] = $docebo_Updates['updateUsers'];
			if(count($docebo_Updates['updateUsers']) >= '1'){
				foreach($docebo_Updates['updateUsers'] as $doceboKey => $doceboValue){
					if($doceboValue['PTIN ( if none enter N/A)[1]'] != '')
					{
					   $upd_ptin = $doceboValue['PTIN ( if none enter N/A)[1]'];
					}
					foreach($syncfields as $doceboField => $IsField){
						if(isset($doceboValue[$doceboField])){
							$output['updateIs'][$doceboKey][$IsField] = $doceboValue[$doceboField];
							$output['updateIs'][$doceboKey]['_PTIN'] = $upd_ptin;
						}
					}
				}
			}
			if(count($docebo_Updates['newUsers']) >= '1'){
				$x = '0';
				
				
				foreach($docebo_Updates['newUsers'] as $doceboValue){
					
					if($doceboValue['PTIN ( if none enter N/A)[1]'] != '')
					{
					   $ptin = $doceboValue['PTIN ( if none enter N/A)[1]'];
					}
					
					$password = rand_string(8);
					foreach($syncfields as $doceboField => $IsField){
						//echo $doceboValue[$doceboField];
						if(isset($doceboValue[$doceboField])){
							$output['addIs'][$x][$IsField] = $doceboValue[$doceboField];
							$output['addIs'][$x]['_PTIN'] = $ptin;
							$output['addIs'][$x]['_Password0'] = $password;
							
						}
					}
					$x++;
				}
				
			}
			bf_debugOutput('bf_compareUserData', $output);
			return $output;
			
		}
	}

	// Update Docebo
	if(!function_exists('bf_updateDoceboUser')){
		function bf_updateDoceboUser($update){
			global $subdomain, $access_token;
			if(count($update) >= '1'){
				foreach($update as $key => $updateUser){
					$submiturl = "https://".$subdomain.".docebosaas.com/api/user/edit";
					$authorization = "Authorization: Bearer ".$access_token['accessToken'];
					$data = array('id_user' => $key);
					foreach($updateUser as $docField => $docValue){
						$data[$docField] = $docValue;
					}
					$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
					$httppost = wp_safe_remote_post($submiturl, $options);
					$result = json_decode($httppost['body'], true);
					bf_debugOutput('bf_UpdateDoceboUserData', $result);
				}
				return true;
			}
			return false;
		}
	}

	
	function rand_string( $length ) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		return substr(str_shuffle($chars),0,$length);
	}
    		
	/**** End Functions ****/

	/**** Start Cron ****/

	// Collect Docebo Cached User Data
	$cached_docebo_results = bf_getCachedDoceboData();

	// Collect Docebo Current User Data
	$current_docebo_results = bf_getCurrentDoceboData();

	// Compare old and new data to find updates and new users
	$docebo_Updates = bf_compareDoceboData($cached_docebo_results, $current_docebo_results);
	unset($cached_docebo_results);

	// Collect Infusionsoft Updated Data from Contacts
	$is_Updates = bf_getUpdatedIsContacts();

	// Check if changes in Infusionsoft exist in Docebo if not then update
	$to_Process = bf_compareUserData($is_Updates, $docebo_Updates, $current_docebo_results);
	
	if(isset($to_Process['updateDocebo']) && count($to_Process['updateDocebo']) >= '1'){
		foreach($to_Process['updateDocebo'] as $to_Process_contact){
			$foundKey = ltrim(bf_searchDoceboArray($current_docebo_results, 'id_user', $to_Process_contact['id_user']), 'x');
			foreach($to_Process_contact as $key => $value){
				$current_docebo_results[$foundKey][$key] = $value;
			}
		}
	}
	unset($is_Updates, $docebo_Updates);
    //print_r($to_Process['addIs']); 
	
	
	// Update cache data
	bf_updateCachedDoceboUserData($current_docebo_results);
	
	// Process data
	bf_updateDoceboUser($to_Process['updateDocebo']);
	bf_updateIsUser($to_Process['updateIs']);
	bf_addIsUserAndTag($to_Process['addIs']);

	// Collect Docebo Cached Course Data
	//$cached_docebo_Courses = bf_getCachedDoceboCourseData();

	// Collect Docebo Current Course Data
	//$current_docebo_Courses = bf_getCurrentDoceboCourseData();

	// Compare Docebo Course Data for new Courses
	//$course_Is_Updates = bf_compareDoceboCourseData($cached_docebo_Courses, $current_docebo_Courses);

	// Process data
	//bf_addIsProducts($course_Is_Updates);

	// Check for new purchases in Docebo
	//$purchase_Is_Updates = bf_getNewPurchaseData($current_docebo_Courses);

	// Process data
	//bf_addIsPurchases($purchase_Is_Updates);

	// Update cache data
	//bf_updateCachedDoceboUserData($current_docebo_results);
	//bf_updateCachedDoceboCourseData();
	
	$docebo = new Docebo_data();
	
	$config = json_decode(file_get_contents($config_log_file_name),true);
	
	$flag_data_value = $config['flag'] + 1;
	$docebo->update_LogFile("flag", $flag_data_value,$config_log_file_name);
	
	
	if($last_updated_contact > $total_users)
	{
		$total_data_value = $total_users;
	    $docebo->update_LogFile("total", $total_data_value,$config_log_file_name);
	}
	else if($total_users == $total_data)
	{
		$flag_data_value = 0;
		$docebo->update_LogFile("flag", $flag_data_value,$config_log_file_name);
					
		$lastup_data_value = 0;
		$docebo->update_LogFile("last_updated", $lastup_data_value,$config_log_file_name);
					
		$st_data_value = 0;
		$docebo->update_LogFile("start", $st_data_value,$config_log_file_name);
	}
    echo "Cron Run Successfully";
	/**** End Cron ****/
	
}
?>