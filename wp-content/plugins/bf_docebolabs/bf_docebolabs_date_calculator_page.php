<div class="wrap">
<h2>DoceboLabs - Date Calculator</h2>
<div class="bf_docebolabs_container">
	<a href="admin.php?page=bf_docebolabs_menu-home">&lt; Back to More Docebolabs Scripts</a>
	<h3>What it does</h3>
	<p class="descriptive">Calculates Dates and applies value to Infusionsoft via HTTP Post</p>
	<h3>Setup - Send HTTP POST</h3>
	<p class="descriptive">Using the following, set up the required 'Send HTTP POST' within your Infusionsoft Campaign</p>
	<div class="isview">
		<p class="istext">POST URL</br><div class="isbox" style="max-width: 100%;"><?php echo plugin_dir_url( __FILE__ )."bf_docebolabs_date_calculator.php";?></div></p>
		<p class="istext">Name/Value Pairs:</p>
		<table>
			<?php
				$fields = array(
					'contactId' => 'Merge ContactID i.e, &#126;Contact.Id&#126;',
					'DateInputValue' => 'Merge the field that holds the Date to amend i.e, &#126;_DateField&#126;',
					'DateOutputField' => 'Enter the field name for the Date to be stored too *',
					'Adjustment' => 'Enter the number of Days/Months you wish to Add/Subtract from i.e, +365 days OR +1 month OR -14 days OR -1 month'
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