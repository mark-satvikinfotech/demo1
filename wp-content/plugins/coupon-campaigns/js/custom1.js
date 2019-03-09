$(document).ready(function(){
     
	     $('#popupmessage').hide();

	     $( "#popupmessage" ).addClass( "" );

	$("#coupon-submit").click(function(){

		$( "#popupmessage" ).empty();

	var name = $('#name').val();

	var campagin_id = $('#campagin_id').val();

	var email = $('#email').val();		

		var data = {

			action: 'sendmail',

			campagin_id: campagin_id,

			name:name,

            email:email,

		};



		jQuery.post(custom.ajax_url, data, function(response) {

			$('#name').val("");

			$('#email').val("");



			var myJSON = JSON.parse(response);

			var flag = myJSON.flag;

			var email_count = myJSON.email_count;

			var ip_count = myJSON.ip_count;



			if(email_count != "" && ip_count != ""){

				$('#popupmessage').show();

				$( "#popupmessage" ).addClass( "error" );

				$( "#popupmessage" ).append('Email and IP same chhe');

			}else if(email_count != ""){

				$('#popupmessage').show();

				$( "#popupmessage" ).addClass( "error" );

				$( "#popupmessage" ).append(email_count);

			}else if(ip_count != ""){

				$('#popupmessage').show();

				$( "#popupmessage" ).addClass( "error" );

				$( "#popupmessage" ).append(ip_count);

			}



			if(flag == "popup"){

				$('#popupmessage').show();

				$( "#popupmessage" ).addClass( "success" );

				var message = myJSON.message;

				$( "#popupmessage" ).append(message);

			}

			if(flag == "redirect"){

				var url = myJSON.url;

				var couponcode = myJSON.couponcode;

				url = url+'?couponCode='+couponcode;

				window.location.href = url;

			}



		});

	});

});

