$(document).ready(function(e){
	$("#orderform").on('submit',(function(e) {

		e.preventDefault();
		var firstname = $('#firstname').val();
		var lastname = $('#lastname').val();
    	var date = new Date($('#datepicker').val());
	    day = date.getDate();
	    month = date.getMonth() + 1;
	    year = date.getFullYear();
	    var date1=[day, month, year].join('/');
	    var myval = document.getElementById("fonts").value;
 		//var file = document.getElementById("file").value;
 		//var file = $("#file").prop("file")[0];
 		//var file=document.getElementById("file").files[0].name;
 		//var file= $('#file').val();
	    var file = $('#file').val();
	   // alert(file);
	   // alert(myval);
    	var dollar="$";
		var splitted = myval.split("|");
		var touroperator=splitted[0];
		var address=splitted[1];
		var finalprice=splitted[2];
		var price=finalprice.concat(dollar);
		var tax1=(finalprice*0.2).toFixed(2);
		var tax=tax1.concat(dollar);
		var subtotall=Number(finalprice)+Number(tax1);
		var subtotal=subtotall+dollar;
		var nationality=document.getElementById("nationality").value;
		var countryesta=document.getElementById("countryesta").value;
		var email = $('#email').val();
		//var cctype=document.getElementById("cctype").value;
		//var ccnum = $('#ccnum').val();
		//var emonth=document.getElementById("emon").value;
		//var eyear=document.getElementById("eyear").value;
		//var csc = $('#csc').val();
		var redirecturl = $('#redirecturl').attr('value');

		//alert(redirecturl);
		//var redirecturl1 = "https://secure.worldpay.com/wcc/purchase";
		//$('#fonts option:not(:selected)').prop('disabled', true);
			terms=document.getElementById("terms").checked;
   			if(email=="" || firstname=="" || terms==""){
   				if(email==""){
   					$( "#emailmessage" ).empty();
				    $('#emailmessage').show();
					$( "#emailmessage" ).addClass( "error" );
					$( "#emailmessage" ).append('Please Enter Email Address');
   				}
   				if(firstname==""){
   					$( "#firstmessage" ).empty();
				    $('#firstmessage').show();
					$( "#firstmessage" ).addClass( "error" );
					$( "#firstmessage" ).append('Please Enter First Name');
   				}
   				if(terms==""){
   					$( "#termsmessage" ).empty();
				    $('#termsmessage').show();
					$( "#termsmessage" ).addClass( "error" );
					$( "#termsmessage" ).append('Please checked terms and condition');
   				}
   			
				return false;
   			}
  			




   			




   			if(document.getElementById("terms").checked) {
				var fd = new FormData();
				var file = jQuery(document).find('input[type="file"]');
				var caption = jQuery(this).find('input[name=file1]');
				var individual_file = file[0].files[0];
				fd.append("file", individual_file);
				var individual_capt = caption.val();
				fd.append("caption", individual_capt);  
				fd.append('action', 'orderformdata');
				fd.append("firstname", firstname);
				fd.append("lastname", lastname);
				fd.append("date1", date1);
				fd.append("touroperator", touroperator);
				fd.append("address", address);
				fd.append("price", price);
				fd.append("tax", tax);
				fd.append("subtotal", subtotal);
				fd.append("nationality", nationality);
				fd.append("countryesta", countryesta);
				fd.append("email", email);
				//fd.append("cctype", cctype);
				//fd.append("ccnum", ccnum);
				//fd.append("emonth", emonth);
				//fd.append("eyear", eyear);
				//fd.append("csc", csc);
				fd.append("redirecturl", redirecturl);
				 $('#load').show();
				jQuery.ajax({
					type: 'POST',
					url: custom.ajax_url,				
					data: fd,
					contentType: false,
					processData: false,
					success: function(response){
						console.log(response);

						//alert(response);

						if(response==1){

						document.getElementById("orderform").submit();
						}

						else{
							window.location.href =redirecturl;
						}

						//var obj1 = JSON.stringify(response);
						// var obj =  JSON.parse(response);
				  //       if( obj.Successful == false ){  
      //                    	alert(obj.Message);
      //                    	 $('#load').hide();                          	
      //                    }                        
      //                  else{
      //                  	alert(obj.Message);
      //                  	window.location.href =redirecturl;
      //                  }
						//alert(response.Successful);
						//window.location.href =redirecturl;
						//document.getElementById("orderform").reset();
					}
				});	 
			    var data = {
				type : 'POST',
				action: 'orderformdata',
				firstname:firstname,
				lastname:lastname,
				date1:date1,
				touroperator:touroperator,
				address:address,
				price:price,
				tax:tax,
				subtotal:subtotal,
				nationality:nationality,
				countryesta:countryesta,
				email:email,
				redirecturl:redirecturl,
				//cctype:cctype,
				//ccnum:ccnum,
				//emonth:emonth,
				//eyear:eyear,
				//csc:csc,
				contentType: false,
				cache: false,
				processData:false,
		    };
		}
		else{
				$( "#termsmessage" ).empty();
			    $('#termsmessage').show();
				$( "#termsmessage" ).addClass( "error" );
				$( "#termsmessage" ).append('Please checked terms and condition');
		}
	}));

	   //  $("#file").change(function() {
	   //  	//alert("lisha");
    //     var file = this.files[0];
    //     var imagefile = file.type;
    //     var match= ["image/jpeg","image/png","image/jpg"];
    //     if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2]))){
    //         alert('Please select a valid image file (JPEG/JPG/PNG).');
    //         $("#file").val('');
    //         return false;
    //     }
    // });
});