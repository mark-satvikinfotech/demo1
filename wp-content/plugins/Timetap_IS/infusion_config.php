<?php
if(empty(session_id())) 
session_start();

require_once 'infusionsoft/vendor/autoload.php';

    $tempoptions = get_option("timetap_credential");
	$cred_arr = unserialize($tempoptions);		
    $client_id = $cred_arr['client_id'];
    $client_secrete = $cred_arr['client_secrete'];
	
    $inf_url = admin_url();
    $redirectUrl=$inf_url.'admin.php?page=get_product_services';
    $infusionsoft = new \Infusionsoft\Infusionsoft(array(
		'clientId'     => $client_id,
		'clientSecret' => $client_secrete,
		'redirectUri'  => $redirectUrl,

	));	
	
	//print_r($infusionsoft); exit;
   $get_token = get_option('accesstoken');
   //print_r($get_token);
   if(!empty($get_token))   {
     $access_token = unserialize($get_token);
     //print_r($access_token);
	 
	 $ap_name = explode('|',$access_token->extraInfo['scope']);
	 $ap_name = explode('.',$ap_name[1]);
	 $appname = $ap_name[0];
	 
     $access_tok=$access_token->accessToken;    
	 // echo $access_tok;
	 
     //echo $access_token->endOfLife;
	if( strtotime("now") > $access_token->endOfLife)    {
	   $infusionsoft->setToken($access_token);

       $infusionsoft->refreshAccessToken();	
	   
	   $token = serialize($infusionsoft->getToken());
	   $_SESSION['token'] = $token;

	   $upd_token =update_option("accesstoken",$token);
	   $access_tok = $upd_token->accessToken;		
	}
	else
	{
	  
	  $_SESSION['token'] =  serialize($access_token);
	  $access_tok = $access_token->accessToken;
	 
	}   
	
  } if(isset($access_tok) && $access_tok != '')   {	    echo "<h4>You are already authorize with Infusionsoft</h4>";   }
  

// If the serialized token is available in the session storage, we tell the SDK
// to use that token for subsequent requests.
if (isset($_SESSION['token'])) {
	$infusionsoft->setToken(unserialize($_SESSION['token']));
	//echo "<pre>";
	//print_r($infusionsoft);
}
	
  
// If we are returning from Infusionsoft we need to exchange the code for an
// access token.
if (isset($_GET['code']) and !$infusionsoft->getToken()) {
	
	$infusionsoft->requestAccessToken($_GET['code']);
   
	$token = serialize($infusionsoft->getToken());
    $_SESSION['token'] = $token;


	$id = "accesstoken";
	$option_exists = (get_option($id, null) !== null);

	if($option_exists) {
				
	  update_option($id, $token);
	}
	else 
	{
	  add_option($id, $token);
    }
}

else if (!$infusionsoft->getToken()) {
	
	echo '<a href="' . $infusionsoft->getAuthorizationUrl() . '">Click here to authorize</a>';
	
}

 

 

?>