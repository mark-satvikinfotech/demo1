<?php
if(realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit('Please don\'t access this file directly.');
}
 	class IS_orderform_details {
	    function __construct() {
	    }
	function ISmember_load_script_init_admin() {
       wp_enqueue_script('ajax-script', plugins_url('/js/custom.js', __FILE__));
        wp_localize_script( 'ajax-script', 'custom',
        array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        wp_enqueue_style('style', plugins_url('/css/style.css', __FILE__));
    }
    function ISmember_load_script_init() {
        wp_enqueue_script('script-min', plugins_url('js/jquery-3.3.1.min.js', __FILE__));
       wp_enqueue_script('script-validate', plugins_url('js/jquery.validate.min.js', __FILE__));
        wp_enqueue_script( 'ajax-script', get_template_directory_uri() . '/js/custom.js', array('jquery') );
        wp_enqueue_script('ajax-script1', plugins_url('/js/custom1.js', __FILE__));
         wp_enqueue_script('ajax-script', plugins_url('/js/custom1.js', __FILE__));
         wp_enqueue_script( 'ajax-script', get_template_directory_uri() . '/js/custom1.js', array('jquery') );
       wp_localize_script( 'ajax-script', 'custom',
           array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
       //echo admin_url('admin-ajax.php'); 
        wp_localize_script( 'ajax-script', 'custom',
           array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        wp_enqueue_script('ajax-script1', plugins_url('/js/custom1.js', __FILE__));
        wp_enqueue_style('style', plugins_url('/css/style.css', __FILE__));
    }    
}