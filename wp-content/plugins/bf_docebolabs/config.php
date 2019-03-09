<?php
$config_log_file_name =  plugin_dir_path(__FILE__)."config.txt";
$course_config_log_file_name =  plugin_dir_path(__FILE__)."courseconfig.txt";
$purchase_config_log_file_name =  plugin_dir_path(__FILE__)."purchaseconfig.txt";
$config_course_file_name = plugin_dir_path(__FILE__).'log/'.date('Y-m-d').'/compare_coursedata.txt';
define('COMP_COURSEDATA', plugin_dir_path(__FILE__).'log/'.date('Y-m-d'));
/*define('CSV_FILE_COPY','log/'.date('Y-m-d'));
define('ALREADY_UPDATED_MCNT', 'log/'.date('Y-m-d').'/already_updated.txt');
define('PAST_UPDATES_MCNT', 'log/'.date('Y-m-d').'/past_updates.txt');
define('NOT_FOUND_MCNT', 'log/'.date('Y-m-d').'/not_found.txt');
define('DEVELOPER_MCNT', 'log/'.date('Y-m-d').'/developer_log_'.time().'.txt');
define('CSV_FILE_DATA','log/'.date('Y-m-d').'/csv_file.txt');*/
?>