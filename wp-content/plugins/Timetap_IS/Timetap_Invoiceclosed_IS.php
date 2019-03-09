<?php
  error_reporting(1);
  ini_set('error_reporting', E_ALL);
  include('timetap_config.php');
  include('timetap_data.php');
  include('infusion_config.php');
  $config = json_decode(file_get_contents($timetap_invoiceclosed_log_file_name),true);
  $current_page = $config['current_page'];
  $limit = $config['limit'];
  $last_updated_invoice= $config['last_updated'];
  $timetap = new Timetap_data();
  $sess_token = $timetap->get_sessiontoken($ts,$signature);
  $sessiontok = $sess_token->sessionToken;  
  $status= 'CLOSED';
  $inv_data = $timetap->timetap_invoice($status,$current_page,$limit,$sessiontok);
  $invoices = json_decode($inv_data);  
  $total_invoice_data = $timetap->get_total_invoice($status,$sessiontok);
  $data1 = json_decode($total_invoice_data);
  $total_invoice = count($data1);

if($total_invoice >= $last_updated_invoice){
	$timetap->writeToLog(TIMETAP_INVOICECLOSED_CRON_FILE_NAME,"\n\n"."****************** Invoice Cron Start*******"."\n");
	$date = date("Y-m-d H:i:s");
	$timetap->writeToLog(TIMETAP_INVOICECLOSED_CRON_FILE_NAME,'Date: '.$date."\n");
	$timetap->writeToLog(TIMETAP_INVOICECLOSED_CRON_FILE_NAME,'Total Invoice: '.$total_invoice."\n");
	$timetap->writeToLog(TIMETAP_INVOICECLOSED_CRON_FILE_NAME,'Last updated Invoice: '.$last_updated_invoice."\n");
	$last_total_contacts = $config['total']; 
	foreach($invoices as $key => $value)
	{
			$Invoice_Id = $value->invoiceId;
		    $Client = $value->client;
		    if(empty($value->invoiceItem))			{
				$timetap->writeToLog(TIMETAP_INVOICECLOSED_CRON_FILE_NAME,'Invoice item not found:'.$Invoice_Id."\n");
			}
			if($Client->emailAddress != "" && !empty($value->invoiceItem))
			{
				 $Invoice_Id = $value->invoiceId;
				 $Invoice_Date = $value->invoiceDate;
				 $Invoice_Totalamt = $value->invoiceTotalAmount;
				 $Invoice_Currency = $value->currency;
				 $Invoice_Status = $value->status;
				 $Invoice_Substatus = $value->subStatus;
				 $Invoice_Subtotalamt = $value->subtotalAmount;
				 $Invoice_DiscountAmount = $value->discountAmount;
				 $Invoice_BalanceAmount = $value->balanceAmount;
				if($value->invoiceId != "")				{
				   $timetap->writeToLog(TIMETAP_INVOICECLOSED_CRON_FILE_NAME,'Invoice ID: '.$Invoice_Id."\n");
				}				if(isset($Client) && !empty($Client)) 				{					$clientId =  $Client->clientId;
				    $contactData = array();
				    if($clientId != '')					{					  $contactData['_ClientId'] = "$clientId";
				    }					$apptmnt_data = $timetap->get_client_appointment($sessiontok,   $clientId);					$apptdata = json_decode($apptmnt_data);
				    $app_prev= array();
					foreach($apptdata as $key=>$val)					{
					   $app_prv = $val->appointmentDateTimeClient;
					   $t = explode(',',$app_prv);
					   $final_day= $t[0];
					   $final_month_dt =  $t[1];
					   $year = $t[2];
					   $yr = explode('at',$year);
					   $final_yer = $yr[0];
					   $time = $yr[1];
					   if(strpos($time, 'BST') !== false)					   {
					     $final_tm = str_replace('BST','',$time);
					   }					   else					   {
						 $final_tm = str_replace('GMT','',$time);
					   }
					   $get_date = strtotime($final_day.",".$final_month_dt.",".$final_yer.",".$final_tm);
					   $ap_prev = date('Y-m-d H:i:s' ,$get_date);
					   array_push($app_prev,$ap_prev);
					}
					/*for previous appointment details code start*/					
				    $prevalues = $timetap->previousdt($app_prev, date('Y-m-d H:i:s'));
					if(isset($prevalues) && $prevalues != '')					{ 
					  $pre_dt = date('Y-m-d', (int)$prevalues);					  $pre_time = date('Hi', (int)$prevalues); 
					  $apptmnt_predata = $timetap->get_client_appointmentbydate($sessiontok,$clientId,$pre_dt);
					  $aptm_pr_dt = json_decode($apptmnt_predata);
					  foreach($aptm_pr_dt as $key=>$val)
					  {
						if($pre_time == $val->startTime)
						{
						    if($val->calendarId != '')
							{
							  $contactData['_LastAppointmentId'] = $val->calendarId;
							}
							if($val->status != '')
							{							  $contactData['_LastAppointmentStatus'] = $val->status;
							}							if($val->location->locationName != '')							{								$contactData['_LastAppointmentLocation'] = $val->location->locationName;							}
							if($val->staff->fullName != '')
							{
							  $contactData['_LastAppointmentPractitioner'] = $val->staff->fullName;
							}
							if($val->reason->internalDisplayName != '')
							{
							  $contactData['_LastAppointmentServiceClass'] = $val->reason->internalDisplayName;
							}
							if($val->startDate != '')
							{
							  $startDate = $val->startDate;
							  $contactData['_LastAppointmetnStartDate'] = "$startDate";
							}
							if($val->startTime != '')
							{
							  if(strlen($val->startTime)==3)
							  {
								$val->startTime = '0'.$val->startTime;
							  }
							  $contactData['_LastappointmentStartTime'] =  date('g:i A',strtotime($val->startTime));
							}
							if($val->endDate != '')							{
							  $endDate = $val->endDate;
							  $contactData['_LastAppointmentEndDate'] = "$endDate";
							}
							if($val->endTime != '')
							{
							  if(strlen($val->endTime)==3)
							  {
								$val->endTime = '0'.$val->endTime;
							  }
							  $contactData['_LastAppointmentEndTime'] = date('g:i A',strtotime($val->endTime));
							}
						}
					  }
					}
					/*for previous appointment details code end*/
					/*for next appointment details code start*/
				    $nextvalues = $timetap->nextdt($app_prev, date('Y-m-d H:i:s'));
					if(isset($nextvalues) && $nextvalues != '')					{
					  $next_dt = date('Y-m-d', (int)$nextvalues);
					  $next_time = date('Hi', (int)$nextvalues);
					  $apptmnt_nextdata = $timetap->get_client_appointmentbydate($sessiontok,$clientId,$next_dt);
					  $aptm_next_dt = json_decode($apptmnt_nextdata);
					  foreach($aptm_next_dt as $key=>$val)					  {
						if($next_time == $val->startTime)
						{
							if($val->calendarId != '')
							{
							  $contactData['_NextAppointmentId'] = $val->calendarId;
							}
							if($val->status != '')
							{
							  $contactData['_NextAppointmentStatus'] = $val->status;
							}
							if($val->location->locationName != '')
							{
							  $contactData['_NextAppointmentLocation'] = $val->location->locationName;
							}
							if($val->staff->fullName != '')
							{
							  $contactData['_NextAppointmentPractitioner'] = $val->staff->fullName;
							}
							if($val->reason->internalDisplayName != '')
							{
							  $contactData['_NextAppointmentServiceClass'] = $val->reason->internalDisplayName;
							}
							if($val->startDate != '')							{
							  $startDate = $val->startDate;
							  $contactData['_NextAppointmetnStartDate'] = "$startDate";
							}
							if($val->startTime != '')
							{
							  if(strlen($val->startTime)==3)
							  {
								$val->startTime = '0'.$val->startTime;
							  }
							  $contactData['_NextappointmentStartTime'] = date('g:i A',strtotime($val->startTime)); 
							}
							if($val->endDate != '')							{
						
							  $endDate = $val->endDate;
					          $contactData['_NextAppointmentEndDate'] = "$endDate";
							}
							if($val->endTime != '')
							{
							   if(strlen($val->endTime)==3)
							   {
								 $val->endTime = '0'.$val->endTime;
							   }
							   $contactData['_NextAppointmentEndTime'] =  date('g:i A',strtotime($val->endTime));
							}
						}
					  }
					}
					/*for next appointment details code end*/
					/*from client id get appointments and add next and previous appointments :: code end*/
				  
				  if($Client->firstName != '' || $Client->firstName != null)
				  {
				    $contactData['FirstName'] = $Client->firstName;
				  }
				  if($Client->lastName != '' || $Client->lastName != null)
				  {
				    $contactData['LastName'] = $Client->lastName;
				  }
				  if($Client->emailAddress != '' || $Client->emailAddress != null)
				  {
					$contactData['Email'] = $Client->emailAddress;
				  }
				  if($Client->companyName != '' || $Client->companyName != null)
				  {
				    $contactData['Company'] = $Client->companyName;
				  }
				  if($Client->address1 != '' || $Client->address1 != null)
				  {
				    $contactData['StreetAddress1'] = $Client->address1;
				  }
				  if($Client->address2 != '' || $Client->address2 != null)
				  {
				    $contactData['StreetAddress2'] = $Client->address2;
				  }
				  if($Client->city != '' || $Client->city != null)
				  {
				    $contactData['City'] = $Client->city;
				  }
				  if($Client->state != '' || $Client->state != null)
				  {
				    $contactData['State'] = $Client->state;
				  }
				  if($Client->country != '' || $Client->country != null)
				  {
				    $contactData['Country'] = $Client->country;
				  }
				  if($Client->zip != '' || $Client->zip != null)
				  {
				    $contactData['PostalCode'] = $Client->zip;
				  }
				  if($Client->homePhone != '' || $Client->homePhone != null)
				  {
				    $contactData['Phone1'] = $Client->homePhone;
				  }
				  if($Client->cellPhone != '' || $Client->cellPhone != null)
				  {
				    $contactData['Phone2'] = $Client->cellPhone;
				  }
			      $conID = $infusionsoft->contacts('xml')->addWithDupCheck($contactData,  'Email');
				  
				  $infusionsoft->emails('xml')->optIn((string)$Client->emailAddress, (string)'Home page newsletter subscriber');
				  
				  $timetap->writeToLog(TIMETAP_INVOICECLOSED_CRON_FILE_NAME,'Contact Id: '.$conID."\n");
				  
				if($conID != '')
				{
				    $current_date =  date("Y-m-d", $Invoice_Date/1000);
					$cur_dt = "$current_date";
					$orderDate  = new \DateTime($cur_dt,new \DateTimeZone('America/New_York'));
				    $InvoiceId = "Timetap-".$Invoice_Id;
					$order_id = $infusionsoft->invoices('xml')->createBlankOrder($conID, $InvoiceId, $orderDate, 0, 0);
				
			        $timetap->writeToLog(TIMETAP_INVOICECLOSED_CRON_FILE_NAME, "Order Id : ".$order_id." \n\n");
					$total_price = 0;	
				    foreach($value->invoiceItem as $order_key=>$order_val)
				    {
					  $current_order = $order_val;
					  $invoice_id = str_replace(",", "", $current_order->invoiceId);
					  
					  if(!empty($order_val))
					  {
						 
						 $product_invoiceItemId = $current_order->invoiceItemId;
						 $product_Id = $current_order->productId;
						 $product_desc = $current_order->description;
						 $product_qty = $current_order->quantity;
						 $product_rate = $current_order->rate;
						 $product_grossamt = $current_order->grossAmount;
						 $product_disamt = $current_order->discountAmount;
						 $product_netamt= $current_order->netAmount;
						 if($product_Id != "" || $product_Id != null)
						 {
							 
							$product = $timetap->get_product($sessiontok,$product_Id);
							$product_data = json_decode($product);
							
							$product_name = $product_data->productName;
							$product_price= $product_rate;
						 }
						 else
						 {
							 $product_name = $product_desc;
							 $product_price= $product_rate; //for ask mam
						 }
		                 $query = array('productName' => $product_name, 'productPrice' => $product_price);
						 $returnFields = array('Id');
						 $product_query = $infusionsoft->data('xml')->query("Product", 1, 0, $query, $returnFields, (string)'Id',(boolean)'ASC');
						
						   if(empty($product_query))
						   {
								$product = array(
									'ProductName' => $product_name,
									  'Sku' => $product_name,
									  'Status' => (int) 1,
									  'ShortDescription' => $product_desc,
									  'ProductPrice' => $product_price
								);
							$productID = $infusionsoft->data('xml')->add("Product", $product);

							} else {
									$productID = $product_query[0]['Id'];
							}
						
						/*achieve goal code start here*/
						global $wpdb;
						$table_name7 = $wpdb->prefix ."product_goals";
				        $product_goals_name= $wpdb->get_results("SELECT * FROM $table_name7");
						 if(!empty($product_goals_name))
						 {
							foreach($product_goals_name as $key=>$val)
							{
							  $goal_productname = $val->pro_ser_name;
							  $goal_productId = $val->pro_ser_id;
							  $goal_goalName = $val->goal_name;
							  
							  if($goal_productId == $product_Id)
							  {
								$cron =  $infusionsoft->funnels('xml')->achieveGoal($appname, $goal_goalName, $conID);
							  }
							  
							}
						 }
						/*achieve goal code end here*/
						/*create order item*/
						$item_id = $infusionsoft->invoices('xml')->addOrderItem($order_id, $productID, 4, (float) $product_price, (int) $product_qty, $product_name, "Product");
						$product_total =  $product_qty * $product_price;
						$total_price = $total_price + (float) $product_total;
					 }				}
				  if($Invoice_DiscountAmount != '')				  {
				    $Invoice_DiscountAmount = -1 * $Invoice_DiscountAmount;
				    $item_id = $infusionsoft->invoices('xml')->addOrderItem($order_id, 0, 11, (float)$Invoice_DiscountAmount, (int)1, "Discount", "Discount");
				    $total_price = $total_price + (float) $Invoice_DiscountAmount;
				  }
				  $result2 = $infusionsoft->invoices('xml')->addManualPayment($order_id, (float)$total_price, $orderDate, '', '', false);
			      /*START Create Tag */
			      /*Collecting catery tags need to be added to contact*/
			      $ContactGroupCategoryArray = array(
					array(
							 'cat_name' => 'Timetap Invoice',
							 'cat_id' => ''
						 )
					 );
				    for($i=0;$i<count($ContactGroupCategoryArray);$i++){
					 $returnFields = array(
						 'CategoryDescription',
						 'CategoryName',
						 'Id'
					 );
					 $query = array(
						 'CategoryName' => $ContactGroupCategoryArray[$i]['cat_name']
					 );
					 $contacts = $infusionsoft->data('xml')->query("ContactGroupCategory", 1000, 0, $query, $returnFields,(string)'Id', (boolean)'ASC');
					 if(count($contacts)==0){
						 $data = array(
							 'CategoryName' => $ContactGroupCategoryArray[$i]['cat_name'],
							 'CategoryDescription' => '',
						 );
						 $ContactGroupCategoryID = $infusionsoft->data('xml')->add("ContactGroupCategory", $data);
						 $ContactGroupCategoryArray[$i]['cat_id'] = $ContactGroupCategoryID;
					 }else{
						 $ContactGroupCategoryArray[$i]['cat_id'] = $contacts[0]['Id'];
					 }
				    }
				
			        /* Iterating for each tag from list */
				    $tag_array = array();
				    if($ContactGroupCategoryArray[0]['cat_id'] !='')
				    {
					  $tag_array[0]['tag'] = 'timetap invoice';
				      $tag_array[0]['category'] = $ContactGroupCategoryArray[0]['cat_id'];
				    }
						
				    for($tag_count=0;$tag_count<count($tag_array);$tag_count++){
					  if(trim($tag_array[$tag_count]['tag'])!=""){
					  /* Start Checking Tag exist or not */
					  $returnFields = array(
						 'GroupCategoryId',
						 'GroupDescription',
						 'GroupName',
						 'Id'
					  );
					  $query = array(
							 'GroupName' => $tag_array[$tag_count]['tag'],
					  );
					  $tag_search = $infusionsoft->data('xml')->query("ContactGroup", 1000, 0, $query, $returnFields, (string)'Id',(boolean)'ASC');
					 
					  /* End Checking Tag exist or not */

					  if(isset($tag_search[0]['Id'])){
							 $tagId = $tag_search[0]['Id'];
					  }else{
					  /* Start Creating Tag if does not exist */
					  $data = array(
						     'GroupName' => $tag_array[$tag_count]['tag'],
							 'GroupCategoryId' => $tag_array[$tag_count]['category'], 
						    );
							
					  $tagId = $infusionsoft->data('xml')->add("ContactGroup", $data);
					 
					  /* End Creating Tag if does not exist */
					  }
					 /* Start asigning Tag to contact */
					 $result = $infusionsoft->contacts('xml')->addToGroup($conID, (int)$tagId);
					 /* End asigning Tag to contact */
					 }	 
				    }
			}
		}
	}
  	$config = json_decode(file_get_contents($timetap_invoiceclosed_log_file_name),true);
	$data_value = $config['last_updated']+ 1;
	$timetap->update_LogFile("last_updated", $data_value,$timetap_invoiceclosed_log_file_name);
    }
	$config = json_decode(file_get_contents($timetap_invoiceclosed_log_file_name),true);		
	$data_value = $config['current_page']+ 1;
	$timetap->update_LogFile("current_page", $data_value, $timetap_invoiceclosed_log_file_name);
	$current_total = $config['current_page'] * $config['limit'];
	if($current_total <= $total_invoice)
	{
		$timetap->update_LogFile("total", $total_invoice, $timetap_invoiceclosed_log_file_name);
	}
}
 $timetap->writeToLog(TIMETAP_INVOICECLOSED_CRON_FILE_NAME, "****************Invoice Cron end********************* \n\n");
 echo "Success";
 exit;?>