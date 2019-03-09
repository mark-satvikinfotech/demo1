<?php

define('dir_path', plugin_dir_path( __FILE__ ) );

define('plugin_url', plugin_dir_url(__FILE__)  );

define('subscriber_email', get_option('admin_email'));

require_once('functions.php');
require_once(dir_path . '/includes/customer_delete.php');



add_action( 'wp_enqueue_scripts', array('cp_list_subscriptions','ISmember_load_script_init'));

add_action( 'admin_enqueue_scripts', array('cp_list_subscriptions','ISmember_load_script_init_admin'));


