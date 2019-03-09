<?php
/*
Plugin Name: Timetap-Infusionsoft Sync
Description: Help to synchronize data between Timetap and Infusionsoft. Plugin pushes data from Timetap to Infusionsoft and vice-versa. 
Version: 1.0 | By <a href="https://www.hikebranding.com" target="_blank">Hike Branding</a> 
*/

add_action('admin_menu','timetap_modifymenu');

function timetap_modifymenu() {
	
	//this is the main item for the menu
	add_menu_page('Timetap-IS', //page title
	'Timetap-IS', //menu title
	'manage_options', //capabilities
	'manage_settings', //menu slug
	'manage_settings' //function
	);

	//custom tags submenu
	add_submenu_page('manage_settings', //parent slug
	'Settings', //page title
	'Settings', //menu title
	'manage_options', //capability
	'manage_settings', //menu slug
	'manage_settings'); //function


		//custom tags submenu
	add_submenu_page('manage_settings', //parent slug
	'Help', //page title
	'Help', //menu title
	'manage_options', //capability
	'manage_help', //menu slug
	'manage_help'); //

	

	add_submenu_page('manage_settings', //parent slug
	'Timetap-Products-IFS', //page title
	'Timetap-Products-IFS', //menu title
	'manage_options', //capability
	'get_product_services', //menu slug
	'get_product_services'); //

	add_submenu_page('timetap_services', //parent slug
	'timetap_services', //page title
	'timetap_services', //menu title
	'manage_options', //capability
	'get_timetap_services', //menu slug
	'get_timetap_services'); //function
	

	add_submenu_page('ttap_to_ifs', //parent slug
	'ttap_to_ifs', //page title
	'ttap_to_ifs', //menu title
	'manage_options', //capability
	'manage_ttap_to_ifs', //menu slug
	'manage_ttap_to_ifs'); //function
	
	add_submenu_page('is_tag', //parent slug
	'is_tag', //page title
	'is_tag', //menu title
	'manage_options', //capability
	'get_is_tag', //menu slug
	'get_is_tag'); //function
		
}

define('TIMETAP_DIR', plugin_dir_path(__FILE__));
define('TIMETAP_DIR_PLUGIN_URL',plugin_dir_url(__FILE__));


require('timetap_to_ifs.php');
require('timetap_services.php');
require('ttap_to_ifs.php');
require('settings.php');
include('functions.php');
//include('Timetap_Invoiceopen_IS.php');
//include('Timetap_Invoiceclosed_IS.php');
//include('Timetap_Invoicevoid_IS.php');
include('config_wordpress.php');
include('IS_tag.php');
include('help.php');




/*include('config_wordpress.php');
include('functions.php');
require('settings.php');
require('ttap_to_ifs.php');
require('help.php');
require('timetap_to_ifs.php');
require('timetap_services.php');
require('infusion_config.php');
require('class-definitions.php');*/




function product_goal_table_schema() {

   global $wpdb;
   $table_name =$wpdb->prefix ."product_goals";
   $charset_collate = $wpdb->get_charset_collate();
   $sql = "CREATE TABLE $table_name (
           `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
           `pro_ser_id` BIGINT(30) NOT NULL,
           `pro_ser_name` varchar(500) NOT NULL,
           `goal_name` varchar(500) NOT NULL,
            PRIMARY KEY  (id)
           ) $charset_collate; ";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
           dbDelta($sql);
   }

   register_activation_hook(__FILE__, 'product_goal_table_schema');

  










