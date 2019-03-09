<div class="wrap">
<h2>DoceboLabs - Reinstate a user's access to a course</h2>
<div class="bf_docebolabs_container">
	<a href="admin.php?page=bf_docebolabs_menu-home">&lt; Back to More Docebolabs Scripts</a>
	<h3>What it does</h3>
	<p class="descriptive">Reinstate a user's access to a particular course via HTTP Post</p>
	<h3>Setup - Send HTTP POST</h3>
	<p class="descriptive">Using the following, set up the required 'Send HTTP POST' within your Infusionsoft Campaign</p>
	<div class="isview">
		<p class="istext">POST URL</br><div class="isbox" style="max-width: 100%;"><?php echo plugin_dir_url( __FILE__ )."bf_docebolabs_reinstate_user_enrolment.php";?></div></p>
		<p class="istext">Name/Value Pairs:</p>
		<table>
			<?php
				$fields = array(
					'contactId' => 'Merge ContactID i.e, &#126;Contact.Id&#126;',
					'DoceboId' => 'Merge the field the Docebo Id is stored in, not required but speeds up function *',
					'CourseCode or CourseId' => 'Merge the field that holds the Course code or Course ID i.e, &#126;Contact._CourseId&#126; *'
				);
				foreach($fields as $key => $value){	
			?>
					<tr>
						<td class="isbox" style="width: 130px;"><?php echo $key;?></td>
						<td> = </td>
						<td class="isbox"><?php echo $value;?></td>
					</tr>
			<?php
				}
			?>
		</table>
		<p class="note"><strong>* NOTE</strong>: If any custom field names are used you must include a leading underscore(_) before the fieldname, i.e, the custom field name maybe CustomField so you would enter _CustomField.<?=(isset($bf_is_page['note'])?'</p><p class="note"><strong>EXTRA NOTE</strong>: '.$bf_is_page['note']:'');?></p>
	</div>
</div>
</div>