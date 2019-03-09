<?php
$debug = false;
if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
	fwrite($fp, "From Infusionsoft: ".date('d-m-Y H:i:s')." - ".print_r($_REQUEST, true)."\r\n");
	fclose($fp);
}
if(isset($_REQUEST['DoceboIdEmail']) && $_REQUEST['DoceboIdEmail'] != '' && $_REQUEST['DoceboIdEmail'] != null && isset($_REQUEST['BranchID']) && $_REQUEST['BranchID'] != '' && $_REQUEST['BranchID'] != null){
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

	$userid = $result['idst'];

	// create branch
	$submiturl = "https://".$subdomain.".docebosaas.com/api/orgchart/assignUsersToNode";
	$data = array('id_org' => $_REQUEST['BranchID'], 'user_ids' => $userid);

	$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
	$httppost = wp_safe_remote_post($submiturl, $options);

	$result = json_decode($httppost['body'], true);
	if($debug == true){
		$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
		fwrite($fp, "docebo api attempted assigning profile name ".$_REQUEST['ProfileName']." to user id ".$userid.": ".date('d-m-Y H:i:s')." - ".print_r($result, true)."\r\n");
		fclose($fp);
	}
}
?>