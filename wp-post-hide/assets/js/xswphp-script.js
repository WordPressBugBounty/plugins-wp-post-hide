jQuery(document).ready(function($) {

	"use strict";



	if ($(".xswphp-post:checked").length == $(".xswphp-post").length) {

        $(".xswphp-posts").prop('checked', true);

    }



	$('.xswphp-post').change(function(){

		var xswphp_value = "#" + $(this).val();

		if($(this).prop("checked") == true){

			$(xswphp_value).show();

		}else{

			$(xswphp_value).hide();

		}

		if ($(".xswphp-post:checked").length == $(".xswphp-post").length) {

        	$(".xswphp-posts").prop('checked', true);

    	}else{

    		$(".xswphp-posts").prop('checked', false);

    	}

	});

    

	$(".xswphp-form").find('.xswphp-post').each(function(){

		if($(this).is(":checked")){

			var xswphp_value = "#" + $(this).val();

			$(xswphp_value).show();

		}

	});

	

	$(".xswphp-posts").change(function() {

        $(".xswphp-post").prop("checked", $(this).prop("checked"));

        if($(this).prop("checked") == true){

        	$('.xswphp-enables').show();

        }else{

        	$('.xswphp-enables').hide();

        }

    });



    $(".xswphp-select2").select2({

		placeholder : "Select the Hidden Option",
		allowClear: true,
        multiple: true,
        closeOnSelect: false,
        tags: true,
	});

});