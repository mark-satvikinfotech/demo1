<div class="wrap">
<h2>DoceboLabs - Create Branch</h2>
<div class="bf_docebolabs_container">
	<a href="admin.php?page=bf_docebolabs_menu-home">&lt; Back to More Docebolabs Scripts</a>
	<h3>What it does</h3>
	<p class="descriptive">Creates a New Branch via HTTP Post</p>
	<h3>Setup - Send HTTP POST</h3>
	<p class="descriptive">Using the following, set up the required 'Send HTTP POST' within your Infusionsoft Campaign</p>
	<div class="isview">
		<p class="istext">POST URL</br><div class="isbox" style="max-width: 100%;"><?php echo plugin_dir_url( __FILE__ )."bf_docebolabs_create_branch.php";?></div></p>
		<p class="istext">Name/Value Pairs:</p>
		<table>
			<?php
				$fields = array(
					'BranchName' => 'Provide the New Branch Name (case sensitive)',
					'contactId' => 'Merge ContactID i.e, &#126;Contact.Id&#126; (only required if wanting to collect the Branch ID)',
					'BranchIDField' => 'Field name you wish to store the Branch ID in if required *',
					'ParentBranch' => 'Optional, if Parent Branch name is supplied then new branch will be set as child of parent (case sensitive)'
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