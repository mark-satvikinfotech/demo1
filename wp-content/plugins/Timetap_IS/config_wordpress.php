<?php

include_once('functions.php');

define('UPLOADS', 'wp-content/uploads');



//script and js added

add_action( 'admin_enqueue_scripts', array($timetapobj,'load_custom_wp_admin_style'));

add_action( 'admin_enqueue_scripts', array($timetapobj,'load_custom_wp_admin_script'));



//ajax script added

add_action('wp_ajax_ttapsync_credential', array($timetapobj,'timetap_sync_credential'));



register_activation_hook( __FILE__,array('Product_Goals_Definitions', 'product_goal_table_schema' ) );


?>