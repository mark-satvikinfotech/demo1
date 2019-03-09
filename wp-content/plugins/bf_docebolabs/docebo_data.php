<?php
class Docebo_data
{
	public function __construct()
	{
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

?>