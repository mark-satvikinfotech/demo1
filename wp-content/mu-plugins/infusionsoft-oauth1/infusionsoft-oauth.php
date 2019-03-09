<?php

/**
 *  Plugin Name: InfusionCrafting
 *  Plugin URI: https://github.com/sitecrafting/infusioncrafting
 *  Description: Simple InfusionSoft API OAuth2 integration
 *  Version: 0.1
 *  Author: Coby Tamayo <ctamayo@sitecrafting.com>
 *  Author URI: https://www.sitecrafting.com
 */
/* error_reporting(E_ALL);
ini_set('display_errors', 1); */
//echo WPMU_PLUGIN_DIR ; exit;
use InfusionCrafting\AdminPage;

if (!defined('ABSPATH')) {
  die();
}

require_once __DIR__.'/vendor/autoload.php';
if($_GET['cronforoauth'] == "Y")
{
	require_once('infusionsoft-cron.php');
	exit;
}
//require_once('infusionsoft-cron.php');

spl_autoload_register(function($className) {
  $file = __DIR__ . '/lib/' . str_replace('\\', '/', $className) . '.php';
  if (file_exists($file)) {
    require $file;
  }
});
$current_url = $_SERVER['REQUEST_URI'];
add_filter('infusioncrafting/client', function() {
  $client = InfusionCrafting\Client::init([
    'clientId'     => get_option('infusioncrafting_client_id'),
    'clientSecret' => get_option('infusioncrafting_client_secret'),
    'redirectUri'  => admin_url('/admin-ajax.php?action=infusioncrafting_authorize'),
  ]);

  if ($client->refreshed()) {
    // token was refreshed during this HTTP request,
    // so we have to save the new one
    update_option('infusioncrafting_token', serialize($client->getToken()));
  }

  return $client;
});

add_action('wp_ajax_infusioncrafting_authorize', function() {
	
  $client = apply_filters('infusioncrafting/client', false);
  $token = $client->request_token($_GET['code'] ?? '');

  if ($token) {
    update_option('infusioncrafting_token', serialize($token));
	$current_url= admin_url('/admin.php/?page=infusioncrafting&action=confirm_auth');
	
    wp_redirect($current_url);
  }
  exit;
  
});

add_action('admin_menu', function() {
  $client = apply_filters('infusioncrafting/client', false);
  AdminPage::init($client);
}); 

