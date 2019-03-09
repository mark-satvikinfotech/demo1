<?php
$debug = false;
if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'w');
	fwrite($fp, date('d-m-Y H:i:s')." - ".print_r($_REQUEST, true)."\r\n");
	fclose($fp);
}
if(isset($_REQUEST['lastupdated']) && $_REQUEST['lastupdated'] != '' && $_REQUEST['lastupdated'] != null && isset($_REQUEST['syncfields']) && $_REQUEST['syncfields'] != '' && $_REQUEST['syncfields'] != null){
	global $connInfo;
	$connInfo = array('isconn:'.$_REQUEST['isappid'].':i:'.$_REQUEST['isapikey'].':This is the connection for '.$_REQUEST['isappid'].'.infusionsoft.com');
	require("../aisdk.php");
	$app = new iSDK;
	if($app->cfgCon("isconn"))
	{
		$query = array('LastUpdated' => '~>~'.$_REQUEST['lastupdated']);
		$page = '0';
		$allcontacts = array();
		$run = true;
		while($run == true){
			$contacts = $app->dsQuery("Contact",1000,$page,$query,$_REQUEST['syncfields']);
			if(count($contacts) <= '999'){
				$run = false;
			}
			$allcontacts = array_merge($allcontacts, $contacts);
			$page++;
		}
if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
	fwrite($fp, "is contacts: ".date('d-m-Y H:i:s')." - ".print_r($allcontacts, true)."\r\n");
	fclose($fp);
}
		echo json_encode($allcontacts);
	}
}
?>