<div class="wrap">
<h2>DoceboLabs - Update User Level</h2>
<div class="bf_docebolabs_container">
	<a href="admin.php?page=bf_docebolabs_menu-home">&lt; Back to More Docebolabs Scripts</a>
	<h3>What it does</h3>
	<p class="descriptive">Upgrade/Down Grade a user level between Power User (Upgrade) and Regular User (Downgrade) via HTTP Post, optionally can also set the Power User's Profile Name while Upgrading</p>
	<h3>Setup - Send HTTP POST</h3>
	<p class="descriptive">Using the following, set up the required 'Send HTTP POST' within your Infusionsoft Campaign</p>
	<div class="isview">
		<p class="istext">POST URL</br><div class="isbox" style="max-width: 100%;"><?php echo plugin_dir_url( __FILE__ )."bf_docebolabs_update_user_level.php";?></div></p>
		<p class="istext">Name/Value Pairs:</p>
		<table>
			<?php
				$fields = array(
					'DoceboIdEmail' => 'Merge contact Email or Docebo Id i.e, &#126;Contact.Email&#126; or &#126;Contact._DoceboId&#126; *',
					'Action' => 'Provide either value \'Upgrade\' or \'Downgrade\'',
					'ProfileName' => '(optional and case sensitive) Provide the Profile Name to set against the Power User whilst Upgrading',
					'BranchID' => '(optional and case sensitive) Merge the field that contains the Branch ID Value to Assign to the Power User whilst Upgrading'
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
	</div>
</div>
</div>