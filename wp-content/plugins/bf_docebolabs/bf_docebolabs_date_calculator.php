<?php
$debug = false;
if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'w');
	fwrite($fp, "From Infusionsoft: ".date('d-m-Y H:i:s')." - ".print_r($_REQUEST, true)."\r\n");
	fclose($fp);
}
if(isset($_REQUEST['contactId']) && $_REQUEST['contactId'] != '' && $_REQUEST['contactId'] != null && isset($_REQUEST['DateInputValue']) && $_REQUEST['DateInputValue'] != '' && $_REQUEST['DateInputValue'] != null && isset($_REQUEST['DateOutputField']) && $_REQUEST['DateOutputField'] != '' && $_REQUEST['DateOutputField'] != null && isset($_REQUEST['Adjustment']) && $_REQUEST['Adjustment'] != '' && $_REQUEST['Adjustment'] != null){
	require('../../../wp-load.php');

	$submiturl = plugin_dir_url(__file__)."docebolabs/".basename(__FILE__);
	$data = array(
		'email' => esc_attr(get_option('bf_docebolabs_regemail')),
		'domain' => urlencode($sitedomain),
		'isappid' => esc_attr(get_option('bf_docebolabs_is_app_name')),
		'isapikey' => esc_attr(get_option('bf_docebolabs_is_api_key')),
		'plugin' => 'DoceboLabs',
		'contactId' => $_REQUEST['contactId'],
		'DateInputValue' => $_REQUEST['DateInputValue'],
		'DateOutputField' => $_REQUEST['DateOutputField'],
		'Adjustment' => $_REQUEST['Adjustment']
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
?>