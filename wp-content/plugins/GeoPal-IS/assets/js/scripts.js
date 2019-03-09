$(document).ready(function () {

    var navListItems = $('div.setup-panel div a'),
        allWells = $('.setup-content'),
        allNextBtn = $('.nextBtn');

    allWells.hide();

    navListItems.click(function (e) {
        e.preventDefault();
        var $target = $($(this).attr('href')),
            $item = $(this);

        if (!$item.hasClass('disabled')) {
            navListItems.removeClass('btn-success').addClass('btn-default');
            $item.addClass('btn-success');
            allWells.hide();
            $target.show();
            $target.find('input:eq(0)').focus();
        }
    });

    allNextBtn.click(function () {
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find("input[type='text'],input[type='url']"),
            isValid = true;

        $(".form-group").removeClass("has-error");
        for (var i = 0; i < curInputs.length; i++) {
            if (!curInputs[i].validity.valid) {
                isValid = false;
                $(curInputs[i]).closest(".form-group").addClass("has-error");
            }
        }

        if (isValid) nextStepWizard.removeAttr('disabled').trigger('click');
    });

    $('div.setup-panel div a.btn-success').trigger('click');
	
	//using bootstrap datatable load table 
	 $('#example').DataTable(); 
	
	//synchronized button click
	   
    $('#synchronize_products').click(function() { 
	
	   var data = {
       action: 'sync_product'
	   };
	   
	    var self = $( this );
		var loaderContainer = $( '<span/>', {
            'class': 'loader-image-container'
        }).insertAfter( self );

        var loader = $( '<img/>', {
            src:  'http://localhost/wordpress/wp-admin/images/loading.gif',
            'class': 'loader-image'
        }).appendTo( loaderContainer );
		
	     $.post(ajaxurl, data, function(data) {
		   loaderContainer.remove();
		   window.location.reload();
		   //$('#display_products').html(data);
		  //$('#msg').html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>Products successfully synchronize</div></div>');
	   });
	}); 
	//alert disapper after some time
	window.setTimeout(function() {
     $(".alert").fadeTo(500, 0).slideUp(500, function(){
         $(this).remove(); 
	 });
   }, 4000); 
  //

});
function geo_credential()
{

	$('#geopal_credential').click(function(){
		
		var uname = $('#uname').val();
		var pass = $('#pass').val();
		var app_name = $('#app_name').val();
		var app_key = $('#app_key').val();
	    var data = {uname: uname, pass: pass,app_name: app_name,app_key: app_key,  action: 'geosync_credential' };
	    $.post(ajaxurl, data, function(data) {
		  
		   $('#credential').html(data);
				
		});
	});
}
function appntmntcreate()
{
	$('#appointmentbtn_update').click(function(){
		
		 var apptmnt_crt= $('#appointment_create').val();
		 var apptmnt_upd= $('#appointment_update').val();
		 var apptmnt_del= $('#appointment_delete').val();
		
		 var data = {apptmnt_crt_tag: apptmnt_crt,apptmnt_upd_tag: apptmnt_upd,apptmnt_del_tag: apptmnt_del,  action: 'geoappnt_create' };
		 $.post(ajaxurl, data, function(data) {
		  
		   $('#msg').html('<div class="alert alert-success"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Success!</strong> Custom Appointment tag successfully Added.</div>');
				
		});
	});
}


