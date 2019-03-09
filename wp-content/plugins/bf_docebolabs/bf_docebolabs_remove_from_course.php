<?php
$debug = false;
if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
	fwrite($fp, "From Infusionsoft: ".date('d-m-Y H:i:s')." - ".print_r($_REQUEST, true)."\r\n");
	fclose($fp);
}
if(isset($_REQUEST['contactId']) && $_REQUEST['contactId'] != '' && $_REQUEST['contactId'] != null && isset($_REQUEST['DoceboIdEmail']) && $_REQUEST['DoceboIdEmail'] != '' && $_REQUEST['DoceboIdEmail'] != null && ((isset($_REQUEST['CourseCode']) && $_REQUEST['CourseCode'] != '' && $_REQUEST['CourseCode'] != null) || (isset($_REQUEST['CourseId']) && $_REQUEST['CourseId'] != '' && $_REQUEST['CourseId'] != null))){
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
	// if doesnt exist create new as per new user
	if($result['success'] != '1'){
		// user should exist
		die;
	}

	$userid = $result['idst'];

	// if course code then get course id
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

	// add user to course
	$submiturl = "https://".$subdomain.".docebosaas.com/api/course/deleteUserSubscription";
	$authorization = "Authorization: Bearer ".$access_token['accessToken'];
	$data = array(
		'id_user' => $userid,
		'course_id' => $courseId
	);
	$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
	$httppost = wp_safe_remote_post($submiturl, $options);
}
?>