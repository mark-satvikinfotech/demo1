<?php
include_once('functions.php');
define('UPLOADS', 'wp-content/uploads');

//script and js added
add_action( 'admin_enqueue_scripts', array($geopalobj,'load_custom_wp_admin_style'));
add_action( 'admin_enqueue_scripts', array($geopalobj,'load_custom_wp_admin_script'));

//ajax script added
add_action('wp_ajax_geosync_credential', array($geopalobj,'geopal_sync_credential'));
add_action('wp_ajax_geoappnt_create', array($geopalobj,'geopal_appointment_create'));
?>