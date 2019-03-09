<?php

/**
 *  Plugin Name: InfusionCrafting
 *  Plugin URI: https://github.com/sitecrafting/infusioncrafting
 *  Description: Simple InfusionSoft API OAuth2 integration
 *  Version: 0.1
 *  Author: Coby Tamayo <ctamayo@sitecrafting.com>
 *  Author URI: https://www.sitecrafting.com
 */

use InfusionCrafting\AdminPage;

if (!defined('ABSPATH')) {
  die();
}

require_once __DIR__.'/vendor/autoload.php';

spl_autoload_register(function($className) {
  $file = __DIR__ . '/lib/' . str_replace('\\', '/', $className) . '.php';
  if (file_exists($file)) {
    require $file;
  }
});

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
  $token = $client->request_token($_GET['code'] );

  if ($token) {
    update_option('infusioncrafting_token', serialize($token));
    wp_redirect(admin_url('?page=infusioncrafting&action=confirm_auth'));
  }
});

add_action('admin_menu', function() {
  $client = apply_filters('infusioncrafting/client', false);
  AdminPage::init($client);
});