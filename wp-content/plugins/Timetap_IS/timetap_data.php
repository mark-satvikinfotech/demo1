<?php
class Timetap_data
{
	public function __construct()
	{
		/*if(isset($_REQUEST['Timetapmessagecron']))
		{
			$Timetapmessagecron = $_REQUEST['Timetapmessagecron']; 
			if(strcasecmp($Timetapmessagecron, "Y") == 0)
			{
			  include_once('timetap_cron.php');
			}
		}*/
	}
	function get_sessiontoken($ts,$signature)
	{
	   $curlSession = curl_init("https://api.timetap.com/test/sessionToken?apiKey=23402&timestamp=".$ts."&signature=".$signature);

	   curl_setopt($curlSession, CURLOPT_HEADER, false);
				
				
	   curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
	   curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
	   $response = curl_exec($curlSession);
				
	   $err = curl_error($curlSession);

	   curl_close($curlSession);
	   if ($err) 
	   {
		  $response = $err;
	   } 
	   
	   $session_data = json_decode($response);
	   return $session_data;
	}
	function get_messages($current_page,$limit,$sessionToken)
	{
		
		$curlSession = curl_init('https://api.timetap.com/test/emails?sessionToken='.$sessionToken.'&pageNumber='.$current_page.'&pageSize='.$limit);

		curl_setopt($curlSession, CURLOPT_HEADER, false);
		curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
		curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curlSession);
		
		$err = curl_error($curlSession);

		curl_close($curlSession);

		if ($err) 
		{
		  $response = $err;
		} 

		return $response;
				 
	}
	function get_total_messages($sessionToken)
	{
		$curlSession = curl_init('https://api.timetap.com/test/emails?sessionToken='.$sessionToken);

		curl_setopt($curlSession, CURLOPT_HEADER, false);
		curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
		curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curlSession);
		
		$err = curl_error($curlSession);

		curl_close($curlSession);

		if ($err) 
		{
		  $response = $err;
		} 

		return $response;
	}
	function timetap_invoice($status,$current_page,$limit,$sessionToken)
    {
	    $curlSession = curl_init('https://api.timetap.com/test/invoices/status/'.$status.'?pageNumber='.$current_page.'&pageSize='.$limit.'&sessionToken='.$sessionToken);

	    curl_setopt($curlSession, CURLOPT_HEADER, false);
		curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
		curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curlSession);
		
		$err = curl_error($curlSession);

		curl_close($curlSession);

		if ($err) 
		{
		  $response = $err;
		} 

		return $response;
		//print_r($response);

   }
    function get_client_appointment($sessionToken,$client_Id)
    {
	   $curlSession = curl_init('https://api.timetap.com/test/appointments/report?sessionToken='.$sessionToken.'&clientId='.$client_Id);

	   curl_setopt($curlSession, CURLOPT_HEADER, false);
	   curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
	   curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
	   $response = curl_exec($curlSession);
		
	   $err = curl_error($curlSession);

	   curl_close($curlSession);

	   if($err) 
	   {
		 $response = $err;
	   } 
	   return $response;
   }
    function get_client_appointmentbydate($sessionToken,$client_Id,$pre_dt)
    {
	   $curlSession = curl_init('https://api.timetap.com/test/appointments/report?sessionToken='.$sessionToken.'&clientId='.$client_Id.'&startDate='.$pre_dt);

	   curl_setopt($curlSession, CURLOPT_HEADER, false);
	   curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
	   curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
	   $response = curl_exec($curlSession);
		
	   $err = curl_error($curlSession);

	   curl_close($curlSession);

	   if($err) 
	   {
		 $response = $err;
	   } 
	   return $response;
   }
    function previousdt($app_prev, $findate)
    {
		$newDates = array();
		
		foreach($app_prev as $date)
		{
			$newDates[] = strtotime($date);
		}
        //print_r($newDates);
		//echo strtotime($findate);
		
		$finalpre_date = "";
		sort($newDates);
		foreach ($newDates as $a)
		{
		  if ($a <= strtotime($findate))
			  $finalpre_date =  $a."<br/>";
				
		}
		return $finalpre_date;
		//return end($newDates);
   }
    function nextdt($app_prev, $findate)
    {
		$newDates = array();
		foreach($app_prev as $date)
		{
			$newDates[] = strtotime($date);
		}

		$final_date = "";
		sort($newDates);
		foreach ($newDates as $a)
		{
		  if ($a >= strtotime($findate))
			  $final_date = $a;
				
		}
		return $final_date;
		
   }
    function get_total_invoice($status,$sessionToken)
    {
	    $curlSession = curl_init('https://api.timetap.com/test/invoices/status/'.$status.'?sessionToken='.$sessionToken);

	    curl_setopt($curlSession, CURLOPT_HEADER, false);
		curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
		curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curlSession);
		
		$err = curl_error($curlSession);

		curl_close($curlSession);

		if ($err) 
		{
		  $response = $err;
		} 

		return $response;
   }
    function get_product($sessionToken,$productId)
    {
	    $curlSession = curl_init('https://api.timetap.com/test/products/'.$productId.'?sessionToken='.$sessionToken);

	    curl_setopt($curlSession, CURLOPT_HEADER, false);
		curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
		curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curlSession);
		
		$err = curl_error($curlSession);

		curl_close($curlSession);

		if ($err) 
		{
		  $response = $err;
		} 

		return $response;
	   
   }
    function IS_product_data()
    {
	   $curlSession = curl_init('https://api.infusionsoft.com/crm/rest/v1/products?limit=10&offset=0&active=true&access_token=m53zgjdqxuncxa9p4nzrb3nb');
	   
   }
	function writeToLog($file,$string)
	{
    	$open = fopen($file, "a"); 
    	$write = fputs($open, $string); 
    	fclose($open);
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
	function get_clients($sessiontok,$search)
	{
		
		$curlSession = curl_init('https://api.timetap.com/test/clients/filter?sessionToken='.$sessiontok); 
		  
		  curl_setopt($curlSession, CURLOPT_HEADER, false);
		  
		  curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
		  curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, TRUE);
		  curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "POST");
		  curl_setopt($curlSession, CURLOPT_POSTFIELDS, $search);
		  curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
										
		
		  
		    $response = curl_exec($curlSession);
			$err = curl_error($curlSession);

			curl_close($curlSession);

			if ($err) {
			  echo "cURL Error #:" . $err;
			} else {
			  return $response;
			}
		

	}
}
$obj = new Timetap_data();
?>