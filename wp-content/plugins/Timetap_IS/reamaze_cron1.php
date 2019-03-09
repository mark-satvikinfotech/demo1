<?php 
  //include_once "reamaze_data.php";
  include "config.php";
  include_once('infusion_config.php');
  
  // for all the old contacts 
  if($_REQUEST['cron4old'] == 'Y')
  {
  	 include_once "reamaze_data.php";
  	 $reamaze = new Reamaze_data();
	 
  	 $config = json_decode(file_get_contents($IS_config_log_file_name),true);
	 $current_page = $config['current_page'];
	 $limit = $config['limit'];
	 $last_updated_contact = $config['last_updated'];

  	$returnFields = array('Id','Email','LastUpdated','FirstName','LastName','Phone1','_Data','_FriendlyName','_LastUpadated','_CustomPhone');
	$query = array('Id' => '~<>~');
	//$total_contact = $app->dsCount("Contact",$query);
	$total_contact = $infusionsoft->data('xml')->count("Contact",$query);
	$total_page = $total_contact/10;
	$total_page = (int)$total_page + 1;
	
	if($current_page <= $config['total'])
	{
		file_put_contents(OLD_CONTACT_CRON_FILE_NAME, "\n******* Cron start ******\n", FILE_APPEND);
		file_put_contents(OLD_CONTACT_CRON_FILE_NAME, "\nDate: ".date("Y-m-d H:i:s")."\n", FILE_APPEND);
		file_put_contents(OLD_CONTACT_CRON_FILE_NAME, "\nTotal Contacts: ".$total_contact."\n", FILE_APPEND);
       //$contact = $app->dsQuery("Contact", 10, $current_page, $query, $returnFields);
		$contact = $infusionsoft->data()->query("Contact", 10, $current_page, $query, $returnFields, (string)'Id',(boolean)'ASC');
		//print_r($contact); exit;
		file_put_contents(OLD_CONTACT_CRON_FILE_NAME, print_r($contact, true), FILE_APPEND);
		$i=0;
		foreach ($contact as $key => $value) 
		{
			$contactId = $value['Id'];
			if($value['Phone1'] !="")
			{
				$plus =0;
				if(strpos($value['Phone1'],'+',0) !== false)
				{
					$plus=1;
				}
				$phone1 = preg_replace("/[^0-9]/", "", $value['Phone1']);
				//echo $phone1;
				
				$phone1 = ltrim($phone1,'0');
				
				if($plus == 1)
					$phone2 = '+'.$phone1;
				else
					$phone2 = $phone1;
				
				$contactData = array("_CustomPhone" => $phone2);
				
				
				//$contact_id = $app->updateCon($value['Id'], $contactData);
				$contact_id = $infusionsoft->contacts('xml')->update($value['Id'], $contactData);
			}
			$name='';
			if(isset($value['FirstName']) && $value['FirstName'] !="")
		     {
				$name = $value['FirstName'];
			 }
			 if(isset($value['LastName']) && $value['LastName'] !="")
		     {
		     	if($name !='')
					$name .= ' '.$value['LastName'];
				else
					$name = $value['LastName'];
			}
			if(isset($value['Email']) && $value['Email'] !="")
		    {
				$email = $value['Email'];
			}
			if(isset($value['Phone1']) && $value['Phone1'] !="")
		    {
				$phone1 = $phone2;
			}
			if(isset($value['_FriendlyName']) && $value['_FriendlyName'] !="")
		    {
				$friendly_name = $value['_FriendlyName'];
			}
			if($value['Email']  != "")
			{
				echo $value['Email'];
				$curlSession = curl_init('https://Infusionsoft.reamaze.io/api/v1/contacts/?q='.$email);
			}
			else if($value['Phone1'] != "")
			{
				echo $value['Phone1'];
				$curlSession = curl_init('https://Infusionsoft.reamaze.io/api/v1/contacts/?q='.$phone2);
			}
			
			if(isset($curlSession) && $curlSession != '')
			{
				curl_setopt($curlSession, CURLOPT_HEADER, false);
				curl_setopt($curlSession,CURLOPT_USERPWD,implode(':', array('pieter@pieterkdevilliers.co.uk', '60eb0ada86180343044e0ced9b6dfcd43388249a560faef1ba6d37bd36d0b71e'))
				);
				curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
				curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "GET");
				curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
								
				$response = curl_exec($curlSession);
				$err = curl_error($curlSession);

				
				if ($err) 
				{
					echo "CURL Error #:" . $err;
							  
				} 
				
				$response = json_decode($response);
				//curl_close($curlSession);
				print_r($response);
				//update contact in reamaze
				$reamaze_data = array(
					"contact" => array(
						"name" => $name,
						"friendly_name" => $name
						)
					);

				

				if(!empty($response) && $response->id !="")
				{

					if($phone1 != "")
					{
						if($response->mobile != "")
							$reamaze_data['contact']['mobile'] = $response->mobile;
						else
							$reamaze_data['contact']['mobile'] = $phone2;
					}		 
					$flag = 0;
					//echo "Update";
					
					if($name == $response->name && $friendly_name == $response->friendly_name)
					{
						if(($mobile!= "" && $mobile == $response->mobile) || ($email != "" && $email == $response->email))
						{
							$flag == 1;
						}
					}

					
					//echo '<pre>';print_r($contactData);
					if($flag == 0)
					{
						if(($value['_LastUpadated'] > strtotime($value['LastUpdated'])) || $value['_LastUpadated'] == '')
						{
							$curlSession = curl_init('https://Infusionsoft.reamaze.io/api/v1/contacts/'.$email);

							curl_setopt($curlSession, CURLOPT_HEADER, false);
							curl_setopt($curlSession,CURLOPT_USERPWD,implode(':', array('pieter@pieterkdevilliers.co.uk', '60eb0ada86180343044e0ced9b6dfcd43388249a560faef1ba6d37bd36d0b71e'))
							);
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

							echo '<pre>	';print_r($response);
							echo '<br>update:'.$response->id;
							
						}
					}
					
					//print_r($reamaze_data);
						
				}
				else
				{
					if($phone1 != "")
					{
						if($response->mobile != "")
							$reamaze_data['contact']['mobile'] = $response->mobile;
						else
							$reamaze_data['contact']['mobile'] = $phone2;
					}
					
					// print_r($reamaze_data);
						$curlSession = curl_init('https://Infusionsoft.reamaze.io/api/v1/contacts');

						curl_setopt($curlSession, CURLOPT_HEADER, false);
						curl_setopt($curlSession,CURLOPT_USERPWD,implode(':', array('pieter@pieterkdevilliers.co.uk', '60eb0ada86180343044e0ced9b6dfcd43388249a560faef1ba6d37bd36d0b71e'))
						);
						curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
						curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
						curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "POST");
						curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($reamaze_data));
						curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
										
						$response = curl_exec($curlSession);
						$err = curl_error($curlSession);
						if ($err) 
						{
							echo "CURL Error #:" . $err;
									  
						} 
						$response = json_decode($response);

						echo '<pre>	';print_r($response);
						
				}
		    }
			if(isset($contactId) && $contactId !="")
			{
				
				//Collecting catery tags need to be added to contact
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
				
				//echo $contactId.'<br>';
				$config = json_decode(file_get_contents($reamaze_config_log_file_name),true);
				$data_value = $config['last_updated']+ 1;
				$reamaze->update_LogFile("last_updated", $data_value, $reamaze_config_log_file_name);

		  }
		  $i++;
		}
		$data_value = $config['current_page']+ 1;
		$reamaze->update_LogFile("current_page", $data_value, $IS_config_log_file_name);
	   
		//$current_total = $config['current_page'] * $config['limit'];
		$reamaze->update_LogFile("total", $total_page, $IS_config_log_file_name);
			
		echo $data_value;

	}
	else
	{
		echo 'completed';exit;
	}
	//echo '<pre>';print_r($contacts);
	file_put_contents(OLD_CONTACT_CRON_FILE_NAME, "\n******* Cron end ******\n\n\n", FILE_APPEND);
	exit;
  }

?>