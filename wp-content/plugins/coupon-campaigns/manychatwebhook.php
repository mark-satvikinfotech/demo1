<?php
// Turn off all error reporting
//error_reporting(1);

require_once('coupon-campaigns.php');
/*if($json = json_decode(file_get_contents("php://input"), true)) {
    //print_r($json);
    $data = $json;
} else {
   // print_r($_POST);
    $data = $_POST;
}  */


$data=Array
(
    'key' => 'user:2780028928689639',
    'name' => 'Hiren Patel',
    'custom_fields' => Array
        (
            'Email' => 'lisha.hikebranding@gmail.com'
        ),

); 
//exit();

$campian_id='';
if(isset($_REQUEST['campian_id']) && $_REQUEST['campian_id'] !=''){
  $data['campian_id'] = $_REQUEST['campian_id'];

  file_put_contents(dir_path.'log.txt', "\n\n====================\n", FILE_APPEND);
file_put_contents(dir_path.'log.txt', print_r($data, true), FILE_APPEND);

$name = $data['name'];
$email = $data['custom_fields']['Email'];
$ip = $data['key'];
$campagin_id = $_REQUEST['campian_id'];

sendmail($name,$email,$ip,$campagin_id);

exit();
}else{

    echo "Please enter campagin id";
}

//echo "Saving data ...\n";
//$array = $_REQUEST;




/*exit();

$coupon_code['CouponCode'] = "ABCDE";
print_r(json_encode($coupon_code));
//file_put_contents(dir_path.'log.txt', $campian_id, FILE_APPEND);
exit();*/

?>