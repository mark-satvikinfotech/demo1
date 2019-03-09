<?php  class Infusionsoft_data{
	function get_IS_emailhistory($acs_tok,$limit,$offset)
    {	
	    
		//echo $access_tok; exit;
		$url = file_get_contents("https://api.infusionsoft.com/crm/rest/v1/emails?limit=".$limit."&offset=".$offset."&access_token=".$acs_tok);
		
		$data = json_decode($url);		//echo "<pre>" ;print_r($data); exit;
        return $data;
	}
    function get_IS_emailcontent($acs_tok,$id)
    { 
        
        $url = file_get_contents("https://api.infusionsoft.com/crm/rest/v1/emails/".$id."?access_token=".$acs_tok);
        $data = json_decode($url);
        //print_r($data);exit;
        if($data->html_content != '')
            $body = $data->html_content;
        if($data->plain_content != '')
            $body = $data->plain_content;
        return $body;
    }
	/*function get_campaign()
	{
	  include('infusion_config.php');	
		
	  $cron =  $infusionsoft->funnels()->achieveGoal('xd295', 'Reamazemsg', 36792);
	  return $con;
	}*/
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
?>