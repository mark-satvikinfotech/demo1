<?php
 error_reporting(1);
 ini_set('error_reporting', E_ALL); 
 include_once('timetap_config.php'); 
 include_once('timetap_data.php');
 
 $config = json_decode(file_get_contents($timetap_log_file_name),true);

 $current_page = $config['current_page'];
 $limit = $config['limit'];
 $last_updated_message = $config['last_updated'];
 
 $timetap = new Timetap_data();
 $sess_token = $timetap->get_sessiontoken($ts,$signature);
 $sessiontok = $sess_token->sessionToken;
 
 $message_data = $timetap->get_messages($current_page,$limit,$sessiontok);
 
 $messages = json_decode($message_data);
  
 $message_data1 = $timetap->get_total_messages($sessiontok);
 $data1 = json_decode($message_data1);
 $total_message = count($data1);
 
 if($total_message >= $last_updated_message)
 {
	$timetap->writeToLog(TIMETAP_MESSAGE_CRON_FILE_NAME,"\n\n"."****************** Message Cron Start*******"."\n");
    $date = date("Y-m-d H:i:s");
    $timetap->writeToLog(TIMETAP_MESSAGE_CRON_FILE_NAME,'Date: '.$date."\n");
    $timetap->writeToLog(TIMETAP_MESSAGE_CRON_FILE_NAME,'Total messages: '.$total_message."\n");
    $timetap->writeToLog(TIMETAP_MESSAGE_CRON_FILE_NAME,'Last updated messages: '.$last_updated_message."\n");
    $last_total_contacts = $config['total'];
	
	foreach($messages as $key => $value)
	{
		
		if($value->toAddress != 'null')
		{
		   $Timetapname = $value->toName; 
		   $Timetap_toemail = $value->toAddress; 
		   $timetap_fromemail = $value->fromAddress;
		   $Timetap_sub = $value->subject; 
		   $Timetap_date = $value->modifiedDate;
		  
		   $created_date = date("F j, Y, g:i a", $Timetap_date/1000); 
		   $Timetap_body = 'TimeTap Conversation | '. $created_date .'|';
		   
		   $Timetap_body .= $value->body;
		   $Timetap_emailcat = $value->emailCatagory;
		   $emailcat = $Timetap_emailcat->emailCatagory;
		   $EmailId = $value->emailId;
		   
		   $reamaze_conversation = array(
				'conversation' => array(
					'subject' => $Timetap_sub,
					'category' => 'support',
					
					'message' => array(
					   'body' => preg_replace('/\s+/', ' ', strip_tags($Timetap_body)),
					   'recipients' => $timetap_fromemail
					),
					'user' => array(
						'name' => $Timetapname,
						'email' => $Timetap_toemail
					),
				),
		    );
			
			
			$curlSession = curl_init('https://Infusionsoft.reamaze.io/api/v1/conversations?for='.$Timetap_toemail);

			curl_setopt($curlSession, CURLOPT_HEADER, false);
			curl_setopt($curlSession,CURLOPT_USERPWD,implode(':', array('pieter@pieterkdevilliers.co.uk', '60eb0ada86180343044e0ced9b6dfcd43388249a560faef1ba6d37bd36d0b71e'))
			);
			
			curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
			curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
			 curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "GET");
			$conver_response = curl_exec($curlSession);
			
			$err = curl_error($curlSession);

			curl_close($curlSession);

			if ($err) 
			{
			  $conver_response = $err;
			} 
			$data = json_decode($conver_response);
			
			$row = $data->total_count;
		
			if($value->body != '')
			{
				 $tbody = preg_replace('/\s+/', ' ', strip_tags($Timetap_body));
				 $t_body = str_replace('"', '', $tbody);
				$tmp_body = "";
			    for($i=0; $i<$row; $i++)
				{
				  $conv_body = $data->conversations[$i]->message->body;
					if($conv_body == $t_body)
					{
						$tmp_body = $t_body;
						break;
					}
			    }	
				if($tmp_body == "")
				{
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
				
				     $timetap->writeToLog(TIMETAP_MESSAGE_CRON_FILE_NAME,"\n"."Email ID: ".$EmailId."\n");
		             $timetap->writeToLog(TIMETAP_MESSAGE_CRON_FILE_NAME,print_r($reamaze_conversation,true));
					 
					 $config = json_decode(file_get_contents($timetap_log_file_name),true);
					 
		             $data_value = $config['last_updated']+ 1;
		             $timetap->update_LogFile("last_updated", $data_value, $timetap_log_file_name);
				 }
			}
		}
		
		
	}
	    
	$data_value = $config['current_page']+ 1;
	$timetap->update_LogFile("current_page", $data_value, $timetap_log_file_name);

	$current_total = $config['current_page'] * $config['limit'];
	if($current_total < $total_message)
	{
	  $timetap->update_LogFile("total", $total_message, $timetap_log_file_name);
	}
	$timetap->writeToLog(TIMETAP_MESSAGE_CRON_FILE_NAME,"\n\n"."****************** Message Cron End*******"."\n");
	echo "success";
 }
exit;
?>