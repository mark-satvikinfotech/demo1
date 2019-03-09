<?php
$debug = true;
if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'w');
	fwrite($fp, date('d-m-Y H:i:s')." - ".print_r($_REQUEST, true)."\r\n");
	fclose($fp);
}
if(isset($_REQUEST['addproduct']) && $_REQUEST['addproduct'] != '' && $_REQUEST['addproduct'] != null){
	global $connInfo;
	$connInfo = array('isconn:'.$_REQUEST['isappid'].':i:'.$_REQUEST['isapikey'].':This is the connection for '.$_REQUEST['isappid'].'.infusionsoft.com');
	require("../aisdk.php");
	$app = new iSDK;
	if($app->cfgCon("isconn"))
	{
		$conDat = array();
		foreach($_REQUEST['addproduct'] as $key => $value){
			$conDat[$key] = $value;
		}
		if(isset($_REQUEST['bf_docebo_taxable']) && $_REQUEST['bf_docebo_taxable'] == 1){
			$conDat['Taxable'] = 1;
			if(isset($_REQUEST['bf_docebo_CountryTaxable']) && $_REQUEST['bf_docebo_CountryTaxable'] == 1){
				$conDat['CountryTaxable'] = 1;
			}
			if(isset($_REQUEST['bf_docebo_StateTaxable']) && $_REQUEST['bf_docebo_StateTaxable'] == 1){
				$conDat['StateTaxable'] = 1;
			}
			if(isset($_REQUEST['bf_docebo_CityTaxable']) && $_REQUEST['bf_docebo_CityTaxable'] == 1){
				$conDat['CityTaxable'] = 1;
			}
		}
		$returnFields = array('Id', 'Sku', 'ProductName', 'Taxable', 'CountryTaxable', 'StateTaxable', 'CityTaxable');
		$query = array('Sku' => $conDat['Sku']);
		$productDetails = $app->dsQuery("Product",1,0,$query,$returnFields);
		if(empty($productDetails)){
          $result = $app->dsAdd("Product", $conDat);
		}
if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
	fwrite($fp, "Product SKU :".$conDat['Sku']."\r\n");
	fwrite($fp, "result: ".print_r($result, true)."\r\n");
	fclose($fp);
}
	}
}
?>