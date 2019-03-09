<div class="wrap">
<h2>DoceboLabs - Assign User/Branch/Group to Power User</h2>
<div class="bf_docebolabs_container">
	<a href="admin.php?page=bf_docebolabs_menu-home">&lt; Back to More Docebolabs Scripts</a>
	<h3>What it does</h3>
	<p class="descriptive">Assign's User/Branch/Group to Power User via HTTP Post</p>
	<h3>Setup - Send HTTP POST</h3>
	<p class="descriptive">Using the following, set up the required 'Send HTTP POST' within your Infusionsoft Campaign</p>
	<div class="isview">
		<p class="istext">POST URL</br><div class="isbox" style="max-width: 100%;"><?php echo plugin_dir_url( __FILE__ )."bf_docebolabs_assign_to_poweruser.php";?></div></p>
		<p class="istext">Name/Value Pairs:</p>
		<table>
			<?php
				$fields = array(// amend these fields **************************
					'DoceboIdEmail' => 'Merge contact Email or Docebo Id of the Power User i.e, &#126;Contact.Email&#126; or &#126;Contact._DoceboId&#126; *',
					'UserToAdd' => '(Optional, can use 1 or more of the optional fields) Merge the field that contains the User Docebo ID Value to Assign to the Power User',
					'BranchToAdd' => '(Optional, can use 1 or more of the optional fields) Merge the field that contains the Branch ID Value to Assign to the Power User',
					'GroupToAdd' => '(Optional, can use 1 or more of the optional fields) Merge the field that contains the Group Name Value to Assign to the Power User'
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