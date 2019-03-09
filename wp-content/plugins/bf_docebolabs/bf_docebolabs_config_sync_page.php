<?php
$doceboFields = array(
	'id_user',
	'userid',
	'firstname',
	'lastname',
	'password',
	'email',
	'address_1',
	'address_2',
	'city',
	'state',
	'zip',
	'company_name'
);
$access_token = bf_docebolabs_fetch_token();
$skipcustom = get_option('bf_docebo_skipcustomfieldsincron');
$subdomain = get_option('bf_docebolabs_docebo_subdomain');
$submiturl = "https://".$subdomain.".docebosaas.com/api/user/fields";
$data = array();
$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
$httppost = wp_safe_remote_post($submiturl, $options);
$fieldsResult = json_decode($httppost['body'], true);
if(isset($fieldsResult['fields']) && count($fieldsResult['fields']) >= '1'){
	foreach($fieldsResult['fields'] as $field){
		$doceboFields[] = $field['name'];
	}
}
$infusionFields = array(
	'FirstName',
	'LastName',
	'Phone1',
	'Phone1Type',
	'Phone2',
	'Phone2Type',
	'StreetAddress1',
	'StreetAddress2',
	'City',
	'State',
	'Country',
	'PostalCode',
	'Username',
	'Birthday',
	'ContactNotes',
	'Email',
	'Password',
	'Username',
	'Company'
);
// handle default changes
if(isset($_POST['submit']) && $_POST['submit'] == 'Reset to Default'){
	$syncfields = array(
		'id_user' => '_',
		'userid' => 'Username',
		'firstname' => 'FirstName',
		'lastname' => 'LastName',
		'email' => 'Email'
	);
	update_option('bf_docebolabs_sync_fields', $syncfields);
	update_option('bf_docebolabs_tag_new_contact', '');
}
// handle save changes
if(isset($_POST['submit']) && $_POST['submit'] == 'Save Changes'){
	$syncfields = array();
	foreach($_POST['db'] as $key => $value){
		if($value != null && $value != '' && $_POST['is'][$key] != null && $_POST['is'][$key] != ''){
			$syncfields[$value] = $_POST['is'][$key];
		}
	}
	update_option('bf_docebolabs_sync_fields', $syncfields);
	update_option('bf_docebolabs_tag_new_contact', $_POST['bf_docebolabs_tag_new_contact']);
	update_option('bf_docebo_taxable', $_POST['bf_docebo_taxable']);
	update_option('bf_docebo_CountryTaxable', $_POST['bf_docebo_CountryTaxable']);
	update_option('bf_docebo_StateTaxable', $_POST['bf_docebo_StateTaxable']);
	update_option('bf_docebo_CityTaxable', $_POST['bf_docebo_CityTaxable']);
	update_option('bf_docebo_skipcustomfieldsincron', $_POST['skipcustomfieldsincron']);
}
$syncfields = get_option('bf_docebolabs_sync_fields');
$tag_selected = get_option('bf_docebolabs_tag_new_contact');
if(!isset($syncfields) || !is_array($syncfields) || $syncfields == '' || $syncfields == null){
	$syncfields = array(
		'id_user' => '_',
		'userid' => 'Username',
		'firstname' => 'FirstName',
		'lastname' => 'LastName',
		'password' => 'Password',
		'email' => 'Email'
	);
}
?>
<div class="wrap">
<h2>DoceboLabs - Configure Sync settings</h2>
<div class="bf_docebolabs_container">
	<a href="admin.php?page=bf_docebolabs_menu-home">&lt; Back to More Docebolabs Scripts</a>
	<style>.form-table th, .form-table td {padding: 0px 10px 2px 0;}</style>
	<form method="post" action="">
		<h3>Sync Fields</h3>
		<p class="descriptive">Configure Docebo/Infusionsoft Sync settings.</br><i>Note: Docebo field id_user is the Docebo numerical ID value for a contact, userid is the value used as a username for when the user signs into Docebo i.e, Username or Email.</i></p>
		<table id="form-table" class="form-table">
			<tr valign="top">
				<th scope="row">Docebo Field</th>
				<th scope="row">Infusionsoft Field</th>
				<td></td>
			</tr>
			<?php
			echo '<datalist id="docebofields">';
			foreach($doceboFields as $field){
				echo '<option value="'.$field.'">';
			}
			echo '</datalist>';
			echo '<datalist id="infusionfields">';
			foreach($infusionFields as $field){
				echo '<option value="'.$field.'">';
			}
			echo '</datalist>';
			$x = '0';
			foreach($syncfields as $dbfield => $isfield){
			?>
			<tr valign="top" id="row-<?=$x;?>">
				<td scope="row"><input type="text" id="dbfield" name="db[<?=$x;?>]" value="<?=$dbfield;?>" list="docebofields" /></td>
				<td scope="row"><input type="text" id="isfield" name="is[<?=$x;?>]" value="<?=$isfield;?>" list="infusionfields" /></td>
				<td><a href="#" onclick="removeRow(<?=$x;?>);return false;">delete</a></td>
			</tr>
			<?php
			$x++;
			}
			?>
			<tr valign="top" id="row-<?=$x;?>">
				<td scope="row"><input type="text" id="dbfield" name="db[<?=$x;?>]" value="" list="docebofields" /></td>
				<td scope="row"><input type="text" id="isfield" name="is[<?=$x;?>]" value="" list="infusionfields" /></td>
				<td><a href="#" onclick="removeRow(<?=$x;?>);return false;">delete</a></td>
			</tr>
		</table>
		<p class="descriptive"><input type="checkbox" name="skipcustomfieldsincron"  value="1" <?php checked( esc_attr( get_option('bf_docebo_skipcustomfieldsincron') ), 1 ); ?>> Skip custom fields in hourly cron sync (reduces cron duration substantially, only syncs fields: id_user, userid, firstname, lastname and email)</p>
		<h3>Tag New Contacts in Infusionsoft</h3>
		<p class="descriptive">If a Tag is selected then this will be assigned to new Infusionsoft contacts when added in the sync.</p>
		<?php
			// select tag to define contacts to work with
			global $connInfo;
			$connInfo = array('isconn:'.esc_attr( get_option('bf_docebolabs_is_app_name') ).':i:'.esc_attr( get_option('bf_docebolabs_is_api_key') ).':This is the connection for '.esc_attr( get_option('bf_docebolabs_is_app_name') ).'.infusionsoft.com');
			require_once(WP_PLUGIN_DIR."/bf_docebolabs/aisdk.php");
			$app = new iSDK;
			if($app->cfgCon("isconn")){
				$returnFields = array('Id', 'GroupName');
				$query = array('Id' => '%');
				$run = 'true';
				$x = '0';
				$tags = array();
				while($run == 'true'){
					$tagstemp = $app->dsQuery("ContactGroup",1000,$x,$query,$returnFields);
					$tags = array_merge($tags, $tagstemp);
					if(count($tagstemp) <= '999'){
						$run = 'false';
					}
					$x++;
				}
			}
			echo '<select name="bf_docebolabs_tag_new_contact" style="margin-left: 1.5em; margin-bottom: 1.5em;">';
			echo '<option value="" '.(!isset($tag_selected) || $tag_selected == null || $tag_selected == '' ? 'selected' : '').'>Select a Tag</option>';
			foreach($tags as $tag){
				echo '<option value="'.$tag['Id'].'" '.($tag_selected == $tag['Id'] ? 'selected' : '').'>'.$tag['GroupName'].' - Tag Id: '.$tag['Id'].'</option>';
			}
			echo '</select></br>';
		?>
		<h3>Tax Settings for New Products in Infusionsoft</h3>
		<p class="descriptive">If a Product doesnt exist in Infusionsoft it will be dynamically created, please choose the tax settings to be applied to all Products being created.</p>
		<p class="descriptive"><input type="checkbox" id="bf_docebo_taxable" name="bf_docebo_taxable" value="1" <?php checked( esc_attr( get_option('bf_docebo_taxable') ), 1 ); ?>> Taxable</p>
		<div id="taxable" style="margin-left: 1.5em;">
			<p class="descriptive"><input type="checkbox" name="bf_docebo_CountryTaxable" value="1" <?php checked( esc_attr( get_option('bf_docebo_CountryTaxable') ), 1 ); ?>> Country Taxable</p>
			<p class="descriptive"><input type="checkbox" name="bf_docebo_StateTaxable" value="1" <?php checked( esc_attr( get_option('bf_docebo_StateTaxable') ), 1 ); ?>> State Taxable</p>
			<p class="descriptive"><input type="checkbox" name="bf_docebo_CityTaxable" value="1" <?php checked( esc_attr( get_option('bf_docebo_CityTaxable') ), 1 ); ?>> City Taxable</p>
		</div>
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" style="margin-top: 1.5em; margin-bottom: 1.5em;"><input type="submit" name="submit" id="submit" class="button button-secondary" value="Reset to Default" style="float: right;">
	</form>
	<script>
	$ = jQuery.noConflict();
	$(function(){
		$(document).on("change","input", function(){
			var allGood=true;
			var lastInputField=0;
			$("input").each(function() {
				if ($(this).val() =="") {
					allGood=false;
					return false;
				}
				if($(this).attr('id') == 'dbfield'){
					lastInputField++;
				}
			});

			if (allGood) {
				$('#form-table').append('<tr valign="top" id="row-' + lastInputField + '"><td scope="row"><input type="text" name="db-[' + lastInputField + ']" value="" list="docebofields" /></td><td scope="row"><input type="text" name="is-[' + lastInputField + ']" value="" list="infusionfields" /></td><td><a href="#" onclick="removeRow(' + lastInputField + ');return false;">delete</a></td></tr>');
			}
		});

		// handle taxables
		if($("input[name=bf_docebo_taxable]").is(':checked')){
			$('#taxable').show();
		} else {
			$('#taxable').hide();
		}
		$("input[name=bf_docebo_taxable]").click(function () {
			$('#taxable').toggle();
		});
	});
	function removeRow(id){
		var elem = document.getElementById('row-' + id);
		elem.parentNode.removeChild(elem);
		return false;
	};
	</script>
	<h3>Sync Data</h3>
	<p class="descriptive">Sync last ran: <?=date('d-m-Y H:i:s', get_option('bf_docebolabs_cron_last_run'));?> UTC, (<?=date('d-m-Y H:i:s', (get_option('bf_docebolabs_cron_last_run') + (get_option('gmt_offset') * 3600))).' '.get_option('timezone_string');?>)</p>
	<p class="descriptive">Sync next run: <?=date('d-m-Y H:i:s', wp_next_scheduled('bf_docebolabs_cron_schedule'));?> UTC, (<?=date('d-m-Y H:i:s', (wp_next_scheduled('bf_docebolabs_cron_schedule') + (get_option('gmt_offset') * 3600))).' '.get_option('timezone_string');?>)</p>	
	<p class="descriptive">Sync Course next run: <?=date('d-m-Y H:i:s', wp_next_scheduled('bf_docebolabs_course_cron_schedule'));?> UTC, (<?=date('d-m-Y H:i:s', (wp_next_scheduled('bf_docebolabs_course_cron_schedule') + (get_option('gmt_offset') * 3600))).' '.get_option('timezone_string');?>)</p>
	<p class="descriptive">Sync purchase next run: <?=date('d-m-Y H:i:s', wp_next_scheduled('bf_docebolabs_purchase_cron_schedule'));?> UTC, (<?=date('d-m-Y H:i:s', (wp_next_scheduled('bf_docebolabs_purchase_cron_schedule') + (get_option('gmt_offset') * 3600))).' '.get_option('timezone_string');?>)</p>
</div>
</div>