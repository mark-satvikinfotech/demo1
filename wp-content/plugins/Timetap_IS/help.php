 <?php function manage_help() {	
 $dynamic_url = get_site_url();	 
 echo "<h4>Please set below Cron For Sync Contacts between Reamaze to Infusionsoft</h4>";      echo "<b>$dynamic_url?ReamazeContactIScron=Y</b>"; 
 echo "<br/><br/>"; 	 
 echo "<h4>Please set below Cron For Sync Contact Updates From Infusionsoft to Reamaze</h4>";	
 echo "<b>$dynamic_url?cron4old=Y</b>"; 
 echo "<br/><br/>"; 	
 echo "<h4>Please set below Cron For Display Email History from Infusionsoft to Reamaze as Email Conversation</h4>";
 echo "<b>$dynamic_url?ISEmailhistorytoReamazecron=Y</b>"; 
 echo "<br/><br/>"; 	
 echo "<h4>Please set below Campaign(Webhook)For Trigger Text Conversation in Reamaze, via an HTTP Post from Infusionsoft</h4>";	
 echo "<b>$dynamic_url?ISTextconversiontoReamazecron=Y</b>"; 
  echo "<br/><br/>";
 echo "<img src=".TIMETAP_DIR_PLUGIN_URL."IS_CUSTOM_FIELDS.PNG />";
 echo "<br/><br/>"; 	
 echo "<h4>Please set below Cron For Sync Text Messages sent from TimeTap to display as conversations in Reamaze</h4>"; 
 echo "<b>$dynamic_url?TimetapmsgToReamazeconversioncron=Y</b>";  
 echo "<br/><br/>"; 	
 echo "<h4>Please set below Cron For Sync TimeTap Invoices as Infusionsoft Orders</h4>";	
 echo "<b>$dynamic_url?TimetapInvoiceopencron=Y</b>";	
 echo "<br/>"; 
 echo "<b>$dynamic_url?TimetapInvoiceclosedcron=Y</b>";
 echo "<br/>";	
 echo "<b>$dynamic_url?TimetapInvoicevoidcron=Y</b>"; 
 echo "<br/>"; 
 
 echo "<br/><br/>"; 
 echo "<h4>Merge Duplicate Contacts</h4>";
 echo "<b>$dynamic_url?ismergeduplicatecontact=Y</b>";
  echo "<br/><br/>";
 echo "<img src=".TIMETAP_DIR_PLUGIN_URL."merge_contacts.png />";
 }