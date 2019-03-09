<?php
//Infusionsoft App Details
require_once ("lib/isdk.php");

// For live 
//$app_name = 'xd295';
//$api_key =  '671903ca58ee0e5322df85bdd51b2673';


$app = new iSDK;

//$tempoptions = get_option("timetap_credential");
//$cred_arr = unserialize($tempoptions);

 $app_name = 'qn241';
 $api_key =  'c31ce6f980e4c9de594a39bb78fda91d';


$connected = 0;

$connected =  $app->cfgCon($app_name, $api_key);

$connected2 = $app->dsGetSetting("Contact", "optiontypes");

if(mb_substr($connected2, 0, 5) == "ERROR"){

       $connected = 0;

}

?>