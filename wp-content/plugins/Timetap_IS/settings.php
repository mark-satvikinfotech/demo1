<?php



function manage_settings(){

	

    $tempoptions = get_option("timetap_credential");

	$cred_arr = unserialize($tempoptions);

	if(isset($cred_arr['tpkey']))



	 {



	   $tpkey = $cred_arr['tpkey'];



	 }



	 else



	 {



          

	   



	   $tpkey = '';



	   





	 }



	 if(isset($cred_arr['tappkey']))

	 {



		 $tappkey = $cred_arr['tappkey'];



	 }



	 else



	 {



		$tappkey = '';



	 }



	 if(isset($cred_arr['client_id']))



	 {



	   $client_id = $cred_arr['client_id'];



	 }



	 else



	 {



	   $client_id = '';



	 }



	 if(isset($cred_arr['client_secrete']))



	 {



	   $client_secrete = $cred_arr['client_secrete'];



	 }



	 else



	 {



	   $client_secrete = '';



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

			 					

			 <div class="col-md-6 col-sm-6 col-xs-12">

				<label for="Api key">Timetap's Api Key:</label>

				<input type="text"  required="required" name="tappkey" placeholder="Timetap's Api Key..." class="form-control" id="tappkey" value="<?php echo $tappkey; ?>">

			 </div>



			 <div class="col-md-6 col-sm-6 col-xs-12">

				<label for="Privatekey">Timetap's Privatekey:</label>

					<input maxlength="100" type="password" required="required" name="tpkey" id="tpkey" class="form-control" placeholder="Timetap's Privatekey..." value="<?php echo $tpkey; ?>" />

			 </div>





			</div>	 



		    <div class="form-group">

			 <div class="col-md-6 col-sm-6 col-xs-12">

				<label for="appkey">Infusionsoft's ClientId:</label>

					<input maxlength="100" type="text" required="required" name="client_id" id="client_id" class="form-control" placeholder="App key..." value="<?php echo $client_id; ?>" />

			 </div>



									 



			 <div class="col-md-6 col-sm-6 col-xs-12">

				<label for="appsecret">Infusionsoft's ClientSecret:</label>

					<input maxlength="100" type="text" required="required" name="client_secrete" id="client_secrete" class="form-control" placeholder="App secret..." value="<?php echo $client_secrete; ?>" />

			 </div>

			</div>



			<div class="form-group">

			  <div class="col-md-6 col-sm-6 col-xs-12">

			  </div>

			</div>

			<br/><br/>



			<div class="form-group">

                  <button class="btn btn-primary nextBtn pull-right" onclick="ttap_credential()" id="timetap_credential"  type="button">Next</button>

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