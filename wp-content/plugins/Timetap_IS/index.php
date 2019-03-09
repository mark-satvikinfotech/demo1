<?php
 //Contact add in Is From Reamaze :: cron set for this
 error_reporting(1);
 ini_set('error_reporting', E_ALL);
 ini_set('max_execution_time', 1000);
 include_once('is_login.php');
 include "config.php";
 include_once "reamaze_data.php";
 
 $config = json_decode(file_get_contents($config_log_file_name),true);
 $current_page = $config['current_page'];
 $limit = $config['limit'];
 $last_updated_contact = $config['last_updated'];
 
 $reamaze = new Reamaze_data();
 $contacts_data = $reamaze->get_contacts($current_page);
 $contacts = json_decode($contacts_data);
 echo "<pre>";
 print_r($contacts); 
 
 $total_contacts =  $contacts->total_count;
 echo 'Total contacts:'.$total_contacts.'<br>';
 echo 'last contacts:'.$last_updated_contact.'<br>';

 
 $contact = array();
 $contacts_data = array();
 
 if($total_contacts >= $last_updated_contact)
 {
	$reamaze->writeToLog(CONTACT_LOG_FILE_NAME,"\n\n"."****************** Contact Cron Start*******"."\n");
    $date = date("Y-m-d H:i:s");
    $reamaze->writeToLog(CONTACT_LOG_FILE_NAME,'Date: '.$date."\n");
    $reamaze->writeToLog(CONTACT_LOG_FILE_NAME,'Total contacts: '.$total_contacts."\n");
    $reamaze->writeToLog(CONTACT_LOG_FILE_NAME,'Last updated contacts: '.$last_updated_contact."\n");
    $last_total_contacts = $config['total'];
	
	$cont = $contacts->contacts;
	 
	foreach($cont as $key => $value)
	{
		
		if($value->email != 'null' || $value->mobile != 'null')
		{
		  $cont['id'] = $value->id;
		  $cont['name'] = $value->name;
		  $cont['email'] = $value->email;
		  $cont['data'] = $value->data;
		  $cont['twitter'] = $value->twitter;
		  $cont['facebook'] = $value->facebook;
		  $cont['instagram'] = $value->instagram;
		  $cont['mobile'] = $value->mobile;
		  $cont['friendly_name'] = $value->friendly_name;
		  array_push($contacts_data,$cont);
		}	
		
	}
	//echo '<pre>';print_r($contacts_data);exit;
	if(!empty($contacts_data))
	{   
        $i=1;
		foreach($contacts_data as $key => $value)
		{
			$id = $value['id'];
			$name = $value['name'];
			$data = json_encode($value['data']);
			$email = $value['email'];
			$twitter = $value['twitter'];
			$facebook = $value['facebook'];
			$instagram = $value['instagram'];
			$mobile = $value['mobile'];
			$friendly_name = $value['friendly_name'];
			
			
		    $contactData = array(
					'_contactid' => "$id",
					'FirstName' => "$name",
					'_data' => "$data",
					'Email' => "$email",
					'Phone1' => "$mobile",
					'_FriendlyName1' => "$friendly_name"
				);	
			//print_r($contactData);
				
		    $returnFields = array('Id');
			
			$con = array();
			if(isset($email) && $email != '')
			{
			  $query = array('Email' => $value['email']);
			  $con = $app->dsQuery("Contact", 1, 0, $query, $returnFields);
			}
			if(empty($con) && isset($mobile) && $mobile != '')
			{
			  $query = array('Phone1' => $value['mobile']);
			  $con = $app->dsQuery("Contact", 1, 0, $query, $returnFields);
			}
			
			if(!empty($con))
			{
				//echo "Update";
				$contactId = $con[0]['Id'];
				$contactId = $app->updateCon($contactId, $contactData);
				
			}
			else
			{
				//echo "Insert";
				$contactId = $app->addCon($contactData);
				
			}
			
			$app->optIn($value['email'],"Home page newsletter subscriber");
			
			if(isset($contactId) && $contactId !="")
			{
			
				/*Collecting catery tags need to be added to contact*/
				$ContactGroupCategoryArray = array(
					array(
						'cat_name' => 'Reamaze Contacts',
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
					$contacts = $app->dsQuery("ContactGroupCategory", 1000, 0, $query, $returnFields);
					if(count($contacts)==0){
						$data = array(
							'CategoryName' => $ContactGroupCategoryArray[$i]['cat_name'],
							'CategoryDescription' => '',
						);
						$ContactGroupCategoryID = $app->dsAdd("ContactGroupCategory", $data);
						$ContactGroupCategoryArray[$i]['cat_id'] = $ContactGroupCategoryID;
					}else{
						$ContactGroupCategoryArray[$i]['cat_id'] = $contacts[0]['Id'];
					}
				}
				
			    /* Iterating for each tag from list */
				$tag_array = array();
				if($ContactGroupCategoryArray[0]['cat_id'] !='')
				{
					$tag_array[0]['tag'] = 'reamaze contact';
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
					$tag_search = $app->dsQuery("ContactGroup", 1000, 0, $query, $returnFields);
					/* End Checking Tag exist or not */

					if(isset($tag_search[0]['Id'])){
							$tagId = $tag_search[0]['Id'];
					}else{
					/* Start Creating Tag if does not exist */
					$data = array(
						    'GroupName' => $tag_array[$tag_count]['tag'],
							'GroupCategoryId' => $tag_array[$tag_count]['category'], 
						   );
					$tagId = $app->dsAdd("ContactGroup", $data);
					/* End Creating Tag if does not exist */
					}

					/* Start asigning Tag to contact */
					$result = $app->grpAssign($contactId, $tagId);
					/* End asigning Tag to contact */
					}	
				
				}
			}
			
			$reamaze->writeToLog(CONTACT_LOG_FILE_NAME,"\n"."Contact ID: ".$contactId."\n");
			//$reamaze->writeToLog(CONTACT_LOG_FILE_NAME,"\n"."Record count: ".$i."\n");
			$reamaze->writeToLog(CONTACT_LOG_FILE_NAME,print_r($contactData,true));
			
			//echo $contactId.'<br>';
			$config = json_decode(file_get_contents($config_log_file_name),true);
			$data_value = $config['last_updated']+ 1;
			$reamaze->update_LogFile("last_updated", $data_value, $config_log_file_name);
			$i++;
		}
	    $config_log_file_name = "config.txt";
		$data_value = $config['current_page']+ 1;
		$reamaze->update_LogFile("current_page", $data_value, $config_log_file_name);

		$current_total = $config['current_page'] * $config['limit'];
		if($current_total >= $total_contacts)
		{
		  $reamaze->update_LogFile("total", $total_contacts, $config_log_file_name);
		}
    }
 }
 
 $reamaze->writeToLog(CONTACT_LOG_FILE_NAME,"****************** Contact Cron End *************"."\n\n");
 echo "Contact add in IS From Reamaze successfully";
?>