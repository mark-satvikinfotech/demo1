  $(document).ready(function () {
  $(".camp").change(function(){
	var camp_id=$(this).val();

		var data = {

		action: 'get_goals',

		post_id: camp_id,

		};

		$.post(custom.ajax_url, data, function(response) {

			$("#goal").html(response);

		});

		});

  	}); 


	function reply_click(id){
		var data = {
					action: 'deletegoal',
					id:id,
		};

		jQuery.post(custom.ajax_url, data, function(response) {
			 location.reload(); 
		});


	}



