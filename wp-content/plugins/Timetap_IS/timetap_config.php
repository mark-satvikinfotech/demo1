<?php
 
  $timetap_log_file_name = TIMETAP_DIR.'log/Timetap_message/config.txt';
  
  $timetap_invoice_log_file_name = TIMETAP_DIR.'log/Timetap_Invoice/config.txt';
  $timetap_invoiceclosed_log_file_name = TIMETAP_DIR.'log/Timetap_Invoice/config_closed.txt';
  $timetap_invoicevoid_log_file_name = TIMETAP_DIR.'log/Timetap_Invoice/config_void.txt';
  
  
  define('TIMETAP_MESSAGE_CRON_FILE_NAME',TIMETAP_DIR.'log/Timetap_message/'.date('Y-m-d',time()).'.txt');
  
  define('TIMETAP_INVOICE_CRON_FILE_NAME',TIMETAP_DIR.'log/Timetap_Invoice/open/'.date('Y-m-d',time()).'.txt');
  define('TIMETAP_INVOICECLOSED_CRON_FILE_NAME',TIMETAP_DIR.'log/Timetap_Invoice/closed/'.date('Y-m-d',time()).'.txt');
  define('TIMETAP_INVOICEVOID_CRON_FILE_NAME',TIMETAP_DIR.'log/Timetap_Invoice/void/'.date('Y-m-d',time()).'.txt');
  
    $tempoptions = get_option("timetap_credential"); 
	$cred_arr = unserialize($tempoptions);		
	$apikey = $cred_arr['tappkey']; 
	$privatekey = $cred_arr['tpkey'];  
  //$apikey='23402';  
  //$privatekey='9bb476792d24f8a904e562a54ce2t56';
  $url= 'https://api.timetap.com/test/'; 
  $ts=time();  
  //generate MD5 hash  
  $signature = md5($apikey.$privatekey);
  ?>