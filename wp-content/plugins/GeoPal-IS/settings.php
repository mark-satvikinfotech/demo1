<?php
//include_once('lib/geopal/Geopal.php');
include_once('config.php');
function manage_settings()
{
	 $tempoptions = get_option("geopal_credential");
	 $cred_arr = unserialize($tempoptions);
	 if(isset($cred_arr['uname']))
	 {
	   $uname = $cred_arr['uname'];
	 }
	 else
	 {
	   $uname = '';
	 }
	 if(isset($cred_arr['pass']))
	 {
		 $pass = $cred_arr['pass'];
	 }
	 else
	 {
		$pass = '';
	 }
	 if(isset($cred_arr['app_name']))
	 {
	   $app_name = $cred_arr['app_name'];
	 }
	 else
	 {
	   $app_name = '';
	 }
	 if(isset($cred_arr['app_key']))
	 {
	   $app_key = $cred_arr['app_key'];
	 }
	 else
	 {
	   $app_key = '';
	 }
	?>
	<div class="container">
    <div class="stepwizard">
        <div class="stepwizard-row setup-panel">
            <div class="stepwizard-step col-xs-3"> 
                <a href="#step-1" type="button" class="btn btn-success btn-circle btn-primary">1</a>
                <p><small>Credentials</small></p>
            </div>
            <div class="stepwizard-step col-xs-3"> 
                <a href="#step-2" type="button" class="btn btn-default btn-circle btn-primary" disabled="disabled">2</a>
                <p><small>Success</small></p>
            </div>
           
        </div>
    </div>
    
    <form role="form" method="post" name="frminfusedpress" id="frminfusedpress">
        <div class="panel panel-primary setup-content" id="step-1">
            <div class="panel-heading">
                 <h3 class="panel-title">Provide Application Credential:</h3>
            </div>
            <div class="panel-body">
                                   <div class="form-group">
									 <div class="col-md-6 col-sm-3 col-xs-3">
										<label for="username">GeoPal's Username:</label>
											<input maxlength="100" type="text" required="required" name="uname" id="uname" class="form-control" placeholder="Username..." value="<?php echo $uname; ?>" />
									 </div>
									
									 <div class="col-md-6 col-sm-3 col-xs-3">
										<label for="password">GeoPal's Password:</label>
										<input type="password"  required="required" name="pass" placeholder="Password..." class="form-control" id="pass" value="<?php echo $pass; ?>">
									 </div>
									</div>	 
								    <div class="form-group">
									 <div class="col-md-6 col-sm-3 col-xs-3">
										<label for="appkey">Infusionsoft's App Name:</label>
											<input maxlength="100" type="text" required="required" name="app_name" id="app_name" class="form-control" placeholder="App key..." value="<?php echo $app_name; ?>" />
									 </div>
									 
									 <div class="col-md-6 col-sm-3 col-xs-3">
										<label for="appsecret">Infusionsoft's App Key:</label>
											<input maxlength="100" type="text" required="required" name="app_key" id="app_key" class="form-control" placeholder="App secret..." value="<?php echo $app_key; ?>" />
									 </div>
									</div>
									<div class="form-group">
									  <div class="col-md-6 col-sm-3 col-xs-3">
									  </div>
									</div>
									<br/><br/>
								<div class="form-group">
							
                                      <button class="btn btn-primary nextBtn pull-right" onclick="geo_credential()" id="geopal_credential"  type="button">Next</button>
								  </div>
            </div>				
        </div>
        <div class="panel panel-primary setup-content" id="step-2">
            <div class="panel-heading" >
                <h3 class="panel-title">Set up your account:</h3>
            </div>
			 
             <div class="panel-body">
			 <div class="form-group" >
			     <p  id="credential"> </p>
			 </div>
			  
            </div>
			
        </div>
    </form>
</div>
<?php
}
?>