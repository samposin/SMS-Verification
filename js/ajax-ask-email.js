

	jQuery(document).ready(function($) {

	  /**
	   * When user clicks on button...
	   *
	   */
	  $('#btn-ask-email').click( function(event) {

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
	    $('.ps_ask_email_indicator').show();

	    // If for some reason result field is visible hide it
	    $('.ask-email-result-message').hide();

	    // Collect data from inputs
	    var ask_email_nonce = $('#ps_ask_email_nonce').val();
	    var ask_email  = $('#ps_ask_email').val();
	    var ask_email_mobile=$('#hdn_ask_email_mobile').val();


	    /**
	     * AJAX URL where to send data
	     * (from localize_script)
	     */
	    var ajax_url = ps_reg_vars.ps_ajax_url;

	    // Data to send
	    data = {
	      action: 'ps_ask_email',
	      nonce: ask_email_nonce,
	      email: ask_email,
	      mobile:ask_email_mobile
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
	            $('.ps_ask_email_indicator').hide();

	            if( response.success) {


					$('.ps-login-cont').show();
					$('.ps-ask-email-cont').hide();


		            $('.login-result-message').html('An OTP is sent to your mobile no. Please use it as password on login page.'); // Add success message to results div
	                $('.login-result-message').removeClass('ps-danger');
	                $('.login-result-message').addClass('ps-success'); // Add class success to results div
	                $('.login-result-message').show(); // Show results div

	                // If user is created
	                $('.ask-email-result-message').html(''); // Add success message to results div
	                $('.ask-email-result-message').removeClass('ps-danger');
	                $('.ask-email-result-message').removeClass('ps-success'); // Add class success to results div
	                $('.ask-email-result-message').hide(); // Show results div

	            }
	            else
	            {

	                var html="";
	                for(var i=0;i<response.errors.length;i++)
	                {
	                    html+=response.errors[i]+'<br>';
	                }

	                $('.ask-email-result-message').html( html); // If there was an error, display it in results div
	                $('.ask-email-result-message').removeClass('ps-success');
	                $('.ask-email-result-message').addClass('ps-danger'); // Add class failed to results div
	                $('.ask-email-result-message').show(); // Show results div
	            }


			},
			error: function(html){
			}
		});


	  });
	});