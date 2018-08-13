

	jQuery(document).ready(function($) {

	  /**
	   * When user clicks on button...
	   *
	   */
	  $('#btn-ask-mobile').click( function(event) {

	    /**
	     * Prevent default action, so when user clicks button he doesn't navigate away from page
	     *
	     */
	    if (event.preventDefault) {
	        event.preventDefault();
	    } else {
	        event.returnValue = false;
	    }

	    // Show 'Please wait' loader to user, so she/he knows something is going on
	    $('.ps_ask_mobile_indicator').show();

	    // If for some reason result field is visible hide it
	    $('.ask-mobile-result-message').hide();

	    // Collect data from inputs
	    var ask_mobile_nonce = $('#ps_ask_mobile_nonce').val();
	    var ask_mobile  = $('#ps_ask_mobile').val();
	    var login_user_id=$('#hdn_ask_mobile_user_id').val();


	    /**
	     * AJAX URL where to send data
	     * (from localize_script)
	     */
	    var ajax_url = ps_reg_vars.ps_ajax_url;

	    // Data to send
	    data = {
	      action: 'ask_mobile',
	      nonce: ask_mobile_nonce,
	      mobile: ask_mobile,
	      user_id:login_user_id
	    };

		jQuery.ajax({
			type:   "POST",
			url:    ajax_url,
			data:   data,
			dataType: "json",
			success: function(response){
				console.log('response');

				console.log(response);
				console.log('enter');
	            // Hide 'Please wait' indicator
	            $('.ps_ask_mobile_indicator').hide();

	            if( response.success) {


					$('.ps-login-cont').show();
					$('.ps-ask-mobile-cont').hide();


		            $('.login-result-message').html('An OTP is sent to your mobile no. Please use it as password on login page.'); // Add success message to results div
	                $('.login-result-message').removeClass('ps-danger');
	                $('.login-result-message').addClass('ps-success'); // Add class success to results div
	                $('.login-result-message').show(); // Show results div

	                // If user is created
	                $('.ask-mobile-result-message').html(''); // Add success message to results div
	                $('.ask-mobile-result-message').removeClass('ps-danger');
	                $('.ask-mobile-result-message').removeClass('ps-success'); // Add class success to results div
	                $('.ask-mobile-result-message').hide(); // Show results div

	            }
	            else
	            {

	                var html="";
	                for(var i=0;i<response.errors.length;i++)
	                {
	                    html+=response.errors[i]+'<br>';
	                }

	                $('.ask-mobile-result-message').html( html); // If there was an error, display it in results div
	                $('.ask-mobile-result-message').removeClass('ps-success');
	                $('.ask-mobile-result-message').addClass('ps-danger'); // Add class failed to results div
	                $('.ask-mobile-result-message').show(); // Show results div
	            }


			},
			error: function(html){
			}
		});


	  });
	});