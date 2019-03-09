<?php
 function manage_help()
 {
	 
	 $tempoptions = get_option("geopal_credential");
	 $cred_arr = unserialize($tempoptions);
	 $dynamic_url = get_site_url();
	 if($cred_arr['uname'] == "hes0012" &&  $cred_arr['pass'] == "48hrlaunch" && $cred_arr['app_name'] == "qn241" && $cred_arr['app_key'] == "c31ce6f980e4c9de594a39bb78fda91d" )
	 {
	  ?>
	  <div class="container">
	  <fieldset class="col-md-10"> 
	  <?php
	   echo "<h4><b>Please set below Webhook in GeoPal:</b><br/><br/>";
	   echo $dynamic_url."?geopalcron=Y<br/><br/>";

	   //echo $dynamic_url."/wp-content/plugins/GeoPal-IS/geo_to_ifs.php<br/><br/>";

	   echo "<b>Please set below Webhook in Infusionsoft:<br/><br/></b>";
	   echo $dynamic_url."?Iscron=Y</h4>";
  
	 }
	 else
	 {
		 echo "<h4>You don't have access.</h4>";
	 }
	 ?>
	 </div>
	 </fieldset>
	 <?php
 }

?>