<?php

/**
 * Add Plugin's Admin Menu
 * Since Version 2.0  
 */	
add_action('admin_menu', 'splite_addmenu_page_in_admin', 99); 
function splite_addmenu_page_in_admin() {
	//add_options_page(__('All Settings'), __('All Settings'), 'administrator', 'options.php');
	global $_wp_last_object_menu;
	$_wp_last_object_menu++;

	global $splite_hook; 	
	$splite_hook = array();
	$icon = SPLITE_PLUGIN_URL . '/admin/img/menu_icon.png';
	
	$splite_hook[] = add_submenu_page( 'slick-options', 'Import Demos', 'Import Demos', 'manage_options', 'import-demos', 'splite_import_demos' );
	//$hook = "load-".$splite_hook[0];
	
	//add_action($hook, 'splite_load_admin');	
}

add_action( 'admin_enqueue_scripts', 'splite_admin_enqueue_scripts' );
function splite_admin_enqueue_scripts( $hook_suffix ) {
	if ( false === strpos( $hook_suffix, 'slick' ) ) {
		return;
	}
	if ( $_GET['page'] == 'import-demos' ) {
		wp_enqueue_style( 'bootstrap-min-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css' );
		wp_enqueue_script( 'bootstrap-min-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js' );
		wp_enqueue_script( 'jquery-tab', 'https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js' );
	}
}


/**
 * Import Demos Features
 * Since Version 2.0 - ToDo
 * @param none
 
 * @return none
 * Creates the post list table 
 */
function splite_import_demos() { ?>
	<style>
		p.notice {
			padding: 10px;
		}
		.section {
			overflow: hidden; 
			margin-bottom: 30px; 
		}
		.sp-btn {
		     
		}
		.sp-btn:hover {
		}
		.sp-btn-importer {
			float: right; 
		}
		.import-result {
			width: auto;
		    height: 30px;
		    padding: 10px 2px 5px 10px;
		    display: block;
		    font-family: sans-serif;
		    font-size: 20px;
		    font-weight: bold;
		    border-radius: 5px;
		    background: red;
		    color: white;
		    margin-top: 10px;
		    display: none;
		}
		
		.import-box {
			overflow: hidden; 
		}
		.import-box {
			float: left;
			margin: 0 29px 30px 0;
			position: relative;
			width: 30.6%;
			border: 1px solid #ddd;
			box-shadow: 0 1px 1px -1px rgba(0,0,0,.1);
			box-sizing: border-box;
		}
		.import-box:last-child {
			margin-right: 0; 
		}
		.import-box img {
			max-height: 280px;
			width: 100%;
		}
		.import-box img:hover {
			transform: scale(0.99);
		}
		.import-box-title {
			padding: .25rem 1rem;
		}
		.sp-label {
			font-weight: bold; 
		}
		.sp-btn-importer {
			
		}
		.sp-import-handle {
			float: right; 
		}
		.import-box-result {
			text-align: center;
			padding: 3px 10px;
			color: #efefef;
			position: absolute;
			width: 100%;
			bottom: 30px;
			line-height: 1.3em; 
		}
		.import-box-result a {
			color: #efefef; 
		}
		.import-box-result.success {
			background: green; 
			color: #efefef; 
		}
		.import-box-result.error {
			background: red; 
			color: #efefef; 
			margin: 0 !important; 
		}
		@media only screen and (max-width: 769px) {
			.section-inline {
				display: block;
				width: auto; 
				margin-right: 0; 
			}
		}
	</style>
	
	<div class="wrap">
		<div class="card col-md-12">
			<span class="card-title text-center m-2 display-4"><?php echo esc_html( __( "Import CF7 Demo Forms", 'slick-popup' ) ); ?></span>
			<div class="card-body m-2">
				<span style="font-size: 1.15rem;" class="text-info"><?php echo esc_html( __( "Choose a form and click import button, this will create a ", 'slick-popup' ) ); ?><strong><em><a href="<?php echo admin_url('/admin.php?page=wpcf7/'); ?>">Contact Form 7</a></em></strong><?php echo esc_html( __( " form with the desired layout. Once imported, you may want to change the To Email and Mail Body for the form.", 'slick-popup' ) ); ?><br><?php echo esc_html( __( "For any kind of suppport please email us at: ", 'slick-popup' ) ); ?><strong><em><a href="mailto:poke@slickpopup.com">poke@slickpopup.com</a></em></strong></span>
			</div>
		</div>
		<div class="notice-info settings-error notice is-dismissible mb-2 mt-3">
			<p style="font-weight:bold;" class="text-danger"><?php echo esc_html( __( "Note: This will just import the cf7 forms, you will have to create and edit the popups.", 'slick-popup' ) ); ?></p>
		</div>
		<div class="card col-md-12">
			<span class="card-subtitle text-secondary font-weight-normal m-2" style="font-size: 2rem;	"><?php echo esc_html( __( "One-click Import for Contact Form 7", 'slick-popup' ) ); ?></span>
			<div class="import-holder">
				<?php $demos = array(
					'basic-enquiry' => 'Basic Enquiry Form', 
					'subscribe' => 'Subscribe Form',
					'unsubscribe' => 	'Unsubscribe Form',				
					'get-a-quote' => 'Get a Quote Form',
					'survey' => 'Survey Form',
					'booking' => 'Booking Form',
				);
				$output = '';
					$output .= '<div id="welcome-panel" class="welcome-panel">';
						foreach($demos as $label => $demo) {			
							$output .='<div class="import-box">';
								$output .='<img src="'.splite_plugin_url('/libs/js/img/'.$label.'.jpg').'" title="'.$demo.'">'; 
								$output .='<div class="import-box-result" style="display:none;"></div>';
								$output .='<div class="import-box-title">';
									$output .='<span class="sp-label">'.$demo.'</span>';
									$output .='<span class="sp-import-handle">';
										$output .='<span class="sp-loader" style="visibility:hidden"><i class="fa fa-refresh fa-spin" style="font-size:14px;color:#f56e28;position:relative;left:-8px;"></i></span>';						
										$output .='<span class="sp-btn button-link sp-btn-importer splite-btn-importer" data-title="'.$label.'"><strong>Import</strong></span>';
									$output .='</span>';
								$output .='</div>';
							$output .='</div>';
						} 
					$output .='</div>';
				echo $output; 
				?>
			</div>
		</div>
		<div class="notice-info settings-error notice is-dismissible">
			<p style="font-weight:bold;"><?php echo esc_html( __( "For any kind of suppport please email us at:", 'slick-popup' ) ); ?> 
				<em><a href="mailto:poke@slickpopup.com">poke@slickpopup.com</a></em>
			</p>
		</div>
	</div>

<?php }

add_action( 'admin_notices', 'splite_admin_notices' );
function splite_admin_notices() {
	$install_date = get_option('splite_install_date'); 
	
	// review_notice - numeric counter for multiplying 14 days
	$review_notice = get_option('splite_review_notice') ? get_option('splite_review_notice') : 1; 
	
	if(!isset($install_date)) {
		update_option('splite_install_date', current_time('Y-m-d H:i:s')); 
		return; 
	}
	
	$install_date_object = DateTime::createFromFormat('Y-m-d H:i:s', $install_date);
	$today = DateTime::createFromFormat('U', current_time('U')); 
	$diff = $today->diff($install_date_object); 
	//print_r($diff); 
	
	if($diff->d >= 14*$review_notice and $review_notice!=0) {
		echo '<div class="notice notice-success">
			<h2 style="margin:0.5em 0;">Hope you are enjoying - <span style="color:#0073aa;">Slick Popup Lite</span></h2>
			<p>
			'.__( 'Thanks for using one of the best WordPress Popup Plugin for Contact Form 7. We hope that it has been useful for you and would like you to leave review on WordPres.org website, it will help us improve the product features.', 'slick-popup' ).'
			<br><br>
			<a class="button-primary" href="'.admin_url('admin.php?page=slick-options').'">Leave a Review</a>
			&nbsp;<a class="button-link sp-dismissable" data-btn="ask-later" href="#">Ask Later</a> |
			<a class="button-link sp-dismissable" data-btn="ask-never" href="#">Never Show Again</a></p>
		</div>';		
	}
}


/**
 * Show a notice to anyone who has just updated this plugin
 * This notice shouldn't display to anyone who has just installed the plugin for the first time
 */
function splite_display_update_notice() {
	// Check the transient to see if we've just updated the plugin
	if( get_transient( 'splite_updated' ) ) {
		echo '<div class="notice notice-success is-dismissible">
			<h2 style="margin:0.5em 0;">Thanks for updating - <span style="color:#0073aa;">Slick Popup Lite</span></h2>
			<p>
			'.__( 'One of the best WordPress Popup Plugin for Contact Form 7. ', 'slick-popup' ).'
			<span style="display: block; margin: 0.5em 0.5em 0 0; clear: both; font-weight: bold;"><a href="'.admin_url('admin.php?page=slick-options').'">Go to Settings</a> | <a href="'.admin_url('admin.php?page=import-demos').'">Import Demo Forms</a> </span>
			</p>
		</div>';
		
		// Save splite_install_date for already existing users (before: 1.5.3)
		if(!get_option('splite_install_date'))
			update_option('splite_install_date', current_time('Y-m-d H:i:s')); 			
		
		delete_transient( 'splite_updated' );
	}
}
add_action( 'admin_notices', 'splite_display_update_notice' );

/**
 * Show a notice to anyone who has just installed the plugin for the first time
 * This notice shouldn't display to anyone who has just updated this plugin
 */
function splite_display_install_notice() {
	// Check the transient to see if we've just activated the plugin
	if( get_transient( 'splite_activated' ) ) {
		
		echo '<div class="notice notice-success is-dismissible">
			<h2 style="margin:0.5em 0;">Thanks for installing - <span style="color:#0073aa;">Slick Popup Lite</span></h2>
			<p>
			'.__( 'One of the best WordPress Popup Plugin for Contact Form 7. ', 'slick-popup' ).'
			<span style="display: block; margin: 0.5em 0.5em 0 0; clear: both; font-weight: bold;"><a href="'.admin_url('admin.php?page=slick-options').'">Go to Settings</a> | <a href="'.admin_url('admin.php?page=import-demos').'">Import Demo Forms</a> </span>
			</p>
		</div>';
		
		// Delete the transient so we don't keep displaying the activation message
		delete_transient( 'splite_activated' );
	}
}
add_action( 'admin_notices', 'splite_display_install_notice' );

?>