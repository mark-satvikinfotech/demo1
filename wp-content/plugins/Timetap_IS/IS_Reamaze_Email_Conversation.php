<?php
 error_reporting(1);
 ini_set('error_reporting', E_ALL);
 include "config_reamaze.php";
 include('infusion_config.php');
 include "infusionsoft_data.php";
 
 $acs_tok = $access_tok; 
 
 $config = json_decode(file_get_contents($config_log_file_name),true);
 
 $current_page = $config['current_page'];
 $limit = $config['limit'];
 $offset = $config['offset'];
 $last_updated_emails = $config['offset'];
 $infusion = new Infusionsoft_data();
 
 $email_data = $infusion->get_IS_emailhistory($acs_tok,$limit,$offset);
 
 $total_emails = $email_data->count;
 
 if(empty($email_data))
 {
	$last_value = $config['last_updated']+ 5;
	$infusion->update_LogFile("last_updated", $last_value, $config_log_file_name);
	$infusion->update_LogFile("offset", $last_value, $config_log_file_name);
	
	$data_value = $config['current_page']+ 1;
	$infusion->update_LogFile("current_page", $data_value, $config_log_file_name);
		
	$current_total = $config['current_page'] * $config['limit'];
	if($current_total < $total_emails)
	{
	  $infusion->update_LogFile("total", $total_emails, $config_log_file_name);
	} 
	exit;
 }
  $emails_data = json_decode(json_encode($email_data),true);

  $email = array();
  $emails_data = array();

  if($total_emails > $last_updated_emails)
  {
	$infusion->writeToLog(CONTACT_EMAILHISTORY_CRON_FILE_NAME,"\n\n"."****************** Email History Cron Start*******"."\n");
    $date = date("Y-m-d H:i:s");
    $infusion->writeToLog(CONTACT_EMAILHISTORY_CRON_FILE_NAME,'Date: '.$date."\n");
    $infusion->writeToLog(CONTACT_EMAILHISTORY_CRON_FILE_NAME,'Total emails: '.$total_emails."\n");
    $infusion->writeToLog(CONTACT_EMAILHISTORY_CRON_FILE_NAME,'Last updated emails: '.$last_updated_emails."\n");
    $last_total_emails = $config['total'];
	
	$email = $email_data->emails;
	
    foreach($email as $key => $value)
    {
	   if($value->sent_to_address != '' || $value->sent_from_address != '')
	   {
		  $eml['id'] = $value->id;
		  $eml['subject'] = $value->subject;
		  $eml['headers'] = $value->headers;
		  $eml['contact_id'] = $value->contact_id;
		  $eml['sent_to_address'] = $value->sent_to_address;
		  $eml['sent_from_address'] = $value->sent_from_address;
		  $eml['sent_date'] = $value->sent_date;
		  $eml['received_date'] = $value->received_date;
		  $eml['opened_date'] = $value->opened_date;
		  $eml['clicked_date'] = $value->clicked_date;
		  $eml['original_provider'] = $value->original_provider;
		  $email_body = $infusion->get_IS_emailcontent($acs_tok,$value->id);
		  $eml['body'] = base64_decode($email_body);
		  //echo "<pre>"; print_r($eml['body']); 
		  array_push($emails_data,$eml); 
		}
	}
	
	if(!empty($emails_data))
	{   
	  foreach($emails_data as $key => $value)
	  {
		  $subject = $value['subject'];
		  $Id = $value['id'];
		  $category = "support";
		  $tag_list = "";
		  $message = $value['headers'];
		  $name ="";
		  
		  $body = 'Infusionsoft Conversation | ';
		  $body .= $value['body'];
		  $recipients = $value['sent_from_address'];
		  $user_email = $value['sent_to_address'];
		  
		  $reamaze_email_data = array(
		     "conversation" => array(
			     "subject" => $subject,
				 "category" => $category,
				 "tag_list" => $tag_list,
				 "message" => array(
				       "body"=>  preg_replace('/\s+/', ' ', strip_tags($body)),
				       "recipients" => array(0=>$value['sent_from_address']),
					   "suppress_notification" => true
				   ),
				  "user" => array(
				  		"name" => $name,
				  		"email" => $user_email,
				  	),   
				 ),
			 );
		 
		 $curlSession = curl_init('https://Infusionsoft.reamaze.io/api/v1/conversations?for='.$user_email);

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
			
		 if($value['body'] != '')
		 {
				$Isbody = preg_replace('/\s+/', ' ', strip_tags($body));
				$IS_body = str_replace('"', '', $Isbody);
				$tmp_body = "";
			    for($i=0; $i<$row; $i++)
				{
				    $conv_body = $data->conversations[$i]->message->body;
					if($conv_body == $IS_body)
					{
						$tmp_body = $IS_body;
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
					curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($reamaze_email_data));
					curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
													
					$conv_response = curl_exec($curlSession);
					$err = curl_error($curlSession);
					if ($err) 
					{
						echo "CURL Error #:" . $err;						  
					} 
					$conversation_data = json_decode($conv_response);
					//print_r($conversation_data); exit;
					$infusion->writeToLog(CONTACT_EMAILHISTORY_CRON_FILE_NAME,"\n"."ID: ".$Id."\n");
					
		            $infusion->writeToLog(CONTACT_EMAILHISTORY_CRON_FILE_NAME,print_r($reamaze_email_data,true));
					  
					$infusion->writeToLog(CONTACT_EMAILHISTORY_CRON_FILE_NAME,"\n\n");	
		            $config = json_decode(file_get_contents($config_log_file_name),true);
		 
		            $data_value = $config['last_updated']+ 1;
		            $infusion->update_LogFile("last_updated", $data_value, $config_log_file_name);
					  
		            $infusion->update_LogFile("offset", $data_value, $config_log_file_name);
				} 
		 } 
	  }
		$data_value = $config['current_page']+ 1;
		$infusion->update_LogFile("current_page", $data_value, $config_log_file_name);
		
		$current_total = $config['current_page'] * $config['limit'];
		if($current_total < $total_emails)
		{
		  $infusion->update_LogFile("total", $total_emails, $config_log_file_name);
		} 
		
	}
   $infusion->writeToLog(CONTACT_EMAILHISTORY_CRON_FILE_NAME,"****************** Email History Cron End *************"."\n\n");
   echo "Cron Run Successfully";
  }
  
exit;
?>