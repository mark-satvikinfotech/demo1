<?php
/*
	if(realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
	{
	    exit('Please don\'t access this file directly.');
	}

	include ('config.php');

	
	//$myFile = plugin_dir_url(__FILE__).'Log.txt';
	$myFile = "http://69.195.124.141/~satvikso/project/demo/wp-content/plugins/orderforms-stripe/Log.txt";
	
	
	$fh = fopen($myFile, 'a+') or die("can't open file");
		
	$text .= "\r\nSTART Log: =========".date('Y-m-d h:i:s')."============ \n\n" ;
	fwrite($fh, $text);
	
	$text .= "POST: ======================= \n\n" ;
	fwrite($fh, $text);
	
	fwrite($fh, var_export($_REQUEST, TRUE));
	*/

?>