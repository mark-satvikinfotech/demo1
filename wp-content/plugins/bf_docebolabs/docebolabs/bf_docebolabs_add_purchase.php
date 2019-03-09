<?php
$debug = true;
if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'w');
	fwrite($fp, date('d-m-Y H:i:s')." - ".print_r($_REQUEST, true)."\r\n");
	fclose($fp);
}
if(isset($_REQUEST['addpurchase']) && $_REQUEST['addpurchase'] != '' && $_REQUEST['addpurchase'] != null){
	global $connInfo;
	$connInfo = array('isconn:'.$_REQUEST['isappid'].':i:'.$_REQUEST['isapikey'].':This is the connection for '.$_REQUEST['isappid'].'.infusionsoft.com');
	require("../aisdk.php");
	$app = new iSDK;
	if($app->cfgCon("isconn"))
	{
		// find contact
		$returnFields = array('Id');
		$query = array($_REQUEST['syncfields']['id_user'] => $_REQUEST['addpurchase']['id_user']);
		$contacts = $app->dsQuery("Contact",1,0,$query,$returnFields);
		echo 'contacts<pre>';
		print_r($contacts);
		echo '</pre>';
		if($debug == true){
			$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
			fwrite($fp, date('d-m-Y H:i:s')." - contacts: ".print_r($contacts, true)."\r\n");
			fclose($fp);
		}
		$userData = array();
		if(count($_REQUEST['syncfields']) >= '1'){
			foreach($_REQUEST['syncfields'] as $doceboField => $IsField){
				if(isset($_REQUEST['addpurchase'][$doceboField])){
					$userData[$IsField] = $_REQUEST['addpurchase'][$doceboField];
				}
			}
		}
		if(count($contacts) >= '1'){
			$contactId = $contacts['0']['Id'];
			$contactId = $app->updateCon($contactId, $userData);
		} else {
			// create new contact
			echo 'userData<pre>';
			print_r($userData);
			echo '</pre>';
			if($debug == true){
				$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
				fwrite($fp, date('d-m-Y H:i:s')." - userData: ".print_r($userData, true)."\r\n");
				fclose($fp);
			}
			$contactId = $app->addWithDupCheck($userData, 'Email');
			$contactId = $app->updateCon($contactId, $userData);
			$app->optIn($userData['Email'],"DoceboLabs Opt In");
			echo 'contactId<pre>';
			print_r($contactId);
			echo '</pre>';
			if($debug == true){
				$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
				fwrite($fp, date('d-m-Y H:i:s')." - contactId: ".print_r($contactId, true)."\r\n");
				fclose($fp);
			}
			if(isset($_REQUEST['tag']) && $_REQUEST['tag'] != null && $_REQUEST['tag'] != ''){
				$result = $app->grpAssign($contactId, $_REQUEST['tag']);
			}
		}

		
if($_REQUEST['addpurchase']['status'] != "declined")
{
		// add line items to order
		$taxable = false;
		if(count($_REQUEST['addpurchase']['items']) >= '1'){
			foreach($_REQUEST['addpurchase']['items'] as $item){
				$returnFields = array('Id', 'ProductName', 'Taxable', 'CountryTaxable', 'StateTaxable', 'CityTaxable');
				$query = array('Sku' => $item['Sku']);
				$productDetails = $app->dsQuery("Product",1,0,$query,$returnFields);
				if(count($productDetails) >= '1'){
					$productId = $productDetails['0']['Id'];
					if(isset($productDetails['0']['Taxable']) && $productDetails['0']['Taxable'] == '1'){
						$taxable = true;
					}
				} else {
					// product doesnt exist so create
					$conDat = $item;
					if(isset($_REQUEST['bf_docebo_taxable']) && $_REQUEST['bf_docebo_taxable'] == 1){
						$conDat['Taxable'] = 1;
						$taxable = true;
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
					$productId = $app->dsAdd("Product", $conDat);
				}
				echo 'productId<pre>';
				print_r($productId);
				echo '</pre>';
				if($debug == true){
					$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
					fwrite($fp, date('d-m-Y H:i:s')." - productId: ".print_r($productId, true)."\r\n");
					fclose($fp);
				}
				if($item['ProductPrice'] != '0.00')
				{
				    $returnFields1 = array('Id','ContactId', 'DateCreated');
					$query1 = array('ContactId' => $contactId);
					$getorderItemDetails = $app->dsQuery("Job",1000,0,$query1,$returnFields1);
					/* $fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
					fwrite($fp, date('d-m-Y H:i:s')." - Duplicate check Job Order Details: ".print_r($getorderItemDetails, true)."\r\n");
					fclose($fp);  */
					foreach ($getorderItemDetails as $job)
					{
						$ord_id = $job['Id'];
						$returnFields_1 = array('Id', 'ProductId', 'ItemType','OrderId',);
						$query_1 = array('OrderId' => $ord_id, 'ProductId'=> $productId, 'ItemType' => 4);
						$addorderItemDetails = $app->dsQuery("OrderItem",1,0,$query_1,$returnFields_1);
						//echo "<pre>"; print_r($addorderItemDetails);
						$product_Id[] = $addorderItemDetails[0]['ProductId'];
						/* $fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
					    fwrite($fp, date('d-m-Y H:i:s')." - Products Id: ".print_r($product_Id, true)."\r\n");
					    fwrite($fp, date('d-m-Y H:i:s')." - Duplicate check Add Order Details: ".print_r($addorderItemDetails, true)."\r\n");
					    fclose($fp);  */
					}
					    
					
				  if(!in_array($productId,$product_Id))
				  {
					 // create new order
					 $invoiceId = $app->blankOrder($contactId, 'Synced from Docebo', $app->infuDate(date('Y-m-d\TH:i:s', strtotime($_REQUEST['addpurchase']['date_created']))), 0, 0);
					 echo 'invoiceId<pre>';
					 print_r($invoiceId);
					 echo '</pre>';
					 if($debug == true){
						$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
						fwrite($fp, date('d-m-Y H:i:s')." - invoiceId: ".print_r($invoiceId, true)."\r\n");
						fclose($fp);
					 }
					 
					$qty = '1';
					$result = $app->addOrderItem($invoiceId, $productId, 4, (double)$item['ProductPrice'], (int)$qty, $productDetails[0]['ProductName'], $productDetails[0]['ProductName']);
					echo 'result<pre>';
					print_r($result);
					echo '</pre>';
					if($debug == true){
						$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
						fwrite($fp, date('d-m-Y H:i:s')." - result: ".print_r($result, true)."\r\n");
						fclose($fp);
					}

					// check if product is taxable
					if($taxable == true){
						// is taxable add tax to invoice
						$ret = $app->recalculateTax($invoiceId);
					echo 'recalculateTax<pre>';
					print_r($ret);
					echo '</pre>';
					if($debug == true){
						$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
						fwrite($fp, date('d-m-Y H:i:s')." - recalculateTax: ".print_r($recalculateTax, true)."\r\n");
						fclose($fp);
					}
					}

					// mark as paid
					$manualPmt = $app->manualPmt((int)$invoiceId,(double)$_REQUEST['addpurchase']['total'],$app->infuDate(date('Y-m-d\TH:i:s', strtotime($_REQUEST['addpurchase']['date_created']))),$_REQUEST['addpurchase']['payment_method'],$_REQUEST['addpurchase']['total'].' order added via DoceboLabs',false);
					echo 'manualPmt<pre>';
					print_r($manualPmt);
					echo '</pre>';
					if($debug == true){
						$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
						fwrite($fp, date('d-m-Y H:i:s')." - manualPmt: ".print_r($manualPmt, true)."\r\n");
						fclose($fp);
					}
				  }
		    }
		}
	}
		}
	}
}
?>