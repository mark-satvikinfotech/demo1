<?php

class wp_geopal_data
{
	public function __construct()
	{
		if(isset($_REQUEST['geopalcron']))
		{
	     $geopalcron = $_REQUEST['geopalcron'];
		 if(strcasecmp($geopalcron, "Y") == 0)
		 {
			include_once('lib/infusionsoft/is_config.php');
			if($json = json_decode(file_get_contents("php://input"), true)) {
				 print_r($json);
				 $data = $json;
			 } else {
				 print_r($_REQUEST);
				 $data = $_REQUEST;
			 }
			 if(empty($data))
			 {
				print_r($_POST);
				 $data = $_POST;
			 }
			
			
			$id="Geopal_log_generate";
			$option_exists = (get_option($id, null) !== null);

			if ($option_exists) {
				update_option($id, $data);
			} else {
				add_option($id, $data);
			}
			
			$str = $data;
			
			$tempoptions = get_option("geopal_credential");
	        $cred_arr = unserialize($tempoptions);
			
			if($cred_arr['uname'] == "hes0012" &&  $cred_arr['pass'] == "48hrlaunch" && $cred_arr['app_name'] == "qn241" && $cred_arr['app_key'] == "c31ce6f980e4c9de594a39bb78fda91d" )
			{
			  
			  $ap = get_option('appointment_custom_tags');
			  $profile_arr = unserialize($ap);		
			  
			  $crt_tag = $profile_arr['apptmnt_crt_tag'];
			  $upd_tag = $profile_arr['apptmnt_upd_tag'];
			  $del_tag = $profile_arr['apptmnt_del_tag'];	
			  
			  $msg_status_arr = array(
							    0 => 'Unassigned',
								1 => 'Assigned',
								2 => 'Rejected',
								3 => 'Completed',
								4 => 'Deleted',
								5 => 'Inprogress',
								6 => 'Accepted',
								7 => 'Incomplete',
								8 => 'Review',
								9 => 'Archive',
								10 => 'Linked',
								11 => 'Cancelled',
								12 => 'Pending'
							);
			  
			  if(isset($str['tag']) && $str['tag'] == "job")
			  {
				    $job_per_email="";
					$job =json_decode($str['job']);
					
					$jb_data = $job->job;
					//print_r($jb_data);
					$job_id = $jb_data->id;
					$job_created_on = $jb_data->created_on;
					$job_updated_on = $jb_data->updated_on;
					$job_identifier = $jb_data->identifier;
					$job_status_id = $jb_data->job_status_id;
					$job_notes = $jb_data->notes;
					$job_cmp_id = $jb_data->company->id;  
					$job_cmp_name = $jb_data->company->name;
					$job_per_id = $jb_data->person->id;
					$job_per_idt =$jb_data->person->identifier;
					$job_per_fname = $jb_data->person->first_name;
					$job_per_lname = $jb_data->person->last_name;
					$job_per_phone = $jb_data->person->phone_number;
					$job_per_mno = $jb_data->person->mobile_number;
					$job_per_email = $jb_data->person->email;
					$job_per_created_on = $jb_data->person->created_on;
					$job_per_updated_on = $jb_data->person->updated_on;
					$job_per_add_id = $jb_data->address->id;
					$job_per_add_1 = $jb_data->address->address_line_1;
					$job_per_add_2 = $jb_data->address->address_line_2;
					$job_per_add_3 = $jb_data->address->address_line_3;
					$job_per_add_city = $jb_data->address->city;
					$job_per_add_postal_code = $jb_data->address->postal_code;
					$job_per_add_cntry_id = $jb_data->address->country_id;
					$job_asset_id = $jb_data->asset->id;
					$job_asset_idt = $jb_data->asset->identifier;
					$job_asset_tempid = $jb_data->asset->asset_template_id;
					$job_asset_cmp_stid = $jb_data->asset->asset_company_status_id;
					$job_cust_id = $jb_data->customer->id;
					$job_cust_idtf = $jb_data->customer->identifier;
					$job_cust_name = $jb_data->customer->name;
					$job_cust_type_id = $jb_data->customer->customer_type_id;
					$job_cust_email = $jb_data->customer->email;
					$job_cust_phone_ofc = $jb_data->customer->phone_office;
					$job_cust_created_on = $jb_data->customer->created_on;
					$job_cust_updated_on = $jb_data->customer->updated_on;
					$job_template_id = $jb_data->job_template->id;
					$job_template_name = $jb_data->job_template->name;
					$job_template_status_id = $jb_data->job_template->job_template_status_id;
					$job_template_created = $jb_data->job_template->created_on;
					$job_template_updated = $jb_data->job_template->updated_on;
					$job_emp_id = $jb_data->employee->id;
					$job_emp_fname = $jb_data->employee->first_name;
					$job_emp_lname = $jb_data->employee->last_name;
					$job_assigned_id = $jb_data->assigned_to->id;
					
					$status_msg = $jb_data->job_status_change_messages;
					foreach($status_msg as $key=>$value)
					{
						$st_id =$value->job_status_id;
						if($st_id == $job_status_id)
						{
							$change_st_msgid = $value->id;
							$change_st_msg = $msg_status_arr[$job_status_id];
						    $change_done_at = $value->done_at;
						}
					}
					
					$jobdata = array('_jobid0' => "$job_id",
								'_Assetstatus' => "$job_template_name",
								'_Appointmentstatusid' => "$job_status_id",
								'_Appointmentcompanyid' => "$job_cmp_id",
								'_Appointmentcompanyname' => "$job_cmp_name",
								'_Appointmentstatus' => "$job_emp_id",
								'_Appointmentidentifier' => "$job_identifier",
								'_Appointmentpersonid' => "$job_per_id",
								'_Appointmentpersonidentifier' => "$job_per_idt",
								/*'_Appointmentfirstname' => "$job_per_fname",
								'_Appointmentpersonlastname' => "$job_per_lname",
								'_Appointmentpersonmobilenumber' => "$job_per_mno",
								'_Appointmentpersonemail' => "$job_per_email",*/
								'FirstName' => "$job_per_fname",
								'LastName' => "$job_per_lname",
								'Company' => "$job_cmp_name",
								'Phone1' => "$job_per_phone",
								'Phone2' => "$job_per_mno",
								'Email' => "$job_per_email",
								'_Appointmentpersoncreatedon' => "$job_per_created_on",
								'_Appointmentpersonupdatedon' => "$job_per_updated_on",
								'_Appointmentaddressid' => "$job_per_add_id",
								/*'_Appointmentaddressline1' => "$job_per_add_1",
								'_Appointmentaddressline2' => "$job_per_add_2",
								'_Appointmentaddressline3' => "$job_per_add_3",
								'_Appointmentaddresscity' => "$job_per_add_city",
								'_Appointmentaddresspostalcode' => "$job_per_add_postal_code",*/
								'StreetAddress1' => "$job_per_add_1",
								'StreetAddress2' => "$job_per_add_2"." $job_per_add_3",
								'City' => "$job_per_add_city",
								'PostalCode' => "$job_per_add_postal_code",
								'_Appointmentaddresscountryid' => "$job_per_add_cntry_id",
								'_Appointmentassetid' => "$job_asset_id",
								'_Appointmentassetidentifier' => "$job_asset_idt",
								'_Appointmentassettemplateid' => "$job_asset_tempid",
								'_Appointmentassetcompanystatusid' => "$job_asset_cmp_stid",
								'_Appointmentcustomerid' => "$job_cust_id",
								'_Appointmentcustomeridentifier' => "$job_cust_idtf",
								'_Appointmentcustomername' => "$job_cust_name",
								'_Appointmentcustomertypeid' => "$job_cust_type_id",
								'_Appointmentcustomeremail' => "$job_cust_email",
								'_Appointmentcustomerphoneoffice' => "$job_cust_phone_ofc",
								'_Appointmentcustomercreatedon' => "$job_cust_created_on",
								'_Appointmentcustomerupdatedon' => "$job_cust_updated_on",
								'_Appointmenttemplateid' => "$job_template_id",
								'_Appointmenttemplatecreatedon' => "$job_template_created",
								'_Appointmenttemplateupdatedon' => "$job_template_updated",
								'_Appointmentempfirstname' => "$job_emp_fname",
								'_Appointmentemplastname' => "$job_emp_lname",
								'_Appointmentassignedid'=> "$job_assigned_id",
								'_Appointmentnotes0' => "$job_notes",
								'_Appointmentdate' => "$job_created_on",
								'_Appointmentupdateddate0' => "$job_updated_on",
								'_Appointmentstatuschangemsgid' => "$change_st_msgid",
								'_Appointmentstatuschangemsg' => "$change_st_msg",
								'_Appointmentchangemsgdoneat' => "$change_done_at"
								
							);
						
						
						$returnFields = array('Id','_jobid0','Email');
					
						if(isset($job_per_email))
						{
							$con = array();
							if($job_per_email != '')
							{
							  $query = array('Email' => $job_per_email);
							  $con = $app->dsQuery("Contact",1,0,$query,$returnFields);
							}
							if(empty($con))
							{
							  $query = array('_jobid0' => $job_id);
							  $con = $app->dsQuery("Contact",1,0,$query,$returnFields);
							}
						}
						if(empty($con))
						{
						   //echo "insert";
						   $contactId = $app->dsAdd("Contact", $jobdata);
						   
						   //job insert tag add
						   $app->grpAssign($contactId, $crt_tag);
						   
						   $notes = array(
						         'ContactId' => $contactId,
								 'CreationNotes' => $str['job'],
								 'ObjectType' => "Note",
								 'CreationDate' => "$job_created_on"
						    );
						   //notes create
						    $app->dsAdd("ContactAction", $notes);
						}
						else
						{
							//echo "update";
							$contactId = $con[0]['Id'];
							$app->updateCon($contactId, $jobdata);
							
							//job update tag add
							$app->grpAssign($contactId, $upd_tag);
							
							$notes = array(
						         'ContactId' => $contactId,
								 'CreationNotes' => $str['job'],
								 'ObjectType' => "Note",
								 'CreationDate' => "$job_updated_on"
						    );
							//notes create
						    $app->dsAdd("ContactAction", $notes);	
							
						}
						
						$app->optIn($job_per_email,"Home page newsletter subscriber");
						
						/*Collecting all tags need to be added to contact*/
						$tag_array = array();
						if($change_st_msg!='')
						{
							$tag_array[0]['tag'] = $change_st_msg;
							$tag_array[0]['category'] = 210;
						}
						
						
						/* Iterating for each tag from list */
						for($tag_count=0;$tag_count<count($tag_array);$tag_count++){
							if(trim($tag_array[$tag_count]['tag'])!=""){
								/* Start Checking Tag exist or not */
								$returnFields = array(
									'GroupCategoryId',
									'GroupDescription',
									'GroupName',
									'Id'
								);
								$query = array(
									'GroupName' => $tag_array[$tag_count]['tag'],
								);
								$tag_search = $app->dsQuery("ContactGroup", 1000, 0, $query, $returnFields);
								/* End Checking Tag exist or not */

								if(isset($tag_search[0]['Id'])){
									$tagId = $tag_search[0]['Id'];
								}else{
									/* Start Creating Tag if does not exist */
									$data = array(
										'GroupName' => $tag_array[$tag_count]['tag'],
										'GroupCategoryId' => $tag_array[$tag_count]['category'], 
									);
									$tagId = $app->dsAdd("ContactGroup", $data);
									/* End Creating Tag if does not exist */
								}

								/* Start asigning Tag to contact */
								$result = $app->grpAssign($contactId, $tagId);
								/* End asigning Tag to contact */
							}
						}
			  }
			  else if(isset($str['tag']) && $str['tag'] == "job_workflow_file")
			  {
				 $jb_wrkflow_fl = json_decode($str['job_workflow_file']);
				 $jb_cmp_id = $jb_wrkflow_fl->company->id;
				 $jb_cmp_name = $jb_wrkflow_fl->company->name;
				 $jb_wrkflow_fl_id = $jb_wrkflow_fl->job_workflow_file->id;
				 $jb_wrkflow_fl_job_id = $jb_wrkflow_fl->job_workflow_file->job_id;
				 $jb_wrkflow_fl_wrkflw_id = $jb_wrkflow_fl->job_workflow_file->job_workflow_id;
				 $jb_wrkflow_fl_s3file_id = $jb_wrkflow_fl->job_workflow_file->s3file_id;
				 $jb_wrkflow_fl_s3file_thumbnail_id = $jb_wrkflow_fl->job_workflow_file->s3file_thumbnail_id;
				 $jb_wrkflow_fl_file_name = $jb_wrkflow_fl->job_workflow_file->file_name;
				 $jb_wrkflow_fl_link_2_file = $jb_wrkflow_fl->job_workflow_file->link_2_file;
				 $jb_wrkflow_fl_uploaded_from = $jb_wrkflow_fl->job_workflow_file->uploaded_from;
				 $jb_wrkflow_fl_created_on = $jb_wrkflow_fl->job_workflow_file->created_on;
				 $jb_wrkflow_fl_updated_on = $jb_wrkflow_fl->job_workflow_file->updated_on;
						
				 if(isset($str['job_workflow']))
				 {
					$jb_wrkflow = json_decode($str['job_workflow']);
					$jb_wrkflow_cmp_id =  $jb_wrkflow->company->id;
					$jb_wrkflow_cmp_name = $jb_wrkflow->company->name;
					$jb_wrkflow_id = $jb_wrkflow->job_workflow->id;
					$jb_wrkflow_job_id = $jb_wrkflow->job_workflow->job_id;
					$jb_wrkflow_template_workflow_id = $jb_wrkflow->job_workflow->template_workflow_id;
					$jb_wrkflow_name = $jb_wrkflow->job_workflow->name;
					$jb_wrkflow_action = $jb_wrkflow->job_workflow->action;
					$jb_wrkflow_action_value = $jb_wrkflow->job_workflow->action_value_entered;
					$jb_wrkflow_created_on = $jb_wrkflow->job_workflow->created_on;
					$jb_wrkflow_updated_on = $jb_wrkflow->job_workflow->updated_on;
				 }
						
				 $curlSession = curl_init('https://app.geopalsolutions.com/api/jobs/get?job_id='.$jb_wrkflow_fl_job_id);

				 curl_setopt($curlSession, CURLOPT_HEADER, false);
					curl_setopt(
					  $curlSession,
					  CURLOPT_USERPWD,
					  implode(':', array('hes0012', '48hrlaunch'))
					);
							
				 curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
				 curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
				 $response = curl_exec($curlSession);
							
				 //curl_close($response);
				 $responseAsAnArray = (is_null($response) || ($response === false))? array(): json_decode($response, true);
				 
				 $get_jb_data = json_decode($response);
							
				 $job_id = $get_jb_data->job->id;
				 $job_created_on = $get_jb_data->job->created_on;
				 $job_updated_on = $get_jb_data->job->updated_on;
				 $job_identifier = $get_jb_data->job->identifier;
				 $job_notes = $get_jb_data->job->notes;
				 $job_status_id = $get_jb_data->job->job_status_id;
				 $job_cmp_id = $get_jb_data->company->id;  
				 $job_cmp_name = $get_jb_data->company->name;
				 $job_per_id = $get_jb_data->job->person->id;
				 $job_per_idt =$get_jb_data->job->person->identifier;
				 $job_per_fname = $get_jb_data->job->person->first_name;
				 $job_per_lname = $get_jb_data->job->person->last_name;
				 $job_per_phone = $jb_data->job->person->phone_number;
				 $job_per_mno = $get_jb_data->job->person->mobile_number;
				 $job_per_email = $get_jb_data->job->person->email;
				 $job_per_created_on = $get_jb_data->job->person->created_on;
				 $job_per_updated_on = $get_jb_data->job->person->updated_on;
				 $job_per_add_id = $get_jb_data->job->address->id;
				 $job_per_add_1 = $get_jb_data->job->address->address_line_1;
				 $job_per_add_2 = $get_jb_data->job->address->address_line_2;
				 $job_per_add_3 = $get_jb_data->job->address->address_line_3;
				 $job_per_add_city = $get_jb_data->job->address->city;
				 $job_per_add_postal_code = $get_jb_data->job->address->postal_code;
				 $job_per_add_cntry_id = $get_jb_data->job->address->country_id;
				 $job_asset_id = $get_jb_data->job->asset->id;
				 $job_asset_idt = $get_jb_data->job->asset->identifier;
				 $job_asset_tempid = $get_jb_data->job->asset->asset_template_id;
				 $job_asset_cmp_stid = $get_jb_data->job->asset->asset_company_status_id;
				 $job_cust_id = $get_jb_data->job->customer->id;
				 $job_cust_idtf = $get_jb_data->job->customer->identifier;
				 $job_cust_name = $get_jb_data->job->customer->name;
				 $job_cust_type_id = $get_jb_data->job->customer->customer_type_id;
				 $job_cust_email = $get_jb_data->job->customer->email;
				 $job_cust_phone_ofc = $get_jb_data->job->customer->phone_office;
				 $job_cust_created_on = $get_jb_data->job->customer->created_on;
				 $job_cust_updated_on = $get_jb_data->job->customer->updated_on;
				 $job_template_id = $get_jb_data->job->job_template->id;
				 $job_template_name = $get_jb_data->job->job_template->name;
				 $job_template_status_id = $get_jb_data->job_template->job_template_status_id;
				 $job_template_created = $get_jb_data->job->job_template->created_on;
				 $job_template_updated = $get_jb_data->job->job_template->updated_on;
				 $job_emp_id = $get_jb_data->job->employee->id;
				 $job_emp_fname = $get_jb_data->job->employee->first_name;
				 $job_emp_lname = $get_jb_data->jobemployee->last_name;
				 $job_assigned_id = $get_jb_data->job->assigned_to->id;
							
				 $status_msg = $get_jb_data->job->job_status_change_messages;
				
				 foreach($status_msg as $key=>$value)
				 {
					$st_id =$value->job_status_id;
					if($st_id == $job_status_id)
					{
					  $change_st_msgid = $value->id;
					  $change_st_msg = $msg_status_arr[$job_status_id];
					  $change_done_at = $value->done_at;
					}
				 }
							
				 $jobdata = array('_jobid0' => "$job_id",
						'_Assetstatus' => "$job_template_name",
						'_Appointmentstatusid' => "$job_status_id",
						'_Appointmentcompanyid' => "$job_cmp_id",
						'_Appointmentstatus' => "$job_emp_id",
						'_Appointmentidentifier' => "$job_identifier",
						'_Appointmentpersonid' => "$job_per_id",
						'_Appointmentpersonidentifier' => "$job_per_idt",
						'FirstName' => "$job_per_fname",
						'LastName' => "$job_per_lname",
						'Company' => "$job_cmp_name",
						'Phone1' => "$job_per_phone",
						'Phone2' => "$job_per_mno",
						'Email' => "$job_per_email",
						'_Appointmentpersoncreatedon' => "$job_per_created_on",
						'_Appointmentpersonupdatedon' => "$job_per_updated_on",
						'_Appointmentaddressid' => "$job_per_add_id",
						'StreetAddress1' => $job_per_add_1,
						'StreetAddress2' => $job_per_add_2." ".$job_per_add_3,
						'City' => "$job_per_add_city",
						'PostalCode' => "$job_per_add_postal_code",
						'_Appointmentaddresscountryid' => "$job_per_add_cntry_id",
						'_Appointmentassetid' => "$job_asset_id",
						'_Appointmentassetidentifier' => "$job_asset_idt",
						'_Appointmentassettemplateid' => "$job_asset_tempid",
						'_Appointmentassetcompanystatusid' => "$job_asset_cmp_stid",
						'_Appointmentcustomerid' => "$job_cust_id",
						'_Appointmentcustomeridentifier' => "$job_cust_idtf",
						'_Appointmentcustomername' => "$job_cust_name",
						'_Appointmentcustomertypeid' => "$job_cust_type_id",
						'_Appointmentcustomeremail' => "$job_cust_email",
						'_Appointmentpersoncreatedon' => "$job_per_created_on",
						'_Appointmentpersonupdatedon' => "$job_per_updated_on",
						'_Appointmentcustomerphoneoffice' => "$job_cust_phone_ofc",
						'_Appointmentcustomercreatedon' => "$job_cust_created_on",
						'_Appointmentcustomerupdatedon' => "$job_cust_updated_on",
						'_Appointmenttemplateid' => "$job_template_id",
						'_Appointmenttemplatecreatedon' => "$job_template_created",
						'_Appointmenttemplateupdatedon' => "$job_template_updated",
						'_Appointmentempfirstname' => "$job_emp_fname",
						'_Appointmentemplastname' => "$job_emp_lname",
						'_Appointmentassignedid'=> "$job_assigned_id",
						'_Appointmentdate' => "$job_created_on",
						'_Appointmentupdateddate0' => "$job_updated_on",
						'_Appointmentworkflowfileid' => "$jb_wrkflow_fl_id",
						'_Appointmentworkflowid' => "$jb_wrkflow_fl_wrkflw_id",
						'_Appointments3fileid' => "$jb_wrkflow_fl_s3file_id",
						'_Appointments3filethumbnailid' => "$jb_wrkflow_fl_s3file_thumbnail_id",
						'_link2file' => "$jb_wrkflow_fl_link_2_file",
						'_Appointmentnotes0' => "$job_notes",
						'_Appointmentworkflowcreatedon' => "$jb_wrkflow_created_on",
						'_Appointmentworkflowupdatedon' => "$jb_wrkflow_updated_on",
						'_Appointmenttemplateworkflowid' => "$jb_wrkflow_template_workflow_id",
						'_Appointmentworkflowfilecreatedon' => "$jb_wrkflow_fl_created_on",
						'_Appointmentworkflowfileupdatedon' => "$jb_wrkflow_fl_updated_on",
						'_Appointmentstatuschangemsgid' => "$change_st_msgid",
						'_Appointmentstatuschangemsg' => "$change_st_msg",
						'_Appointmentchangemsgdoneat' => "$change_done_at"
						);
						
						$returnFields = array('Id','_jobid0','Email');
						
						if(isset($job_per_email))
						{
							$con = array();
							if($job_per_email != '')
							{
							  $query = array('Email' => $job_per_email);
							  $con = $app->dsQuery("Contact",1,0,$query,$returnFields);
							}
							if(empty($con))
							{
							  $query = array('_jobid0' => $job_id);
							  $con = $app->dsQuery("Contact",1,0,$query,$returnFields);
							}
						}
						if(empty($con))
						{
						   //echo "insert";
						    $contactId = $app->dsAdd("Contact", $jobdata);
                           
						   //job insert tag add
						   $app->grpAssign($contactId, $crt_tag);
						  
						   $notes = array(
						         'ContactId' => $contactId,
								 'CreationNotes' => $str['job_workflow'],
								 'ObjectType' => "Note",
								 'CreationDate' => "$job_created_on"
						    );
							
						   //notes create
						    $app->dsAdd("ContactAction", $notes);
							
						}
						else
						{
							//echo "update";
							$contactId = $con[0]['Id'];
							$app->updateCon($contactId, $jobdata);
							
							//job update tag add
							$app->grpAssign($contactId, $upd_tag);
							
							$notes = array(
						         'ContactId' => $contactId,
								 'CreationNotes' => $str['job_workflow'],
								 'ObjectType' => "Note",
								 'CreationDate' => "$job_updated_on"
						    );
							//notes create
						    $app->dsAdd("ContactAction", $notes);	
						}
						
						$app->optIn($job_per_email,"Home page newsletter subscriber");
						//notes insert
						
						/*Collecting all tags need to be added to contact*/
						$tag_array = array();
						if($change_st_msg != '')
						{
							$tag_array[0]['tag'] = $change_st_msg;
							$tag_array[0]['category'] = 210;
						}
						
						/* Iterating for each tag from list */
						for($tag_count=0;$tag_count<count($tag_array);$tag_count++){
							
						  if(trim($tag_array[$tag_count]['tag'])!=""){
							/* Start Checking Tag exist or not */
							$returnFields = array(
								'GroupCategoryId',
								'GroupDescription',
								'GroupName',
								'Id'
							 );
							$query = array(
								'GroupName' => $tag_array[$tag_count]['tag'],
							);
							
							$tag_search = $app->dsQuery("ContactGroup", 1000, 0, $query, $returnFields);
							/* End Checking Tag exist or not */

							if(isset($tag_search[0]['Id'])){
								$tagId = $tag_search[0]['Id'];
							}else{
								/* Start Creating Tag if does not exist */
								$data = array(
										'GroupName' => $tag_array[$tag_count]['tag'],
										'GroupCategoryId' => $tag_array[$tag_count]['category'], 
										);
								$tagId = $app->dsAdd("ContactGroup", $data);
								/* End Creating Tag if does not exist */
							}

								/* Start asigning Tag to contact */
								$result = $app->grpAssign($contactId, $tagId);
								/* End asigning Tag to contact */
							}
						}
			  }
			  else if(isset($str['tag']) && $str['tag'] == "job_workflow")
			  {   
		         
				 $jb_wrkflow = json_decode($str['job_workflow']);
				 
				 $jb_wrkflow_cmp_id =  $jb_wrkflow->company->id;
				 $jb_wrkflow_cmp_name = $jb_wrkflow->company->name;
				 $jb_wrkflow_id = $jb_wrkflow->job_workflow->id;
				 $jb_wrkflow_job_id = $jb_wrkflow->job_workflow->job_id;
				 $jb_wrkflow_template_workflow_id = $jb_wrkflow->job_workflow->template_workflow_id;
				 $jb_wrkflow_name = $jb_wrkflow->job_workflow->name;
				 $jb_wrkflow_action = $jb_wrkflow->job_workflow->action;
				 $jb_wrkflow_action_value = $jb_wrkflow->job_workflow->action_value_entered;
				 $jb_wrkflow_created_on = $jb_wrkflow->job_workflow->created_on;
				 $jb_wrkflow_updated_on = $jb_wrkflow->job_workflow->updated_on;
				
				 $curlSession = curl_init('https://app.geopalsolutions.com/api/jobs/get?job_id='.$jb_wrkflow_job_id);

				 curl_setopt($curlSession, CURLOPT_HEADER, false);
				 curl_setopt($curlSession, CURLOPT_USERPWD, implode(':', array('hes0012', '48hrlaunch')));
							
				 curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
				 curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
				 $response = curl_exec($curlSession);
							
				 //curl_close($response);
				 $responseAsAnArray = (is_null($response) || ($response === false))? array(): json_decode($response, true);
				 
				 $get_jb_data = json_decode($response);
				
							
				 $job_id = $get_jb_data->job->id;
				 $job_created_on = $get_jb_data->job->created_on;
				 $job_updated_on = $get_jb_data->job->updated_on;
				 $job_identifier = $get_jb_data->job->identifier;
				 $job_status_id = $get_jb_data->job->job_status_id;
				 $job_notes = $get_jb_data->job->notes;
				 $job_cmp_id = $get_jb_data->company->id;  
				 $job_cmp_name = $get_jb_data->company->name;
				 $job_per_id = $get_jb_data->job->person->id;
				 $job_per_idt =$get_jb_data->job->person->identifier;
				 $job_per_fname = $get_jb_data->job->person->first_name;
				 $job_per_lname = $get_jb_data->job->person->last_name;
				 $job_per_phone = $get_jb_data->job->person->phone_number;
				 $job_per_mno = $get_jb_data->job->person->mobile_number;
				 $job_per_email = $get_jb_data->job->person->email;
				 $job_per_created_on = $get_jb_data->job->person->created_on;
				 $job_per_updated_on = $get_jb_data->job->person->updated_on;
				 $job_per_add_id = $get_jb_data->job->address->id;
				 $job_per_add_1 = $get_jb_data->job->address->address_line_1;
				 $job_per_add_2 = $get_jb_data->job->address->address_line_2;
				 $job_per_add_3 = $get_jb_data->job->address->address_line_3;
				 $job_per_add_city = $get_jb_data->job->address->city;
				 $job_per_add_postal_code = $get_jb_data->job->address->postal_code;
				 $job_per_add_cntry_id = $get_jb_data->job->address->country_id;
				 $job_asset_id = $get_jb_data->job->asset->id;
				 $job_asset_idt = $get_jb_data->job->asset->identifier;
				 $job_asset_tempid = $get_jb_data->job->asset->asset_template_id;
				 $job_asset_cmp_stid = $get_jb_data->job->asset->asset_company_status_id;
				 $job_cust_id = $get_jb_data->job->customer->id;
				 $job_cust_idtf = $get_jb_data->job->customer->identifier;
				 $job_cust_name = $get_jb_data->job->customer->name;
				 $job_cust_type_id = $get_jb_data->job->customer->customer_type_id;
				 $job_cust_email = $get_jb_data->job->customer->email;
				 $job_cust_phone_ofc = $get_jb_data->job->customer->phone_office;
				 $job_cust_created_on = $get_jb_data->job->customer->created_on;
				 $job_cust_updated_on = $get_jb_data->job->customer->updated_on;
				 $job_template_id = $get_jb_data->job->job_template->id;
				 $job_template_name = $get_jb_data->job->job_template->name;
				 $job_template_status_id = $get_jb_data->job_template->job_template_status_id;
				 $job_template_created = $get_jb_data->job->job_template->created_on;
				 $job_template_updated = $get_jb_data->job->job_template->updated_on;
				 $job_emp_id = $get_jb_data->job->employee->id;
				 $job_emp_fname = $get_jb_data->job->employee->first_name;
				 $job_emp_lname = $get_jb_data->job->employee->last_name;
				 $job_assigned_id = $get_jb_data->job->assigned_to->id;
				 
				 $status_msg = $get_jb_data->job->job_status_change_messages;
				
				 foreach($status_msg as $key=>$value)
				 {
					$st_id = $value->job_status_id;
				    if($st_id == $job_status_id)
					{
					  $change_st_msgid = $value->id;
					  $change_st_msg = $msg_status_arr[$job_status_id];
					  $change_done_at = $value->done_at;
					}
				 }
							
				$jobdata = array(
						   '_jobid0' => "$job_id",
						   '_Assetstatus' => "$job_template_name",
						   '_Appointmentstatusid' => "$job_status_id",
						   '_Appointmentcompanyid' => "$job_cmp_id",
						   '_Appointmentstatus' => "$job_emp_id",
						   '_Appointmentidentifier' => "$job_identifier",
						   '_Appointmentpersonid' => "$job_per_id",
						   '_Appointmentpersonidentifier' => "$job_per_idt",
						   'FirstName' => "$job_per_fname",
						   'LastName' => "$job_per_lname",
						   'Company' => "$job_cmp_name",
						   'Phone1' => "$job_per_phone",
						   'Phone2' => "$job_per_mno",
						   'Email' => "$job_per_email",
						   '_Appointmentpersoncreatedon' => "$job_per_created_on",
						   '_Appointmentpersonupdatedon' => "$job_per_updated_on",
						   '_Appointmentaddressid' => "$job_per_add_id",
						   'StreetAddress1' => "$job_per_add_1",
						   'StreetAddress2' => "$job_per_add_2"." $job_per_add_3",
						   'City' => "$job_per_add_city",
						   'PostalCode' => "$job_per_add_postal_code",
						   '_Appointmentaddresscountryid' => "$job_per_add_cntry_id",
						   '_Appointmentassetid' => "$job_asset_id",
						   '_Appointmentassetidentifier' => "$job_asset_idt",
						   '_Appointmentassettemplateid' => "$job_asset_tempid",
						   '_Appointmentassetcompanystatusid' => "$job_asset_cmp_stid",
						   '_Appointmentcustomerid' => "$job_cust_id",
						   '_Appointmentcustomeridentifier' => "$job_cust_idtf",
						   '_Appointmentcustomername' => "$job_cust_name",
						   '_Appointmentcustomertypeid' => "$job_cust_type_id",
						   '_Appointmentcustomeremail' => "$job_cust_email",
						   '_Appointmentcustomerphoneoffice' => "$job_cust_phone_ofc",
						   '_Appointmentcustomercreatedon' => "$job_cust_created_on",
						   '_Appointmentcustomerupdatedon' => "$job_cust_updated_on",
						   '_Appointmenttemplateid' => "$job_template_id",
						   '_Appointmenttemplatecreatedon' => "$job_template_created",
						   '_Appointmenttemplateupdatedon' => "$job_template_updated",
						   '_Appointmentempfirstname' => "$job_emp_fname",
						   '_Appointmentemplastname' => "$job_emp_lname",
						   '_Appointmentassignedid'=> "$job_assigned_id",
						   '_Appointmentworkflowid' => "$jb_wrkflow_id",
						   '_Appointmentworkflowcreatedon' => "$jb_wrkflow_created_on",
						   '_Appointmentworkflowupdatedon' => "$jb_wrkflow_updated_on",
						   '_Appointmenttemplateworkflowid' => "$jb_wrkflow_template_workflow_id",
						   '_Appointmentnotes0' => "$job_notes",
						   '_Appointmentdate' => "$job_created_on",
						   '_Appointmentupdateddate0' => "$job_updated_on",
						   '_Appointmentstatuschangemsgid' => "$change_st_msgid",
						   '_Appointmentstatuschangemsg' => "$change_st_msg",
						   '_Appointmentchangemsgdoneat' => "$change_done_at"
								
						);
						
					$returnFields = array('Id','_jobid0','Email');
						
					if(isset($job_per_email))
					{
						$con = array();
						if($job_per_email != '')
						{
							$query = array('Email' => $job_per_email);
							$con = $app->dsQuery("Contact",1,0,$query,$returnFields);
						}
						if(empty($con))
						{
							$query = array('_jobid0' => $job_id);
							$con = $app->dsQuery("Contact",1,0,$query,$returnFields);
						}
					}
					if(empty($con))
					{
						
					    $contactId = $app->dsAdd("Contact", $jobdata);
						   
						//job insert tag add
						$app->grpAssign($contactId, $crt_tag);
						   
						$notes = array(
						         'ContactId' => $contactId,
								 'CreationNotes' => $str['job_workflow'],
								 'ObjectType' => "Note",
								 'CreationDate' => "$job_created_on"
						);
						
						//notes create
						$app->dsAdd("ContactAction", $notes);
					}
					else
					{
						$contactId = $con[0]['Id'];
						$app->updateCon($contactId, $jobdata);
							
						//job update tag add
						$app->grpAssign($contactId, $upd_tag);
							
						$notes = array(
						         'ContactId' => $contactId,
								 'CreationNotes' => $str['job_workflow'],
								 'ObjectType' => "Note",
								 'CreationDate' => "$job_updated_on"
						);
						//notes create
						$app->dsAdd("ContactAction", $notes);	
							
					}
						
					$app->optIn($job_per_email,"Home page newsletter subscriber");
						
					/*Collecting all tags need to be added to contact*/
					$tag_array = array();
					if($change_st_msg != '')
					{
						$tag_array[0]['tag'] = $change_st_msg;
						$tag_array[0]['category'] = 210;
					}
						
						
					/* Iterating for each tag from list */
					for($tag_count=0;$tag_count<count($tag_array);$tag_count++){
						if(trim($tag_array[$tag_count]['tag'])!=""){
							/* Start Checking Tag exist or not */
							$returnFields = array(
								'GroupCategoryId',
								'GroupDescription',
								'GroupName',
								'Id'
							);
						$query = array(
								'GroupName' => $tag_array[$tag_count]['tag'],
						);
						
						$tag_search = $app->dsQuery("ContactGroup", 1000, 0, $query, $returnFields);
						/* End Checking Tag exist or not */

						if(isset($tag_search[0]['Id'])){
								$tagId = $tag_search[0]['Id'];
						}else{
						/* Start Creating Tag if does not exist */
						    $data = array(
								'GroupName' => $tag_array[$tag_count]['tag'],
								'GroupCategoryId' => $tag_array[$tag_count]['category'], 
							);
						$tagId = $app->dsAdd("ContactGroup", $data);
						/* End Creating Tag if does not exist */
						}

						/* Start asigning Tag to contact */
						$result = $app->grpAssign($contactId, $tagId);
						/* End asigning Tag to contact */
					}
				}
			  }
			  else if(isset($str['tag']) && $str['tag'] == "job_field")
			  {
				  if(isset($str['job_field']))
				  {
					$jb_field = json_decode($str['job_field']);
					$job_field_id = $jb_field->job_field->id;
					$job_id = $jb_field->job_field->job_id;
					$job_field_name = $jb_field->job_field->name;
					$job_field_action_value = $jb_field->job_field->action_value_entered;
					$job_filed_done= $jb_field->job_field->done_at;
				 	
					$curlSession = curl_init('https://app.geopalsolutions.com/api/jobs/get?job_id='.$job_id);

					curl_setopt($curlSession, CURLOPT_HEADER, false);
					curl_setopt($curlSession, CURLOPT_USERPWD, implode(':', array('hes0012', '48hrlaunch')));
							
					curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
					curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
					$response = curl_exec($curlSession);
							
					//curl_close($response);
					$responseAsAnArray = (is_null($response) || ($response === false))? array(): json_decode($response, true);
					$get_jb_data = json_decode($response);
							
					$job_id = $get_jb_data->job->id;
					$job_created_on = $get_jb_data->job->created_on;
					$job_updated_on = $get_jb_data->job->updated_on;
					$job_identifier = $get_jb_data->job->identifier;
					$job_status_id = $get_jb_data->job->job_status_id;
					$job_notes = $get_jb_data->job->notes;
					$job_cmp_id = $get_jb_data->company->id;  
					$job_cmp_name = $get_jb_data->company->name;
					$job_per_id = $get_jb_data->job->person->id;
					$job_per_idt =$get_jb_data->job->person->identifier;
					$job_per_fname = $get_jb_data->job->person->first_name;
					$job_per_lname = $get_jb_data->job->person->last_name;
					$job_per_phone = $jb_data->job->person->phone_number;
					$job_per_mno = $get_jb_data->job->person->mobile_number;
					$job_per_email = $get_jb_data->job->person->email;
					$job_per_created_on = $get_jb_data->job->person->created_on;
					$job_per_updated_on = $get_jb_data->job->person->updated_on;
					$job_per_add_id = $get_jb_data->job->address->id;
					$job_per_add_1 = $get_jb_data->job->address->address_line_1;
					$job_per_add_2 = $get_jb_data->job->address->address_line_2;
					$job_per_add_3 = $get_jb_data->job->address->address_line_3;
					$job_per_add_city = $get_jb_data->job->address->city;
					$job_per_add_postal_code = $get_jb_data->job->address->postal_code;
					$job_per_add_cntry_id = $get_jb_data->job->address->country_id;
					$job_asset_id = $jb_data->job->asset->id;
					$job_asset_idt = $jb_data->job->asset->identifier;
					$job_asset_tempid = $jb_data->job->asset->asset_template_id;
					$job_asset_cmp_stid = $jb_data->job->asset->asset_company_status_id;
					$job_cust_id = $get_jb_data->job->customer->id;
					$job_cust_idtf = $get_jb_data->job->customer->identifier;
					$job_cust_name = $get_jb_data->job->customer->name;
					$job_cust_type_id = $get_jb_data->job->customer->customer_type_id;
					$job_cust_email = $get_jb_data->job->customer->email;
					$job_cust_phone_ofc = $get_jb_data->job->customer->phone_office;
					$job_cust_created_on = $get_jb_data->job->customer->created_on;
					$job_cust_updated_on = $get_jb_data->job->customer->updated_on;
					$job_template_id = $get_jb_data->job->job_template->id;
					$job_template_name = $get_jb_data->job->job_template->name;
					$job_template_status_id = $get_jb_data->job_template->job_template_status_id;
					$job_template_created = $get_jb_data->job->job_template->created_on;
					$job_template_updated = $get_jb_data->job->job_template->updated_on;
					$job_emp_id = $get_jb_data->job->employee->id;
					$job_emp_fname = $get_jb_data->job->employee->first_name;
					$job_emp_lname = $get_jb_data->job->employee->last_name;
					$job_assigned_id = $get_jb_data->job->assigned_to->id;
							
				    $status_msg = $get_jb_data->job->job_status_change_messages;
							
					foreach($status_msg as $key=>$value)
					{
				    	$st_id =$value->job_status_id;
					    if($st_id == $job_status_id)
						{
							$change_st_msgid = $value->id;
							$change_st_msg = $msg_status_arr[$job_status_id];
							$change_done_at = $value->done_at;
						}
					}
						 
					$jobdata = array('_jobid0' => "$job_id",
							'_Assetstatus' => "$job_template_name",
							'_Appointmentstatusid' => "$job_status_id",
							'_Appointmentcompanyid' => "$job_cmp_id",
							'_Appointmentstatus' => "$job_emp_id",
							'_Appointmentidentifier' => "$job_identifier",
							'_Appointmentpersonid' => "$job_per_id",
							'_Appointmentpersonidentifier' => "$job_per_idt",
							'FirstName' => "$job_per_fname",
							'LastName' => "$job_per_lname",
							'Company' => "$job_cmp_name",
							'Phone1' => "$job_per_phone",
							'Phone2' => "$job_per_mno",
							'Email' => "$job_per_email",
							'_Appointmentpersoncreatedon' => "$job_per_created_on",
							'_Appointmentpersonupdatedon' => "$job_per_updated_on",
							'_Appointmentaddressid' => "$job_per_add_id",
							'StreetAddress1' => "$job_per_add_1",
							'StreetAddress2' => "$job_per_add_2"." $job_per_add_3",
							'City' => "$job_per_add_city",
							'PostalCode' => "$job_per_add_postal_code",
							'_Appointmentaddresscountryid' => "$job_per_add_cntry_id",
							'_Appointmentassetid' => "$job_asset_id",
							'_Appointmentassetidentifier' => "$job_asset_idt",
							'_Appointmentassettemplateid' => "$job_asset_tempid",
							'_Appointmentassetcompanystatusid' => "$job_asset_cmp_stid",
							'_Appointmentcustomerid' => "$job_cust_id",
							'_Appointmentcustomeridentifier' => "$job_cust_idtf",
							'_Appointmentcustomername' => "$job_cust_name",
							'_Appointmentcustomertypeid' => "$job_cust_type_id",
							'_Appointmentcustomeremail' => "$job_cust_email",
							'_Appointmentcustomerphoneoffice' => "$job_cust_phone_ofc",
							'_Appointmentcustomercreatedon' => "$job_cust_created_on",
							'_Appointmentcustomerupdatedon' => "$job_cust_updated_on",
							'_Appointmenttemplateid' => "$job_template_id",
							'_Appointmenttemplatecreatedon' => "$job_template_created",
							'_Appointmenttemplateupdatedon' => "$job_template_updated",
							'_Appointmentempfirstname' => "$job_emp_fname",
							'_Appointmentemplastname' => "$job_emp_lname",
							'_Appointmentassignedid'=> "$job_assigned_id",
							'_Appointmentfieldid0' => "$job_field_id",
							'_Appointmentfiledname' => "$job_field_name",
							'_Appointmentfiledvalue' => "$job_field_action_value",
							'_Appointmentfielddoneat' => "$job_filed_done",
							'_Appointmentnotes0' => "$job_notes",
							'_Appointmentdate' => "$job_created_on",
							'_Appointmentupdateddate0' => "$job_updated_on",
							'_Appointmentstatuschangemsgid' => "$change_st_msgid",
							'_Appointmentstatuschangemsg' => "$change_st_msg",
							'_Appointmentchangemsgdoneat' => "$change_done_at"
						);
							
						$returnFields = array('Id','_jobid0','Email');
						
						if(isset($job_per_email))
						{
							$con = array();
							if($job_per_email != '')
							{
							  $query = array('Email' => $job_per_email);
							  $con = $app->dsQuery("Contact",1,0,$query,$returnFields);
							}
							if(empty($con))
							{
							  $query = array('_jobid0' => $job_id);
							  $con = $app->dsQuery("Contact",1,0,$query,$returnFields);
							}
						}
						if(empty($con))
						{
						  
						   $contactId = $app->dsAdd("Contact", $jobdata);
						   
						   //job insert tag add
						   $app->grpAssign($contactId, $crt_tag);
						   
						   $notes = array(
						         'ContactId' => $contactId,
								 'CreationNotes' => $str['job_field'],
								 'ObjectType' => "Note",
								 'CreationDate' => "$job_created_on"
						    );
							
						   //notes create
						    $app->dsAdd("ContactAction", $notes);
						}
						else
						{
							$contactId = $con[0]['Id'];
							$app->updateCon($contactId, $jobdata);
							
							//job update tag add
							$app->grpAssign($contactId, $upd_tag);
							
							$notes = array(
						         'ContactId' => $contactId,
								 'CreationNotes' => $str['job_field'],
								 'ObjectType' => "Note",
								 'CreationDate' => "$job_updated_on"
						    );
							//notes create
						    $app->dsAdd("ContactAction", $notes);	
							
						}
							
						$app->optIn($job_per_email,"Home page newsletter subscriber");
							
						/*Collecting all tags need to be added to contact*/
						$tag_array = array();
						if($change_st_msg!='')
						{
							$tag_array[0]['tag'] = $change_st_msg;
							$tag_array[0]['category'] = 210;
						}
						
						
						/* Iterating for each tag from list */
						for($tag_count=0;$tag_count<count($tag_array);$tag_count++){
							if(trim($tag_array[$tag_count]['tag'])!=""){
								/* Start Checking Tag exist or not */
								$returnFields = array(
									'GroupCategoryId',
									'GroupDescription',
									'GroupName',
									'Id'
								);
								$query = array(
									'GroupName' => $tag_array[$tag_count]['tag'],
								);
								$tag_search = $app->dsQuery("ContactGroup", 1000, 0, $query, $returnFields);
								/* End Checking Tag exist or not */

								if(isset($tag_search[0]['Id'])){
									$tagId = $tag_search[0]['Id'];
								}else{
									/* Start Creating Tag if does not exist */
									$data = array(
										'GroupName' => $tag_array[$tag_count]['tag'],
										'GroupCategoryId' => $tag_array[$tag_count]['category'], 
									);
									$tagId = $app->dsAdd("ContactGroup", $data);
									/* End Creating Tag if does not exist */
								}

								/* Start asigning Tag to contact */
								$result = $app->grpAssign($contactId, $tagId);
								/* End asigning Tag to contact */
							}
						}
					}
			    }
			}
			$date1= date("Y-m-d");
			$date = date("Y-m-d H:i:s");
			   
			file_put_contents(GEOPAL_DIR.'uploads/geopal_logs/'.$date1.'.txt', "\n\n====================\n", FILE_APPEND);
			file_put_contents(GEOPAL_DIR.'uploads/geopal_logs/'.$date1.'.txt','Date: '.$date."\n", FILE_APPEND);
			if(isset($contactId))
			{
				file_put_contents(GEOPAL_DIR.'uploads/geopal_logs/'.$date1.'.txt','Contact Id: '.$contactId."\n",FILE_APPEND);
			}
			file_put_contents(GEOPAL_DIR.'uploads/geopal_logs/'.$date1.'.txt', print_r($data, true), FILE_APPEND);
			file_put_contents(GEOPAL_DIR.'uploads/geopal_logs/'.$date1.'.txt',"\n\n====================\n", FILE_APPEND);
			exit;
		  }
		}
		if(isset($_REQUEST['Iscron']))
		{
			 $Iscron = $_REQUEST['Iscron']; 
			if(strcasecmp($Iscron, "Y") == 0)
			{
				
				include_once('ifs_to_geo.php');
			}
		}
		
	}
	
	function load_custom_wp_admin_style() {
		
		$plugin_url = plugin_dir_url( __FILE__ );
		wp_enqueue_style( 'bootstrap', $plugin_url . 'assets/bootstrap/css/bootstrap.min.css' );
		wp_enqueue_style( 'bootstrap', $plugin_url . 'assets/bootstrap/css/bootstrap3.3.7.min.css' );
		wp_enqueue_style( 'style', $plugin_url . 'assets/css/style1.css' );
	}

	function load_custom_wp_admin_script() {
		
		$plugin_url = plugin_dir_url( __FILE__ );
		wp_enqueue_script( 'jquery-min', $plugin_url . 'assets/js/jquery.min.js' );
		wp_enqueue_script( 'bootstrap-min', $plugin_url . 'assets/bootstrap/js/bootstrap.min.js' );
		wp_enqueue_script( 'script', $plugin_url . 'assets/js/scripts.js' );
		wp_localize_script( 'ajaxHandle', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin_ajax.php' )));
	}
	
	function geopal_sync_credential()
	{
		if(isset($_POST['uname']) && isset($_POST['pass']))
		{
		  $uname = $_POST['uname'];
		  $pass = $_POST['pass'];
		  $app_name = $_POST['app_name'];	
		  $app_key = $_POST['app_key'];
		 
		  
		  if($uname == "hes0012" && $pass == "48hrlaunch" && $app_name == "qn241" && $app_key == "c31ce6f980e4c9de594a39bb78fda91d")	
		  {
			echo "<h4>GeoPal and Infusionsoft's credentials are successfully added.<br/><br/> Navigate to help page to setup Crons and Webhook.<br/><br/> You can assign tags in Infusionsoft from IS Tags page.</h4>";
			
			$custom_tag = serialize($_POST);
			$id = "geopal_credential";
			$option_exists = (get_option($id, null) !== null);

			if ($option_exists) {
				update_option($id, $custom_tag);
			} else {
				add_option($id, $custom_tag);
			}
			
		  }		
		  else		  
		  {
			echo "<h4>Please try again:</h4>";
		    echo "<span class='alert-danger'><h4>Your username and password is incorrect.</h4></span>";
		  }
		 
		}
		wp_die();
	}
	function geopal_appointment_create()
	{
		$appt_cr = $_POST['apptmnt_crt_tag'];
		$appt_upt = $_POST['apptmnt_upd_tag'];
		$appt_del = $_POST['apptmnt_del_tag'];
	    $custom_tag = serialize($_POST);
		
		$id = "appointment_custom_tags";
        $option_exists = (get_option($id, null) !== null);

		if ($option_exists) {
			update_option($id, $custom_tag);
		} else {
			add_option($id, $custom_tag);
		}
	}
}
$geopalobj = new wp_geopal_data();
?>