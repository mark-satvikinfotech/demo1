<?php

  
  //echo '<pre>';print_r($_GET);
  $data = $_POST;
  file_put_contents(GEOPAL_DIR.'uploads/log.txt', print_r($data, true), FILE_APPEND); exit;
  //Changing Job Customer
   $cust_data = array();
    if(isset($_POST['_jobid0']) || isset($_POST['_Appointmentcustomerid']))
    {
		$cust_data = array(  
			'job_id' => $_POST['_jobid0'],
			'customer_id' => $_POST['_Appointmentcustomerid']
	   );
    }
		
	if(isset($_POST['_Appointmentcustomername']) && !empty($_POST['_Appointmentcustomername']))
	{
	  $cust_data['customer_name'] = $_POST['_Appointmentcustomername'];
	}
	
	if(isset($_POST['_Appointmentcustomeridentifier']) && !empty($_POST['_Appointmentcustomeridentifier']))
	{
	  $cust_data['customer_identifier'] = $_POST['_Appointmentcustomeridentifier'];
	}

	if(isset($_POST['_Appointmentcustomerphoneoffice']) && !empty($_POST['_Appointmentcustomerphoneoffice']))
	{
	  $cust_data['customer_phone_office'] = $_POST['_Appointmentcustomerphoneoffice'];
	}
	
	if(isset($_POST['_Appointmentcustomeremail']) && !empty($_POST['_Appointmentcustomeremail']))
	{
	  $cust_data['customer_email'] = $_POST['_Appointmentcustomeremail'];
	}
		
	if(isset($_POST['_Appointmentcustomerupdatedon']) && !empty($_POST['_Appointmentcustomerupdatedon']))
	{
	   $cust_data['updated_on'] = $_POST['_Appointmentcustomerupdatedon'];
	}
	
	$curlSession = curl_init('https://app.geopalsolutions.com/api/jobs/changecustomer');
		
	curl_setopt($curlSession, CURLOPT_HEADER, false);
	curl_setopt($curlSession, CURLOPT_USERPWD,implode(':', array('contact@48hourlaunch.com', 'password1')));
								
	curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
	curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($curlSession, CURLOPT_POSTFIELDS, http_build_query($cust_data));
	$response = curl_exec($curlSession);
			
	$get_jb_data = json_decode($response);
	//echo '<pre>';print_r($get_jb_data); exit;
	
  
 
      //Changing Job Person
	  $per_data= array();
	  if(isset($_POST['_jobid0']) || isset($_POST['_Appointmentpersonid']))
	  {
	    $per_data = array(
			   'job_id' => $_POST['_jobid0'],
	  		   'person_id' => $_POST['_Appointmentpersonid']
	    );
	  }
	 
	  
	  if(isset($_POST['_Appointmentpersonidentifier']) && !empty($_POST['_Appointmentpersonidentifier']))
	  {
		 $per_data['person_identifier'] = $_POST['_Appointmentpersonidentifier'];
	  }
		
	  if(isset($_POST['FirstName']) && !empty($_POST['FirstName']))
	  {
	    $per_data['person_first_name'] = $_POST['FirstName'];
	  }
	  
      if(isset($_POST['LastName']) && !empty($_POST['LastName']))
	  {
	    $per_data['person_last_name'] = $_POST['LastName'];
	  }	
	  
	  if(isset($_POST['Phone1']) && !empty($_POST['Phone1']))
	  {
	    $per_data['person_phone_number'] =  $_POST['Phone1'];
	  }
	  
	  if(isset($_POST['Phone2']) && !empty($_POST['Phone2']))
	  {
		$per_data['person_mobile_number'] = $_POST['Phone2'];
	  }
	  
	  if(isset($_POST['Email']) && !empty($_POST['Email']))
	  {
		  $per_data['person_email'] = $_POST['Email'];
	  }
	  
	  if(isset($_POST['StreetAddress1']) && !empty($_POST['StreetAddress1']))
	  {
		 $per_data['person_address_line_1'] = $_POST['StreetAddress1'];
	  }
	  
	  if(isset($_POST['StreetAddress2']) && !empty($_POST['StreetAddress2']))
	  {
		 $per_data['person_address_line_2'] = $_POST['StreetAddress2'];
	  }
	  
	  if(isset($_POST['City']) && !empty($_POST['City']))
	  {
		 $per_data['person_city'] = $_POST['City'];
	  }
	  
	  if(isset($_POST['PostalCode']) && !empty($_POST['PostalCode']))
	  {
		$per_data['person_postal_code'] = $_POST['PostalCode'];  
	  }
	  
	  if(isset($_POST['_Appointmentaddresscountryid']) && !empty($_POST['_Appointmentaddresscountryid']))
	  {
		$per_data['person_country_id'] = $_POST['_Appointmentaddresscountryid'];
	  }
	   // print_r($per_data);
	    $curlSession = curl_init('https://app.geopalsolutions.com/api/jobs/changeperson');

	  curl_setopt($curlSession, CURLOPT_HEADER, false);
	  curl_setopt($curlSession, CURLOPT_USERPWD,implode(':', array('contact@48hourlaunch.com', 'password1')));
								
	  curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
	  curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
	  curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "POST");
	  curl_setopt($curlSession, CURLOPT_POSTFIELDS, http_build_query($per_data));
	  $response = curl_exec($curlSession);
			
	  $get_jb_data = json_decode($response);
	  
	 //Changing Job Address
	  $add_data = array();
	  if(isset($_POST['_jobid0']) || isset($_POST['_Appointmentaddressid']))
	  {
         $add_data = array(
			'job_id' => $_POST['_jobid0'],
			'address_id' => $_POST['_Appointmentaddressid']
		 );
	  }
	  
	  if(isset($_POST['StreetAddress1']) && !empty($_POST['StreetAddress1']))
	  {
		 $add_data['address_line_1'] = $_POST['StreetAddress1'];
	  }
	  
	  if(isset($_POST['StreetAddress2']) && !empty($_POST['StreetAddress2']))
	  {
	     $add_data['address_line_2'] = $_POST['StreetAddress2'];
	  }
	  
	  if(isset($_POST['City']) && !empty($_POST['City']))
	  {
		 $add_data['address_city'] =  $_POST['City'];
	  }
	  
	  if(isset($_POST['PostalCode']) && !empty($_POST['PostalCode']))
	  {
		 $add_data['address_postal_code'] = $_POST['PostalCode'];
	  }
	  
	  if(isset($_POST['_Appointmentaddresscountryid']) && !empty($_POST['_Appointmentaddresscountryid']))
	  {
		  $add_data['address_country_code'] = $_POST['_Appointmentaddresscountryid'];
	  }
	  
	  $curlSession = curl_init('https://app.geopalsolutions.com/api/jobs/changeaddress');

	  curl_setopt($curlSession, CURLOPT_HEADER, false);
	  curl_setopt($curlSession, CURLOPT_USERPWD,implode(':', array('contact@48hourlaunch.com', 'password1')));
								
	  curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
	  curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
	  curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "POST");
	  curl_setopt($curlSession, CURLOPT_POSTFIELDS, http_build_query($add_data));
	  $response = curl_exec($curlSession);
			
	  $get_jb_data = json_decode($response);
	
     //Changing Job Status, Address, Asset, Contact and Customer at the same time
  
	  /*if(isset($_POST['_jobid0']) || isset($_POST['_Appointmentstatusid']) || isset($_POST['_Appointmentaddressid']) || isset($_POST['_Appointmentassetidentifier']) || isset($_POST['_Appointmentpersonid']) || isset($_POST['_Appointmentcustomerid']))
	  {
		  
		   $curlSession = curl_init('https://app.geopalsolutions.com/api/jobs/changeall');

			curl_setopt($curlSession, CURLOPT_HEADER, false);
			curl_setopt(
			$curlSession,
			CURLOPT_USERPWD,
			implode(':', array('contact@48hourlaunch.com', 'password1'))
			);
									
			curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
			curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($curlSession, CURLOPT_POSTFIELDS, http_build_query($data));
			$response = curl_exec($curlSession);
				
			$get_jb_data = json_decode($response);
	  }*/
  
	//Updating and Creating an Asset
	/*$ast_data = array();
		if(isset($_POST['_Appointmentassetid']) && !empty($_POST['_Appointmentassetid']))
		{
		  $ast_data['asset_id'] = $_POST['_Appointmentassetid'];
		}
		
		if(isset($_POST['_Appointmentassetidentifier']) && !empty($_POST['_Appointmentassetidentifier']))
		{
		  $ast_data['asset_identifier'] = $_POST['_Appointmentassetidentifier'];
		}
		
		if(isset($_POST['_Appointmentassettemplateid']) && !empty($_POST['_Appointmentassettemplateid']))
		{
		  $ast_data['asset_template_id'] = $_POST['_Appointmentassettemplateid'];
		}
		
		if(isset($_POST['_Appointmentassetcompanystatusid']) && !empty($_POST['_Appointmentassetcompanystatusid']))
		{
		  $ast_data['asset_company_status_id'] = $_POST['_Appointmentassetcompanystatusid'];
		}
		print_r($ast_data);
	    $curlSession = curl_init('https://app.geopalsolutions.com/api/assets/replace');

		curl_setopt($curlSession, CURLOPT_HEADER, false);
		curl_setopt(
		$curlSession,
		CURLOPT_USERPWD,
		implode(':', array('hes0012', '48hrlaunch'))
		);
								
		curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
		curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curlSession, CURLOPT_POSTFIELDS, http_build_query($ast_data));
		$response = curl_exec($curlSession);
			
		$get_jb_data = json_decode($response);
		print_r($get_jb_data);*/
  exit;
  
?>