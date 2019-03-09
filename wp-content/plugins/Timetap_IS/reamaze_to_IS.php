<?php error_reporting(1); ini_set('error_reporting', E_ALL);
ini_set('max_execution_time', 30000);
 include("reamaze_data.php");
 include("config.php");
 include('infusion_config.php');
 include('timetap_config.php');
 include('timetap_data.php');
 
 $timetap = new Timetap_data();
 $sess_token = $timetap->get_sessiontoken($ts,$signature);
 $sessiontok = $sess_token->sessionToken; 
 
 $config = json_decode(file_get_contents($reamaze_config_log_file_name),true);
 
 $current_page = $config['current_page'];
 $limit = $config['limit'];
 $last_updated_contact = $config['last_updated'];
 $reamaze = new Reamaze_data();
 $contacts_data = $reamaze->get_contacts($current_page);
 $contacts = json_decode($contacts_data); 
 //echo "<pre>";print_r($contacts); exit;
 $total_contacts =  $contacts->total_count;
 file_put_contents(REAMAZE_CONTACT_LOG_FILE_NAME, "\n******* Cron start ******\n", FILE_APPEND);
 file_put_contents(REAMAZE_CONTACT_LOG_FILE_NAME, "\nDate: ".date("Y-m-d H:i:s")."\n", FILE_APPEND);
 $i=0;
 foreach ($contacts->contacts as $key => $value) 
 {  if($value->email != '' || $value->mobile != '')
	{
		//$value->email;
		$id = $value->id;
		$data = json_encode($value->data);
	    $friendly_name = $value->friendly_name;		
		$returnFields = array('Id','LastUpdated','_LastUpadated');
		$con = array();
		$tmcontactData = array();
		$query = array();
		if($value->mobile != '')
		{
			$re_mobile = str_replace(' ','',$value->mobile);
			$remaze_mobile = substr($re_mobile, -10);
			
			$search1= $value->mobile;
			$search = array($search1);
		    $search = json_encode($search);
			$client = $timetap->get_clients($sessiontok,$search);
			$clients =json_decode($client);
				
			foreach($clients as $key=>$val)
			{
			  if($val->clientId != -1)
			  {
			    if($val->homePhone != "" || $val->cellPhone != "")
			    {
				  $tm_hmobile = str_replace(' ','',$val->homePhone);
				  $tmtp_hmobile = substr($tm_hmobile, -10);
			      
				  $tm_cmobile = str_replace(' ','',$val->cellPhone);
				  $tmtp_cmobile = substr($tm_cmobile, -10);
			     
				  if($remaze_mobile == $tmtp_hmobile || $remaze_mobile == $tmtp_cmobile)
				  {
					  $fname = $val->firstName;
					  $lname = $val->lastName;
					  $cmpname = $val->companyName;
					  $email = $val->emailAddress;
					  $add1 = $val->address1;
					  $add2 = $val->address2;
					  $city = $val->city;
					  $state = $val->state;
					  $zip = $val->zip;
					  $clientId = $val->clientId;
					  
					  if($val->county != '')
					  {
						 $country = $val->county;
					  }
					  else if($val->country != '')
					  {
						  $country = $val->country;
					  }
					  if($val->cellPhone != '')
					  {
						$hphone = str_replace(' ','',$val->cellPhone);
						$custom_phone = $hphone;
						$phone1 = $val->cellPhone;
					  }
					  
					  if($fname != '' && $lname != '')
					  {
						  $name = $fname.' '.$lname;
					  }
					  else
					  {
						 $name = $fname; 
					  }
					  
					  $tmcontactData = array(
						'_ContactId' => (string)$id,
						'_ClientId' => "$clientId"
						);
						
					  if($fname != '')
						$tmcontactData['FirstName'] = $fname;
					  if($lname != '')
						$tmcontactData['LastName'] = $lname;
					  if($cmpname != '')
						  $tmcontactData['Company'] = $cmpname;
					  if($email != '')
						$tmcontactData['Email'] = $email;
					  if($add1 != '')
					    $tmcontactData['StreetAddress1'] = $add1;
					  if($add2 != '')
						  $tmcontactData['StreetAddress2'] = $add2;
					  if($city != '')
						  $tmcontactData['City'] = $city;
					  if($country != '')
						  $tmcontactData['Country'] = $country;
					  if($state != '')
						  $tmcontactData['State'] = $state;
					  if($zip != '')
						  $tmcontactData['PostalCode'] = $zip;
					  if($friendly_name!= '')
				          $tmcontactData['_FriendlyName'] = $name;
			          if($phone1 != '')
					      $tmcontactData['Phone1'] = $phone1;
					      $tmcontactData['_CustomPhone'] = $custom_phone;
				   }
				   
			    }
			  }
			}
		}
		else if(isset($value->email) && $value->email != '')
		{
			$search1= $value->email;
		    $search = array($search1);
		    $search = json_encode($search);
			$client = $timetap->get_clients($sessiontok,$search);
			$clients =json_decode($client);
			foreach($clients as $key=>$val)
			{
			  if($val->clientId != -1)
			  {
			    if($val->emailAddress == $value->email)
			    {
			      $fname = $val->firstName;
			      $lname = $val->lastName;
			      $cmpname = $val->companyName;
			      $email = $val->emailAddress; 
			      $add1 = $val->address1;
			      $add2 = $val->address2;
			      $city = $val->city;
			      $state = $val->state;
			      $zip = $val->zip;
				  $clientId	 = $val->clientId;
				  
					  
			      if($val->county != '' || $val->county != null)
			      {
				    $country = $val->county;
			      }
			      else if($val->country != '' || $val->country != null)
			      {
				    $country = $val->country;
			      }
				  
				  if($fname != '' && $lname != '')
				  {
					 $name = $fname.' '.$lname;
				  }
				  else
				  {
					$name = $fname;
				  }
				  
				  if($val->cellPhone != '')
				  {
					$hphone = str_replace(' ','',$val->cellPhone);
					$custom_phone = $hphone;
					$phone1 = $val->cellPhone;
				  }
				  
				  $tmcontactData = array(
					'_ContactId' => (string)$id,
					'_ClientId' => "$clientId"
					);
					
			      if($fname != '')
				    $tmcontactData['FirstName'] = $fname;
				  if($lname != '')
				    $tmcontactData['LastName'] = $lname;
				  if($cmpname != '')
				    $tmcontactData['Company'] = $cmpname;
				  if($email != '')
				    $tmcontactData['Email'] = $email;
				  if($add1 != '')
				    $tmcontactData['StreetAddress1'] = $add1;
				  if($add2 != '')
				    $tmcontactData['StreetAddress2'] = $add2;
				  if($city != '')
				    $tmcontactData['City'] = $city;
				  if(isset($country) && $country != "")
				    $tmcontactData['Country'] = $country;
				  if($state != '')
				    $tmcontactData['State'] = $state;
				  if($zip != '')
				    $tmcontactData['PostalCode'] = $zip;
				  if($friendly_name!= '')
				    $tmcontactData['_FriendlyName'] = $name;
				  if($phone1 != '')
					  $tmcontactData['Phone1'] = $phone1;
				      $tmcontactData['_CustomPhone'] =  $custom_phone;
				  
			    }
			}
		  }	
		}
		//echo "exit";
	    //echo "<pre>"; print_r($tmcontactData); exit;
		if(!empty($tmcontactData)) 
	    {
			//echo "Match Data";
			//echo "<pre>"; print_r($tmcontactData); 
			 //echo "timetap data"."<br/>"; exit;
			 if($tmcontactData['Email'] != '')
			 {
			    $email = $tmcontactData['Email'];
			 }
			 if($tmcontactData['Phone1'] != '')
			 {
			   $mobile = $tmcontactData['Phone1'];
			   $custom_phone = $tmcontactData['_CustomPhone'];
             }
			 
			 if($tmcontactData['_FriendlyName'] != '')
			 {
				 $friendlyName = $tmcontactData['_FriendlyName'];
			 }
			 
			 $time = time();
			 $tmcontactData['_LastUpadated'] = (string)$time; 
			 //print_r($tmcontactData); exit;
			 if($email != "")
			 {
			   $query = array('Email' => $email);
			   $con = $infusionsoft->data('xml')->query("Contact", 1, 0, $query, $returnFields, (string)'Id',(boolean)'ASC');
			   //print_r($con); exit;
			   $reamaze_data = array(
				    "contact" => array(
					  "name" => $name,
					  "friendly_name" => $friendlyName
				  ));
				if($value->friendly_name != $friendlyName) 
				{
				   $curlSession = curl_init('https://Infusionsoft.reamaze.io/api/v1/contacts/'.$email);

					  curl_setopt($curlSession, CURLOPT_HEADER, false);
					  curl_setopt($curlSession,CURLOPT_USERPWD,implode(':', array('pieter@pieterkdevilliers.co.uk', '60eb0ada86180343044e0ced9b6dfcd43388249a560faef1ba6d37bd36d0b71e')));
					  curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
					  curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
					  curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "PUT");
					  curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($reamaze_data));
					  curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
												
					  $response = curl_exec($curlSession);
					  $err = curl_error($curlSession);
					  if ($err) 
					  {
					   echo "CURL Error #:" . $err;
					  } 
					  $response = json_decode($response);
				}
			 }
			 else if($mobile != "")
			 {
				$mobile1 = substr($mobile, -10);
				$query = array('_CustomPhone' => '%'.$mobile1);
				$con = $infusionsoft->data('xml')->query("Contact", 1, 0, $query, $returnFields, (string)'Id',(boolean)'ASC');
				
				$reamaze_data = array(
					    "contact" => array(
						  "name" => $name,
						  "friendly_name" => $friendlyName
						));
				if($value->friendly_name != $friendlyName)  
				{
					$curlSession = curl_init('https://Infusionsoft.reamaze.io/api/v1/contacts/'.$mobile);

					curl_setopt($curlSession, CURLOPT_HEADER, false);
					curl_setopt($curlSession,CURLOPT_USERPWD,implode(':', array('pieter@pieterkdevilliers.co.uk', '60eb0ada86180343044e0ced9b6dfcd43388249a560faef1ba6d37bd36d0b71e')));
					curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
					curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "PUT");
					curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($reamaze_data));
					curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
													
					$response = curl_exec($curlSession);
					$err = curl_error($curlSession);
					if ($err) 
					{
					  echo "CURL Error #:" . $err;
					} 
					$response = json_decode($response);
				}
			}
			//echo '<pre>';print_r($tmcontactData);
			if(!empty($con))           {
			$query = array();
					if($custom_phone != "")
						 $query['_CustomPhone']= $custom_phone;
					if($email != "")
						$query['Email'] = $email;
					if($fname != "")
						$query['FirstName'] = $fname;
					if($lname != "")
						$query['LastName'] = $lname;
					if($friendly_name != "")
						$query['_FriendlyName'] = $friendlyName;
					if($id != "")
						$query['_ContactId'] = $id;
					if($clientId != '')
					   $query['_ClientId'] = $clientId;
					if($add1 != '')
						$query['StreetAddress1'] = $add1;
					if($add2 != '')
						$query['StreetAddress2'] = $add2;
					if($city != '')
						$query['City'] = $city;
					if(isset($country) && $country != "")
						$query['Country'] = $country;
					if($state != '')
						$query['State'] = $state;
					if($zip != '')
						$query['PostalCode'] = $zip;
					$same_con = $infusionsoft->data('xml')->query("Contact", 1, 0, $query, $returnFields, (string)'Id',(boolean)'ASC');
					$contactId = $con[0]['Id'];
					if(empty($same_con))
					{
						$lst = $con[0]['LastUpdated'];
						$lst_dt = $lst->date; 
						if(($con[0]['_LastUpadated'] > strtotime($lst_dt)) || $con[0]['_LastUpadated'] == '')						{
						  $contactId = $infusionsoft->contacts('xml')->update($contactId, $tmcontactData);
							echo '<br>update:'.$contactId;
						}
					}
			}
			else            {
				$contactId = $infusionsoft->contacts('xml')->add($tmcontactData);
				echo '<br>Add:'.$contactId;			}
			$infusionsoft->emails('xml')->optIn($email,"Home page newsletter subscriber");
			$reamaze->writeToLog(REAMAZE_CONTACT_LOG_FILE_NAME,"\n"."Contact ID: ".$contactId."\n");
			$reamaze->writeToLog(REAMAZE_CONTACT_LOG_FILE_NAME,print_r($tmcontactData,true));
		}
		else if(empty($tmcontactData))
		{
		    $email = $value->email;
			//echo "<br/>";
			$id = $value->id;
			$data = '';
			if($value->data != null && $value->data != '{}')
			{
				$data = $value->data;
			}
		    $friendly_name = $value->friendly_name;	
		    
			$mobile = $value->mobile;
			$fname = $value->name;
			
			   
				if($friendly_name != '' && $friendly_name != null)
				    $tmcontactData['_FriendlyName'] = $friendly_name;
				if($email != '' )
					$tmcontactData['Email'] = $email;
				if($data != '' && $data != null)
					$tmcontactData['_Data'] = $data;
				if($id != '' )
					$tmcontactData['_ContactId'] = $id;
				
				
			 
			 $time = time();
			 $tmcontactData['_LastUpadated'] = (string)$time; 
			 //echo "<pre>"; print_r($tmcontactData); 
			 $rem_con = array();
			 $que = array();
			 $rem_returnFields = array('Id','FirstName','LastName','Email','Phone1');
			 if($email != "" )
			 {
			   $que = array('Email' => $email);
			   $rem_con = $infusionsoft->data('xml')->query("Contact", 1, 0, $que, $rem_returnFields, (string)'Id',(boolean)'ASC');
			   print_r($rem_con); 
			  
			 }
			 else if($mobile != "" && $mobile != null)
			 {
				$mobile1 = substr($mobile, -10);
				$que = array('Phone1' => '%'.$mobile1);
				$rem_con = $infusionsoft->data('xml')->query("Contact", 1, 0, $que, $rem_returnFields, (string)'Id',(boolean)'ASC');
				print_r($rem_con); 
			}
			
			
			if(!empty($rem_con)) {
			
				if(isset($rem_con[0]['Phone1']) && $rem_con[0]['Phone1'] != '')
					{
						$tmcontactData['Phone1'] = $rem_con[0]['Phone1'];
				        $tmcontactData['_CustomPhone'] =  substr($mobile, -10);
					}
					else if($mobile != '')
					{
						$tmcontactData['Phone1'] = $mobile;
						$tmcontactData['_CustomPhone'] =  substr($mobile, -10);
					}
					if($fname != "" && $rem_con[0]['FirstName'] == "")
					{
						$tmcontactData['FirstName'] = $fname;
					}
					else
					{
						if($fname != $rem_con[0]['FirstName'])  
						{
							if($rem_con[0]['FirstName'] != "" && isset($rem_con[0]['LastName']) && $rem_con[0]['LastName'] != "")
							{
								$name = $rem_con[0]['FirstName'].' '.$rem_con[0]['LastName'];
							}
							else{
								$name = $rem_con[0]['FirstName'];
							}
							$reamaze_data = array(
							"contact" => array(
							  "name" => $name,
							));
							//print_r($reamaze_data);
							if(isset($rem_con[0]['Email']) && $rem_con[0]['Email'] != "")
							{
							 $curlSession = curl_init('https://Infusionsoft.reamaze.io/api/v1/contacts/'.$rem_con[0]['Email']);
							}
							
							
							curl_setopt($curlSession, CURLOPT_HEADER, false);
							curl_setopt($curlSession,CURLOPT_USERPWD,implode(':', array('pieter@pieterkdevilliers.co.uk', '60eb0ada86180343044e0ced9b6dfcd43388249a560faef1ba6d37bd36d0b71e')));
							curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
							curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
							curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "PUT");
							curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($reamaze_data));
							curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
															
							$response = curl_exec($curlSession);
							$err = curl_error($curlSession);
							if ($err) 
							{
							  echo "CURL Error #:" . $err;
							} 
							$response = json_decode($response);
						}
						else if(isset($rem_con[0]['Phone1']) && $rem_con[0]['Phone1'] == $mobile)
						{
							$phone = $rem_con[0]['Phone1'];
							$reamaze_data = array(
							"contact" => array(
							  "name" => $fname,
							));
							
							//print_r($reamaze_data);
							$curlSession = curl_init('https://Infusionsoft.reamaze.io/api/v1/contacts/'.$phone);
							
							curl_setopt($curlSession, CURLOPT_HEADER, false);
							curl_setopt($curlSession,CURLOPT_USERPWD,implode(':', array('pieter@pieterkdevilliers.co.uk', '60eb0ada86180343044e0ced9b6dfcd43388249a560faef1ba6d37bd36d0b71e')));
							curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
							curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
							curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "PUT");
							curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($reamaze_data));
							curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
															
							$response = curl_exec($curlSession);
							$err = curl_error($curlSession);
							if ($err) 
							{
							  echo "CURL Error #:" . $err;
							} 
							$response = json_decode($response);
						}
					}
					//echo "<pre>";
			        //print_r($tmcontactData);
					echo '<pre>';print_r($tmcontactData); 
					if($email != "")
					{
					    $query['Email'] = $email; 
						$same_con = $infusionsoft->data('xml')->query("Contact", 1, 0, $query, $rem_returnFields, (string)'Id',(boolean)'ASC');
						$contact_Id = $rem_con[0]['Id'];
						//print_r($same_con);
						if(!empty($same_con))
						{
							
							//echo '<pre>';print_r($tmcontactData); 
							$contactId = $infusionsoft->contacts('xml')->update($contact_Id, $tmcontactData);
							echo '<br>update:'.$contactId; 
						}
						$infusionsoft->emails('xml')->optIn($email,"Home page newsletter subscriber");
					}
					else 
					{
					    $query['_CustomPhone'] = substr($mobile, -10);
						$same_con = $infusionsoft->data('xml')->query("Contact", 1, 0, $query, $rem_returnFields, (string)'Id',(boolean)'ASC');
					    $contact_Id = $rem_con[0]['Id'];
						if(!empty($same_con))
						{				
							$contactId = $infusionsoft->contacts('xml')->update($contact_Id, $tmcontactData);
							echo '<br>update:'.$contactId;
						}
					}
				
			}
			else     
			{
			   if($fname != '' && $fname != null)
			   {
				$tmcontactData['FirstName'] = $fname;
			   }
			   if($mobile != '' && $mobile != null)
			   {
				  $tmcontactData['Phone1'] = $mobile;
			      $tmcontactData['_CustomPhone'] =  substr($mobile, -10);
			   }
				
				echo '<pre>';print_r($tmcontactData); 
				//var_dump($tmcontactData);
				if(!empty($tmcontactData))
				$contactId = $infusionsoft->contacts('xml')->add($tmcontactData);
				echo '<br>Add:'.$contactId;		
                if($email != '')
				{
					$infusionsoft->emails('xml')->optIn($email,"Home page newsletter subscriber");
				}				
			}
			
			//$infusionsoft->emails('xml')->optIn($email,"Home page newsletter subscriber");
			$reamaze->writeToLog(REAMAZE_CONTACT_LOG_FILE_NAME,"\n"."Contact ID: ".$contactId."\n");
			$reamaze->writeToLog(REAMAZE_CONTACT_LOG_FILE_NAME, print_r($tmcontactData,true));
			
		}
	    if(isset($contactId) && $contactId !="")       {
			$ContactGroupCategoryArray = array(
				array(
					'cat_name' => 'Reamaze Contacts',
					'cat_id' => ''
				)
			);
			
			for($i=0;$i<count($ContactGroupCategoryArray);$i++)
			{
				$tag_returnFields = array(
					'CategoryDescription',
					'CategoryName',
					'Id'
				);
				$tag_query = array(
					'CategoryName' => $ContactGroupCategoryArray[$i]['cat_name']
				);
				$contacts = $infusionsoft->data('xml')->query("ContactGroupCategory", 1000, 0, $tag_query, $tag_returnFields, (string)'Id',(boolean)'ASC');
				if(count($contacts)==0)
				{
					$tag_data = array(
						'CategoryName' => $ContactGroupCategoryArray[$i]['cat_name'],
						'CategoryDescription' => '',
					);
					$ContactGroupCategoryID = $infusionsoft->data('xml')->add("ContactGroupCategory", $tag_data);
					$ContactGroupCategoryArray[$i]['cat_id'] = $ContactGroupCategoryID;
				}
				else
				{
					$ContactGroupCategoryArray[$i]['cat_id'] = $contacts[0]['Id'];
				}
			}
				
			// Iterating for each tag from list 
			$tag_array = array();
			if($ContactGroupCategoryArray[0]['cat_id'] !='')
			{
				$tag_array[0]['tag'] = 'reamaze contact';
				$tag_array[0]['category'] = $ContactGroupCategoryArray[0]['cat_id'];
			}
						
			for($tag_count=0;$tag_count<count($tag_array);$tag_count++)
			{
				if(trim($tag_array[$tag_count]['tag'])!="")
				{
					// Start Checking Tag exist or not 
					$tag_returnFields = array(
						'GroupCategoryId',
						'GroupDescription',
						'GroupName',
						'Id'
					);
					$tag_query = array(
							'GroupName' => $tag_array[$tag_count]['tag'],
					);
				
					$tag_search = $infusionsoft->data('xml')->query("ContactGroup", 1000, 0, $tag_query, $tag_returnFields, (string)'Id',(boolean)'ASC');
					
					// End Checking Tag exist or not 

					if(isset($tag_search[0]['Id']))
					{
							$tagId = $tag_search[0]['Id'];
					}
					else
					{
						// Start Creating Tag if does not exist 
						$tag_data = array(
							    'GroupName' => $tag_array[$tag_count]['tag'],
								'GroupCategoryId' => $tag_array[$tag_count]['category'], 
							   );
						$tagId = $infusionsoft->data('xml')->add("ContactGroup", $tag_data);
						// End Creating Tag if does not exist 					}
					}	
					// Start asigning Tag to contact 
						$result = $infusionsoft->contacts('xml')->addToGroup($contactId, $tagId);
						 
						// End asigning Tag to contact 
				}
			}
			$config = json_decode(file_get_contents($reamaze_config_log_file_name),true);
			$data_value = $config['last_updated']+ 1;
			$reamaze->update_LogFile("last_updated", $data_value, $reamaze_config_log_file_name);
		}
		/*else
		{
			echo "not";
		}*/	
	
    }
}
 $data_value = $config['current_page']+ 1;
 $reamaze->update_LogFile("current_page", $data_value, $reamaze_config_log_file_name);

 $current_total = $config['current_page'] * $config['limit'];
 if($current_total <= $total_contacts)
 {
	$reamaze->update_LogFile("total", $total_contacts, $reamaze_config_log_file_name);
 }
 file_put_contents(REAMAZE_CONTACT_LOG_FILE_NAME, "\n******* Cron end ******\n", FILE_APPEND);
 echo "Cron Run Successfully";
 exit;
?>