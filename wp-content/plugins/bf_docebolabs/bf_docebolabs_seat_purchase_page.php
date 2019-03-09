<div class="wrap">
<h2>DoceboLabs - Seat Purchase</h2>
<div class="bf_docebolabs_container">
	<a href="admin.php?page=bf_docebolabs_menu-home">&lt; Back to More Docebolabs Scripts</a>
	<h3>What it does</h3>
	<p class="descriptive">Add Power User plus free extra seats to a specific course via hourly cron (automated process), with product-course association</p>
	<?php
	global $connInfo, $wpdb, $doceboproductcoursedb;
	$doceboproductcoursedb = $wpdb->prefix . "_bf_docebolabs_productcourse";
	$charset_collate = $wpdb->get_charset_collate();
	if(isset($_POST['submit']) && $_POST['submit'] == 'Save Product Course Association'){
		// wipe db
		$wpdb->query('TRUNCATE TABLE '.$doceboproductcoursedb);

		// insert each row to db
		foreach($_POST['product_id'] as $key => $product_id){
			if(isset($product_id) && $product_id != null && $product_id != '' && isset($_POST['course_id'][$key]) && $_POST['course_id'][$key] != null && $_POST['course_id'][$key] != ''){
				//echo '<p>key: '.$key.'</p>';
				$wpdb->query($wpdb->prepare("INSERT INTO $doceboproductcoursedb(product_id, course_id) VALUES (%s, %s)", $product_id, $_POST['course_id'][$key]));
			}
		}
	}
	$connInfo = array('isconn:'.esc_attr( get_option('bf_docebolabs_is_app_name') ).':i:'.esc_attr( get_option('bf_docebolabs_is_api_key') ).':This is the connection for '.esc_attr( get_option('bf_docebolabs_is_app_name') ).'.infusionsoft.com');
	require_once(WP_PLUGIN_DIR."/bf_docebolabs/aisdk.php");
	$app = new iSDK;
	if($app->cfgCon("isconn")){
	?>
		<h3>Setup - Product Course Association</h3>
		<form method="post" action="">
			<table id="productcoursetable">
			<tr>
				<th>Infusionsoft Product Id</th>
				<th>Docebo Course Id</th>
				<th></th>
			</tr>
			<?php
			// collect all products from infusionsoft
			$returnFields = array('Id', 'ProductName');
			$query = array('Id' => '%');
			$run = true;
			$page = '0';
			$productlist = array();
			while($run == true){
				$productlisttemp = $app->dsQuery("Product",1000,$page,$query,$returnFields);
				$productlist = array_merge($productlist, $productlisttemp);
				if(count($productlisttemp) <= '999'){
					$run = false;
				}
				unset($productlisttemp);
				$page++;
			}
			echo '<datalist id="productlist">';
			foreach($productlist as $field){
				echo '<option value="'.$field['Id'].'">'.$field['ProductName'].'</option>';
			}
			echo '</datalist>';

			// collect all courses from docebo
			$access_token = bf_docebolabs_fetch_token();
			$subdomain = get_option('bf_docebolabs_docebo_subdomain');
			$submiturl = "https://".$subdomain.".docebosaas.com/api/course/courses";
			$authorization = "Authorization: Bearer ".$access_token['accessToken'];
			$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
			$httppost = wp_safe_remote_post($submiturl, $options);
			$courselist = json_decode($httppost['body'], true);
			echo '<datalist id="courselist">';
			if(isset($courselist['success']) && $courselist['success'] == 1 && count($courselist['success']) >= '1'){
				foreach($courselist['courses'] as $field){
					echo '<option value="'.$field['course_id'].'">'.$field['course_name'].'</option>';
				}
			}
			echo '</datalist>';
			$x = '0';

			// collect from doceboproductcoursedb
			$sql = "SELECT * FROM $doceboproductcoursedb";
			$productcourse_results = $wpdb->get_results($sql);

			if(isset($productcourse_results) && count($productcourse_results) >= '1'){
				foreach($productcourse_results as $productcourse_result){
					?>
					<tr valign="top" id="row-<?=$x;?>">
						<td scope="row"><input type="text" class="watchinput" name="product_id[<?=$x;?>]" value="<?=$productcourse_result->product_id;?>" list="productlist" style="width: 300px!important;"/></td>
						<td scope="row"><input type="text" name="course_id[<?=$x;?>]" value="<?=$productcourse_result->course_id;?>" list="courselist" style="width: 300px!important;"/></td>
						<td><a href="#" onclick="docebolabsRemoveRow(<?=$x;?>);return false;">delete</a></td>
					</tr>
					<?php
					$x++;
				}
			}
			?>
			<tr valign="top" id="row-<?=$x;?>">
				<td scope="row"><input type="text" class="watchinput" name="product_id[<?=$x;?>]" value="" placeholder="Select Product" list="productlist" style="width: 300px!important;" /></td>
				<td scope="row"><input type="text" name="course_id[<?=$x;?>]" value="" placeholder="Select Course" list="courselist" style="width: 300px!important;" /></td>
				<td><a href="#" onclick="docebolabsRemoveRow(<?=$x;?>);return false;">delete</a></td>
			</tr>
			</table>
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Product Course Association">
		</form>
		<script>
		$ = jQuery.noConflict();
		$(function(){
			$(document).on("change","input", function(){
				var tableemptyfields=0;
				var lastInputField=0;
				$(".watchinput").each(function() {
					if($(this).val() == null || $(this).val() == '') {
						tableemptyfields++;
					}
					lastInputField++;
				});
				if(tableemptyfields == '0'){
					$('#productcoursetable').append('<tr valign="top" id="row-' + lastInputField + '"><td scope="row"><input type="text" class="watchinput" name="product_id[' + lastInputField + ']" value="" placeholder="Select Product" list="productlist" style="width: 300px!important;" /></td><td scope="row"><input type="text" name="course_id[' + lastInputField + ']" value="" placeholder="Select Course" list="courselist" style="width: 300px!important;" /></td><td><a href="#" onclick="docebolabsRemoveRow(' + lastInputField + ');return false;">delete</a>');
				}
			});
		});
		function docebolabsRemoveRow(id){
			var elem = document.getElementById('row-' + id);
			elem.parentNode.removeChild(elem);
			return false;
		};
		</script>
	<?php
	}
	?>
</div>
</div>