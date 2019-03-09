<?php

namespace InfusionCrafting;

use Infusionsoft\Http\HttpException;
use Infusionsoft\TokenExpiredException;
use Infusionsoft\Infusionsoft;
use Infusionsoft\Token;

error_reporting(E_ALL);
ini_set('display_errors', 1);

	//get client_id and secret from DB
	$config = [];
	$config[ 'clientId' ] = get_option('infusioncrafting_client_id');
    $config[ 'clientSecret' ] = get_option('infusioncrafting_client_secret');
	$config[ 'token' ] = get_option('infusioncrafting_token'); 
	
    $config[ 'redirectUri' ] = admin_url('/admin-ajax.php?action=infusioncrafting_authorize');
	
	// create object for infusionsoft
	$internalClient = new Infusionsoft($config);
	
		//refreshAccessToken
		$token = unserialize($config[ 'token' ]);
		$internalClient->setToken($token);
	    $internalClient->refreshAccessToken();
		
	    $tokenNew = serialize($internalClient->getToken());
		$updateTime = date('Y-m-d H:i:s');
		//updateToken to DB
		$updateToken = update_option('infusioncrafting_token', $tokenNew);
		$updateTokenMsg = update_option( 'infusioncrafting_TokenUpdateTime', $updateTime , 'yes' );
		// LOG START
		
			// make Directory
			
			//$directoryName = WPMU_PLUGIN_DIR."/infusionsoft-oauth/Log";
			
				/* if(!is_dir($directoryName)){
					//Directory does not exist, so lets create it.
					mkdir($directoryName);
				} */
			
			$date = date('Y-m-d');
			$createdirectory = wp_upload_dir();
			
			//echo "<pre>";print_r($createdirectory['basedir']);exit;
			//$directoryPath = WPMU_PLUGIN_DIR. "/infusionsoft-oauth/Log/$date.txt";
			$directoryName = $createdirectory['basedir']."/LogIStoken";
			if(!is_dir($directoryName)){
					//Directory does not exist, so lets create it.
					mkdir($directoryName,0777);
				}
				
			$directoryPath = $directoryName."/$date.txt";
			echo $directoryPath;
			echo get_option( 'infusioncrafting_TokenUpdateTime' );
			//Create datewise log file
			$cronfile = fopen($directoryPath, "a") or die("Unable to open file!");
			
			//append data to log text file
			$start = "*********************CRON start*********************\n\n";
			fwrite($cronfile, $start);
			
			$txt = date("d/m/Y H:i:s")."\n\n";
			fwrite($cronfile, $txt);
			$txt = "Previous Token -->> ".$config[ 'token' ]."\n\n";
			fwrite($cronfile, $txt);
			$txt = "New Token -->> ".$tokenNew."\n\n";
			fwrite($cronfile, $txt);
			
			$end =   "**********************CRON end**********************\n\n";
			fwrite($cronfile, $end);
			
			fclose($cronfile);
		
		// LOG END
			
?>