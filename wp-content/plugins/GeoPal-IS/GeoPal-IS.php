<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/*
Plugin Name: GeoPal-Infusionsoft Sync
Description: Help to synchronize data between GeoPal and Infusionsoft. Plugin pushes data from GeoPal to Infusionsoft and vice-versa. 
Version: 1.0 | By <a href="https://www.hikebranding.com" target="_blank">Hike Branding</a> 
*/

add_action('admin_menu','geopal_modifymenu');

function geopal_modifymenu() {
	
	//this is the main item for the menu
	add_menu_page('GeoPal-IS', //page title
	'GeoPal-IS', //menu title
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
	'IS Tags', //page title
	'IS Tags', //menu title
	'manage_options', //capability
	'manage_customtags', //menu slug
	'manage_customtags'); //
	
	//custom tags submenu
	add_submenu_page('manage_settings', //parent slug
	'Help', //page title
	'Help', //menu title
	'manage_options', //capability
	'manage_help', //menu slug
	'manage_help'); //
	
	add_submenu_page('geo_to_ifs', //parent slug
	'geo_to_ifs', //page title
	'geo_to_ifs', //menu title
	'manage_options', //capability
	'manage_geo_to_ifs', //menu slug
	'manage_geo_to_ifs'); //function
	
	
	
}

//require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

define('GEOPAL_DIR', plugin_dir_path(__FILE__));
define('GEOPAL_PLUGIN_URL',plugin_dir_url(__FILE__));

include_once('config.php');
include_once('functions.php');
require_once('settings.php');
require_once('custom_tags.php');
require_once('help.php');

//custom updates/upgrades
/*$this_file_wphelp7vik = __FILE__;
$update_check_wphelp7vik = "http://69.195.124.141/~satvikso/live_sites/hikebranding.com/keys/security_hb.chk";
if(is_admin()){
  require_once('gill-updates-wphelp7vik.php');
}*/




