<?php
$debug = false;
if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'w');
	fwrite($fp, "From Infusionsoft: ".date('d-m-Y H:i:s')." - ".print_r($_REQUEST, true)."\r\n");
	fclose($fp);
}
if(isset($_REQUEST['contactId']) && $_REQUEST['contactId'] != '' && $_REQUEST['contactId'] != null && isset($_REQUEST['UserName']) && $_REQUEST['UserName'] != '' && $_REQUEST['UserName'] != null && isset($_REQUEST['Password']) && $_REQUEST['Password'] != '' && $_REQUEST['Password'] != null && isset($_REQUEST['DoceboId']) && $_REQUEST['DoceboId'] != '' && $_REQUEST['DoceboId'] != null){
	require('../../../wp-load.php');

	function randomPassword(){
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$numerics = '1234567890';
		$pass = array();
		$alphaLength = strlen($alphabet) - 1;
		for ($i = 0; $i < 4; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		$alphaLength = strlen($numerics) - 1;
		for ($i = 0; $i < 4; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $numerics[$n];
		}
		return implode($pass);
	}

	$access_token = bf_docebolabs_fetch_token();
	$subdomain = get_option('bf_docebolabs_docebo_subdomain');
	$contactId = $_REQUEST['contactId'];
	$passwordField = $_REQUEST['Password'];
	$password = randomPassword();
	$doceboIdField = $_REQUEST['DoceboId'];

	// get user details from infusionsoft
	global $sitedomain;
	$submiturl = plugin_dir_url(__file__)."docebolabs/bf_docebolabs_get_user_is.php";
	$data = array(
		'email' => esc_attr(get_option('bf_docebolabs_regemail')),
		'domain' => urlencode($sitedomain),
		'isappid' => esc_attr(get_option('bf_docebolabs_is_app_name')),
		'isapikey' => esc_attr(get_option('bf_docebolabs_is_api_key')),
		'plugin' => 'DoceboLabs',
		'contactId' => $contactId
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
	$firstname = $contacts['0']['FirstName'];
	$lastname = $contacts['0']['LastName'];
	if(!isset($_REQUEST['UserName'])){
		$userid = $contacts['0']['Username'];
	} else {
		$userid = $_REQUEST['UserName'];
	}
	if(!isset($_REQUEST['Email'])){
		$email = $contacts['0']['Email'];
	} else {
		$email = $_REQUEST['Email'];
	}

	$submiturl = "https://".$subdomain.".docebosaas.com/api/user/create";
	$authorization = "Authorization: Bearer ".$access_token['accessToken'];
	$data = array(
		'userid' => $userid,
		'firstname' => $firstname,
		'lastname' => $lastname,
		'password' => $password,
		'email' => $email
	);


if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
	fwrite($fp, "data to docebo api: ".date('d-m-Y H:i:s')." - ".print_r($data, true)."\r\n");
	fclose($fp);
}

	$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
	$httppost = wp_safe_remote_post($submiturl, $options);
	$result = json_decode($httppost['body'], true);

	echo json_encode($result);

if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
	fwrite($fp, "result from docebo api: ".date('d-m-Y H:i:s')." - ".print_r($result, true)."\r\n");
	fclose($fp);
}

	if(isset($result) && $result['success'] == '1' && isset($result['idst'])){
		$submiturl = plugin_dir_url(__file__)."docebolabs/".basename(__FILE__);
		$data = array(
			'email' => esc_attr(get_option('bf_docebolabs_regemail')),
			'domain' => urlencode($sitedomain),
			'isappid' => esc_attr(get_option('bf_docebolabs_is_app_name')),
			'isapikey' => esc_attr(get_option('bf_docebolabs_is_api_key')),
			'plugin' => 'DoceboLabs',
			'contactId' => $contactId,
			'field' => $passwordField,
			'password' => $password,
			'doceboid' => $result['idst'],
			'doceboidfield' => $doceboIdField
		);

if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
	fwrite($fp, "data to infusionsoft api: ".date('d-m-Y H:i:s')." - ".print_r($data, true)."\r\n");
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
	}
}
?>