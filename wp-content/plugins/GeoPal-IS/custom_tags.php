<?php
 include_once('config.php');
 function manage_customtags()
 {
	 include_once('lib/infusionsoft/is_config.php');
	
	$table = "ContactGroup";
	$page = 0;
	$limit = 100;
	$query = array('Id'=>'~<>~');
	$returnFields = array('Id','FirstName','LastName','Email','Phone1');
	$orderBy = "Id";
	$ascending = false;
	
	$returnFields = array('GroupCategoryId','GroupDescription','GroupName', 'Id');
	
	$tagslist = $app->dsQuery($table,$limit,0,$query,$returnFields,$orderBy,$ascending);
	
	//get custom tag from wp_options table
	$a = get_option('appointment_custom_tags');
	$profile_arr = unserialize($a);		
    $crt_tag = $profile_arr['apptmnt_crt_tag'];
	$upd_tag = $profile_arr['apptmnt_upd_tag'];
	$del_tag = $profile_arr['apptmnt_del_tag'];	
					
	?>
	
	<div class="container">
	 <div id="msg"></div>
	  <form method="post" id="frmappointment">
	  <!--job custom fieldset -->
	  <fieldset class="col-md-14"> 
	  <legend>Assign Tags based on Appointment Status of GeoPal</legend><br/>
	  
	   <div class="form-group">
	    	<div class="col-md-3 col-sm-3 col-xs-3">
		   		<label for="Appointment">On Appointment Create:</label>
			 	<select id="appointment_create" name="appointment_create" class="form-control">
			   <option>Select Tag</option>
			   <?php
			   for($i=0; $i<count($tagslist); $i++)
			   {
				  if($crt_tag == $tagslist[$i]['Id']) 
				  {
					?>
					<option value="<?php echo $tagslist[$i]['Id']; ?>" selected><?php echo $tagslist[$i]['GroupName']; ?></option>
					<?php
				  }
				  else
				  {
					?>
					<option value="<?php echo $tagslist[$i]['Id']; ?>"><?php echo $tagslist[$i]['GroupName']; ?></option>
					<?php
				  }
			   }
			   ?>
			 	</select>
		 	</div>
		 	<!-- Appointment update tag -->
		 	<div class="col-md-3 col-sm-3 col-xs-3">
			  	<label for="Appointment"> On Appointment Update:</label>
				 <select id="appointment_update" name="appointment_update" class="form-control">
				   <option>Select Tag</option>
				   <?php
				   for($i=0; $i<count($tagslist); $i++)
				   {
					  if($upd_tag == $tagslist[$i]['Id']) 
					  {
						?>
						<option value="<?php echo $tagslist[$i]['Id']; ?>" selected><?php echo $tagslist[$i]['GroupName']; ?></option>
						<?php
					  }
					  else
					  {
						?>
						<option value="<?php echo $tagslist[$i]['Id']; ?>"><?php echo $tagslist[$i]['GroupName']; ?></option>
						<?php
					  }
				   }
				   ?>
				 </select>
			</div>
			<!-- Appointment delete tag -->
			<div class="col-md-3 col-sm-3 col-xs-3">
			  <label for="Appointment">On Appointment Delete:</label>
				 <select id="appointment_delete" name="appointment_delete" class="form-control">
				   <option>Select Tag</option>
				   <?php
				   for($i=0; $i<count($tagslist); $i++)
				   {
					  if($del_tag == $tagslist[$i]['Id']) 
					  {
						?>
						<option value="<?php echo $tagslist[$i]['Id']; ?>" selected><?php echo $tagslist[$i]['GroupName']; ?></option>
						<?php
					  }
					  else
					  {
						?>
						<option value="<?php echo $tagslist[$i]['Id']; ?>"><?php echo $tagslist[$i]['GroupName']; ?></option>
						<?php
					  }
				   }
				   ?>
				 </select>
			</div>
			<div class="col-md-3 col-sm-3 col-xs-3">
				<input type="button" class="btn btn-info" name="appointmentbtn_update" id="appointmentbtn_update" onclick="appntmntcreate()" value="Update">
			</div>
		</div>
		</fieldset>
		
	   <!-- Engineers custom tag -->
	  <!--<fieldset class="col-md-10"> 
	   <legend>Engineers Custom Tag:</legend>
	   <div class="form-group">
		  <div class="col-md-3 col-sm-3 col-xs-3">
		  <label for="Appointment">Engineer Create Tag:</label>
			 <select id="engineer_create" class="form-control">
			   <option>Select Tag</option>
			   <?php
			  /* for($i=0; $i<count($tagslist); $i++)
			   {
				?>
				<option value="<?php echo $tagslist[$i]['Id']; ?>"><?php echo $tagslist[$i]['GroupName']; ?></option>
				<?php
			   }
			   ?>
			 </select>
		 </div>
		</div>
		 <!-- Engineers update tag -->
		 <div class="form-group">
			 <div class="col-md-3 col-sm-3 col-xs-3">
			  <label for="Appointment">Engineer Update Tag:</label>
				 <select id="engineer_update" class="form-control">
				   <option>Select Tag</option>
				   <?php
				   for($i=0; $i<count($tagslist); $i++)
				   {
					?>
					<option value="<?php echo $tagslist[$i]['Id']; ?>"><?php echo $tagslist[$i]['GroupName']; ?></option>
					<?php
				   }
				   ?>
				 </select>
			 </div>
		 </div>
		 <!-- Engineers delete tag -->
		 <div class="form-group">
			 <div class="col-md-3 col-sm-3 col-xs-3">
			  <label for="Appointment">Engineer Delete Tag:</label>
				 <select id="engineer_delete" class="form-control">
				   <option>Select Tag</option>
				   <?php
				   for($i=0; $i<count($tagslist); $i++)
				   {
					?>
					<option value="<?php echo $tagslist[$i]['Id']; ?>"><?php echo $tagslist[$i]['GroupName']; ?></option>
					<?php
				   }
				   ?>
				 </select>
			 </div>
			 <br/>
			 <button class="btn btn-info" name="engineerbtn_update">Engineer Tag Update</button>
		 </div>
		</fieldset>-->
		
	   <!-- Properties custom tag -->
	   <!--<fieldset class="col-md-10"> 
	   <legend>Properties Custom Tag:</legend>
	   <div class="form-group">
		  <div class="col-md-3 col-sm-3 col-xs-3">
		  <label for="Appointment">Properties Create Tag:</label>
			 <select id="properties_create" class="form-control">
			   <option>Select Tag</option>
			   <?php
			   for($i=0; $i<count($tagslist); $i++)
			   {
				?>
				<option value="<?php echo $tagslist[$i]['Id']; ?>"><?php echo $tagslist[$i]['GroupName']; ?></option>
				<?php
			   }
			   ?>
			 </select>
		 </div>
		</div>
		 <!-- Properties update tag -->
		 <div class="form-group">
			 <div class="col-md-3 col-sm-3 col-xs-3">
			  <label for="Appointment">Properties Update Tag:</label>
				 <select id="properties_update" class="form-control">
				   <option>Select Tag</option>
				   <?php
				   for($i=0; $i<count($tagslist); $i++)
				   {
					?>
					<option value="<?php echo $tagslist[$i]['Id']; ?>"><?php echo $tagslist[$i]['GroupName']; ?></option>
					<?php
				   }
				   ?>
				 </select>
			 </div>
		 </div>
		 <!-- Properties delete tag -->
		 <div class="form-group">
			 <div class="col-md-3 col-sm-3 col-xs-3">
			  <label for="Appointment">Properties Delete Tag:</label>
				 <select id="properties_delete" class="form-control">
				   <option>Select Tag</option>
				   <?php
				   for($i=0; $i<count($tagslist); $i++)
				   {
					?>
					<option value="<?php echo $tagslist[$i]['Id']; ?>"><?php echo $tagslist[$i]['GroupName']; ?></option>
					<?php
				   }*/
				   ?>
				 </select>
			 </div>
		 </div>
		 <br/>
		 <button class="btn btn-info" name="engineerbtn_update">Properies Tag Update</button>
		</fieldset>-->
	  </form>
	 
	</div>
	<?php
	
 }
?>