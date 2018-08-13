

	jQuery(document).ready(function($) {


	    $('.anc_register').click( function(event) {

	        /**
		     * Prevent default action, so when user clicks button he doesn't navigate away from page
		     *
		     */
		    if (event.preventDefault) {
		        event.preventDefault();
		    } else {
		        event.returnValue = false;
		    }

		    $('.ps-login-cont').hide();
		    $('.ps-forgot-password-cont').hide();
			$('.ps-registration-cont').show();



		});

		$('.anc_forgot_password').click( function(event) {

	        /**
		     * Prevent default action, so when user clicks button he doesn't navigate away from page
		     *
		     */
		    if (event.preventDefault) {
		        event.preventDefault();
		    } else {
		        event.returnValue = false;
		    }

		    $('.ps-login-cont').hide();
		    $('.ps-registration-cont').hide();
			$('.ps-forgot-password-cont').show();



		});

		$('.anc_login').click( function(event) {

	        /**
		     * Prevent default action, so when user clicks button he doesn't navigate away from page
		     *
		     */
		    if (event.preventDefault) {
		        event.preventDefault();
		    } else {
		        event.returnValue = false;
		    }

		    $('.ps-login-cont').show();
		    $('.ps-registration-cont').hide();
			$('.ps-forgot-password-cont').hide();



		})

	  /**
	   * When user clicks on button...
	   *
	   */
	  $('#btn-user-login').click( function(event) {

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
	    $('.ps_login_indicator').show();

	    // If for some reason result field is visible hide it
	    $('.login-result-message').hide();

	    // Collect data from inputs
	    var login_nonce = $('#ps_login_user_nonce').val();
	    //var reg_user  = $('#ps_username').val();
	    var login_pass  = $('#ps_user_login_pass').val();
	    var login_user  = $('#ps_user_login').val();
	    //var reg_mobile  = $('#ps_user_mobile').val();
	    //var reg_name  = $('#ps_name').val();
	    //var reg_nick  = $('#ps_nick').val();

	    /**
	     * AJAX URL where to send data
	     * (from localize_script)
	     */
	    var ajax_url = ps_reg_vars.ps_ajax_url;

	    // Data to send
	    data = {
	      action: 'login_user',
	      nonce: login_nonce,
	      //user: reg_user,
	      login_pass: login_pass,
	      login_user: login_user,
	      //mobile:reg_mobile,
	      //name: reg_name,
	      //nick: reg_nick,
	    };

		jQuery.ajax({
			type:   "POST",
			url:    ajax_url,
			data:   data,
			dataType: "json",
			success: function(response){
				//console.log('response');

				//console.log(response);
				//console.log('enter');
	            // Hide 'Please wait' indicator
	            $('.ps_login_indicator').hide();

	            if( response.success) {

	                //if( response.login) {
						if(response.ask_mobile)
						{
							$('.ps-login-cont').hide();
							$('.ps-ask-mobile-cont').show();
							$('#hdn_ask_mobile_user_id').val(response.user_id);

							$('.ask-mobile-result-message').html('Please verify mobile number.'); // Add success message to results div
			                $('.ask-mobile-result-message').addClass('ps-danger');
			                $('.ask-mobile-result-message').removeClass('ps-success'); // Add class success to results div
			                $('.ask-mobile-result-message').show(); // Show results div


						}
						else
						{
							//login only redirect
							location.href=response.url;
						}
	                //}

	            }
	            else
	            {

	                var html="";
	                for(var i=0;i<response.errors.length;i++)
	                {
	                    html+=response.errors[i]+'<br>';
	                }

	                $('.login-result-message').html( html); // If there was an error, display it in results div
	                $('.login-result-message').removeClass('ps-success');
	                $('.login-result-message').addClass('ps-danger'); // Add class failed to results div
	                $('.login-result-message').show(); // Show results div
	            }


			},
			error: function(html){
			}
		});


	  });
	});