<?php
class Is_tag
{
    public function getphoneno($contact_id,$tagx,$tagy)
	{
		include('infusion_config.php');
		
	    $contactId = $contact_id;
		//echo $contactId;
		$selectedFields = array('Phone1');			
		$phon=$infusionsoft->contacts('xml')->load($contactId, $selectedFields);
		$ph = $phon['Phone1'];
		$phone_num = preg_replace('/[^a-zA-Z0-9-_\.]/','', $ph);
		$hphone = str_replace("-", "",$phone_num);
        $phone = substr($hphone, -10);

		//retrive contacts from is using phone no
		$table = "Contact";
		$limit = 1000;
		//$page = 0;

		$queryData = array('_CustomPhone'=>'%'.$phone,'Id'=>'~<>~'.$contactId);
		$selectedFields = array('Id','FirstName','LastName','Company','JobTitle','Email','Phone1','ContactType','LeadSourceId','OwnerID','StreetAddress1','StreetAddress2','City','State','PostalCode','ZipFour1','Country','Phone1Type','Phone1','Phone1Ext','Phone2Type','Phone2','Phone2Ext','Fax1Type','Fax1','Website','Language','TimeZone','_CustomPhone','_ContactId','_Data','_FriendlyName','_LastUpadated','_Reamazemessage');
		$orderBy = "Id";
		$ascending = false;
		$table = "Contact";
		
        $qry = array('Id'=>'~<>~');
		 $total_contact = $infusionsoft->data('xml')->count($table, $qry);
	    
	    //echo $total_contact;  
	   
	    $total_page = round($total_contact/$limit); 
		
	   //echo $total_page;	
	   //file_put_contents(TIMETAP_DIR.'log.txt', "IS Contacts:". $total_contact, FILE_APPEND); 
	   //file_put_contents(TIMETAP_DIR.'log.txt', "IS Contacts Page:". $total_page, FILE_APPEND); 
	   for($i=0; $i<$total_page; $i++)
	   {
		echo $limit;
		echo $page = $i;
		echo "<br/>";	
		$data_contact=$infusionsoft->data('xml')->query($table, $limit, $page, $queryData, $selectedFields, $orderBy, $ascending);
		print_r($data_contact);
		echo "<br/>";
	    if(empty($data_contact))
		{
			$queryData = array('Phone1'=>'%'.$phone,'Id'=>'~<>~'.$contact_id);
			$data_contact=$infusionsoft->data('xml')->query($table, $limit, $page, $queryData, $selectedFields, $orderBy, $ascending);
			print_r($data_contact);
		}
		//echo "<pre>";
		//print_r($data_contact);

		$contact_data = array();
		if (!empty($data_contact)){
			foreach ($data_contact as $result_contact)
			{
	            if($result_contact['Id'] != $contactId)
	            {
					echo "Duplicate Contact Find.";
	            	/*$contact_ID=$result_contact['Id']; 
		            $data=$infusionsoft->contacts('xml')->merge($contactId, $contact_ID);
					//echo "<pre>";
					//print_r($data);*/
	            }
			}
		}
		else
		{
			echo "Record Not Found.";
            /*$tag=$infusionsoft->contacts('xml')->addToGroup($contactId,$tagy);
			$infusionsoft->contacts('xml')->removeFromGroup($contactId,$tagx);*/
		}
		//$limit= $limit+1000;
	}
  }
}
?>








