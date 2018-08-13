

	jQuery(document).ready(function($) {

	  /**
	   * When user clicks on button...
	   *
	   */
	  $('#btn-forgot-password').click( function(event) {

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
	    $('.ps_forgot_password_indicator').show();

	    // If for some reason result field is visible hide it
	    $('.forgot-password-result-message').hide();

	    // Collect data from inputs
	    var forgot_password_nonce = $('#ps_forgot_password_nonce').val();
	    var forgot_password_mobile  = $('#ps_forgot_password_mobile').val();
	    var login_user_id=$('#hdn_ask_mobile_user_id').val();


	    /**
	     * AJAX URL where to send data
	     * (from localize_script)
	     */
	    var ajax_url = ps_reg_vars.ps_ajax_url;

	    // Data to send
	    data = {
	      action: 'ps_forgot_password',
	      nonce: forgot_password_nonce,
	      mobile: forgot_password_mobile,
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
	            $('.ps_forgot_password_indicator').hide();

	            if( response.success) {


					if(response.ask_email)
					{
						$('.ps-forgot-password-cont').hide();
						$('.ps-ask-email-cont').show();
						$('#hdn_ask_email_mobile').val(response.mobile);

						$('.ask-email-result-message').html('Mobile number not linked with any user. Link your mobile number with your email ID and get new password on mobile'); // Add success message to results div
		                $('.ask-email-result-message').addClass('ps-danger');
		                $('.ask-email-result-message').removeClass('ps-success'); // Add class success to results div
		                $('.ask-email-result-message').show(); // Show results div
					}
					else
					{
						$('.ps-forgot-password-cont').hide();
						$('.ps-login-cont').show();

			            $('.login-result-message').html('An OTP is sent to your mobile no. Please use it as password on login page.'); // Add success message to results div
			            $('.login-result-message').removeClass('ps-danger');
			            $('.login-result-message').addClass('ps-success'); // Add class success to results div
			            $('.login-result-message').show(); // Show results div
					}

	            }
	            else
	            {

	                var html="";
	                for(var i=0;i<response.errors.length;i++)
	                {
	                    html+=response.errors[i]+'<br>';
	                }

	                $('.forgot-password-result-message').html( html); // If there was an error, display it in results div
	                $('.forgot-password-result-message').removeClass('ps-success');
	                $('.forgot-password-result-message').addClass('ps-danger'); // Add class failed to results div
	                $('.forgot-password-result-message').show(); // Show results div
	            }


			},
			error: function(html){
			}
		});


	  });
	});