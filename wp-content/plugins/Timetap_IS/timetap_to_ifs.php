<?php

set_time_limit(3000);

add_action('wp_ajax_deletegoal', 'deletegoal' );

    add_action('wp_ajax_nopriv_deletegoal', 'deletegoal' );

    function deletegoal(){

        $id = $_POST['id'];

        global $wpdb;

        $table_name = $wpdb->prefix . "product_goals";

      $wpdb->delete($table_name, array('id'=>$id) );

    }



    function get_product_services()    {    	       include('infusion_config.php');	  

       $sessionToken=manage_ttap_to_ifs();

       $curlSession = curl_init('https://api.timetap.com/test/products?sessionToken='.$sessionToken);

        curl_setopt($curlSession, CURLOPT_HEADER, false);

        curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);

        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curlSession);

        $err = curl_error($curlSession);

        //curl_close($curlSession);

        if ($err)

        {

          $response = $err;

        }

        $responseAsAnArray = (is_null($response) || ($response === false))? array(): json_decode($response, true);

		$get_product_data= json_decode($response);

		$product_data = array();

		foreach ($get_product_data as $_results4)

		{

            $product_data[$_results4->productId] = $_results4->productName;

            $product_id[]=$_results4->productId; 

            $product_name[]=$_results4->productName;	

		}

		

		$get_services_data=get_timetap_services();

		       //echo '<pre>';

		$service_data = array();

		foreach ($get_services_data as $_results_ser)

		{

            $service_data[$_results_ser->reasonId] = $_results_ser->reasonDetail;

            $service_id[]=$_results_ser->reasonId; 

            $service_name[]=$_results_ser->reasonDetail;

		}



		$pro_ser_id=array_merge($product_id, $service_id);

    	$dropdown_id=implode("\n",$pro_ser_id);

    	

		$pro_ser_name=array_merge($product_name, $service_name);

		$dropdown_name=implode("\n",$pro_ser_name);

    	

    	$dropdownArray = array();

    	$dropdownArray=array_combine($pro_ser_id,$pro_ser_name);



    	if(isset($_POST['submit']))

    	{

			if(isset($_POST['product_service_data']) && isset($_POST['goal_name']))

			{

			    $product_service_data=$_POST['product_service_data'];

		        $pro_ser_data=explode(",",$product_service_data);

			    $Product_id=$pro_ser_data[0];

			    $Product_name=$pro_ser_data[1];

			    $Goal_name=$_POST['goal_name'];

			    //echo $Goal_name;

			   

			    global $wpdb;

				$table_name7=$wpdb->prefix ."product_goals";

				$product_goals_name= $wpdb->get_results("SELECT pro_ser_name,goal_name FROM $table_name7");

						$product_goal_db=array();

				foreach ($product_goals_name as $key => $value) 

				{

					$product_goal_db[$value->pro_ser_name] = $value->goal_name;

				}

				

				$flg= 0;

				foreach($product_goal_db as $key => $value)

				{

					if($key == $Product_name && $value == $Goal_name)

					{

						$flg = 1;

					}

				}

				if($flg == 1)

			    {



			    	echo $flag;

					echo "<div class='pro_goal_error'>Product-Goal already in Database </div>";

				}

				else

				{

					echo $flag;

				 	$table_name =$wpdb->prefix ."product_goals";

					$result= $wpdb->insert($table_name,array('pro_ser_id' => $Product_id, 'pro_ser_name' => $Product_name,'goal_name'=>$Goal_name));



					if($result)

					{

						$_SESSION["errormsg"]="Product-Goal Added Successfully";

							

					}



				}

				    

			}



		}

	



	?>

          

	<form name="product_goals" action="" method="post" class="product_goals">

	<div class="container">

		<div class="row">

	        <div class="col-md-4 col-sm-4 col-xs-12">

			  <label for="Appointment">Product and Services</label>
			  <div class="custom-select" style="width:100%;">
				<select id="product_service_data" name="product_service_data" class="form-control">

			    <option value="">Select Product & Services</option>

			    <?php

    		    foreach ($dropdownArray as $key => $value) {

    			if($value != ''){

    			?>

			   <option value="<?php echo $key.','.$value ?>"><?php echo $value; ?></option> 

				<?php

	    		}

	    	    }

	    		?> 

    			</select>
    		</div>
	        </div>

		    <div class="col-md-4 col-sm-4 col-xs-12">

		    <label for="Goal">Goal-call Name</label>

			<input class="goalname" type="text" id="goal_name" required name="goal_name" />

	        </div>



	        <div class="col-md-4 col-sm-4 col-xs-4">

	        <label for="Goal"></label>

	      	<input type="submit" id="submit" name="submit" value="Add" class="btn btn-primary nextBtn" />

	        </div>



		</div>

	</div>

	</form>

            <?php

            global $wpdb;

            $table_name7 = $wpdb->prefix . "product_goals";

            $product_goals = $wpdb->get_results("SELECT * FROM $table_name7");

            ?>

           <div class="container">
           	<div class="table-responsive">
	       <table id='tableid' width='100%' border=1>

            <tr>

            <th>Product-Services</th> <th>Campian-Goals</th><th>Action</th>

            </tr>

			<?php

            foreach($product_goals as $product_goal){

            ?>

            <tr>

            <td><?php echo $product_goal->pro_ser_name ?></td>

            <td><?php echo $product_goal->goal_name ?></td>

            <td><input class="btn btn-primary nextBtn" type="button" class='delete_button' value="Delete" onClick="reply_click(<?php echo $product_goal->id; ?>);" class='delete' ></td>

            </tr>

            <?php

            }

            ?>

            </table>
        	</div>
        </div>

	<?php

}





