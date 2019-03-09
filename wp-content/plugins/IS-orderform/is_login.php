<?php
//Infusionsoft App Details
include("lib/isdk.php");
//if(get_option('is_app'.$user_id) && get_option('is_key'.$user_id)){
$app = new iSDK;
$app_name = 'rh290';//'qn241-xd295';
$api_key =  '36990e4a10167b1a6b820adbc4ea064f';		
//'c31ce6f980e4c9de594a39bb78fda91d-671903ca58ee0e5322df85bdd51b2673';
$connected = 0;
$connected =  $app->cfgCon($app_name, $api_key);
$connected2 = $app->dsGetSetting("Contact", "optiontypes");
if(mb_substr($connected2, 0, 5) == "ERROR"){
        $connected = 0;
}
?>