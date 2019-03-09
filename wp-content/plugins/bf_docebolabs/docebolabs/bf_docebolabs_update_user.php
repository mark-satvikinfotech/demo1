<?php
$debug = false;
if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'w');
	fwrite($fp, date('d-m-Y H:i:s')." - ".print_r($_REQUEST, true)."\r\n");
	fclose($fp);
}
if(isset($_REQUEST['contactId']) && $_REQUEST['contactId'] != null && $_REQUEST['contactId'] != ''){
	global $connInfo;
	$connInfo = array('isconn:'.$_REQUEST['isappid'].':i:'.$_REQUEST['isapikey'].':This is the connection for '.$_REQUEST['isappid'].'.infusionsoft.com');
	require("../aisdk.php");
	$app = new iSDK;
	if($app->cfgCon("isconn")){
		$conDat = array();
		if(is_array($_REQUEST['fields']) && count($_REQUEST['fields']) >= '1' && is_array($_REQUEST['values']) && count($_REQUEST['values']) >= '1'){
			$keyNum = '0';
			foreach($_REQUEST['fields'] as $field){
				$conDat[$field] = $_REQUEST['values']['0'];
				$keyNum++;
			}
			$contactId = $app->updateCon($_REQUEST['contactId'], $conDat);
		}
	}
}
?>