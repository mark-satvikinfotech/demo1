<?php
$debug = false;
if($debug == true){
	$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
	fwrite($fp, "From Infusionsoft: ".date('d-m-Y H:i:s')." - ".print_r($_REQUEST, true)."\r\n");
	fclose($fp);
}
if(isset($_REQUEST['BranchName']) && $_REQUEST['BranchName'] != '' && $_REQUEST['BranchName'] != null){
	require('../../../wp-load.php');

	$access_token = bf_docebolabs_fetch_token();
	$subdomain = get_option('bf_docebolabs_docebo_subdomain');

	// create branch
	$branchName = $_REQUEST['BranchName'];
	$submiturl = "https://".$subdomain.".docebosaas.com/api/orgchart/createNode";
	$data = array('code' => $branchName, 'translation' => array(
		'arabic' => $branchName,
		'bosnian' => $branchName,
		'bulgarian' => $branchName,
		'croatian' => $branchName,
		'czech' => $branchName,
		'danish' => $branchName,
		'dutch' => $branchName,
		'english' => $branchName,
		'english_uk' => $branchName,
		'farsi' => $branchName,
		'finnish' => $branchName,
		'french' => $branchName,
		'german' => $branchName,
		'greek' => $branchName,
		'hebrew' => $branchName,
		'hungarian' => $branchName,
		'indonesian' => $branchName,
		'italian' => $branchName,
		'japanese' => $branchName,
		'kazakh' => $branchName,
		'korean' => $branchName,
		'lithuanian' => $branchName,
		'norwegian' => $branchName,
		'polish' => $branchName,
		'portuguese' => $branchName,
		'portuguese-br' => $branchName,
		'romanian' => $branchName,
		'russian' => $branchName,
		'simplified_chinese' => $branchName,
		'slovenian' => $branchName,
		'spanish' => $branchName,
		'spanish_latam' => $branchName,
		'swedish' => $branchName,
		'thai' => $branchName,
		'turkish' => $branchName,
		'ukrainian' => $branchName
	));
	if(isset($_REQUEST['ParentBranch']) && $_REQUEST['ParentBranch'] != null && $_REQUEST['ParentBranch'] != ''){
		$branchesSubmiturl = "https://".$subdomain.".docebosaas.com/api/orgchart/stats";
		$branchesData = array('from' => '0', 'count' => '100');

		$options = array('timeout' => 20, 'body' => $branchesData, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
		$httppost = wp_safe_remote_post($branchesSubmiturl, $options);

		$branchesResult = json_decode($httppost['body'], true);
		if($debug == true){
			$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
			fwrite($fp, "docebo api attempted retrieving all branches: ".date('d-m-Y H:i:s')." - ".print_r($branchesResult, true)."\r\n");
			fclose($fp);
		}
		$branchesFound = array();
		$translationsFound = array();
		if(isset($branchesResult['branches']) && count($branchesResult['branches']) >= '1'){
			// find ParentBranch first in code field then under translations
			foreach($branchesResult['branches'] as $branchResult){
				if($branchResult['code'] == $_REQUEST['ParentBranch']){
					$branchesFound[] = $branchResult['id_org'];
				} else {
					foreach($branchResult['translation'] as $translationLanguage){
						if($translationLanguage == $_REQUEST['ParentBranch']){
							$translationsFound[] = $branchResult['id_org'];
							break;
						}
					}
				}
			}
			if(count($branchesFound) == '1'){
				$data['id_parent'] = $branchesFound['0'];
			} elseif(count($translationsFound) == '1'){
				$data['id_parent'] = $translationsFound['0'];
			} else {
				// no parent found do nothing
				die;
			}
		}
	}

	$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1', 'headers' => array('Authorization' => 'Bearer '.$access_token['accessToken']));
	$httppost = wp_safe_remote_post($submiturl, $options);

	$result = json_decode($httppost['body'], true);
	if($debug == true){
		$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
		fwrite($fp, "docebo api attempted assigning profile name ".$_REQUEST['ProfileName']." to user id ".$userid.": ".date('d-m-Y H:i:s')." - ".print_r($result, true)."\r\n");
		fclose($fp);
	}

	if(isset($result['success']) && $result['success'] == '1' && isset($result['id_org']) && $result['id_org'] != '' && $result['id_org'] != null){
		if(isset($_REQUEST['contactId']) && $_REQUEST['contactId'] != '' && $_REQUEST['contactId'] != null && isset($_REQUEST['BranchIDField']) && $_REQUEST['BranchIDField'] != '' && $_REQUEST['BranchIDField'] != null){
			require('../../../wp-load.php');
			global $sitedomain;
			$submiturl = plugin_dir_url(__file__)."docebolabs/bf_docebolabs_update_user.php";
			$data = array(
				'email' => esc_attr(get_option('bf_docebolabs_regemail')),
				'domain' => urlencode($sitedomain),
				'isappid' => esc_attr(get_option('bf_docebolabs_is_app_name')),
				'isapikey' => esc_attr(get_option('bf_docebolabs_is_api_key')),
				'plugin' => 'DoceboLabs',
				'contactId' => $_REQUEST['contactId'],
				'fields' => array($_REQUEST['BranchIDField']),
				'values' => array($result['id_org'])
			);

			if($debug == true){
				$fp = fopen('debug_'.basename(__FILE__).'.txt', 'a');
				fwrite($fp, "data to infusionsoft api : ".date('d-m-Y H:i:s')." - ".print_r($data, true)."\r\n");
				fclose($fp);
			}

			$options = array('timeout' => 20, 'body' => $data, 'httpversion' => '1.1');
			$httppost = wp_safe_remote_post($submiturl, $options);
			if(isset($_REQUEST['debug']) && $_REQUEST['debug'] == 'true'){
				echo '<p>body:</p>';
				echo '<pre>';
				print_r($httppost['body']);
				echo '</pre>';
				echo '<p>complete:</p>';
				echo '<pre>';
				print_r($httppost);
				echo '</pre>';
			}
		}
	}
}
?>