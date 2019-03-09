<?php
if(isset($cronrun) && $cronrun == true ){
	
	include('config.php');
	echo "Course Cron";
	$config = json_decode(file_get_contents($course_config_log_file_name),true);
	
	$total_data = $config['total'] ;
	$last_updated_contact = $config['last_updated'];
	
    $access_token = bf_docebolabs_fetch_token();
	$data = array();
	$authorization = "Authorization: Bearer ".$access_token['accessToken']; 
	$curlSession = curl_init("https://".$subdomain.".docebosaas.com/api/course/courses"); 
			  
	curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
	curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curlSession, CURLOPT_TIMEOUT, 300);
	curl_setopt($curlSession, CURLOPT_POST, 1);
	curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($curlSession, CURLOPT_FOLLOWLOCATION, 1);
	$response = curl_exec($curlSession);
	
	curl_close($curlSession); 			
	$result = json_decode($response);
	
	echo $total_courses = $result->item_count; 
	if($total_courses == $total_data)
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
				$fp = fopen(dirname(__file__).'/debug_bf_docebolabs_course_cron.txt', 'a');
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


	// Collect cached Docebo Course data from db
	if(!function_exists('bf_getCachedDoceboCourseData')){
		function bf_getCachedDoceboCourseData(){
			global $wpdb, $docebocoursedb;
			//$docebocoursedb = $wpdb->prefix."_bf_docebolabs_courses";
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "SELECT * FROM '$docebocoursedb'";
			$pre_cached_results = $wpdb->get_results($sql);
			$cached_results = json_decode(json_encode($pre_cached_results), true);
			unset($pre_cached_results);
			//bf_debugOutput('bf_getCachedDoceboCourseData', $cached_results);
			return $cached_results;
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
			//bf_debugOutput('bf_getCurrentDoceboCourseData', $result['courses']);
			return $result['courses'];
		}
	}

	// Compare Docebo course cached vs current data
	if(!function_exists('bf_compareDoceboCourseData')){
		function bf_compareDoceboCourseData($cached, $current){
			
			$output = array();
			if(count($current) >= '1'){
				$x = '0';
				foreach($current as $currentCourse){
					if(bf_searchDoceboArray($cached, 'id', $currentCourse['course_id']) == false){
						$output[$x]['Sku'] = $currentCourse['course_id'].'-'.$currentCourse['code'];
						$output[$x]['ProductName'] = $currentCourse['course_name'].' - '.$currentCourse['code'];
						$output[$x]['ProductPrice'] = ($currentCourse['price'] == null || $currentCourse['price'] == '' ? '0.00' : $currentCourse['price']);
						$output[$x]['ShortDescription'] = $currentCourse['course_name'].' - '.$currentCourse['course_type'];
						$output[$x]['Description'] = $currentCourse['course_description'];
					}
					$x++;
				}
			}
			//bf_debugOutput('bf_compareDoceboCourseData', $output);
			return $output;
		}
	}

	// Update Infusionsoft Products
	if(!function_exists('bf_addIsProducts')){
		function bf_addIsProducts($products){
			
			include('config.php');
			include('docebo_data.php');
			
			$docebo = new Docebo_data();
					
			$config = json_decode(file_get_contents($course_config_log_file_name),true);
				 
			$start = $config['start'];
			$limit = $config['limit']+ $start;
			$last_updated_contact = $config['last_updated'];
			$total = $config['total'];
			$flag_var = $config['flag'];
			
			if(count($products) >= '1'){
				global $sitedomain, $syncfields, $tag_selected;
				
				for($i=$start; $i<$limit; $i++)
				{
					
				   $product = $products[$i];
				   //echo "<pre>"; print_r($product);
				   $submiturl = plugin_dir_url(__file__)."docebolabs/bf_docebolabs_add_product.php";
				  
					$data = array(
						'email' => esc_attr(get_option('bf_docebolabs_regemail')),
						'domain' => urlencode($sitedomain),
						'isappid' => esc_attr(get_option('bf_docebolabs_is_app_name')),
						'isapikey' => esc_attr(get_option('bf_docebolabs_is_api_key')),
						'plugin' => 'DoceboLabs',
						'addproduct' => $product,
						'bf_docebo_taxable' => get_option('bf_docebo_taxable'),
						'bf_docebo_CountryTaxable' => get_option('bf_docebo_CountryTaxable'),
						'bf_docebo_StateTaxable' => get_option('bf_docebo_StateTaxable'),
						'bf_docebo_CityTaxable' => get_option('bf_docebo_CityTaxable'),
						'tag' => $tag_selected
					);
					//bf_debugOutput('bf_addIsProducts', "Product");
					bf_debugOutput('bf_addIsProducts', $data);
					$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1');
					$httppost = wp_safe_remote_post($submiturl, $options);
					sleep(1);
					
					$config = json_decode(file_get_contents($course_config_log_file_name),true);
					
					$st_data_value = $config['start'] + 1;
	                $docebo->update_LogFile("start", $st_data_value, $course_config_log_file_name);
					
					$last_data_value = $config['last_updated'] + 1;
	                $docebo->update_LogFile("last_updated", $last_data_value, $course_config_log_file_name);
					
				}
			}
		}
	}

	// Update Docebo Cache Course data
	if(!function_exists('bf_updateCachedDoceboCourseData')){
		function bf_updateCachedDoceboCourseData(){
			global $wpdb, $docebocoursedb, $access_token, $subdomain;
			//$docebocoursedb = $wpdb->prefix."_bf_docebolabs_courses";
			$charset_collate = $wpdb->get_charset_collate();
			$delete = $wpdb->query("TRUNCATE TABLE '$docebocoursedb'");
			$submiturl = "https://".$subdomain.".docebosaas.com/api/course/courses";
			$authorization = "Authorization: Bearer ".$access_token['accessToken'];
			$data = array();
			$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
			$httppost = wp_safe_remote_post($submiturl, $options);
			$result = json_decode($httppost['body'], true);
			if(count($result['courses']) >= '1'){
				foreach($result['courses'] as $course){
					$wpdb->insert($docebocoursedb, array(
						'id' => $course['course_id'],
						'code' => $course['code'],
						'course_name' => $course['course_name']
					));
				}
			}
		}
	}


	/**** End Functions ****/

	/**** Start Cron ****/


	// Collect Docebo Cached Course Data
	$cached_docebo_Courses = bf_getCachedDoceboCourseData();
	

	// Collect Docebo Current Course Data
	$current_docebo_Courses = bf_getCurrentDoceboCourseData();

	// Compare Docebo Course Data for new Courses
	$course_Is_Updates = bf_compareDoceboCourseData($cached_docebo_Courses, $current_docebo_Courses);

	// Process data
	bf_addIsProducts($course_Is_Updates);

	// Check for new purchases in Docebo
	/*$purchase_Is_Updates = bf_getNewPurchaseData($current_docebo_Courses);

	// Process data
	bf_addIsPurchases($purchase_Is_Updates);*/

	// Update cache data
	bf_updateCachedDoceboCourseData();
	
	$docebo =new Docebo_data();
	
	$config = json_decode(file_get_contents($course_config_log_file_name),true);
	
	
	$flag_data_value = $config['flag'] + 1;
	$docebo->update_LogFile("flag", $flag_data_value,$course_config_log_file_name);
	
	$total_data_value = $total_course;
	if($last_updated_contact > $total_courses)
	{
		$docebo->update_LogFile("total", $total_data_value,$course_config_log_file_name);
	}
	
    echo "Cron Run Successfully";
	/**** End Cron ****/
}
?>