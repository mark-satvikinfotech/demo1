<?php
session_start();
class wp_timetap_IS_data{

	public function __construct(){	
		
		//IS create/update contacts from IS to Reamaze
	    if(isset($_REQUEST['cron4old']))
		{
	      $Reamazecron = $_REQUEST['cron4old']; 
		  if(strcasecmp($Reamazecron, "Y") == 0)
		  {
			include('reamaze_cron1.php');
		  }
		}
	    
		//reamaze contact add in IS Cron
		if(isset($_REQUEST['ReamazeContactIScron']))
		{
		   $Reamazecontcron = $_REQUEST['ReamazeContactIScron']; 
		   if(strcasecmp($Reamazecontcron, "Y") == 0)
		   {
			 include('reamaze_to_IS.php');
		   }
		}
		
		//Display Email History from Infusionsoft in Reamaze as Email Conversation
		if(isset($_REQUEST['ISEmailhistorytoReamazecron']))
		{
		   $ISEmailhistorytoReamazecron = $_REQUEST['ISEmailhistorytoReamazecron']; 
		   if(strcasecmp($ISEmailhistorytoReamazecron, "Y") == 0)
		   {
			 include('IS_Reamaze_Email_Conversation.php');
		   }	
		}
		
		//Trigger Text Conversation in Reamaze, via an HTTP Post from Infusionsoft
		if(isset($_REQUEST['ISTextconversiontoReamazecron']))
		{
		   $ISTextconversiontoReamazecron = $_REQUEST['ISTextconversiontoReamazecron']; 
		   if(strcasecmp($ISTextconversiontoReamazecron, "Y") == 0)
		   {
			 include('reamaze_textconversationcron.php');
		   }	
		}
		
		//Sync Text Messages sent from TimeTap to display as conversations in Reamaze
		if(isset($_REQUEST['TimetapmsgToReamazeconversioncron']))
		{
		   $TimetapmsgToReamazeconversioncron = $_REQUEST['TimetapmsgToReamazeconversioncron']; 
		   if(strcasecmp($TimetapmsgToReamazeconversioncron, "Y") == 0)
		   {
			 include('Timetap_Reamaze_Text_Conversation.php');
		   }	
		}
		
		//Sync TimeTap Invoicesopen as Infusionsoft Orders
		if(isset($_REQUEST['TimetapInvoiceopencron']))
		{
		   $TimetapInvoiceopencron = $_REQUEST['TimetapInvoiceopencron']; 
		   if(strcasecmp($TimetapInvoiceopencron, "Y") == 0)
		   {
			 include('Timetap_Invoiceopen_IS.php');
		   }	
		}
		
		//Sync TimeTap Invoicesclosed as Infusionsoft Orders
		if(isset($_REQUEST['TimetapInvoiceclosedcron']))
		{
		   $TimetapInvoiceclosedcron = $_REQUEST['TimetapInvoiceclosedcron']; 
		   if(strcasecmp($TimetapInvoiceclosedcron, "Y") == 0)
		   {
			 include('Timetap_Invoiceclosed_IS.php');
		   }	
		}
		
		//Sync TimeTap Invoicesvoid as Infusionsoft Orders
		if(isset($_REQUEST['TimetapInvoicevoidcron']))
		{
		   $TimetapInvoicevoidcron = $_REQUEST['TimetapInvoicevoidcron'];
		   if(strcasecmp($TimetapInvoicevoidcron, "Y") == 0)
		   {
			 include('Timetap_Invoicevoid_IS.php');
		   }	
		}
		
		//merge contact webhook request
		if(isset($_REQUEST['ismergeduplicatecontact']))
		{
	      $Reamazecron = $_REQUEST['ismergeduplicatecontact']; 
		  if(strcasecmp($Reamazecron, "Y") == 0)
		  {
			include('iscontactwebhook.php');
		  }
		}
	
	}

function load_custom_wp_admin_style() {
		
		$plugin_url = plugin_dir_url( __FILE__ );
		wp_enqueue_style( 'bootstrap', $plugin_url . 'assets/bootstrap/css/bootstrap.min.css' );
		wp_enqueue_style( 'bootstrap1', $plugin_url . 'assets/bootstrap/css/bootstrap3.3.7.min.css' );
		wp_enqueue_style( 'style', $plugin_url . 'assets/css/style1.css' );
	}

	function load_custom_wp_admin_script() {
		
		$plugin_url = plugin_dir_url( __FILE__ );
		wp_enqueue_script( 'jquery-min', $plugin_url . 'assets/js/jquery.min.js' );
		wp_enqueue_script( 'bootstrap-min', $plugin_url . 'assets/bootstrap/js/bootstrap.min.js' );
		wp_enqueue_script( 'script', $plugin_url . 'assets/js/scripts.js' );
		
		wp_localize_script( 'ajaxHandle', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin_ajax.php' )));


		  wp_enqueue_script( 'ajax-script', $plugin_url . '/assets/js/custom.js', array('jquery') );

		  wp_localize_script( 'ajax-script', 'custom',
           array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		 
	}


	function timetap_sync_credential()
	{
		if(isset($_POST['tappkey']) && isset($_POST['tpkey']))
		{
		  $tappkey = $_POST['tappkey'];
		  $tpkey = $_POST['tpkey'];
		  $client_id = $_POST['client_id'];	
		  $client_secrete = $_POST['client_secrete'];

			//echo "<h4>Timetap and Infusionsoft's credentials are successfully added.<br/><br/> Navigate to help page to setup Crons and Webhook.<br/><br/> You can assign product in Infusionsoft from Timetap product.</h4>";
			
			$custom_tag = serialize($_POST);
			$id = "timetap_credential";
			$option_exists = (get_option($id, null) !== null);
		    $tempoptions = get_option("timetap_credential");	        $cred_arr = unserialize($tempoptions);					if(!empty($cred_arr))			{				if($cred_arr['client_id'] == $_POST['client_id'] && $cred_arr['client_secrete'] == $_POST['client_secrete'])				{										include('infusion_config.php');				}				else if($cred_arr['client_id'] != $_POST['client_id'] && $cred_arr['client_secrete'] != $_POST['client_secrete'])				{					$id_access = "accesstoken";					$option_exists = (delete_option($id_access, null) !== null);					if($option_exists) {						update_option($id, $custom_tag);					    session_destroy();					    include('infusion_config.php');					}					 				}			}			else			{
				if ($option_exists) {					update_option($id, $custom_tag);					include('infusion_config.php');				} else {
					add_option($id, $custom_tag);					include('infusion_config.php');
				}			}
		}
		wp_die();
	}
}


$timetapobj = new wp_timetap_IS_data();
?>
