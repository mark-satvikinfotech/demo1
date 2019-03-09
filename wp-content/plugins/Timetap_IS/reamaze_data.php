<?php
class Reamaze_data
{
	public function __construct()
	{
		
		/*if(isset($_REQUEST['Reamazecron']))
		{
			$Reamazecron = $_REQUEST['Reamazecron']; 
			if(strcasecmp($Reamazecron, "Y") == 0)
			{
			  include_once('reamaze_cron.php');
			}
		}
		if(isset($_REQUEST['ReamazeMessagecron']))
		{
			echo "Helo";
			$ReamazeMessagecron = $_REQUEST['ReamazeMessagecron']; 
			if(strcasecmp($ReamazeMessagecron, "Y") == 0)
			{
			  include_once('reamaze_textconversationcron.php');
			}
		}*/
	}
	function get_contacts($current_page)
	{
       /* $curlSession = curl_init('https://Infusionsoft.reamaze.io/api/v1/contacts?=&sort=date&page='.$current_page);
		curl_setopt($curlSession, CURLOPT_HEADER, false);
		curl_setopt($curlSession,CURLOPT_USERPWD,implode(':', array('pieter@pieterkdevilliers.co.uk', '60eb0ada86180343044e0ced9b6dfcd43388249a560faef1ba6d37bd36d0b71e'))
	    );
		curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
		curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curlSession);
		
		$err = curl_error($curlSession);

		curl_close($curlSession);

		if ($err) 
		{
		  $response = $err;
		} 
		print_r($response); exit;
		return $response;*/		$curl = curl_init();			curl_setopt_array($curl, array(			  CURLOPT_URL => "https://infusionsoft.reamaze.io/api/v1/contacts?sort=date&page=1",			  CURLOPT_RETURNTRANSFER => true,			  CURLOPT_ENCODING => "",			  CURLOPT_MAXREDIRS => 10,			  CURLOPT_TIMEOUT => 30,			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,			  CURLOPT_CUSTOMREQUEST => "GET",			  CURLOPT_HTTPHEADER => array(				"authorization: Basic cGlldGVyQHBpZXRlcmtkZXZpbGxpZXJzLmNvLnVrOjYwZWIwYWRhODYxODAzNDMwNDRlMGNlZDliNmRmY2Q0MzM4ODI0OWE1NjBmYWVmMWJhNmQzN2JkMzZkMGI3MWU=",				"cache-control: no-cache",				"postman-token: 6f2661d7-c4c4-0db4-afe5-145e035a9107"			  ),			));			$response = curl_exec($curl);			$err = curl_error($curl);			curl_close($curl);			if ($err) {			  echo "cURL Error #:" . $err;			  			} else {			  return $response;			}
	}
	
	function writeToLog($file,$string)
	{
		
    	$open = fopen( $file, "a" ); 
    	$write = fputs( $open, $string); 
    	fclose( $open );
	}
	function update_LogFile($upd_data, $data_value, $config_log_file_name)
    {
        $config = json_decode(file_get_contents($config_log_file_name),true);    
        
        foreach($config as $key => $val)
        {    
            if($key == $upd_data)
            {
                $config[$key] = $data_value;
            }        
        }    
        file_put_contents($config_log_file_name, '');
        $config = json_encode($config);
        $this->writeToLog($config_log_file_name, $config."\n");        
    }
}
$obj = new Reamaze_data();
?>