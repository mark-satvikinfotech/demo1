<?php
$debug = true;
if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
	fwrite($fp, "From Infusionsoft: ".date('d-m-Y H:i:s')." - ".print_r($_REQUEST, true)."\r\n");
	fclose($fp);
}
if(isset($_REQUEST['DoceboIdEmail']) && $_REQUEST['DoceboIdEmail'] != '' && $_REQUEST['DoceboIdEmail'] != null){
	require('../../../wp-load.php');

	$access_token = bf_docebolabs_fetch_token();
	$subdomain = get_option('bf_docebolabs_docebo_subdomain');

	if(strpos($_REQUEST['DoceboIdEmail'], '@') !== false){
		$data = array(
			'userid' => $_REQUEST['DoceboIdEmail'],
			'also_check_as_email' => true
		);
	} else {
		$doceboId = $_REQUEST['DoceboIdEmail'];
		$data = array(
			'userid' => $_REQUEST['DoceboIdEmail'],
			'also_check_as_email' => false
		);
	}

	// check docebo for user id
	$submiturl = "https://".$subdomain.".docebosaas.com/api/user/checkUsername";
	$authorization = "Authorization: Bearer ".$access_token['accessToken'];

	if($debug == true){
		$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
		fwrite($fp, "data to docebo api check email: ".date('d-m-Y H:i:s')." - ".print_r($data, true)."\r\n");
		fclose($fp);
	}

	$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
	$httppost = wp_safe_remote_post($submiturl, $options);

	$result = json_decode($httppost['body'], true);
	if($debug == true){
		$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
		fwrite($fp, "docebo api check email result: ".date('d-m-Y H:i:s')." - ".print_r($result, true)."\r\n");
		fclose($fp);
	}

	if($result['success'] != '1'){
		// user should exist
		die;
	}

	$poweruserid = $result['idst'];

	//add user as power user using API
	if(isset($poweruserid) && $poweruserid != '')
	{
	    $submiturl = "https://".$subdomain.".docebosaas.com/api/poweruser/add";
		//$submiturl = "https://".$subdomain.".docebosaas.com/api/poweruser/assignUser";
		$authorization = "Authorization: Bearer ".$access_token['accessToken'];
		$data =  array('id_user' => $poweruserid);
		//$data = array('id_user' => $poweruserid, 'item_type' => 'id_user', 'item_value' => $poweruserid);
		$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
		$httppost = wp_safe_remote_post($submiturl, $options);
		$result = json_decode($httppost['body'], true);
    }
	
	// run instances of add to power user
	 //$submiturl1 = "https://".$subdomain.".docebosaas.com/api/poweruser/assignCourses";
     $submiturl1 = "https://".$subdomain.".docebosaas.com/api/course/addUserSubscription";
	// course
	if(isset($_REQUEST['CourseCode'])){
		$submiturl = "https://".$subdomain.".docebosaas.com/api/course/courses";
		$authorization = "Authorization: Bearer ".$access_token['accessToken'];
		$data = array();
		$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
		$httppost = wp_safe_remote_post($submiturl, $options);
		$result = json_decode($httppost['body'], true);
		foreach($result['courses'] as $course){
			if(isset($course['code']) && $course['code'] == $_REQUEST['CourseCode']){
				$courseId = $course['course_id'];
				break;
			}
		}
	} else {
		$courseId = $_REQUEST['CourseId'];
	}
	if(isset($courseId) && $courseId != null && $courseId != ''){
		$data = array('id_user' => $poweruserid, 'items' => array('id_course' => $courseId));
		//$data = array('id_user' => $poweruserid, 'course_id' => $courseId);
		$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
		$httppost = wp_safe_remote_post($submiturl1, $options);
		$result1 = json_decode($httppost['body'], true);				
		if($debug == true){
			$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');		
			fwrite($fp, "Assign course result:".print_r($result1,true));
			fwrite($fp, "docebo api attempted assigning course id ".$courseId." to power user id ".$poweruserid."\r\n");
			fclose($fp);
		}
	}
}
?>