<?php
set_time_limit(0);
date_default_timezone_set('Europe/London');
error_reporting(E_ERROR | E_PARSE);
if(isset($_REQUEST["settings-updated"]) && $_REQUEST["settings-updated"] == "true"){
	global $sitedomain, $bfsdkurl;
	// Infusionsoft
	if(esc_attr(get_option('bf_docebolabs_enable_is')) && esc_attr(get_option('bf_docebolabs_enable_is')) == 1){
		global $connInfo;
		$connInfo = array('isconn:'.esc_attr( get_option('bf_docebolabs_is_app_name') ).':i:'.esc_attr( get_option('bf_docebolabs_is_api_key') ).':This is the connection for '.esc_attr( get_option('bf_docebolabs_is_app_name') ).'.infusionsoft.com');
		require_once(WP_PLUGIN_DIR."/bf_docebolabs/aisdk.php");
		$app = new iSDK;
		if($app->cfgCon("isconn")){
			echo '<p style="color: #00d414; font-weight: bold;">Infusionsoft API Connection Confirmed</p>';
		} else {
			echo '<p style="color: red; font-weight: bold;">Infusionsoft API Connection Failed</p>';
		}
	}
	// Docebo
	if(esc_attr(get_option('bf_docebolabs_enable_docebo')) && esc_attr(get_option('bf_docebolabs_enable_docebo')) == 1){
		$access_token = bf_docebolabs_fetch_token();
		if(!is_wp_error($access_token) && isset($access_token) && $access_token['accessToken'] != null && $access_token['accessToken'] != '' && current_time('timestamp', true) < $access_token['expires'] && is_numeric($access_token['expires'])){
			echo '<p style="color: #00d414; font-weight: bold;">Docebo API Connection Confirmed</p>';
		} else {
			echo '<p style="color: red; font-weight: bold;">Docebo API Connection Failed:'.print_r($access_token, true).'</p>';
		}
		// populate docebo user cache
		global $wpdb, $docebodb, $docebocoursedb;
		$charset_collate = $wpdb->get_charset_collate();
		$numRows = $wpdb->get_var("SELECT COUNT(*) FROM $docebodb");
		if($numRows == '' || $numRows == null || $numRows == '0'){
			$subdomain = get_option('bf_docebolabs_docebo_subdomain');
			// check for changes in Docebo
			$submiturl = "https://".$subdomain.".docebosaas.com/api/user/listUsers";
			$authorization = "Authorization: Bearer ".$access_token['accessToken'];
			$data = array();
			$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
			$httppost = wp_safe_remote_post($submiturl, $options);
			$result = json_decode($httppost['body'], true);
			foreach($result['users'] as $contact){
				$wpdb->insert($docebodb, array(
					'id' => $contact['id_user'],
					'userid' => $contact['userid'],
					'firstname' => $contact['firstname'],
					'lastname' => $contact['lastname'],
					'email' => $contact['email']
				));
			}
		}
		// populate docebo courses cache
		$numRows = $wpdb->get_var("SELECT COUNT(*) FROM $docebocoursedb");
		if($numRows == '' || $numRows == null || $numRows == '0'){
			$subdomain = get_option('bf_docebolabs_docebo_subdomain');
			// check for changes in Docebo
			$submiturl = "https://".$subdomain.".docebosaas.com/api/courses/courses";
			$authorization = "Authorization: Bearer ".$access_token['accessToken'];
			$data = array();
			$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
			$httppost = wp_safe_remote_post($submiturl, $options);
			$result = json_decode($httppost['body'], true);
			foreach($result['courses'] as $courses){
				$wpdb->insert($docebocoursedb, array(
					'id' => $courses['course_id'],
					'code' => $courses['code'],
					'course_name' => $courses['course_name']
				));
			}
		}
	}
	// save checkbox value
	update_option('bf_docebolabs_settings', $options);
	$lastcronrun = get_option('bf_docebolabs_cron_last_run');
	if($lastcronrun == null || $lastcronrun == ''){
		$lastcronrun = strtotime('-10 years', current_time('timestamp', true));
		update_option('bf_docebolabs_cron_last_run', $lastcronrun);
	}
}
?>
<div class="wrap">
<h2>DoceboLabs</h2>
	<div class="bf_docebolabs_container">
		<h3>Settings</h3>
		<form method="post" action="options.php">
			<?php settings_fields( 'bf_docebolabs_settings-group' ); ?>
			<?php do_settings_sections( 'bf_docebolabs_settings-group' ); ?>
			<?php wp_nonce_field( 'bf_docebolabs_settings_nonce', '_bf_docebolabs_nonce' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Plugin Registered Email Address</th>
					<td><input type="text" name="bf_docebolabs_regemail" value="<?php echo esc_attr( get_option('bf_docebolabs_regemail') ); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row">Show Scripts in sub menu</th>
					<td><input type="checkbox" name="bf_docebolabs_submenu_show" value="1" <?php checked( esc_attr( get_option('bf_docebolabs_submenu_show') ), 1 ); ?>></td>
				</tr>
				<tr valign="top">
					<th scope="row">Enable hourly Docebo/Infusionsoft data sync</th>
					<td><input type="checkbox" name="bf_docebolabs_enable_cron" value="1" <?php checked( esc_attr( get_option('bf_docebolabs_enable_cron') ), 1 ); ?>></td>
				</tr>
				<!-- docebolabs -->
				<tr valign="top">
					<th scope="row"><h3>Docebo</h3><a href="admin.php?page=bf_docebolabs_menu_settings_help_page" target="_blank">Help to obtain required details</a></th>
					<th scope="row"></th>
					<td></td>
				</tr>
				<tr valign="top">
					<th scope="row"></th>
					<th scope="row">Docebo Sub Domain</th>
					<td><input type="text" name="bf_docebolabs_docebo_subdomain" value="<?php echo esc_attr( get_option('bf_docebolabs_docebo_subdomain') ); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"></th>
					<th scope="row">Docebo API Client ID</th>
					<td><input type="text" name="bf_docebolabs_docebo_client_id" value="<?php echo esc_attr( get_option('bf_docebolabs_docebo_client_id') ); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"></th>
					<th scope="row">Docebo API Client Secret</th>
					<td><input type="text" name="bf_docebolabs_docebo_client_secret" value="<?php echo esc_attr( get_option('bf_docebolabs_docebo_client_secret') ); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"></th>
					<th scope="row">Docebo Super Admin Username</th>
					<td><input type="text" name="bf_docebolabs_docebo_super_admin_username" value="<?php echo esc_attr( get_option('bf_docebolabs_docebo_super_admin_username') ); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"></th>
					<th scope="row">Docebo Super Admin Password</th>
					<td><input type="text" name="bf_docebolabs_docebo_super_admin_password" value="<?php echo esc_attr( get_option('bf_docebolabs_docebo_super_admin_password') ); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"></th>
					<th scope="row">Enable Docebo Intergration</th>
					<td><input type="checkbox" name="bf_docebolabs_enable_docebo" value="1" <?php checked( esc_attr( get_option('bf_docebolabs_enable_docebo') ), 1 ); ?>></td>
				</tr>
				<!-- Infusionsoft -->
				<tr valign="top">
					<th scope="row"><h3>Infusionsoft</h3></th>
					<th scope="row"></th>
					<td></td>
				</tr>
				<tr valign="top">
					<th scope="row"></th>
					<th scope="row">Infusionsoft App Name/ID</th>
					<td><input type="text" name="bf_docebolabs_is_app_name" value="<?php echo esc_attr( get_option('bf_docebolabs_is_app_name') ); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"></th>
					<th scope="row">Infusionsoft API Key</th>
					<td><input type="text" name="bf_docebolabs_is_api_key" value="<?php echo esc_attr( get_option('bf_docebolabs_is_api_key') ); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"></th>
					<th scope="row">Enable Infusionsoft Intergration</th>
					<td><input type="checkbox" name="bf_docebolabs_enable_is" value="1" <?php checked( esc_attr( get_option('bf_docebolabs_enable_is') ), 1 ); ?>></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
</div>