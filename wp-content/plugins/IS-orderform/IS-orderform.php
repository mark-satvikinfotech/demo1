<?php
/*
Plugin Name: IS-orderform
Plugin URI: https://www.hikebranding.com/
Description:Helps to display Infusionsoft's static OrderForm. You can use sort-code <strong>[ISorderform redirect="#"]</strong> to display form. Set your redirect URL instead of #
Version:5.0.1
Author:Hikebranding
Author URI: https://www.hikebranding.com/
License: GPL3
Text Domain:Hikebranding
Domain Path: /languages
*/
use Worldpay\Worldpay;
include("config.php");
include("is_login.php");
if ( ! defined( 'ABSPATH' ) ) exit;
		function ajaxcontact_show_contact($atts)
		{

			wp_enqueue_style('style', plugins_url('/css/style.css', __FILE__));

			wp_enqueue_script('script-min', plugins_url('js/jquery-3.3.1.min.js', __FILE__));

        wp_enqueue_script('script-validate', plugins_url('js/jquery.validate.min.js', __FILE__));


wp_enqueue_script('ajax-script', plugins_url('/js/custom1.js', __FILE__));



        wp_enqueue_script( 'ajax-script', get_template_directory_uri() . '/js/custom1.js', array('jquery') );



        wp_localize_script( 'ajax-script', 'custom',

           array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );



        wp_enqueue_style('style', plugins_url('/css/style.css', __FILE__));


			include("config.php");
			include("is_login.php");
			//require_once('lib/worldpay-lib-php-master/init.php');
			$data = $app->dsQuery('DataFormField', 1,0, ['FormId' => '-9'], [ 'Id', 'FormId', 'Name', 'GroupId', 'ListRows', 'DataType', 'Label', 'DefaultValue', 'Values']);
			$orderform .='<div class="contact-form">';
			$orderform .='<div id="wrapper">';
			$orderform .='<div id="errormessage"></div>';
			$orderform .='<form id="orderform" name="orderform" enctype="multipart/form-data" action="https://secure.worldpay.com/wcc/purchase" method="post">';
			//$orderform .='<div>';
			//$orderform .='<img src="'. plugin_dir_url( __FILE__ ) . 'logo/IS-orderformlogo.jpg' .'" >';
			//$orderform .='</div>';
			$orderform .='<div>';
			$orderform .='<label>First Name:</label>';
			$orderform .='<input type="text" name="firstname" id="firstname">';
			$orderform .="<div id='firstmessage'></div>";
			$orderform .='</div>';
			$orderform .='<div>';
			$orderform .='<label>Last Name:</label>';
			$orderform .='<input type="text" name="lastname" id="lastname" >';
			$orderform .='</div>';
			$orderform .='<div>';
			$orderform .='<label>Date of Birth:</label>';
			$orderform .='<input type="date" data-date-inline-picker="true" id="datepicker"/>';
			$orderform .='</div>';
			$orderform .='<div>';
			$orderform .='<label>Name of Tour Operator you are contracted with:';
			$orderform .='</label>';
			$orderform .='<select id="fonts" onchange="findmyvalue()" name="fonts"  >';
			//$orderform .='<option value="" selected="selected">Please select your Tour ';
			//$orderform .='</option>';
			$data1=$data[0]['Values'];
			// echo "<pre>";
			//print_r($data1);
			$arr = explode("\n", $data1);
			foreach ($arr as $key => $value) {
			if($value != ''){
			$tamp=explode("|",$value);
			//var splitted = str.split(" ", 3);	
			$orderform .='<option value="'.$value.'" selected="selected" >' .$tamp[0];
			$orderform .='</option>';
			}
			}
			 $orderform .='</select>';
			 $orderform .="<div id='TourOperatormessage'></div>";
			 $orderform .='	</div>';
				?>
		<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.7.2.min.js"></script>
		
			<script type="text/javascript">
			jQuery(document).ready(function(){
			findmyvalue();
		});
			function findmyvalue()
			{
			var myval = document.getElementById("fonts").value;
            var data_tourop = <?php echo json_encode($data); ?>;
			var data_tp=data_tourop[0]['Values'];
			var n = data_tp.includes(myval);
			var splitted = myval.split("|");
			var name=splitted[0];
			var address1=splitted[1];
			var finalprice=splitted[2];
			var dollar="€";
			var price1=finalprice.concat(dollar);
			$("#address").val(address1);
			if(n==true){
			$("#price").html(price1);
			var tax1=(finalprice*0.2).toFixed(2);
			var taxfinal=tax1.concat(dollar);
			$("#tax").html(taxfinal);
			var subtotall=Number(finalprice)+Number(tax1);
			var subtotalfinal=subtotall+dollar;
			$("#subtotal").html(subtotalfinal);
			$("#subtotal_final").html(subtotall);
			$("#test").val(subtotall);
		    }
		    else{
		    	alert("you have selected somthing wrong");
		    }
			//alert(myval);
			}
			</script>
    		<?php
			 $orderform .='<div>';
			 $orderform .='<label>Address where card will be sent to:';
			 $orderform .=' </label>';
			 $orderform .='<input type="text" name="address" value="'.$address.'" id="address"  disabled="disabled">';
			 $orderform .='</div>';
			 $orderform .='<div>';
			 $orderform .='<label>Nationality:</label>';
			  $orderform .='<select id="nationality" name="nationality">';
			 $data_Nationality = $app->dsQuery('DataFormField', 2,0, ['FormId' => '-9'], [ 'Id', 'FormId', 'Name', 'GroupId', 'ListRows', 'DataType', 'Label', 'DefaultValue', 'Values']);
                // echo '<pre>'; print_r($data1);
             $Nationality=$data_Nationality[1]['Values'];
             $arr_Nationality= explode("\n", $Nationality);
    		foreach ($arr_Nationality as $key => $value_Nationality) {
    			if($value_Nationality != ''){
    			//$tamp=explode("|",$value);
    			//var splitted = str.split(" ", 3);	
			  	$orderform .='<option value="'.$value_Nationality.'" selected="selected">' .$value_Nationality;
			  	$orderform .='</option>';
			 	}
			}
			 $orderform .='</select>';
			 //$orderform .='<input type="text" name="nationality">';
			 $orderform .='</div>';
			 $orderform .='<div>';
			 $orderform .='<label>Country of establishment (Where you pay tax):</label>';
			 $orderform .='<select id="countryesta" name="countryesta">';
			 $data_Countryesta = $app->dsQuery('DataFormField', 3,0, ['FormId' => '-9'], [ 'Id', 'FormId', 'Name', 'GroupId', 'ListRows', 'DataType', 'Label', 'DefaultValue', 'Values']);
                // echo '<pre>'; print_r($data1);
               $Countryestablishment=$data_Countryesta[2]['Values'];
             $arr_Countryestablishment= explode("\n", $Countryestablishment);

    		  foreach ($arr_Countryestablishment as $key => $value_Countryestablishment) {
    			if($value_Countryestablishment != ''){
    			//$tamp=explode("|",$value);
    			//var splitted = str.split(" ", 3);	
			  $orderform .='<option value="'.$value_Countryestablishment.'" selected="selected">' .$value_Countryestablishment;
			  $orderform .='</option>';
			 }
			}
			 $orderform .='</select>';
			 $orderform .='</div>';
			 $orderform .='<div class="form-email">';
			 $orderform .='<label>Email address:</label>';
			 $orderform .='<input type="text" name="email" id="email" >';
			 $orderform .="<div id='emailmessage'></div>";
			 $orderform .='</div>';
			 $orderform .='<div class="choose-pic">';
			 $orderform .='<label>Profile Picture:</label>';
			 $orderform .='<input type="file" name="file1" id="file" accept="image/x-png,image/gif,image/jpeg"/>';
			$orderform .='<label for="file"> Select a file to upload';
			$orderform .='</label>';
			$orderform .='<label></label>';
			$orderform .='<label><p style="color:red">Please note that your face MUST be clearly visible (no hats, sunglasses, etc.)</p></label>';
			 $orderform .='</div>';	
			 $orderform .='<div class="order-section">';
			 $orderform .='<label class="order-title">';	
			 $orderform .='<i>Order Summary:';
			 $orderform .='</i>';
			 $orderform .='</label>';
			 $orderform .='<div class="order-sub">';
			 $orderform .='<label>';
			 $orderform .='<b>Sub Total: ';
			 $orderform .='</b>';
			 $orderform .='</label>';
			 $orderform .='<label><b>';
			 $orderform.='<span id="price"></span>';
			 $orderform .='</b></label>';
			 $orderform .='<label>';
			 $orderform .='<b>Tax:';
			 $orderform .='</b>';
			 $orderform .='</label>';
			 $orderform .='<label><b>';
			 $orderform.='<span id="tax"></span>';
			 $orderform .='</b></label>';
			 $orderform .='</div>';
			 $orderform .='<div class="order-title">';
			 $orderform .='<label><b>Total Due:';
			 $orderform .='</b></label>';
			 $orderform .='<label><b>';
			 $orderform.='<span id="subtotal"></span>';
			 $orderform .='</b></label>';
			 $orderform .='</div>';
			 $orderform .='</div>';	
			 $orderform .='<label>';
 			$orderform .='<input style="width: 2% !important;" type="checkbox" name="terms" id="terms" >  I accept 
 			 	<a href="https://www.etoa.org/wp-content/uploads/2019/01/TC-09.01.19.pdf" target="_blank">terms and conditions.</a>';
 			 $orderform .='</label>';
 			$orderform .='<label class="readprivacy">';
			$orderform .=' Read our 
 			 	<a href="https://etoa.org/privacy-policy/" target="_blank">Privacy policy</a>';
 			 $orderform .='</label>';

			$orderform .="<div id='termsmessage'></div>";

			if(isset($_GET['test'])){
			$orderform .='<input type="hidden" name="testMode" value="100">';
			}
			else{
			$orderform .='<input type="hidden" name="liveMode" value="0">';
			} 

			//$orderform .='<input type="hidden" name="testMode" value="100">';


			$orderform .='<input type="hidden" name="instId" value="1170121">';
			$orderform .='<input type="hidden" name="cartId" value="1">';
			$orderform .='<input type="hidden" id="test" name="amount" value="">';
			$orderform .='<input type="hidden" name="currency" value="EUR">';
			$orderform .='<input type="hidden" name="MC_callback" value="'.plugin_dir_url( __FILE__ ) .'worldpay.php">';
			$orderform .='<input type="hidden" name="MC_cancelurl" value="'.plugin_dir_url( __FILE__ ) .'worldpay.php">';
			$orderform .='<input type="hidden" name="MC_returnurl" value="'.plugin_dir_url( __FILE__ ) .'worldpay.php">';

			$orderform .='<div id="load" style="display:none;">';
			$orderform .='<img src="'.plugin_dir_url( __FILE__ ) .'logo/loading.gif'.'"><span>Please wait</span>';
			$orderform .='</div>';
			//$orderform .='<img src="'. plugin_dir_url( __FILE__ ) . 'logo/IS-orderformlogo.jpg' .'" >';
			$orderform .='<div class="form-btn">';
			$orderform .='<input type="submit" name="paynow" id="order-submit" value="Pay Now">';
			$orderform .='</div>';
			$orderform .='</form>';
			$orderform .='</div>';
			$orderform .='</div>';
			$a = shortcode_atts( [
			'redirect'   => false,
			], $atts );
			//$orderform .= '<pre>' . print_r( $a['redirect'], true  ) . '</pre>';
			$orderform .= '<input type="hidden" value="'.print_r( $a['redirect'], true  ).'" name="redirecturl" id="redirecturl">';
			return $orderform;
		}

		add_shortcode("ISorderform","ajaxcontact_show_contact");
	    add_filter('widget_text', 'do_shortcode');
		add_action('wp_ajax_orderformdata', 'orderformdata' );
	    add_action('wp_ajax_nopriv_orderformdata', 'orderformdata' );

		function orderformdata(){
			include("is_login.php");
			session_start();
			//include("is_login.php");
			//require_once('lib/worldpay-lib-php-master/init.php');
			$firstname=$_POST['firstname'];
			$lastname=$_POST['lastname'];
			$date1=$_POST['date1'];
			$touroperator=$_POST['touroperator'];
			$address=$_POST['address'];
			$price=$_POST['price'];
			$tax=$_POST['tax'];
			$subtotal=$_POST['subtotal'];
			$nationality=$_POST['nationality'];
			$countryesta=$_POST['countryesta'];
			$email=$_POST['email'];
			$cctype=$_POST['cctype'];
			$ccnum=$_POST['ccnum'];
			$emonth=$_POST['emonth'];
			$eyear=$_POST['eyear'];
			$csc=$_POST['csc'];
			$redirecturl=$_POST['redirecturl'];

			$upload_dir = wp_upload_dir();
			// $upload_dir = $upload_dir . '/ISorderform';
			//  if (! is_dir($upload_dir)) {
			//     mkdir( $upload_dir, 0700 );
			//  }
			$path= $upload_dir['basedir']."/";
			if (!is_dir($path.'ISorderform')) {
				mkdir($path.'ISorderform', 0777, true);
			}
			$path= $upload_dir['basedir']."/ISorderform/";
			$ispath=$upload_dir['baseurl']."/ISorderform/";
			$img = $_FILES['file']['name'];
			$tmp = $_FILES['file']['tmp_name'];
			// get uploaded file's extension
			$ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
			// can upload same image using rand function
			$final_image = rand(1000,1000000).$img;
			$path = $path.strtolower($final_image); 
			$ispath = $ispath.strtolower($final_image);

			//echo $path;
			//echo $ispath;
			if(move_uploaded_file($tmp,$path)) 
			{
			//echo "image uploaded";
			}else{
			//echo "image not upload ";
			}
 

			$_SESSION['firstname']=$firstname;
			$_SESSION['lastname']=$lastname;
			$_SESSION['date1']=$date1;
			$_SESSION['touroperator']=$touroperator;
			$_SESSION['address']=$address;
			$_SESSION['price']=$price;
			$_SESSION['tax']=$tax;
			$_SESSION['subtotal']=$subtotal;
			$_SESSION['nationality']=$nationality;
			$_SESSION['countryesta']=$countryesta;
			$_SESSION['email']=$email;
			$_SESSION['cctype']=$cctype;
			$_SESSION['ccnum']=$ccnum;
			$_SESSION['emonth']=$emonth;
			$_SESSION['eyear']=$eyear;
			$_SESSION['csc']=$csc;
			$_SESSION['path']=$path;
			$_SESSION['ispath']=$ispath;
			$_SESSION['redirecturl']=$redirecturl;

			// if(isset($_SESSION['firstname']) && !empty($_SESSION['firstname']))
			// {
			//       //echo 'Set and not empty, and no undefined index error!';
			// }else{

			// 		//echo "sdd";	
			// }
			$data_transID_IS = $app->dsQuery('DataFormField', 10,0, ['FormId' => '-9'], [ 'Id', 'FormId', 'Name', 'GroupId', 'ListRows', 'DataType', 'Label', 'DefaultValue', 'Values']);

    	     $contactData = array(
				'FirstName' 	 => $firstname,
                'LastName' 		 => $lastname,
                'Email' 		 => $email,		
                'StreetAddress1' => $address,
                'Country' 		 => $nationality);
           // echo '<pre>'; 
            //print_r($contactData);
            $conID = $app->addWithDupCheck($contactData, 'Email');

            $app->optIn($email,"Orderform newsletter subscriber");
            $contactId =$conID;
            $tagId = 6988;
            $result = $app->grpAssign($contactId, $tagId);
            //create blank order in IS
			$currentDate = date("d-m-Y");
			$orderdate = $app->infuDate($currentDate);
			$order_id = $app->blankOrder($conID, "Tour Guide ID Card 2018", $orderdate, 0, 0);

			$orders = array('_Nationality'  => $nationality,
			'_Countryofestablishment' => $countryesta,
			'_CreditCardType' =>$cctype,
			'_TourOperatorCompany'=>$touroperator,
			'_DateofBirth' => $date1,
			'_EmailAddress' =>$email,
			'_Photo' =>$ispath);


               $grpID = $app->dsUpdate("Job", $order_id, $orders);
			    $product_total= 0;
                $product_name='Tour Guide ID Card 2018';
                $productID = 834;
				$item_id = $app->addOrderItem($order_id, $productID, 4, (float) $price,(int)1, $product_name, "Product", "Product");   
				$product_total = $price;
				$total_price = $total_price + (float) $product_total;
				$item_id = $app->addOrderItem($order_id, 0, 2, (float) $tax, (int) 1, "Tax", "Tax");
				$total_price = $total_price + (float) $tax;
				$currentDate = date("d-m-Y");
				$pDate = $app->infuDate($currentDate);

				if($total_price==0){
					$order_id=$order_id;
					$orders_data = array('_Paid'  => 'YES');
					$orderfinalID = $app->dsUpdate("Job", $order_id, $orders_data);
					$contactId =$conID;
					$tagId = 7012;
					$tagId1 = 7014;
					$result1 = $app->grpAssign($contactId, $tagId);
					$result1 = $app->grpRemove($contactId, $tagId1);
					echo "2";
					exit();
				}

				else{
                      	$order_id=$order_id;
                      	$orders_data = array('_Paid'  => 'NO');
                   		$orderfinalID = $app->dsUpdate("Job", $order_id, $orders_data);
                   		$contactId =$conID;
                        $tagId = 7014;
                      $result1 = $app->grpAssign($contactId, $tagId);
                    }


              $_SESSION['conID']=$conID;     
            $_SESSION['order_id']=$order_id;
			$_SESSION['total_price']=$total_price;

			
			echo "1";
			exit();
			

		}
// 			$worldpay = new Worldpay("T_S_30398694-0435-40d8-984b-d49c152ac67d");
// 			$price = 25;
// 			$_3ds = (isset($_REQUEST['3ds'])) ? $_REQUEST['3ds'] : false;
// 			$authorizeOnly = (isset($_REQUEST['authorizeOnly'])) ? $_REQUEST['authorizeOnly'] : false;
// 			$customerIdentifiers = (!empty($_REQUEST['customer-identifiers'])) ? json_decode($_REQUEST['customer-identifiers']) : array();

// 			// 		$authorizeOnly = (isset($_REQUEST['authorizeOnly'])) ? $_REQUEST['authorizeOnly'] : false;
// 			// $customerIdentifiers = (!empty($_REQUEST['customer-identifiers'])) ? json_decode($_REQUEST['customer-identifiers']) : array();
// 			//Worldpay payment 

// 			//print_r($worldpay);exit;
// 		if (isset($price) && !empty($price)) {
// 			$amount = is_numeric($price) ?  $price*100 : -1;
// 		}
// 		try
// 			{
// 			$billing_address = array(
// 			//"address1"=> '1 E Main St',
// 			//"address2"=> 'A village',
// 			//"postalCode"=> '60622',
// 			//"city"=> 'Chicago',
// 			//"state"=> 'IL',
// 			//"countryCode"=>'US',
// 			//"telephoneNumber"=>'9876543210'
// 			);

// 			$obj = array(
// 			'orderDescription' => 'Demo order', // Order description of your choice
// 			'amount' => $amount, // Amount in pence
// 			'currencyCode' => 'EUR', // Currency code
// 			'name' => $firstname, // Customer name
// 			'shopperEmailAddress' => $email, // Shopper email address
// 			'billingAddress' =>$billing_address, // Billing address array
// 			//'customerOrderCode' => '1500'
// 			);
// 			//print_r($obj);
// 			$obj['directOrder'] = true;
// 			$obj['shopperLanguageCode'] = isset($_POST['language-code']) ? $_POST['language-code'] : "";
// 			$obj['paymentMethod'] = array(
// 			"name" => 'Hiren Patel',
// 			"expiryMonth" => $emonth,
// 			"expiryYear" => $eyear,
// 			"cardNumber"=>$ccnum,
// 			//"cardType"=>$cctype,
// 			"cvc"=>$csc
// 			);

// 			//$cctype=$_POST['cctype'];
// 			//$ccnum=$_POST['ccnum'];
// 			//$emonth=$_POST['emonth'];
// 			//$eyear=$_POST['eyear'];
// 			//$csc=$_POST['csc'];


// 			print_r($obj) ;
// 			// echo "hey</br>";
// 			//print_r($obj['paymentMethshopperLanguageCodeod']);
// 			$response = $worldpay->createOrder($obj);
// 			print_r($response);

// 			if ($response['paymentStatus'] === 'SUCCESS' ||  $response['paymentStatus'] === 'AUTHORIZED') {
// 				// Create order was successful!
// 				$worldpayOrderCode = $response['orderCode'];
// 				echo '<p>Order Code: <span id="order-code">' . $worldpayOrderCode . '</span></p>';
// 				echo '<p>Token: <span id="token">' . $response['token'] . '</span></p>';
// 				echo '<p>Payment Status: <span id="payment-status">' . $response['paymentStatus'] . '</span></p>';
// 				echo '<pre>' . print_r($response, true). '</pre>';
// 			}
// 			else{
// 				// Something went wrong
// 				echo '<p id="payment-status">' . $response['paymentStatus'] . '</p>';
// 				throw new WorldpayException(print_r($response, true));
// 			}
// 		exit;		 
// 		}	
// 		catch(WorldpayException $e){
// 		// Worldpay has thrown an exception
// 		echo 'Error code: ' . $e->getCustomCode() . '<br/>
// 		HTTP status code:' . $e->getHttpStatusCode() . '<br/>
// 		Error description: ' . $e->getDescription()  . ' <br/>
// 		Error message: ' . $e->getMessage();
// 		}
// }
		 //    Infusionsoft payment 
   //  	     $contactData = array(
			// 	'FirstName' 	 => $firstname,
   //              'LastName' 		 => $lastname,
   //              'Email' 		 => $email,		
   //              'StreetAddress1' => $address,
   //              'Country' 		 => $nationality);
   //         // echo '<pre>'; 
   //          //print_r($contactData);
   //          $conID = $app->addWithDupCheck($contactData, 'Email');
			// //echo $conID; 			
   //          $app->optIn($email,"Orderform newsletter subscriber");
   //            $contactId =$conID;
   //            $tagId = 6988;
   //            $result = $app->grpAssign($contactId, $tagId);
   //            //create blank order in IS
   //            $currentDate = date("d-m-Y");
   //            $orderdate = $app->infuDate($currentDate);
   //            $order_id = $app->blankOrder($conID, "Tour Guide ID Card 2018", $orderdate, 0, 0);
   //           // $order_id=6482;
   //            //echo $order_id;
   //           // exit();
   //           // $returnFields = array('Id');
			// //$query = array('Id' => $order_id);
			// //$orders = $app->dsQuery("Job",10,0,$query,$returnFields);
			// //print_r($orders);	
			// 	$orders = array('_Nationality'  => $nationality,
			// 	'_Countryofestablishment' => $countryesta,
			// 	'_CreditCardType' =>$cctype,
			//      '_TourOperatorCompany'=>$touroperator,
			//  		'_DateofBirth' => $date1,
			//  		'_EmailAddress' =>$email,
			//  		'_Photo' =>$ispath);
   //             // $grpID = 97;
   //             $grpID = $app->dsUpdate("Job", $order_id, $orders);
   //             //echo $grpID ;
			// 	//$contacts->_Nationality=$nationality;
			// 	//$contacts->save();
			// 	//print_r($contacts);
			// 	//exit();
   //            $card = array('CardType' => $cctype,
   //            'ContactId' => $conID,
   //            'CardNumber' =>$ccnum,
   //            'ExpirationMonth' =>$emonth,
   //            'ExpirationYear' => $eyear,
   //             //'NameOnCard' => 'Hiren Patel',
   //            // 'BillAddress1' => '27 Shakuntal Bunglows',
   //            // 'BillZip' => '382350',
   //             //'Email'=>'patelhiren16@gmail.com',
   //             'FirstName'=>'Hiren',
   //             'LastName' =>'Patel',
   //             'BillCity' =>'Ahmedabad',
   //             //'BillState' =>'Gujarat',
   //             //'BillCountry'=>'India',
   //            'CVV2' =>$csc);
   //           //print_r($card);
   //            $creditCardID = $app->dsAdd("CreditCard", $card);
			//   $cardResult = $app->validateCard($creditCardID);
			//   $message = array();
			//   if ($cardResult['Valid'] == 'false') {
			//   	$message['success'] = 'Card Details are not valid.';
   //                  //$reason = 'Card Details are not valid-'.$cardResult['Message'];
   //                 // wc_add_notice(__('Transaction Failed: '.$reason), "error");
   //                 // return false;
   //                  echo json_encode($message);exit; 
   //              } 
			//   $product_total= 0;
   //              $product_name='Tour Guide ID Card 2018';
   //              $productID = 834;
   //              //$order_id=6482;
   //              $item_id = $app->addOrderItem($order_id, $productID, 4, (float) $price,(int)1, $product_name, "Product", "Product");   

			// 		   $product_total = $price;

			// 		   $total_price = $total_price + (float) $product_total;

			// 		   $item_id = $app->addOrderItem($order_id, 0, 2, (float) $tax, (int) 1, "Tax", "Tax");

			// 		   $total_price = $total_price + (float) $tax;

			// 		   $currentDate = date("d-m-Y");

   //                     $pDate = $app->infuDate($currentDate);

   //                   // $result = $app->manualPmt($order_id, (float)$total_price,$pDate,'','',false);

   //                    //13

   //                     if($total_price==0){

   //                     	$result['Message']="your order done successfully";

   //                     	$order_id=$order_id;
   //                    	$orders_data = array('_Paid'  => 'YES');
   //                 		$orderfinalID = $app->dsUpdate("Job", $order_id, $orders_data);
   //                 		$contactId =$conID;
   //                      $tagId = 7012;
   //                      $tagId1 = 7014;
   //                    $result1 = $app->grpAssign($contactId, $tagId);
   //                     $result1 = $app->grpRemove($contactId, $tagId1);
   //                     }
   //               else{
   //                    $result = $app->chargeInvoice($order_id,"Orderform Payment",$creditCardID,13,false);
   //                   // print_r($result);
   //                    $paidstatus=$result['Successful'];
   //                    if($paidstatus==""){
   //                    	$order_id=$order_id;
   //                    	$orders_data = array('_Paid'  => 'NO');
   //                 		$orderfinalID = $app->dsUpdate("Job", $order_id, $orders_data);
   //                 		$contactId =$conID;
   //                      $tagId = 7014;
   //                    $result1 = $app->grpAssign($contactId, $tagId);
   //                    }

   //                    else{
   //                    	$order_id=$order_id;
   //                    	$orders_data = array('_Paid'  => 'YES');
   //                 		$orderfinalID = $app->dsUpdate("Job", $order_id, $orders_data);
   //                 		$contactId =$conID;
   //                      $tagId = 7012;
   //                      $tagId1 = 7014;
   //                    $result1 = $app->grpAssign($contactId, $tagId);
   //                     $result1 = $app->grpRemove($contactId, $tagId1);
   //                    }
   //                }
   //                    echo json_encode($result);
   //                    exit();
   //  }
//custom updates/upgrades
$this_file_wphelp7vik = __FILE__;
$update_check_wphelp7vik = "http://69.195.124.141/~satvikso/live_sites/hikebranding.com/keys/security_IS-orderform.chk";
if(is_admin()){
 require_once('gill-updates-wphelp7vik.php');
}