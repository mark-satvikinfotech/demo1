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


	   });

	}); 

	

	window.setTimeout(function() {

     $(".alert").fadeTo(500, 0).slideUp(500, function(){

         $(this).remove(); 

	 });

   }, 4000); 


});

function ttap_credential()

{



	$('#timetap_credential').click(function(){

		

		var tpkey = $('#tpkey').val();

		var tappkey = $('#tappkey').val();

		var client_id = $('#client_id').val();

		var client_secrete= $('#client_secrete').val();

	    var data = {tpkey: tpkey, tappkey: tappkey,client_id:client_id,client_secrete:client_secrete,action: 'ttapsync_credential' };

	    $.post(ajaxurl, data, function(data) {

		  $('#credential').html(data);

		});

	});

}

$(document).ready(function() {
            var navListItems = $('div.setup-panel div a'),
                allWells = $('.setup-content'),
                allNextBtn = $('.nextBtn');
            allWells.hide();
            navListItems.click(function(e) {
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
            allNextBtn.click(function() {
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
            // for drop down start // 
            var x, i, j, selElmnt, a, b, c;
				/*look for any elements with the class "custom-select":*/
				x = document.getElementsByClassName("custom-select");
				for (i = 0; i < x.length; i++) {
				  selElmnt = x[i].getElementsByTagName("select")[0];
				  /*for each element, create a new DIV that will act as the selected item:*/
				  a = document.createElement("DIV");
				  a.setAttribute("class", "select-selected");
				  a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
				  x[i].appendChild(a);
				  /*for each element, create a new DIV that will contain the option list:*/
				  b = document.createElement("DIV");
				  b.setAttribute("class", "select-items select-hide");
				  for (j = 0; j < selElmnt.length; j++) {
				    /*for each option in the original select element,
				    create a new DIV that will act as an option item:*/
				    c = document.createElement("DIV");
				    c.innerHTML = selElmnt.options[j].innerHTML;
				    c.addEventListener("click", function(e) {
				        /*when an item is clicked, update the original select box,
				        and the selected item:*/
				        var y, i, k, s, h;
				        s = this.parentNode.parentNode.getElementsByTagName("select")[0];
				        h = this.parentNode.previousSibling;
				        for (i = 0; i < s.length; i++) {
				          if (s.options[i].innerHTML == this.innerHTML) {
				            s.selectedIndex = i;
				            h.innerHTML = this.innerHTML;
				            y = this.parentNode.getElementsByClassName("same-as-selected");
				            for (k = 0; k < y.length; k++) {
				              y[k].removeAttribute("class");
				            }
				            this.setAttribute("class", "same-as-selected");
				            break;
				          }
				        }
				        h.click();
				    });
				    b.appendChild(c);
				  }
				  x[i].appendChild(b);
				  a.addEventListener("click", function(e) {
				      /*when the select box is clicked, close any other select boxes,
				      and open/close the current select box:*/
				      e.stopPropagation();
				      closeAllSelect(this);
				      this.nextSibling.classList.toggle("select-hide");
				      this.classList.toggle("select-arrow-active");
				    });
				}
				// for drop down end // 

				function closeAllSelect(elmnt) {
				  /*a function that will close all select boxes in the document,
				  except the current select box:*/
				  var x, y, i, arrNo = [];
				  x = document.getElementsByClassName("select-items");
				  y = document.getElementsByClassName("select-selected");
				  for (i = 0; i < y.length; i++) {
				    if (elmnt == y[i]) {
				      arrNo.push(i)
				    } else {
				      y[i].classList.remove("select-arrow-active");
				    }
				  }
				  for (i = 0; i < x.length; i++) {
				    if (arrNo.indexOf(i)) {
				      x[i].classList.add("select-hide");
				    }
				  }
				}
				/*if the user clicks anywhere outside the select box,
				then close all select boxes:*/
				document.addEventListener("click", closeAllSelect);
            });