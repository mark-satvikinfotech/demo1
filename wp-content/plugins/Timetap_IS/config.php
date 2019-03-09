<?php
//echo TIMETAP_DIR; exit;
$config_log_file_name = TIMETAP_DIR."config.txt";

$IS_config_log_file_name = TIMETAP_DIR."is_config.txt";

$reamaze_config_log_file_name = TIMETAP_DIR."reamaze_config.txt";

define('CONTACT_LOG_FILE_NAME', TIMETAP_DIR.'log/'.date('Y-m-d',time()).'.txt');

define('REAMAZE_CONTACT_LOG_FILE_NAME', TIMETAP_DIR.'log/'.date('Y-m-d',time()).'.txt');

define('CONTACT_CRON_FILE_NAME', TIMETAP_DIR.'log/Reamaze_Cron/'.date('Y-m-d',time()).'.txt');

define('OLD_CONTACT_CRON_FILE_NAME',TIMETAP_DIR.'log/Reamaze_Cron/old_'.date('Y-m-d',time()).'.txt');

define('CONTACT_EMAIL_CRON_FILE_NAME',TIMETAP_DIR.'log/cron/email_'.date('Y-m-d',time()).'.txt');

define('CONTACT_MESSAGE_CRON_FILE_NAME',TIMETAP_DIR.'log/IS_Textconversation/'.date('Y-m-d',time()).'.txt');


define('CONTACT_MERGE_FILE_NAME', TIMETAP_DIR.'log.txt');

?>