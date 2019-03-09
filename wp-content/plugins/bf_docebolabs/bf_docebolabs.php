<?php
/**
 * Plugin Name: DoceboLabs
 * Plugin URI: https://barefoot-labs.net/
 * Description: Barefoot Funnels Docebo/Wordpress Plugin this plugin adds Docebo/Wordpress intergration
 * Version: 0.4.2
 * Date: 16th January 2019
 * Author: Chris Mason
 * Author URI: http://orangewidow.com
 * License: GPL2
 */
define('bf_docebolabs_VERSION', '0.4.2');
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
//date_default_timezone_set("Asia/Calcutta");
date_default_timezone_set("Europe/London");
ini_set('max_execution_time', 600);
if(bf_docebolabs_current_version() == false){
	bf_docebolabs_activation();
}
function bf_docebolabs_current_version(){
	$version = get_option('bf_docebolabs_current_version');
    if($version == null || $version == ''){
		$version = '0.0.0';
	}
    return version_compare($version, bf_docebolabs_VERSION, '=') ? true : false;
}
function my_cron_schedules($schedules){
	if(!isset($schedules["10min"])){
        $schedules["10min"] = array(
            'interval' => 10*60,
            'display' => __('Once every 10 minutes'));
    }
    if(!isset($schedules["8min"])){
        $schedules["8min"] = array(
            'interval' => 8*60,
            'display' => __('Once every 8 minutes'));
    }
	if(!isset($schedules["7min"])){
        $schedules["7min"] = array(
            'interval' => 7*60,
            'display' => __('Once every 7 minutes'));
    }
    return $schedules;
}
add_filter('cron_schedules','my_cron_schedules');
if($_REQUEST['Cron'] == "Y" || $_REQUEST['Course_data'] == "Y")
{
	//echo "Heloo";
	//bf_docebolabs_cron_runtime();
	//include('bf_docebolabs_seat_purchase.php');
	//bf_docebolabs_cron_runtime();
	//bf_docebolabs_course_cron_runtime();
	bf_docebolabs_purchase_cron_runtime();
}   
add_action('activated_plugin','bf_docebolabs_my_save_error');
function bf_docebolabs_my_save_error()
{
	if(ob_get_contents()){
		file_put_contents(dirname(__file__).'/error_activation.txt', ob_get_contents());
	}
}
global $sitedomain, $docebodb, $docebocoursedb;
$sitedomain = get_home_url();
$sitedomain = str_replace('http://', '', $sitedomain);
$sitedomain = str_replace('https://', '', $sitedomain);
$docebodb = $wpdb->prefix . "_bf_docebolabs";
$docebocoursedb = $wpdb->prefix . "_bf_docebolabs_courses";
// Plugin activate
register_activation_hook(__FILE__, 'bf_docebolabs_activation');
function bf_docebolabs_activation(){
    if(!wp_next_scheduled('bf_docebolabs_cron_schedule')){
		wp_schedule_event(current_time('timestamp'), '10min', 'bf_docebolabs_cron_schedule');
    }
	if(!wp_next_scheduled('bf_docebolabs_course_cron_schedule')){
	    wp_schedule_event(current_time('timestamp'), '8min', 'bf_docebolabs_course_cron_schedule');
	}

	if(!wp_next_scheduled('bf_docebolabs_purchase_cron_schedule')){
	    wp_schedule_event(current_time('timestamp'), '7min', 'bf_docebolabs_purchase_cron_schedule');
	}
	global $wpdb, $docebodb, $docebocoursedb, $doceboproductcoursedb;
	$docebodb = $wpdb->prefix . "_bf_docebolabs";
	$docebocoursedb = $wpdb->prefix . "_bf_docebolabs_courses";
	$doceboproductcoursedb = $wpdb->prefix . "_bf_docebolabs_productcourse";
	$charset_collate = $wpdb->get_charset_collate();
	if($wpdb->get_var("show tables like '$docebodb'") != $docebodb){		
		// create the docebodb database table
		$sql = "CREATE TABLE $docebodb (
			id mediumint(15) NOT NULL AUTO_INCREMENT,
			userid VARCHAR(255) NOT NULL,
			firstname VARCHAR(255) NOT NULL,
			lastname VARCHAR(255) NOT NULL,
			email VARCHAR(255) NOT NULL,
			extraFields TEXT(65535),
			UNIQUE KEY id (id)
		) $charset_collate;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	if($wpdb->get_var("show tables like '$docebocoursedb'") != $docebocoursedb){
		// create the docebocoursedb database table
		$sql = "CREATE TABLE $docebocoursedb (
			id mediumint(15) NOT NULL AUTO_INCREMENT,
			code VARCHAR(255) NOT NULL,
			course_name VARCHAR(255) NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	if($wpdb->get_var("show tables like '$doceboproductcoursedb'") != $doceboproductcoursedb){
		// create the doceboproductcoursedb database table
		$sql = "CREATE TABLE $doceboproductcoursedb (
			id mediumint(15) NOT NULL AUTO_INCREMENT,
			product_id VARCHAR(255) NOT NULL,
			course_id VARCHAR(255) NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	update_option('bf_docebolabs_current_version', bf_docebolabs_VERSION);
}

// define settings
add_action( 'admin_init', 'bf_docebolabs_settings' );
function bf_docebolabs_settings() {
	register_setting( 'bf_docebolabs_settings-group', 'bf_docebolabs_regemail' );
	register_setting( 'bf_docebolabs_settings-group', 'bf_docebolabs_submenu_show' );
	register_setting( 'bf_docebolabs_settings-group', 'bf_docebolabs_is_app_name' );
	register_setting( 'bf_docebolabs_settings-group', 'bf_docebolabs_is_api_key' );
	register_setting( 'bf_docebolabs_settings-group', 'bf_docebolabs_enable_is' );
	register_setting( 'bf_docebolabs_settings-group', 'bf_docebolabs_docebo_subdomain' );
	register_setting( 'bf_docebolabs_settings-group', 'bf_docebolabs_docebo_client_id' );
	register_setting( 'bf_docebolabs_settings-group', 'bf_docebolabs_docebo_client_secret' );
	register_setting( 'bf_docebolabs_settings-group', 'bf_docebolabs_docebo_super_admin_username' );
	register_setting( 'bf_docebolabs_settings-group', 'bf_docebolabs_docebo_super_admin_password' );
	register_setting( 'bf_docebolabs_options-group', 'bf_docebolabs_docebo_token' );
	register_setting( 'bf_docebolabs_settings-group', 'bf_docebolabs_enable_docebo' );
	register_setting( 'bf_docebolabs_settings-group', 'bf_docebolabs_enable_cron' );
	register_setting( 'bf_docebolabs_options-group', 'bf_docebolabs_sync_fields' );
	register_setting( 'bf_docebolabs_options-group', 'bf_docebolabs_cron_last_run' );
	register_setting( 'bf_docebolabs_options-group', 'bf_docebolabs_course_cron_last_run' );
	register_setting( 'bf_docebolabs_options-group', 'bf_docebolabs_purchase_cron_last_run' );
	register_setting( 'bf_docebolabs_options-group', 'bf_docebolabs_tag_new_contact' );
	register_setting( 'bf_docebolabs_options-group', 'bf_docebo_taxable' );
	register_setting( 'bf_docebolabs_options-group', 'bf_docebo_CountryTaxable' );
	register_setting( 'bf_docebolabs_options-group', 'bf_docebo_StateTaxable' );
	register_setting( 'bf_docebolabs_options-group', 'bf_docebo_CityTaxable' );
	register_setting( 'bf_docebolabs_options-group', 'bf_docebo_skipcustomfieldsincron' );
}

// define menus
add_action('admin_menu', 'bf_docebolabs_menu');

function bf_docebolabs_menu() {
	if(get_option('bf_docebolabs_submenu_show')){
		$slugvalue = 'bf_docebolabs_menu-home';
	} else {
		$slugvalue = null;
	}
	add_menu_page('DoceboLabs', 'DoceboLabs', 'administrator', 'bf_docebolabs_menu-home', 'bf_docebolabs_menu_home_page', "");
	add_submenu_page('bf_docebolabs_menu-home', 'DoceboLabs - Settings', 'DoceboLabs Settings', 'administrator', 'bf_docebolabs_menu_settings_page', 'bf_docebolabs_menu_settings_page');
	add_submenu_page('bf_docebolabs_menu-home', 'DoceboLabs - Settings Help', 'DoceboLabs Settings Help', 'administrator', 'bf_docebolabs_menu_settings_help_page', 'bf_docebolabs_menu_settings_help_page');
	add_submenu_page($slugvalue, 'DoceboLabs - Add a user', 'Add a user', 'administrator', 'bf_docebolabs_add_user_page', 'bf_docebolabs_add_user_page');
	add_submenu_page($slugvalue, 'DoceboLabs - Add a user to a course', 'Add a user to a course', 'administrator', 'bf_docebolabs_add_to_course_page', 'bf_docebolabs_add_to_course_page');
	add_submenu_page($slugvalue, 'DoceboLabs - Remove a user from a course', 'Remove a user from a course', 'administrator', 'bf_docebolabs_remove_from_course_page', 'bf_docebolabs_remove_from_course_page');
	add_submenu_page($slugvalue, "DoceboLabs - Revoke a user's access to a course", "Revoke a user's access to a course", 'administrator', 'bf_docebolabs_revoke_user_enrolment_page', 'bf_docebolabs_revoke_user_enrolment_page');
	add_submenu_page($slugvalue, "DoceboLabs - Reinstate a user's access to a course", "Reinstate a user's access to a course", 'administrator', 'bf_docebolabs_reinstate_user_enrolment_page', 'bf_docebolabs_reinstate_user_enrolment_page');
	add_submenu_page($slugvalue, "DoceboLabs - Update a user's enrolment level", "Update a user's enrolment level", 'administrator', 'bf_docebolabs_update_user_enrolment_page', 'bf_docebolabs_update_user_enrolment_page');
	add_submenu_page($slugvalue, "DoceboLabs - Update User Level", "Update a user's level between Power User and Regular User, optionally can also set the Power User's Profile Name while Upgrading", 'administrator', 'bf_docebolabs_update_user_level_page', 'bf_docebolabs_update_user_level_page');
	add_submenu_page($slugvalue, "DoceboLabs - Set Power User Profile Name", "Set's the Power User's Profile Name via HTTP Post", 'administrator', 'bf_docebolabs_set_user_profile_page', 'bf_docebolabs_set_user_profile_page');
	add_submenu_page($slugvalue, "DoceboLabs - Create Branch", "Creates a New Branch via HTTP Post", 'administrator', 'bf_docebolabs_create_branch_page', 'bf_docebolabs_create_branch_page');
	add_submenu_page($slugvalue, "DoceboLabs - Assign User to Branch", "Assign's User to a Branch via HTTP Post", 'administrator', 'bf_docebolabs_assign_user_to_branch_page', 'bf_docebolabs_assign_user_to_branch_page');
	add_submenu_page($slugvalue, "DoceboLabs - Assign User/Branch/Group to Power User", "Assign's User/Branch/Group to Power User via HTTP Post", 'administrator', 'bf_docebolabs_assign_to_poweruser_page', 'bf_docebolabs_assign_to_poweruser_page');
	add_submenu_page($slugvalue, "DoceboLabs - Date Calculator", "Calculates Dates and applies value to Infusionsoft via HTTP Post", 'administrator', 'bf_docebolabs_date_calculator_page', 'bf_docebolabs_date_calculator_page');
	add_submenu_page($slugvalue, "DoceboLabs - Assign Course to Power User", "Assign's Course to Power User via HTTP Post", 'administrator', 'bf_docebolabs_assign_course_to_poweruser_page', 'bf_docebolabs_assign_course_to_poweruser_page');
	add_submenu_page($slugvalue, "DoceboLabs - Add Power User to Course Plus Free Seats", "Add a Power User to a course plus any free seats required", 'administrator', 'bf_docebolabs_add_poweruser_to_course_page', 'bf_docebolabs_add_poweruser_to_course_page');
	add_submenu_page($slugvalue, "DoceboLabs - Seat Purchase", "Add a Power User to a course plus any free seats required, with product-course association", 'administrator', 'bf_docebolabs_seat_purchase_page', 'bf_docebolabs_seat_purchase_page');
	add_submenu_page($slugvalue, 'DoceboLabs - Configure Sync settings', 'Configure Sync settings', 'administrator', 'bf_docebolabs_config_sync_page', 'bf_docebolabs_config_sync_page');
}

// define pages
function bf_docebolabs_menu_home_page() {
	include('bf_docebolabs_menu_home_page.php');
}

function bf_docebolabs_menu_settings_page() {
	include('bf_docebolabs_menu_settings_page.php');
}
function bf_docebolabs_menu_settings_help_page() {
	include('bf_docebolabs_menu_settings_help_page.php');
}
function bf_docebolabs_add_user_page() {
	include('bf_docebolabs_add_user_page.php');
}
function bf_docebolabs_add_to_course_page() {
	include('bf_docebolabs_add_to_course_page.php');
}
function bf_docebolabs_remove_from_course_page() {
	include('bf_docebolabs_remove_from_course_page.php');
}
function bf_docebolabs_revoke_user_enrolment_page() {
	include('bf_docebolabs_revoke_user_enrolment_page.php');
}
function bf_docebolabs_reinstate_user_enrolment_page() {
	include('bf_docebolabs_reinstate_user_enrolment_page.php');
}
function bf_docebolabs_update_user_enrolment_page() {
	include('bf_docebolabs_update_user_enrolment_page.php');
}
function bf_docebolabs_update_user_level_page() {
	include('bf_docebolabs_update_user_level_page.php');
}
function bf_docebolabs_set_user_profile_page() {
	include('bf_docebolabs_set_user_profile_page.php');
}
function bf_docebolabs_create_branch_page() {
	include('bf_docebolabs_create_branch_page.php');
}
function bf_docebolabs_assign_user_to_branch_page() {
	include('bf_docebolabs_assign_user_to_branch_page.php');
}
function bf_docebolabs_assign_to_poweruser_page() {
	include('bf_docebolabs_assign_to_poweruser_page.php');
}
function bf_docebolabs_date_calculator_page() {
	include('bf_docebolabs_date_calculator_page.php');
}
function bf_docebolabs_assign_course_to_poweruser_page() {
	include('bf_docebolabs_assign_course_to_poweruser_page.php');
}
function bf_docebolabs_add_poweruser_to_course_page() {
	include('bf_docebolabs_add_poweruser_to_course_page.php');
}
function bf_docebolabs_seat_purchase_page() {
	include('bf_docebolabs_seat_purchase_page.php');
}
function bf_docebolabs_config_sync_page() {
	include('bf_docebolabs_config_sync_page.php');
}

// docebolabs cron
add_action('bf_docebolabs_cron_schedule', 'bf_docebolabs_cron_runtime');
add_action('bf_docebolabs_course_cron_schedule', 'bf_docebolabs_course_cron_runtime');
add_action('bf_docebolabs_purchase_cron_schedule', 'bf_docebolabs_purchase_cron_runtime');

//Docebo user cron code start here
function bf_docebolabs_cron_runtime() {
	global $debug, $bfEnableCron, $access_token, $syncfields, $subdomain, $lastcronrun, $tag_selected, $isTime;
	$debug = true;
	if($debug == true){
		$fp = fopen(dirname(__file__).'/debug_bf_docebolabs_cron.txt', 'a');
		fwrite($fp, "Sync started: ".date('d-m-Y H:i:s', current_time('timestamp', true))." UTC, (".date('d-m-Y H:i:s', current_time('timestamp'))." ".get_option('timezone_string').")\r\n");
		fclose($fp);
	}
	$bfEnableCron = get_option('bf_docebolabs_enable_cron');
	$access_token = bf_docebolabs_fetch_token();
	$syncfields = get_option('bf_docebolabs_sync_fields');
	$subdomain = get_option('bf_docebolabs_docebo_subdomain');
	$tag_selected = get_option('bf_docebolabs_tag_new_contact');
	$lastcronrun = get_option('bf_docebolabs_cron_last_run');
	if($lastcronrun == null || $lastcronrun == ''){
		$lastcronrun = strtotime('-10 years', current_time('timestamp', true));
	}
	$isTime = $lastcronrun - (5 * 3600);
	if($debug == true){
		
		$fp = fopen(dirname(__file__).'/debug_bf_docebolabs_cron.txt', 'a');
		fwrite($fp, "bfEnableCron: ".print_r($bfEnableCron, true)."\r\n");
		fwrite($fp, "access_token: ".print_r($access_token, true)."\r\n");
		fwrite($fp, "syncfields: ".print_r($syncfields, true)."\r\n");
		fwrite($fp, "subdomain: ".print_r($subdomain, true)."\r\n");
		fwrite($fp, "tag_selected: ".print_r($tag_selected, true)."\r\n");
		fwrite($fp, "lastcronrun: ".date('d-m-Y H:i:s', $lastcronrun)."\r\n");
		fwrite($fp, "isTime: ".date('d-m-y H:i:s', $isTime)."\r\n");
		fclose($fp);
	}
	if(isset($bfEnableCron) && $bfEnableCron == 1 && isset($access_token) && is_array($access_token) && isset($syncfields) && is_array($syncfields)){
		$cronrun = 'true';
		include('bf_docebolabs_config_sync.php');
	} elseif(isset($access_token) && !isset($access_token['accessToken'])) {
		if($debug == true){
			$fp = fopen(dirname(__file__).'/debug_bf_docebolabs_cron.txt', 'a');
			fwrite($fp, "Docebo API ERROR: ".print_r($access_token, true)."\r\n");
			fclose($fp);
		}
	}
	// Update Last Cron Run
	update_option('bf_docebolabs_cron_last_run', current_time('timestamp', true));
	if($debug == true){
		$fp = fopen(dirname(__file__).'/debug_bf_docebolabs_cron.txt', 'a');
		fwrite($fp, "Sync ended: ".date('d-m-Y H:i:s', current_time('timestamp', true))." UTC, (".date('d-m-Y H:i:s', current_time('timestamp'))." ".get_option('timezone_string').")\r\n");
		fclose($fp);
		exit;
	}
}
//Docebo user cron code end here

//Docebo course cron code start here
function bf_docebolabs_course_cron_runtime() {
	global $debug, $bfEnableCron, $access_token, $syncfields, $subdomain, $lastcoursecronrun, $tag_selected, $isTime;
	$debug = true;
	if($debug == true){
		$fp = fopen(dirname(__file__).'/debug_bf_docebolabs_course_cron.txt', 'a');
		fwrite($fp, "Sync started: ".date('d-m-Y H:i:s', current_time('timestamp', true))." UTC, (".date('d-m-Y H:i:s', current_time('timestamp'))." ".get_option('timezone_string').")\r\n");
		fclose($fp);
	}
	$bfEnableCron = get_option('bf_docebolabs_enable_cron');
	$access_token = bf_docebolabs_fetch_token();
	$syncfields = get_option('bf_docebolabs_sync_fields');
	$subdomain = get_option('bf_docebolabs_docebo_subdomain');
	$tag_selected = get_option('bf_docebolabs_tag_new_contact');
	$lastcoursecronrun = get_option('bf_docebolabs_course_cron_last_run');
	if($lastcoursecronrun == null || $lastcoursecronrun == ''){
		$lastcoursecronrun = strtotime('-10 years', current_time('timestamp', true));
	}
	$isTime = $lastcoursecronrun - (5 * 3600);
	if($debug == true){
		$fp = fopen(dirname(__file__).'/debug_bf_docebolabs_course_cron.txt', 'a');
		fwrite($fp, "bfEnableCron: ".print_r($bfEnableCron, true)."\r\n");
		fwrite($fp, "access_token: ".print_r($access_token, true)."\r\n");
		fwrite($fp, "syncfields: ".print_r($syncfields, true)."\r\n");
		fwrite($fp, "subdomain: ".print_r($subdomain, true)."\r\n");
		fwrite($fp, "tag_selected: ".print_r($tag_selected, true)."\r\n");
		fwrite($fp, "lastcoursecronrun: ".date('d-m-Y H:i:s', $lastcoursecronrun)."\r\n");
		fwrite($fp, "isTime: ".date('d-m-y H:i:s', $isTime)."\r\n");
		fclose($fp);
	}
	if(isset($bfEnableCron) && $bfEnableCron == 1 && isset($access_token) && is_array($access_token) && isset($syncfields) && is_array($syncfields)){
		$cronrun = 'true';
		//include('bf_docebolabs_config_course_sync.php');
		
	} elseif(isset($access_token) && !isset($access_token['accessToken'])) {
		if($debug == true){
			$fp = fopen(dirname(__file__).'/debug_bf_docebolabs_course_cron.txt', 'a');
			fwrite($fp, "Docebo API ERROR: ".print_r($access_token, true)."\r\n");
			fclose($fp);
		}
	}
	// Update Last Cron Run
	update_option('bf_docebolabs_course_cron_last_run', current_time('timestamp', true));
	if($debug == true){
		$fp = fopen(dirname(__file__).'/debug_bf_docebolabs_course_cron.txt', 'a');
		fwrite($fp, "Sync ended: ".date('d-m-Y H:i:s', current_time('timestamp', true))." UTC, (".date('d-m-Y H:i:s', current_time('timestamp'))." ".get_option('timezone_string').")\r\n");
		fclose($fp);
		exit;
	}
}
//Docebo course cron code end here

//Docebo purchase cron code start here
function bf_docebolabs_purchase_cron_runtime() {
	global $debug, $bfEnableCron, $access_token, $syncfields, $subdomain, $lastpurchasecronrun, $tag_selected, $isTime;
	$debug = true;
	if($debug == true){
		$fp = fopen(dirname(__file__).'/debug_bf_docebolabs_purchase_cron.txt', 'a');
		fwrite($fp, "Sync started: ".date('d-m-Y H:i:s', current_time('timestamp', true))." UTC, (".date('d-m-Y H:i:s', current_time('timestamp'))." ".get_option('timezone_string').")\r\n");
		fclose($fp);
	}
	$bfEnableCron = get_option('bf_docebolabs_enable_cron');
	$access_token = bf_docebolabs_fetch_token();
	$syncfields = get_option('bf_docebolabs_sync_fields');
	$subdomain = get_option('bf_docebolabs_docebo_subdomain');
	$tag_selected = get_option('bf_docebolabs_tag_new_contact');
	$lastpurchasecronrun = get_option('bf_docebolabs_purchase_cron_last_run');
	if($lastpurchasecronrun == null || $lastpurchasecronrun == ''){
		$lastpurchasecronrun = strtotime('-10 years', current_time('timestamp', true));
	}
	$isTime = $lastpurchasecronrun - (5 * 3600);
	if($debug == true){
		$fp = fopen(dirname(__file__).'/debug_bf_docebolabs_purchase_cron.txt', 'a');
		fwrite($fp, "bfEnableCron: ".print_r($bfEnableCron, true)."\r\n");
		fwrite($fp, "access_token: ".print_r($access_token, true)."\r\n");
		fwrite($fp, "syncfields: ".print_r($syncfields, true)."\r\n");
		fwrite($fp, "subdomain: ".print_r($subdomain, true)."\r\n");
		fwrite($fp, "tag_selected: ".print_r($tag_selected, true)."\r\n");
		fwrite($fp, "lastpurchasecronrun: ".date('d-m-Y H:i:s', $lastpurchasecronrun)."\r\n");
		fwrite($fp, "isTime: ".date('d-m-y H:i:s', $isTime)."\r\n");
		
		fclose($fp);
	}
	if(isset($bfEnableCron) && $bfEnableCron == 1 && isset($access_token) && is_array($access_token) && isset($syncfields) && is_array($syncfields)){
		$cronrun = 'true';
         //include('bf_docebolabs_config_purchase_sync.php');
         include('bf_docebolabs_seat_purchase.php');
	} elseif(isset($access_token) && !isset($access_token['accessToken'])) {
		if($debug == true){
			$fp = fopen(dirname(__file__).'/debug_bf_docebolabs_purchase_cron.txt', 'a');
			fwrite($fp, "Docebo API ERROR: ".print_r($access_token, true)."\r\n");
			fclose($fp);
		}
	}
	// Update Last Cron Run
	update_option('bf_docebolabs_purchase_cron_last_run', current_time('timestamp', true));
	if($debug == true){
		$fp = fopen(dirname(__file__).'/debug_bf_docebolabs_purchase_cron.txt', 'a');
		fwrite($fp, "Sync ended: ".date('d-m-Y H:i:s', current_time('timestamp', true))." UTC, (".date('d-m-Y H:i:s', current_time('timestamp'))." ".get_option('timezone_string').")\r\n");
		fclose($fp);
		exit;
	}
}
//Docebo purchase cron code end here

// enqueue scripts
function bf_docebolabs_admin_enqueue_script() {
	if(substr($_REQUEST['page'], 0, 13) === 'bf_docebolabs'){
		$version = get_option('bf_docebolabs_current_version');
		//wp_enqueue_script( 'bf_docebolabs_admin_scripts', plugin_dir_url( __FILE__ ) . 'js/bf_docebolabs_admin.js', array('jquery'), $version );
	}
}
add_action('admin_enqueue_scripts', 'bf_docebolabs_admin_enqueue_script');

// enqueue style
function bf_docebolabs_admin_theme_style() {
	if(substr($_REQUEST['page'], 0, 13) === 'bf_docebolabs'){
		echo '<link rel="stylesheet" href="'.plugin_dir_url( __FILE__ ).'bf_docebolabs_style.css" type="text/css" media="all" />';
	}
}
add_action('admin_head', 'bf_docebolabs_admin_theme_style');

// Docebo sdk functions
function bf_docebolabs_fetch_token($force = null){
	$access_token = get_option('bf_docebolabs_docebo_token');
	$subdomain = get_option('bf_docebolabs_docebo_subdomain');
	$client_id = get_option('bf_docebolabs_docebo_client_id');
	$client_secret = get_option('bf_docebolabs_docebo_client_secret');
	$superAdminUser = get_option('bf_docebolabs_docebo_super_admin_username');
	$superAdminPass = get_option('bf_docebolabs_docebo_super_admin_password');
	if(!is_array($access_token) || !isset($access_token['accessToken']) || (isset($access_token['accessToken']) && (current_time('timestamp', true) > $access_token['expires'] || current_time('timestamp', true) < ($access_token['expires'] - 3600))) || !is_numeric($access_token['expires']) || $force == true){
		$oauth2token_url = "https://".$subdomain.".docebosaas.com/oauth2/token";
		$clienttoken_post = array(
			"client_id" => $client_id,
			"client_secret" => $client_secret,
			"grant_type" => "password",
			"scope" => "api",
			"username" => $superAdminUser,
			"password" => $superAdminPass
		);
		$curl = curl_init($oauth2token_url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $clienttoken_post);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$json_response = curl_exec($curl);
		curl_close($curl);
		$authObj = json_decode($json_response, true);
if($debug == true){
	$fp = fopen(dirname(__file__).'/debug_bf_docebolabs_fetch_token.txt', 'a');
	fwrite($fp, "bf_docebolabs_fetch_token: ".date('d-m-Y H:i:s', current_time('timestamp', true))." UTC, (".date('d-m-Y H:i:s', current_time('timestamp'))." ".get_option('timezone_string').")\r\n");
	fwrite($fp, print_r($authObj, true)."\r\n");
	fclose($fp);
}
		if(!isset($authObj['access_token'])){
			$access_token = $authObj;
		} else {
			$access_token = array();
			$access_token['accessToken'] = $authObj['access_token'];
			$expireTime = current_time('timestamp', true) - 60;
			$access_token['expires'] = $expireTime + $authObj['expires_in'];
		}
		update_option('bf_docebolabs_docebo_token', $access_token);
	}
	return $access_token;
}
?>