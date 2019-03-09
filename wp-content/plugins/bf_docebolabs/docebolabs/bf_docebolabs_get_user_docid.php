<?php
$debug = false;
if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'w');
	fwrite($fp, date('d-m-Y H:i:s')." - ".print_r($_REQUEST, true)."\r\n");
	fclose($fp);
}
if(isset($_REQUEST['id_user']) && $_REQUEST['id_user'] != '' && $_REQUEST['id_user'] != null && isset($_REQUEST['id_field']) && $_REQUEST['id_field'] != '' && $_REQUEST['id_field'] != null && isset($_REQUEST['syncfields']) && $_REQUEST['syncfields'] != '' && $_REQUEST['syncfields'] != null){
	global $connInfo;
	$connInfo = array('isconn:'.$_REQUEST['isappid'].':i:'.$_REQUEST['isapikey'].':This is the connection for '.$_REQUEST['isappid'].'.infusionsoft.com');
	require("../aisdk.php");
	$app = new iSDK;
	if($app->cfgCon("isconn"))
	{
		$query = array($_REQUEST['id_field'] => $_REQUEST['id_user']);
		$contacts = $app->dsQuery("Contact",1,0,$query,$_REQUEST['syncfields']);
		echo json_encode($contacts);

if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
	fwrite($fp, "is contacts: ".date('d-m-Y H:i:s')." - ".print_r($contacts, true)."\r\n");
	fclose($fp);
}
	}
}
?>