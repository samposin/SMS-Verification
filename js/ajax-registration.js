

	jQuery(document).ready(function($) {

	  /**
	   * When user clicks on button...
	   *
	   */
	  $('#btn-new-user').click( function(event) {

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
	    $('.ps_indicator').show();

	    // If for some reason result field is visible hide it
	    $('.result-message').hide();

	    // Collect data from inputs
	    var reg_nonce = $('#ps_new_user_nonce').val();
	    //var reg_user  = $('#ps_username').val();
	    var reg_pass  = $('#ps_user_pass').val();
	    var reg_mail  = $('#ps_user_email').val();
	    var reg_mobile  = $('#ps_user_mobile').val();
	    //var reg_name  = $('#ps_name').val();
	    //var reg_nick  = $('#ps_nick').val();

	    /**
	     * AJAX URL where to send data
	     * (from localize_script)
	     */
	    var ajax_url = ps_reg_vars.ps_ajax_url;

	    // Data to send
	    data = {
	      action: 'register_user',
	      nonce: reg_nonce,
	      //user: reg_user,
	      pass: reg_pass,
	      mail: reg_mail,
	      mobile:reg_mobile,
	      //name: reg_name,
	      //nick: reg_nick,
	    };

	    jQuery.ajax({
			type:   "POST",
			url:    ajax_url,
			data:   data,
			dataType: "json",
			success: function(response){
				console.log('response');

				console.log(response);
			      // If we have response
			      if( response ) {

			        // Hide 'Please wait' indicator
			        $('.ps_indicator').hide();

			        if( response.success) {

			            $('.ps-login-cont').show();
						$('.ps-registration-cont').hide();

			            // If user is created
			            $('.login-result-message').html('An OTP is sent to your mobile no. Please use it as password on login page.'); // Add success message to results div
			            $('.login-result-message').removeClass('ps-danger');
			            $('.login-result-message').addClass('ps-success'); // Add class success to results div
			            $('.login-result-message').show(); // Show results div


			            $('.result-message').html(''); // Add success message to results div
			            $('.result-message').removeClass('ps-danger');
			            $('.result-message').removeClass('ps-success'); // Add class success to results div
			            $('.result-message').hide(); // Show results div

			        } else {

			          var html="";
			          for(var i=0;i<response.errors.length;i++)
			          {
			            html+=response.errors[i]+'<br>';
			          }

			          $('.result-message').html( html); // If there was an error, display it in results div
			          $('.result-message').removeClass('ps-success');
			          $('.result-message').addClass('ps-danger'); // Add class failed to results div
			          $('.result-message').show(); // Show results div
			        }
			      }

			},
			error: function(html){
			}
		});

/*
	    // Do AJAX request
	    $.post( ajax_url, data, function(response) {
			console.log('response');

			console.log(response);
	      // If we have response
	      if( response ) {

	        // Hide 'Please wait' indicator
	        $('.ps_indicator').hide();

	        if( response.success) {
	          // If user is created
	          $('.result-message').html('Your submission is complete.'); // Add success message to results div
	          $('.result-message').addClass('alert-success'); // Add class success to results div
	          $('.result-message').show(); // Show results div
	        } else {
	          $('.result-message').html( JSON.stringify(response) ); // If there was an error, display it in results div
	          $('.result-message').addClass('alert-danger'); // Add class failed to results div
	          $('.result-message').show(); // Show results div
	        }
	      }
	    });
*/
	  });
	});