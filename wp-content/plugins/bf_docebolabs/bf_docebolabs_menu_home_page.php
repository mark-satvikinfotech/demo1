<?php
date_default_timezone_set("Europe/London");
ini_set('max_execution_time', 600); 
?>
<div class="wrap">
	<h2>DoceboLabs</h2>
	<div class="bf_docebolabs_container">
		<!-- <h3>Infusionsoft API status</h3> -->
		<?php
		$isappname = esc_attr( get_option('bf_docebolabs_is_app_name') );
		$isapikey = esc_attr( get_option('bf_docebolabs_is_api_key') );
		$noticeOutputError = '';
		if(isset($isappname) && $isappname != null && $isappname != '' && isset($isapikey) && $isapikey != null && $isapikey != ''){
			global $connInfo;
			$connInfo = array('isconn:'.$isappname.':i:'.$isapikey.':This is the connection for '.$isappname.'.infusionsoft.com');
			require_once(WP_PLUGIN_DIR."/bf_docebolabs/aisdk.php");  
			$app = new iSDK;
			if($app->cfgCon("isconn")){
				//echo '<p style="color: #00d414; font-weight: bold;">API Connection Confirmed</p>';
			} else {
				$noticeOutputError = '<p style="color: red; font-weight: bold;">Please configure settings first <a href="admin.php?page=bf_docebolabs_menu_settings_page">HERE</a></p><hr/>';
			}
		} else {
			$noticeOutputError = '<p style="color: red; font-weight: bold;">Please configure settings first <a href="admin.php?page=bf_docebolabs_menu_settings_page">HERE</a></p><hr/>';
		}
		if(esc_attr(get_option('bf_docebolabs_enable_docebo')) && esc_attr(get_option('bf_docebolabs_enable_docebo')) == 1){
			$accessToken = bf_docebolabs_fetch_token();
			if(isset($accessToken) && $accessToken['accessToken'] != null && $accessToken['accessToken'] != ''){
				//echo '<p style="color: #00d414; font-weight: bold;">Docebo API Connection Confirmed</p>';
			} else {
				$noticeOutputError = '<p style="color: red; font-weight: bold;">Please configure settings first <a href="admin.php?page=bf_docebolabs_menu_settings_page">HERE</a></p><hr/>';
			}
		} else {
			$noticeOutputError = '<p style="color: red; font-weight: bold;">Please configure settings first <a href="admin.php?page=bf_docebolabs_menu_settings_page">HERE</a></p><hr/>';
		}
		if(isset($noticeOutputError) && $noticeOutputError != null && $noticeOutputError != ''){
			echo $noticeOutputError;
		} else {
		?>
			<ul class="menulist">
				<li><a href="admin.php?page=bf_docebolabs_add_user_page" class="biglink">Add a user to Docebo</a><p>Add a user to Docebo from an is http post</p></li>
				<li><a href="admin.php?page=bf_docebolabs_add_to_course_page" class="biglink">Add a user to a course</a><p>Add new and existing users to a specific course via HTTP Post</p></li>
				<li><a href="admin.php?page=bf_docebolabs_remove_from_course_page" class="biglink">Remove a user from a course</a><p>Remove a user from a specific course via HTTP Post</p></li>
				<li><a href="admin.php?page=bf_docebolabs_revoke_user_enrolment_page" class="biglink">Revoke a user's access to a course</a><p>Revoke a user's access to a particular course via HTTP Post</p></li>
				<li><a href="admin.php?page=bf_docebolabs_reinstate_user_enrolment_page" class="biglink">Reinstate a user's access to a course</a><p>Reinstate a user's access to a particular course via HTTP Post</p></li>
				<li><a href="admin.php?page=bf_docebolabs_update_user_enrolment_page" class="biglink">Update a user's enrolment level</a><p>Amend/Update a user's enrolment level from an HTTP Post</p></li>
				<li><a href="admin.php?page=bf_docebolabs_update_user_level_page" class="biglink">Update User Level</a><p>Update a user's level between Power User and Regular User, optionally can also set the Power User's Profile Name while Upgrading</p></li>
				<li><a href="admin.php?page=bf_docebolabs_set_user_profile_page" class="biglink">Set Power User Profile Name</a><p>Set's the Power User's Profile Name via HTTP Post</p></li>
				<li><a href="admin.php?page=bf_docebolabs_create_branch_page" class="biglink">Create Branch</a><p>Creates a New Branch via HTTP Post</p></li>
				<li><a href="admin.php?page=bf_docebolabs_assign_user_to_branch_page" class="biglink">Assign User to Branch</a><p>Assign's User to a Branch via HTTP Post</p></li>
				<li><a href="admin.php?page=bf_docebolabs_assign_to_poweruser_page" class="biglink">Assign User/Branch/Group to Power User</a><p>Assign's User/Branch/Group to Power User via HTTP Post</p></li>
				<li><a href="admin.php?page=bf_docebolabs_date_calculator_page" class="biglink">Date Calculator</a><p>Calculates Dates and applies value to Infusionsoft via HTTP Post</p></li>
				<li><a href="admin.php?page=bf_docebolabs_assign_course_to_poweruser_page" class="biglink">Assign Course to Power User</a><p>Assign's Course to Power User via HTTP Post</p></li>
				<li><a href="admin.php?page=bf_docebolabs_add_poweruser_to_course_page" class="biglink">Add a Power User to a course</a><p>Add Power User plus free extra seats to a specific course via HTTP Post</p></li>
				<li><a href="admin.php?page=bf_docebolabs_seat_purchase_page" class="biglink">Seat Purchase</a><p>Add a Power User to a course plus any free seats required, with product-course association</p></li>
				<li><a href="admin.php?page=bf_docebolabs_config_sync_page" class="biglink">Configure Sync settings</a><p>Configure Docebo/Infusionsoft Sync settings</p></li>
			</ul>
		<?php
		}
		?>
	</div>
</div>