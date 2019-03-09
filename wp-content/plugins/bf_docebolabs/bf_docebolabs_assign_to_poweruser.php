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

	$poweruserid = $result['idst'];


	// run instances of add to power user
	$submiturl = "https://".$subdomain.".docebosaas.com/api/poweruser/assignUser";

	// user
	if(isset($_REQUEST['UserToAdd']) && $_REQUEST['UserToAdd'] != null && $_REQUEST['UserToAdd'] != ''){
		$data = array('id_user' => $poweruserid, 'item_type' => 'id_user', 'item_value' => $_REQUEST['UserToAdd']);
		$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
		$httppost = wp_safe_remote_post($submiturl, $options);
		$result = json_decode($httppost['body'], true);
		if($debug == true){
			$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
			fwrite($fp, "docebo api attempted assigning user id ".$_REQUEST['UserToAdd']." to power user id ".$poweruserid.": ".date('d-m-Y H:i:s')." - ".print_r($result, true)."\r\n");
			fclose($fp);
		}
	}

	// branch
	if(isset($_REQUEST['BranchToAdd']) && $_REQUEST['BranchToAdd'] != null && $_REQUEST['BranchToAdd'] != ''){
		$data = array('id_user' => $poweruserid, 'item_type' => 'branch_name', 'item_value' => $_REQUEST['BranchToAdd']);
		$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
		$httppost = wp_safe_remote_post($submiturl, $options);
		$result = json_decode($httppost['body'], true);
		if($debug == true){
			$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
			fwrite($fp, "docebo api attempted assigning branch name ".$_REQUEST['BranchToAdd']." to power user id ".$poweruserid.": ".date('d-m-Y H:i:s')." - ".print_r($result, true)."\r\n");
			fclose($fp);
		}
	}

	// group
	if(isset($_REQUEST['GroupToAdd']) && $_REQUEST['GroupToAdd'] != null && $_REQUEST['GroupToAdd'] != ''){
		$data = array('id_user' => $poweruserid, 'item_type' => 'group_name', 'item_value' => $_REQUEST['GroupToAdd']);
		$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
		$httppost = wp_safe_remote_post($submiturl, $options);
		$result = json_decode($httppost['body'], true);
		if($debug == true){
			$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
			fwrite($fp, "docebo api attempted assigning group name ".$_REQUEST['GroupToAdd']." to power user id ".$poweruserid.": ".date('d-m-Y H:i:s')." - ".print_r($result, true)."\r\n");
			fclose($fp);
		}
	}
}
?>