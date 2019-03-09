<?php 
  include "reamaze_data.php";
  include "config.php";
 
  $data = $_POST;
  
  $reamaze = new Reamaze_data();
 
  //$reamaze->writeToLog(CONTACT_CRON_FILE_NAME, "Test");
  
  $reamaze->writeToLog(CONTACT_CRON_FILE_NAME,print_r($data,true));
  
	  if(isset($_POST['contactId']) && isset($_POST['Email']))
	  {
		  if(isset($_POST['FirstName']) && !empty($_POST['FirstName']))
		  {
			$name = $_POST['FirstName'];
		  }
		  if(isset($_POST['ReamazeData']) && !empty($_POST['ReamazeData']))
		  {
			$ream_data = json_encode($_POST['ReamazeData']);
		  }
		  if(isset($_POST['Email']) && !empty($_POST['Email']))
		  {
			$email = $_POST['Email'];
		  }
		  
		  if(isset($_POST['FriendlyName']) && !empty($_POST['FriendlyName']))
		  {
			$friendly_name = $_POST['FriendlyName'];
		  }
		  
		  
		  //update contact in reamaze
		  $reamaze_data = array(
			"contact" => array(
				 "name" => $name,
				 "data" => $ream_data
				 )
		  );
	  
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

		  curl_close($curlSession);

		  if ($err) {
			echo "CURL Error #:" . $err;
			  
		  } 
		  
	  }
 
  
  //create a new message under a specific conversation Start
  
  
  //create a new message under a specific conversation End
  exit;
  
?>