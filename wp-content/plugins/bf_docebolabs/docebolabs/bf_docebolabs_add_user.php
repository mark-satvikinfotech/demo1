<?php
$debug = true;
if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
	fwrite($fp, date('d-m-Y H:i:s')." - ".print_r($_REQUEST, true)."\r\n");
	fclose($fp);
}
if(isset($_REQUEST['doceboid']) && $_REQUEST['doceboid'] != null && $_REQUEST['doceboid'] != '' && isset($_REQUEST['contactId']) && $_REQUEST['contactId'] != null && $_REQUEST['contactId'] != '' && isset($_REQUEST['password']) && $_REQUEST['password'] != null && $_REQUEST['password'] != ''){
	global $connInfo;
	$connInfo = array('isconn:'.$_REQUEST['isappid'].':i:'.$_REQUEST['isapikey'].':This is the connection for '.$_REQUEST['isappid'].'.infusionsoft.com');
	require("../aisdk.php");
	$app = new iSDK;
	if($app->cfgCon("isconn")){
		$conDat = array(
			$_REQUEST['field'] => $_REQUEST['password'],
			$_REQUEST['doceboidfield'] => $_REQUEST['doceboid']
		);
		$conID = $app->updateCon($_REQUEST['contactId'], $conDat);	
		$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
		fwrite($fp, "IS Contact - ".$conID."\r\n");	
		fclose($fp);
	}
} elseif(isset($_REQUEST['userdata']) && $_REQUEST['userdata'] != '' && $_REQUEST['userdata'] != null && isset($_REQUEST['syncfields']) && $_REQUEST['syncfields'] != '' && $_REQUEST['syncfields'] != null){
	global $connInfo;
	$connInfo = array('isconn:'.$_REQUEST['isappid'].':i:'.$_REQUEST['isapikey'].':This is the connection for '.$_REQUEST['isappid'].'.infusionsoft.com');
	require("../aisdk.php");
	$app = new iSDK;
	if($app->cfgCon("isconn")){
		$conDat = array();
		foreach($_REQUEST['userdata'] as $key => $field){
			$conDat[$_REQUEST['syncfields'][$key]] = $field;
		}
		$conID = $app->addWithDupCheck($conDat, 'Email');
		$contactId = $app->updateCon($conID, $conDat);
		$app->optIn($conDat['Email'],"DoceboLabs Opt In");
		if(isset($_REQUEST['tag']) && $_REQUEST['tag'] != null && $_REQUEST['tag'] != ''){
			$result = $app->grpAssign($conID, $_REQUEST['tag']);
		}	
		$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
		fwrite($fp, "IS Contact - ".$contactId."\r\n");	
		fclose($fp);
	}
} elseif(isset($_REQUEST['userdata']) && $_REQUEST['userdata'] != '' && $_REQUEST['userdata'] != null && !isset($_REQUEST['syncfields'])){
	global $connInfo;
	$connInfo = array('isconn:'.$_REQUEST['isappid'].':i:'.$_REQUEST['isapikey'].':This is the connection for '.$_REQUEST['isappid'].'.infusionsoft.com');
	require("../aisdk.php");
	$app = new iSDK;
	if($app->cfgCon("isconn")){
		$conID = $app->addWithDupCheck($_REQUEST['userdata'], 'Email');
		$contactId = $app->updateCon($conID, $_REQUEST['userdata']);
		$app->optIn($_REQUEST['userdata']['Email'],"DoceboLabs Opt In");
		if(isset($_REQUEST['tag']) && $_REQUEST['tag'] != null && $_REQUEST['tag'] != ''){
			$result = $app->grpAssign($conID, $_REQUEST['tag']);
		}	
        $fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
		fwrite($fp, "IS Contact - ".$contactId."\r\n");
		fclose($fp);
	}
} elseif(isset($_REQUEST['updateuserdata']) && $_REQUEST['updateuserdata'] != '' && $_REQUEST['updateuserdata'] != null && isset($_REQUEST['id_user']) && $_REQUEST['id_user'] != null && $_REQUEST['id_user'] != '' && isset($_REQUEST['id_user_field']) && $_REQUEST['id_user_field'] != null && $_REQUEST['id_user_field'] != ''){
	global $connInfo;
	$connInfo = array('isconn:'.$_REQUEST['isappid'].':i:'.$_REQUEST['isapikey'].':This is the connection for '.$_REQUEST['isappid'].'.infusionsoft.com');
	require("../aisdk.php");
	$app = new iSDK;
	if($app->cfgCon("isconn")){
		// find contact by id_user (docebo id)
		$returnFields = array('Id');
		$query = array($_REQUEST['id_user_field'] => $_REQUEST['id_user']);
		$contacts = $app->dsQuery("Contact",1,0,$query,$returnFields);
		if(count($contacts) == '1'){
			$conID = $app->updateCon($contacts['0']['Id'], $_REQUEST['updateuserdata']);
		}		
		$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
		fwrite($fp, "IS Contact - ".$conID."\r\n");		
		fclose($fp);
	}
}
?>