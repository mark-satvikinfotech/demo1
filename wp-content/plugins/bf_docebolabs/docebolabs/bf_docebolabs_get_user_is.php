<?php
$debug = false;
if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'w');
	fwrite($fp, date('d-m-Y H:i:s')." - ".print_r($_REQUEST, true)."\r\n");
	fclose($fp);
}
if(isset($_REQUEST['contactId']) && $_REQUEST['contactId'] != '' && $_REQUEST['contactId'] != null){
	global $connInfo;
	$connInfo = array('isconn:'.$_REQUEST['isappid'].':i:'.$_REQUEST['isapikey'].':This is the connection for '.$_REQUEST['isappid'].'.infusionsoft.com');
	require("../aisdk.php");
	$app = new iSDK;
	if($app->cfgCon("isconn"))
	{
		$returnFields = array('FirstName', 'LastName', 'Email', 'Username');
		$query = array('Id' => $_REQUEST['contactId']);
		$contacts = $app->dsQuery("Contact",1,0,$query,$returnFields);
		echo json_encode($contacts);

if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
	fwrite($fp, "is contacts: ".date('d-m-Y H:i:s')." - ".print_r($contacts, true)."\r\n");
	fclose($fp);
}
	}
}
?>