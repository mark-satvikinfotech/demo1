<?php

error_reporting(1);
include('IS_tag.php');
include('infusion_config.php');

$istag = new Is_tag();

if($json = json_decode(file_get_contents("php://input"), true))
{  
  //print_r($json);  
  $data = $json;
} 
else
{
  // print_r($_POST);
  $data = $_POST;
}
 $data = Array( 
    'contact_id' =>6768,  
	'TagX' => 3046,  
	'TagY' => 3048
	);
 file_put_contents(TIMETAP_DIR.'log.txt', "\n\n====================\n", FILE_APPEND);
 file_put_contents(TIMETAP_DIR.'log.txt', print_r($data, true), FILE_APPEND); 
 if(isset($_POST['contactId']) && isset($_POST['TagX']) && isset($_POST['TagY']))
 {	
   $contact_id = $_POST['contactId'];	
   $tagx = $_POST['TagX'];	
   $tagy = $_POST['TagY'];	
   $istag->getphoneno($contact_id,$tagx,$tagy);
   //echo $contact_id;	
   exit(); 
  }
?>