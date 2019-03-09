<?php
function manage_ttap_to_ifs()
{
		$url= 'https://api.timetap.com/test/';
 		$ts=time();
 		//generate MD5 hash
        $tempoptions = get_option("timetap_credential");
	     $cred_arr = unserialize($tempoptions);

	     $apikey = $cred_arr['tappkey'];
	     $privatekey = $cred_arr['tpkey'];

	   $signature = md5($apikey.$privatekey);
	   $curlSession = curl_init("https://api.timetap.com/test/sessionToken?apiKey=23402&timestamp=".$ts."&signature=".$signature);
       curl_setopt($curlSession, CURLOPT_HEADER, false);        
       curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
       curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
       $response = curl_exec($curlSession);         
       $err = curl_error($curlSession);
       //https://api.timetap.com/test/products?sessionToken=st:api:api:4e566173a84c4106a82827a05b5a4441

       curl_close($curlSession);
       if ($err)
       {
          $response = $err;

       }
      
       $session_data = json_decode($response);
       $sessiontoken=$session_data->sessionToken ;
       return $sessiontoken;
    
}



	
