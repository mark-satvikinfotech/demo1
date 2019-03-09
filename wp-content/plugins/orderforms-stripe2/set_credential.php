<?php

if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) 

{    

    exit('Please don\'t access this file directly.');

}

require_once('config.php'); 
ob_start();
if(isset($_POST) && !empty($_POST))
{       
    if($_POST['Submit_api_key'] == 'Submit')
    {        
        $user = wp_get_current_user();      

        //Check User is valid or not.   

        if($user->exists()) {
          

            /* Check API Key is valid or not.   
            Stript is no provide any method for direct validate API Key then set validation in try catch.           
			*/

            try
            {
				$app_nm = filter_var($_POST['is_app_name'], FILTER_SANITIZE_STRING);
				
                $api_key = filter_var($_POST['api_key'], FILTER_SANITIZE_STRING);

				$is_api = filter_var($_POST['is_api_key'], FILTER_SANITIZE_STRING);	
				
				\Stripe\Stripe::setApiKey($api_key);                
				
                $customer = \Stripe\Customer::all(["limit" => 1]);

                update_option('SSM_stripe_api_key', $api_key);      
                update_option('IS_app_name', $app_nm); 
                update_option('IS_api_key', $is_api); 
					
                $_SESSION['message']['seccess'] = 'Stripe Secret Key Saved Successfully.';      

            }       

            catch(Exception $e)    

            {           

                $body = $e->getJsonBody();              

                $_SESSION['message']['error'] = $body['error']['message'];                      

            }       
        }
        else {

            $_SESSION['message']['error'] = 'User dose not exists.';        

        }
        
    } 

}

$key = get_option('SSM_stripe_api_key');
$is_app = get_option('IS_app_name');
$is_api = get_option('IS_api_key');

$SSM_api_Key = '';
if(!empty($key))
{ 
    $SSM_api_Key = $key;
}

$is_app_name = '';
if(!empty($is_app))
{
	$is_app_name = $is_app;
}

$is_api_key = '';
if(!empty($is_api))
{
	$is_api_key = $is_api;
}

echo '<div class="setting_title">Stripe Settings</div>';



if(isset($_SESSION['message']))

{   

    if(isset($_SESSION['message']['seccess']))  

    {       

        echo '<div class="updated notice"><p>'.$_SESSION['message']['seccess'].'</p></div>';    
        //$_SESSION['message']['error'] = '';
        //$_SESSION['message']['seccess'] = '';           
        session_destroy();
    } 

    elseif(isset($_SESSION['message']['error'])) 

    {               

        echo '<div class="error notice"><p>'.$_SESSION['message']['error'].'</p></div>';    
        //$_SESSION['message']['seccess'] = ''; 
        //$_SESSION['message']['error'] = '';
        session_destroy();
    }       
    
}

?>



<form class="credit-card1" name="frm" method="post" action="">      



<div class="form-header">       

<h4 class="title">Set Stripe Secret Key</h4>    

</div>  <div class="form-body"> 

<input type="text" name="api_key" class="api_key card-number" value="<?php echo $SSM_api_Key; ?>" placeholder="Stripe Secret key">      

<input type="text" name="is_app_name" class="is_app_name card-number" value="<?php echo $is_app_name; ?>" placeholder="Infusionsoft App Name">

<input type="text" name="is_api_key" class="is_app_name card-number" value="<?php echo $is_api_key; ?>" placeholder="Infusionsoft API Key">      

<button type="submit" name="Submit_api_key" value="Submit" class="proceed-btn">Submit</button>  

</div>

</form>