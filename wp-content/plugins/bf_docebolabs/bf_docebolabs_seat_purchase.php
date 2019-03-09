<?php
if(isset($cronrun) && $cronrun == true){
	
	global $connInfo, $wpdb, $doceboproductcoursedb;
	$doceboproductcoursedb = $wpdb->prefix . "_bf_docebolabs_productcourse";
	$charset_collate = $wpdb->get_charset_collate();

	$access_token = bf_docebolabs_fetch_token();
	$subdomain = get_option('bf_docebolabs_docebo_subdomain');

	//$lastcronrun = get_option('bf_docebolabs_cron_last_run');		
	$lastcronrun = get_option('bf_docebolabs_purchase_cron_last_run');
	$connInfo = array('isconn:'.esc_attr( get_option('bf_docebolabs_is_app_name') ).':i:'.esc_attr( get_option('bf_docebolabs_is_api_key') ).':This is the connection for '.esc_attr( get_option('bf_docebolabs_is_app_name') ).'.infusionsoft.com');
	require_once(WP_PLUGIN_DIR."/bf_docebolabs/aisdk.php");
	$app = new iSDK;
	
	if($app->cfgCon("isconn")){
		$sql = "SELECT * FROM $doceboproductcoursedb";
		$productcourse_results = $wpdb->get_results($sql);
		//echo "<pre>"; print_r($productcourse_results);
		$productcourses = array();
		foreach($productcourse_results as $productcourse_result){
			$productcourses[$productcourse_result->product_id][] = $productcourse_result->course_id;
		}			
		
		unset($productcourse_results);
		
		// lookup orders since lastcron LastUpdated
		echo $previoustz = date_default_timezone_get();
        echo "<br/>";
		$date = new DateTime(date('Y-m-d H:i:s', $lastcronrun), new DateTimeZone($previoustz));
		print_r($date);
		$date->setTimezone(new DateTimeZone('America/New_York'));      
		//$date->setTimezone(new DateTimeZone('Asia/Calcutta'));
        echo "<br/>";
		echo $date->format('Y-m-d H:i:s');
		$returnFields = array('Id','JobId','PayStatus','ContactId');		
		$query = array('LastUpdated' => '~<=~ '.$date->format('Y-m-d H:i:s'));
		$invoices = $app->dsQuery("Invoice",1000,0,$query,$returnFields);	
		//echo "<pre>"; print_r($invoices); exit;
		foreach($invoices as $invoice){		
			$doceboid = false;			
			//echo "<pre>"; print_r($invoice);
			if($invoice['PayStatus'] == 1){		
				
				$returnFields = array('ProductId','Qty');
				$query = array('OrderId' => $invoice['Id']);
				$items = $app->dsQuery("OrderItem",1000,0,$query,$returnFields);			
				//echo "<pre>"; print_r($items);  exit;
				if(count($items) >= '1'){					
					foreach($items as $item){		
						// check if product id is in association table						
						if(isset($productcourses[$item['ProductId']]) && count($productcourses[$item['ProductId']]) >= '1'){							
							if(!isset($doceboid) || $doceboid == false){	
						
								// get contact email
								$returnFields = array('Email');
								$query = array('Id' => $invoice['ContactId']);
								$contacts = $app->dsQuery("Contact",1,0,$query,$returnFields);																//echo "<pre>"; print_r($contacts); 
								if(isset($contacts['0']['Email']) && $contacts['0']['Email'] != null && $contacts['0']['Email'] != ''){
									// get doceboid
									$data = array(
										'userid' => $contacts['0']['Email'],
										'also_check_as_email' => true
									);
									$submiturl = "https://".$subdomain.".docebosaas.com/api/user/checkUsername";
									$authorization = "Authorization: Bearer ".$access_token['accessToken'];
									$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
									$httppost = wp_safe_remote_post($submiturl, $options);
									$result = json_decode($httppost['body'], true);
									if(isset($result['success']) && isset($result['idst']) && $result['idst'] != null && $result['idst'] != ''){
										 $doceboid = $result['idst'];
										
									}
								}
							}
							if(isset($doceboid) && $doceboid != null && $doceboid != ''){
								foreach($productcourses[$item['ProductId']] as $courseid){
									echo '<p>course id:'.$courseid.', '.$item['Qty'].' seats for '.$doceboid.'</p>';
									// add user to course
									$x = '1';
									while($x <= $item['Qty']){
										$submiturl = "https://".$subdomain.".docebosaas.com/api/course/addUserSubscription";
										//$submiturl1 = "https://".$subdomain.".docebosaas.com/api/poweruser/assignCourses";
										$authorization = "Authorization: Bearer ".$access_token['accessToken'];
										 $data = array(
											'id_user' => $doceboid,
											'course_id' => $courseid,
											'user_level' => 'Learner'
										); 
										//print_r($data);
										//$data = array('id_user' => $poweruserid, 'items' => array('id_course' => $courseId));
										$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
										$httppost = wp_safe_remote_post($submiturl, $options);	
										$result = json_decode($httppost['body'], true);
										print_r($result);
										$fp = fopen(dirname(__file__).'/debug_bf_docebolabs_purchase_cron.txt', 'a');
										fwrite($fp, "AddUser Subscription Date - ".date('Y-m-d H:i:s')."\r\n");										
										fwrite($fp, "AddUser Subscription - ".print_r($data , true)."\r\n");
										fclose($fp);
										
										$x++;
									}
								}
							}
						}
					}
				}
			}
		}
		
	}
	
}
?>