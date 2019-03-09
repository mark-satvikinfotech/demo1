<?php
$debug = false;
if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
	fwrite($fp, "From Infusionsoft: ".date('d-m-Y H:i:s')." - ".print_r($_REQUEST, true)."\r\n");
	fclose($fp);
}
if(isset($_REQUEST['contactId']) && $_REQUEST['contactId'] != '' && $_REQUEST['contactId'] != null && isset($_REQUEST['DoceboId']) && $_REQUEST['DoceboId'] != '' && $_REQUEST['DoceboId'] != null){
	require('../../../wp-load.php');
	global $sitedomain;
	$access_token = bf_docebolabs_fetch_token();
	$subdomain = get_option('bf_docebolabs_docebo_subdomain');
	$submiturl = plugin_dir_url(__file__)."docebolabs/bf_docebolabs_get_user_is.php";

	if(!isset($_REQUEST['DoceboId'])){
		// look up email from infusionsoft
		$data = array(
			'email' => esc_attr(get_option('bf_docebolabs_regemail')),
			'domain' => urlencode($sitedomain),
			'isappid' => esc_attr(get_option('bf_docebolabs_is_app_name')),
			'isapikey' => esc_attr(get_option('bf_docebolabs_is_api_key')),
			'plugin' => 'DoceboLabs',
			'contactId' => $_REQUEST['contactId']
		);

		if($debug == true){
			$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
			fwrite($fp, "data to infusionsoft api contact search: ".date('d-m-Y H:i:s')." - ".print_r($data, true)."\r\n");
			fclose($fp);
		}

		$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1');
		$httppost = wp_safe_remote_post($submiturl, $options);
		if(isset($_REQUEST['debug']) && $_REQUEST['debug'] == 'true'){
			echo '<p>body:</p>';
			echo '<pre>';
			print_r($httppost['body']);
			echo '</pre>';
			echo '<p>complete:</p>';
			echo '<pre>';
			print_r($httppost);
			echo '</pre>';
		}
		$contacts = json_decode($httppost['body'], true);

		// query docebo for user id
		$submiturl = "https://".$subdomain.".docebosaas.com/api/user/checkUsername";
		$authorization = "Authorization: Bearer ".$access_token['accessToken'];
		$data = array(
			'userid' => $contacts['0']['Email'],
			'also_check_as_email' => true
		);
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
	}
	if(isset($result['success']) && $result['success'] == '1'){
		$userid = $result['idst'];
	} else {
		$userid = $_REQUEST['DoceboId'];
	}
	if($debug == true){
		$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
		fwrite($fp, "userid result: ".date('d-m-Y H:i:s')." - ".print_r($userid, true)."\r\n");
		fclose($fp);
	}

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

	if(isset($_REQUEST['UserLevel']) && $_REQUEST['UserLevel'] != null && $_REQUEST['UserLevel'] != ''){
		$userLevel = $_REQUEST['UserLevel'];
	} else {
		$userLevel = 'student';
	}

	// suspended user from course 
	$submiturl = "https://".$subdomain.".docebosaas.com/api/course/updateUserSubscription";
	$authorization = "Authorization: Bearer ".$access_token['accessToken'];
	$data = array(
		'id_user' => $userid,
		'course_id' => $courseId,
		'user_level' => $userLevel
	);
	$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
	$httppost = wp_safe_remote_post($submiturl, $options);
	if($debug == true){
		$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
		fwrite($fp, "updateUserSubscription result: ".date('d-m-Y H:i:s')." - ".print_r($httppost, true)."\r\n");
		fclose($fp);
	}
}
?>