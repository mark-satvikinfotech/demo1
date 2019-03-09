<?php
$debug = false;
if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'w');
	fwrite($fp, date('d-m-Y H:i:s')." - ".print_r($_REQUEST, true)."\r\n");
	fclose($fp);
}
if(isset($_REQUEST['contactId']) && $_REQUEST['contactId'] != '' && $_REQUEST['contactId'] != null && isset($_REQUEST['DateInputValue']) && $_REQUEST['DateInputValue'] != '' && $_REQUEST['DateInputValue'] != null && isset($_REQUEST['DateOutputField']) && $_REQUEST['DateOutputField'] != '' && $_REQUEST['DateOutputField'] != null && isset($_REQUEST['Adjustment']) && $_REQUEST['Adjustment'] != '' && $_REQUEST['Adjustment'] != null){
	global $connInfo;
	$connInfo = array('isconn:'.$_REQUEST['isappid'].':i:'.$_REQUEST['isapikey'].':This is the connection for '.$_REQUEST['isappid'].'.infusionsoft.com');
	require("../aisdk.php");
	$app = new iSDK;
	if($app->cfgCon("isconn"))
	{
		// work out adjustment
		$outputDate = date('Y-m-d', strtotime($_REQUEST['DateInputValue']." ".$_REQUEST['Adjustment']));
		$outputDate = $app->infuDate($outputDate);

		// update contact
		$conDat = array($_REQUEST['DateOutputField'] => $outputDate);
		$contactId = $app->updateCon($_REQUEST['contactId'], $conDat);

if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
	fwrite($fp, "is contacts: ".date('d-m-Y H:i:s')." - ".print_r($contacts, true)."\r\n");
	fclose($fp);
}
	}
}
?>