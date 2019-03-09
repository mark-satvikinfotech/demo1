<?php

function get_timetap_services(){

	$sessionToken=manage_ttap_to_ifs();

   $curlSession = curl_init('https://api.timetap.com/test/services?sessionToken='.$sessionToken);

        curl_setopt($curlSession, CURLOPT_HEADER, false);
        curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curlSession);
        $err = curl_error($curlSession);
        //curl_close($curlSession);
        if ($err)
        {
          $response = $err;
        }

        //print_r($response);

           $responseAsAnArray = (is_null($response) || ($response === false))? array(): json_decode($response, true);
				 
				 $get_services_data= json_decode($response);

				return $get_services_data;

        		



}