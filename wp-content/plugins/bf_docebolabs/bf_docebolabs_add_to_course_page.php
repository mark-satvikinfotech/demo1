<div class="wrap">
<h2>DoceboLabs - Add a user to a course</h2>
<div class="bf_docebolabs_container">
	<a href="admin.php?page=bf_docebolabs_menu-home">&lt; Back to More Docebolabs Scripts</a>
	<h3>What it does</h3>
	<p class="descriptive">Add new and existing users to a specific course via HTTP Post</p>
	<h3>Setup - Send HTTP POST</h3>
	<p class="descriptive">Using the following, set up the required 'Send HTTP POST' within your Infusionsoft Campaign</p>
	<div class="isview">
		<p class="istext">POST URL</br><div class="isbox" style="max-width: 100%;"><?php echo plugin_dir_url( __FILE__ )."bf_docebolabs_add_to_course.php";?></div></p>
		<p class="istext">Name/Value Pairs:</p>
		<table>
			<?php
				$fields = array(
					'contactId' => 'Merge ContactID i.e, &#126;Contact.Id&#126;',
					'DoceboIdEmail' => 'Merge contact Email or Docebo Id i.e, &#126;Contact.Email&#126; or &#126;Contact._DoceboId&#126; *',
					'CourseCode or CourseId' => 'Merge the field that holds the Course code or Course ID i.e, &#126;Contact._CourseId&#126; *',
					'UserLevel' => 'Set the User level within the course to either student, instructor or tutor, this value can be manually set or Merged from an Infusionsoft contact field, if left blank or excluded then user will default to student *',
					'UserNameIfNew' => 'If contact doesnt exist in Docebo already then create user with this login can be merge from Username or other field even Email i.e, &#126;Contact.Username&#126; or &#126;Contact.Email&#126; Note: If left out will default to whatever value is in Username Field in Infusionsoft Contact record',
					'PasswordIfNew' => 'If contact doesnt exist in Docebo already then create user and store the Password in this Infusionsoft field *',
					'DoceboIdIfNew' => 'If contact doesnt exist in Docebo already then create user and store the Docebo Id in this Infusionsoft field *'
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