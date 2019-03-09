<?php
  //Trigger Text Conversation in Reamaze, via an HTTP Post from Infusionsoft
  include_once "reamaze_data.php";
  include "config.php";
  include('infusion_config.php');
  $data = $_POST;  
  
  $date = date('Y-m-d H:i:s');  
  $reamaze = new Reamaze_data();
  
  
  $reamaze->writeToLog(CONTACT_MESSAGE_CRON_FILE_NAME, "\n\n"."****************** Invoice Cron Start*******"."\n");
  $reamaze->writeToLog(CONTACT_MESSAGE_CRON_FILE_NAME, 'Date: '.$date."\n");
  $reamaze->writeToLog(CONTACT_MESSAGE_CRON_FILE_NAME,print_r($data, true)."\n");     
   
  if($_POST['ReamazecontactId'] == "" && $_POST['ReamazeMessage'] != "" && $_POST['ReamazeEmail'] != "")
  {
 
	$ReamazeName = $_POST['ReamazeName']; 
	$Reamaze_email = $_POST['ReamazeEmail']; 
	$Reamaze_msg= $_POST['ReamazeMessage']; 
	$Reamaze_phone = $_POST['ReamazePhone'];
	$contact_id = $_POST['contactId'];
		   
	//Create Conversation after create contact Start
	$reamaze_conversation = array(
		'conversation' => array(
        'subject' => 'Default reamaze conversation',
		'category' => 'support',
					
		'message' => array(
		   'body' => 'Default reamaze conversation'
		 ),
		'user' => array(
			'name' => $ReamazeName,
			'email' => $Reamaze_email
			),
		),
	);
	
	//print_r($reamaze_conversation); exit;
	$curlSession = curl_init('https://Infusionsoft.reamaze.io/api/v1/conversations'); 
		  
	curl_setopt($curlSession, CURLOPT_HEADER, false);
	curl_setopt($curlSession,CURLOPT_USERPWD,implode(':', array('pieter@pieterkdevilliers.co.uk', '60eb0ada86180343044e0ced9b6dfcd43388249a560faef1ba6d37bd36d0b71e'))
		  );
	curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
	curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($reamaze_conversation));
	curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
										
	$conv_response = curl_exec($curlSession);
	$err = curl_error($curlSession);
	if ($err) 
	{
	  echo "CURL Error #:" . $err;
	} 
		  
	$conversation_data = json_decode($conv_response);
    //echo '<pre>';print_r($conversation_data);
	//Create Conversation after create contact End
		  
	//echo "<br/><br/>";
	$id = $conversation_data->author->id;
	$email = $conversation_data->author->email;
		  
	$IS_data = array(
		'_Contactid' => $id 
	);
		  
	//update Reamaze contact id in Is
	$contactId = $contact_id;
	//$app->updateCon($contactId, $IS_data);
    $infusionsoft->contacts('xml')->update($contactId, $IS_data);
		  
	//create a new message under a specific conversation Start
	$msg_data = array(
		'message' => array(
			'body' => $Reamaze_msg,
			'visibility' => 0
			),
		); 
			  
	if($id != '' &&  $email == $Reamaze_email)
	{
		$slug =  $conversation_data->slug;
		$curlSession = curl_init('https://Infusionsoft.reamaze.io/api/v1/conversations/'.$slug.'/messages');

		curl_setopt($curlSession, CURLOPT_HEADER, false);
		curl_setopt($curlSession,CURLOPT_USERPWD,implode(':', array('pieter@pieterkdevilliers.co.uk', '60eb0ada86180343044e0ced9b6dfcd43388249a560faef1ba6d37bd36d0b71e'))
					);
		curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
		curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($msg_data));
		curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					
		$response = curl_exec($curlSession);
		$err = curl_error($curlSession);

		curl_close($curlSession);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			echo $response;
		}
			 
	}
	//create a new message under a specific conversation End
	file_put_contents(CONTACT_MESSAGE_CRON_FILE_NAME, 'Date: '.$date."\n", FILE_APPEND); 
	file_put_contents(CONTACT_MESSAGE_CRON_FILE_NAME, 'Reamaze ContactId : '.$id."\n", FILE_APPEND);  
    file_put_contents(CONTACT_MESSAGE_CRON_FILE_NAME, print_r($response, true), FILE_APPEND);  
    file_put_contents(CONTACT_MESSAGE_CRON_FILE_NAME,"\n\n====================\n",FILE_APPEND); 
  }
  else if($_POST['ReamazecontactId'] != "" && $_POST['ReamazeMessage'] != "" && $_POST['ReamazeEmail'] != "")
  {
	$ReamazeName = $_POST['ReamazeName']; 
	$Reamaze_email = $_POST['ReamazeEmail']; 
	$Reamaze_msg= $_POST['ReamazeMessage']; 
	$Reamaze_phone = $_POST['ReamazePhone']; 
	$contact_id = $_POST['contactId'];
		   
	$curlSession = curl_init('https://Infusionsoft.reamaze.io/api/v1/conversations');

	curl_setopt($curlSession, CURLOPT_HEADER, false);
	curl_setopt($curlSession,CURLOPT_USERPWD,implode(':', array('pieter@pieterkdevilliers.co.uk', '60eb0ada86180343044e0ced9b6dfcd43388249a560faef1ba6d37bd36d0b71e')));
			
	curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
	curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
	$conver_response = curl_exec($curlSession);
			
	$err = curl_error($curlSession);

	curl_close($curlSession);

	if ($err) 
	{
		$response = $err;
	} 
	echo "<pre>";
		
	$msg_data = array(
		'message' => array(
			'body' => $Reamaze_msg,
			'visibility' => 0
			),
	);
			  
	
	$data = json_decode($conver_response);
	//print_r($data);
			
	$row = $data->total_count;
	for($i=0; $i<=$row; $i++)
	{
		$author_id = $data->conversations[$i]->author->id;
		$author_email = $data->conversations[$i]->author->email;
			  
		if($_POST['ReamazecontactId'] == $author_id && $_POST['ReamazeEmail'] == $author_email)
		{
		  $slug =  $data->conversations[$i]->slug;
		  $curlSession = curl_init('https://Infusionsoft.reamaze.io/api/v1/conversations/'.$slug.'/messages');

		  curl_setopt($curlSession, CURLOPT_HEADER, false);
		  curl_setopt($curlSession,CURLOPT_USERPWD,implode(':', array('pieter@pieterkdevilliers.co.uk', '60eb0ada86180343044e0ced9b6dfcd43388249a560faef1ba6d37bd36d0b71e'))
						);
		  curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
		  curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
		  curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "POST");
		  curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($msg_data));
		  curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
						
		  $response = curl_exec($curlSession);
		  $err = curl_error($curlSession);

		  curl_close($curlSession);

		  if ($err) {
			echo "cURL Error #:" . $err;
		  } else {
			echo $response;
		  }
		
		  file_put_contents(CONTACT_MESSAGE_CRON_FILE_NAME, 'Date: '.$date."\n", FILE_APPEND); 
		  file_put_contents(CONTACT_MESSAGE_CRON_FILE_NAME, 'Reamaze ContactId :'  .$author_id."\n", FILE_APPEND);  
		  file_put_contents(CONTACT_MESSAGE_CRON_FILE_NAME, print_r($response, true), FILE_APPEND);  
		  file_put_contents(CONTACT_MESSAGE_CRON_FILE_NAME,"\n\n====================\n",FILE_APPEND);
		  break;
		}
		else
		{
		   //Create Conversation after create contact Start
		   $reamaze_conversation = array(
			  'conversation' => array(
				'subject' => 'Default reamaze conversation',
				'category' => 'support',
							
				 'message' => array(
				  'body' => 'Default reamaze conversation'
				  ),
				  'user' => array(
				   'name' => $ReamazeName,
				   'email' => $Reamaze_email
				   ),
				),
			);
			
			//print_r($reamaze_conversation); exit;
			$curlSession = curl_init('https://Infusionsoft.reamaze.io/api/v1/conversations'); 
				  
			curl_setopt($curlSession, CURLOPT_HEADER, false);
			curl_setopt($curlSession,CURLOPT_USERPWD,implode(':', array('pieter@pieterkdevilliers.co.uk', '60eb0ada86180343044e0ced9b6dfcd43388249a560faef1ba6d37bd36d0b71e'))
				  );
			curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
			curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($reamaze_conversation));
			curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
												
			$conv_response = curl_exec($curlSession);
			$err = curl_error($curlSession);
			if ($err) 
			{
			  echo "CURL Error #:" . $err;
			} 
			
			$conversation_data = json_decode($conv_response);
			//echo '<pre>';print_r($conversation_data);
			//Create Conversation after create contact End
				  
			//echo "<br/><br/>";
			$id = $conversation_data->author->id;
			$email = $conversation_data->author->email;
				  
			$IS_data = array(
				'_Contactid' => $id 
			);
				  
			//update Reamaze contact id in Is
			$contactId = $contact_id;
			//$app->updateCon($contactId, $IS_data);
			$infusionsoft->contacts('xml')->update($contactId, $IS_data);
				  
			//create a new message under a specific conversation Start
			$msg_data = array(
				'message' => array(
					'body' => $Reamaze_msg,
					'visibility' => 0
					),
				); 
					  
			if($id != '' &&  $email == $Reamaze_email)
			{
				$slug =  $conversation_data->slug;
				$curlSession = curl_init('https://Infusionsoft.reamaze.io/api/v1/conversations/'.$slug.'/messages');

				curl_setopt($curlSession, CURLOPT_HEADER, false);
				curl_setopt($curlSession,CURLOPT_USERPWD,implode(':', array('pieter@pieterkdevilliers.co.uk', '60eb0ada86180343044e0ced9b6dfcd43388249a560faef1ba6d37bd36d0b71e'))
							);
				curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
				curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($msg_data));
				curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
							
				$response = curl_exec($curlSession);
				$err = curl_error($curlSession);

				curl_close($curlSession);

				if ($err) {
					echo "cURL Error #:" . $err;
				} else {
					echo $response;
				}
					 
			}
			//create a new message under a specific conversation End
			
			file_put_contents(CONTACT_MESSAGE_CRON_FILE_NAME, 'Date: '.$date."\n", FILE_APPEND); 
			file_put_contents(CONTACT_MESSAGE_CRON_FILE_NAME, 'Reamaze ContactId : '.$id."\n", FILE_APPEND);  
			file_put_contents(CONTACT_MESSAGE_CRON_FILE_NAME, print_r($response, true), FILE_APPEND);  
			file_put_contents(CONTACT_MESSAGE_CRON_FILE_NAME,"\n\n====================\n",FILE_APPEND); 
			 break;
		}
	}
  }
  
  $reamaze->writeToLog(CONTACT_MESSAGE_CRON_FILE_NAME, "\n\n"."****************** Invoice Cron End*******"."\n"); 
	
  exit;
?>