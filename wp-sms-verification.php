<?php
/*
Plugin Name: SMS Verification
Plugin URI: http://www.test.com
Description: Now user can use mobile number as their username with WordPress. SMS verification is using Twilio. Making "Forgot password" functionality easy, OTP over Mobile will be the new Password
Version: 0.1 BETA
Author: Santosh Carpenter
Author URI: https://github.com/samposin
*/
?>
<?php //session_start();
	//error_reporting(E_ALL);
	//ini_set('display_errors','1');
	add_shortcode("sms-verification", "sms_verification_handler");
	function sms_verification_handler() {
  		//run function that actually does the work of the plugin
  		$sms_verification_output = sms_verification_output();
  		//send back text to replace shortcode in post
  		return $sms_verification_output;
	}

/*
	add_filter('manage_users_columns', 'pippin_add_user_id_column');
	function pippin_add_user_id_column($columns) {
	    $columns['user_mobile'] = 'User Mobile';
	    return $columns;
	}

	add_action('manage_users_custom_column',  'pippin_show_user_id_column_content', 10, 3);
	function pippin_show_user_id_column_content($value, $column_name, $user_id) {
	    $user = get_userdata( $user_id );
		if ( 'user_mobile' == $column_name )
			return $user_id;
	    return $value;
	}
*/

	$ps_plugin_abs_path=trailingslashit( str_replace("\\","/", WP_PLUGIN_DIR . '/' . plugin_basename( dirname(__FILE__) ) ) );

	$ps_plugin_url_path=trailingslashit( plugins_url( '', __FILE__ ) );

	require_once($ps_plugin_abs_path."/lib/twilio/vendor/autoload.php");


	// Send an SMS using Twilio's REST API and PHP
	$twilio_sid = ""; // Your Account SID from www.twilio.com/console
	$twilio_token = ""; // Your Auth Token from www.twilio.com/console
	$twilio_from_no="";  // Your from number from twilio

	function sms_verification_output()
	{
		global $wpdb,$siteurl,$ps_plugin_abs_path,$ps_plugin_url_path;
		// Enables Wordpress's DB Error reporting
		$wpdb->show_errors = true;

		$page_url = get_permalink();

		$page_slug = basename(get_permalink());

		$pagename = get_query_var('pagename');

		$rootPath=$ps_plugin_abs_path;
		$url=$ps_plugin_url_path;
		$blog_url=get_site_url();
		$url_page=$blog_url.'/page-name';

		ob_start();
		$html='';

		ps_registration_form();
		ps_login_form();
		ps_ask_mobile_form();
		ps_forgot_password_form();
		ps_ask_email_form();
		$html.= ob_get_clean();

		return $html;
	}

	function ps_register_user_scripts() {

		global $wpdb,$siteurl,$ps_plugin_abs_path,$ps_plugin_url_path;

	    // Enqueue script
	    wp_register_script('ps_reg_script', $ps_plugin_url_path . '/js/ajax-registration.js', array('jquery'), null, false);
	    wp_enqueue_script('ps_reg_script');
	    wp_register_script('ps_login_script', $ps_plugin_url_path . '/js/ajax-login.js', array('jquery'), null, false);
	    wp_enqueue_script('ps_login_script');
	    wp_register_script('ps_ask_mobile_script', $ps_plugin_url_path . '/js/ajax-ask-mobile.js', array('jquery'), null, false);
	    wp_enqueue_script('ps_ask_mobile_script');
	    wp_register_script('ps_forgot_password_script', $ps_plugin_url_path . '/js/ajax-forgot-password.js', array('jquery'), null, false);
	    wp_enqueue_script('ps_forgot_password_script');
	    wp_register_script('ps_ask_email_script', $ps_plugin_url_path . '/js/ajax-ask-email.js', array('jquery'), null, false);
	    wp_enqueue_script('ps_ask_email_script');

	    wp_localize_script( 'ps_reg_script', 'ps_reg_vars', array(
	            'ps_ajax_url' => admin_url( 'admin-ajax.php' ),
	        )
	    );
	}
	add_action('wp_enqueue_scripts', 'ps_register_user_scripts', 100);

	// Register the style like this for a plugin:
	wp_register_style( 'ps_reg_css', plugins_url( 'css/style.css', __FILE__ ), array(), '1', 'all' );

    // For either a plugin or a theme, you can then enqueue the style:
    wp_enqueue_style( 'ps_reg_css' );


	function ps_registration_form() { ?>

		<div class="ps-registration-cont ps-form-cont">
			<form class="form-horizontal registraion-form" role="form">
				<p>
					<label for="user_mobile" class="sr-only">Your Mobile</label>
					<input type="text" name="user_mobile" id="ps_user_mobile" value="" placeholder="+919876543210" class="form-control"/>
				</p>
				<p>
					<label for="user_email" class="sr-only">Your Email</label>
					<input type="email" name="user_email" id="ps_user_email" value="" placeholder="Your Email" class="form-control"/>
				</p>

				<?php wp_nonce_field('ps_new_user', 'ps_new_user_nonce', true, true); ?>

				<p class="ps_indicator">Please wait...</p>
				<p class="result-message"></p>
				<p>
				<input type="submit" class="btn btn-primary" id="btn-new-user" value="Register"/>
				</p>
				<p>
					<a href="javascript:void(0);" class="anc_login">Login</a> | <a href="javascript:void(0);" class="anc_forgot_password">Lost your password</a>
				</p>
			</form>


		</div>

		<?php
		}

	function ps_login_form() { ?>

		<div class="ps-login-cont ps-form-cont" style="display:block;">
			<form name="loginform" id="loginform" action="" method="post">
				<p>
					<label for="ps_user_login">Mobile or Email Address<br>
						<input type="text" name="log" id="ps_user_login" aria-describedby="login_error" class="input" value="" size="20"></label>
				</p>
				<p>
					<label for="ps_user_login_pass">Password<br>
						<input type="password" name="pwd" id="ps_user_login_pass" aria-describedby="login_error" class="input" value="" size="20"></label>
				</p>

				<!--<p class="forgetmenot">
					<label for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever"> Remember Me</label>
				</p>-->

				<p class="ps_login_indicator">Please wait...</p>
				<p class="login-result-message"></p>

				<p class="submit">
					<input type="submit" name="wp-submit" id="btn-user-login" class="button button-primary button-large" value="Log In">
					<input type="hidden" name="redirect_to" value="">
					<input type="hidden" name="testcookie" value="1">
					<?php wp_nonce_field('ps_login_user', 'ps_login_user_nonce', true, true); ?>
				</p>
				<p>
					<a href="javascript:void(0);" class="anc_register">Register</a> | <a href="javascript:void(0);" class="anc_forgot_password">Lost your password</a>
				</p>
			</form>
		</div>

		<?php
	}

	function ps_ask_mobile_form() { ?>

		<div class="ps-ask-mobile-cont ps-form-cont">
			<form name="askmobileform" id="askmobileform" action="" method="post">
				<p>
					<label for="ps_ask_mobile">Mobile No<br>
						<input type="text" name="mobile" id="ps_ask_mobile" aria-describedby="login_error" class="input" value="" size="20"></label>
				</p>

				<p class="ps_ask_mobile_indicator">Please wait...</p>
				<p class="ask-mobile-result-message"></p>

				<p class="submit">
					<input type="submit" name="wp-submit" id="btn-ask-mobile" class="button button-primary button-large" value="Verify">
					<input type="hidden" name="redirect_to" value="">
					<input type="hidden" name="testcookie" value="1">
					<input type="hidden" name="hdn_ask_mobile_user_id" id="hdn_ask_mobile_user_id" value="">

					<?php wp_nonce_field('ps_ask_mobile', 'ps_ask_mobile_nonce', true, true); ?>
				</p>

			</form>
		</div>

		<?php
	}

	function ps_forgot_password_form() { ?>

		<div class="ps-forgot-password-cont ps-form-cont">
			<form name="forgotpasswordform" id="forgotpasswordform" action="" method="post">
				<p>
					<label for="ps_ask_mobile">Mobile No<br>
						<input type="text" name="mobile" id="ps_forgot_password_mobile" aria-describedby="login_error" class="input" value="" size="20"></label>
				</p>

				<p class="ps_forgot_password_indicator">Please wait...</p>
				<p class="forgot-password-result-message"></p>

				<p class="submit">
					<input type="submit" name="wp-submit" id="btn-forgot-password" class="button button-primary button-large" value="Submit">
					<input type="hidden" name="redirect_to" value="">
					<input type="hidden" name="testcookie" value="1">
					<input type="hidden" name="hdn_forgot_password_mobile_user_id" id="hdn_forgot_password_mobile_user_id" value="">

					<?php wp_nonce_field('ps_forgot_password', 'ps_forgot_password_nonce', true, true); ?>
				</p>
				<p>
					<a href="javascript:void(0);" class="anc_login">Login</a> | <a href="javascript:void(0);" class="anc_register">Register</a>
				</p>
			</form>
		</div>

		<?php
	}

	function ps_ask_email_form() { ?>

		<div class="ps-ask-email-cont ps-form-cont">
			<form name="askemailform" id="askemailform" action="" method="post">
				<p>
					<label for="ps_ask_mobile">Email<br>
						<input type="text" name="log" id="ps_ask_email" aria-describedby="login_error" class="input" value="" size="20"></label>
				</p>

				<p class="ps_ask_email_indicator">Please wait...</p>
				<p class="ask-email-result-message"></p>

				<p class="submit">
					<input type="submit" name="wp-submit" id="btn-ask-email" class="button button-primary button-large" value="Submit">
					<input type="hidden" name="redirect_to" value="">
					<input type="hidden" name="testcookie" value="1">
					<input type="hidden" name="hdn_ask_email_mobile" id="hdn_ask_email_mobile" value="">

					<?php wp_nonce_field('ps_ask_email', 'ps_ask_email_nonce', true, true); ?>
				</p>
			</form>
		</div>

		<?php
	}





	function registration_validation( $email, $mobile)
	{
		global $reg_errors,$wpdb;
		$reg_errors = new WP_Error;




		$checkMeta=$wpdb->get_results("SELECT user_id FROM $wpdb->usermeta WHERE meta_key='mobile' AND meta_value=".$mobile,ARRAY_A);
		//echo '<pre>';
		//print_r($checkMeta);

		if ( empty( $mobile )){
			$reg_errors->add( 'mobile', 'Please provide mobile no.' );
		}
		else if(count($checkMeta)>0)
		{
			$reg_errors->add( 'mobile', 'Mobile already in use.' );
		}

/*
		if($wpdb->get_var($wpdb->prepare("SELECT COUNT(`meta_value`) FROM $wpdb->postmeta WHERE `meta_value`=%s", $mobile)) == 0) {
			// Dont exists,.. Add a new post meta for example
		    echo 'not exists';
		}
		else
		{
			 echo 'exists';
		}
*/

		if ( empty( $email )){
			$reg_errors->add( 'email', 'Please provide email.' );
		}
		else if ( !is_email( $email ) ) {
		    $reg_errors->add( 'email', 'Please provide valid email.' );
		}
		else if ( email_exists( $email ) ) {
		    $reg_errors->add( 'email', 'Email already in use.' );
		}

		if ( is_wp_error( $reg_errors ) ) {

		    foreach ( $reg_errors->get_error_messages() as $error ) {

		        //echo '<div>';
		       // echo '<strong>ERROR</strong>:';
		        //echo $error . '<br/>';
		       // echo '</div>';

		    }

		}
	}


	/**
	 * New User registration
	 *
	 */
	function ps_reg_new_user() {

		global $reg_errors,$twilio_sid,$twilio_token,$twilio_from_no;

		$result=array('success'=>false,'msg'=>'');

	    // Verify nonce
	    //if( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'ps_new_user' ) )
	        //die( 'Ooops, something went wrong, please try again later.' );

		$code = rand( 1000, 9999 );

	    // Post values
	    //$username = $_POST['user'];
	    $username = $_POST['mail'];
	    //$password = $_POST['pass'];
	    $password = $code;
	    $email    = $_POST['mail'];
	    $mobile    = $_POST['mobile'];
	    //$name     = $_POST['name'];
	    //$nick     = $_POST['nick'];

	    registration_validation($email, $mobile);

	    /**
	     * IMPORTANT: You should make server side validation here!
	     *
	     */

		if ( 1 > count( $reg_errors->get_error_messages() ) )
		{

			try {

				$client = new Twilio\Rest\Client($twilio_sid, $twilio_token);

				$message = $client->messages->create(
			        //'+918871577510', // Text this number
			        $mobile, // Text this number
			        array(
			            'from' => $twilio_from_no, // From a valid Twilio number
			            'body' => 'Password is '.$code
			        )
				);

				$userdata = array(
					'user_email' => $email,
					'user_login' => $username,
					'user_pass' => $password,
				);

				$user_id = wp_insert_user($userdata);

				// Return
			    if( !is_wp_error($user_id) ) {
			        update_user_meta( $user_id, 'mobile', $mobile );
			        $result['success']=true;
			    }
			    else
			    {
			        foreach ( $user_id->get_error_messages() as $error ) {

		                $result['errors'][]=$error;
		            }
			    }
			}
			//catch exception
			catch(Exception $e) {
			    //echo 'Message: ' .$e->getMessage();
			    if($e->getMessage()=="[HTTP 400] Unable to create record: The 'To' number  is not a valid phone number.")
		        {
		            $result['errors'][] = "Please provide valid phone number with country code.";
		        }
		        else
		        {
			        $result['errors'][] = $e->getMessage();
		        }
			}
		}
		else
		{
			foreach ( $reg_errors->get_error_messages() as $error ) {

		        $result['errors'][]=$error;

		    }
		}

	    header('Content-type: application/json');
        echo json_encode($result);
		die();

	}

	add_action('wp_ajax_register_user', 'ps_reg_new_user');
	add_action('wp_ajax_nopriv_register_user', 'ps_reg_new_user');


	function ps_login_user()
	{

		//wp_mail('sam1.posin@gmail.com', "Mobile no attached to your account", "Message");

		global $wpdb;

		$result=array('success'=>false,'msg'=>'');

		//We shall SQL escape all inputs
	    $login_user = $_POST['login_user'];
	    $login_pass = $_POST['login_pass'];
	    //$remember = $wpdb->escape($_REQUEST['rememberme']);
	    $remember=true;
	    if($remember) $remember = "true";
        else $remember = "false";

		//echo '<pre>';
		$u1=get_users(
	        array(
	            'meta_key' => 'mobile',
	            //'meta_value' => '+918871577510',
	            'meta_value' => $login_user,
	            'number' => 1,
	            'count_total' => false
	        )
	    );

	    //print_r($u1);

		if(count($u1)>0)
		{
			$u=$u1[0];
			if ($u)
			{
				if (wp_check_password($login_pass, $u->user_pass, $u->ID))
				{
					// Success. User login credentials matched. Login user with `wp_set_auth_cookie`.
					wp_clear_auth_cookie();
					wp_set_current_user($u->ID);
					wp_set_auth_cookie($u->ID);

					/*
					//$redirect_to = user_admin_url();
					//wp_safe_redirect($redirect_to);
					//exit();
					*/

					$result['success']=true;
					$result['login']=true;
					$result['ask_mobile']=false;
					$result['url']=get_site_url();

				}
				else
				{
					// Throw Error. Password Does not Match.
					$result['success']=false;
					$result['login']=false;
					$result['ask_mobile']=false;
					$result['errors'][]="Invalid credentials.";
				}
			}
			else
			{
				// Throw Error. User not found with phone no.
			}
		}
		else
		{
			$login_data = array();
		    $login_data['user_login'] = $login_user;
		    $login_data['user_password'] = $login_pass;
		    $login_data['remember'] = $remember;

		    //$user_verify = wp_signon( $login_data, false );
		    $user_verify=wp_authenticate( $login_user,$login_pass);

		    //print_r($user_verify);

		    if ( is_wp_error($user_verify) )
		    {
		        //echo "Invalid login details";
		        $result['success']=false;
				$result['login']=false;
				$result['ask_mobile']=false;
				$result['errors'][]="Invalid credentials.";
		       // Note, I have created a page called "Error" that is a child of the login page to handle errors. This can be anything, but it seemed a good way to me to handle errors.
		    }
		    else
		    {
		        $user_mobile=get_user_meta( $user_verify->ID, 'mobile', true );

			    if($user_mobile)
			    {
			        // Success. User login credentials matched. Login user with `wp_set_auth_cookie`.
					wp_clear_auth_cookie();
					wp_set_current_user($user_verify->ID);
					wp_set_auth_cookie($user_verify->ID);

				    $result['success']=true;
					$result['login']=true;
					$result['ask_mobile']=false;
					$result['user_id']=11; //temporary
					$result['url']=get_site_url();
			    }
			    else
			    {
				    $result['success']=true;
					$result['login']=false;
					$result['ask_mobile']=true;
					$result['user_id']=$user_verify->ID;

			    }
		    }
		}

		header('Content-type: application/json');
        echo json_encode($result);
		die();
	}

	add_action('wp_ajax_login_user', 'ps_login_user');
	add_action('wp_ajax_nopriv_login_user', 'ps_login_user');

	//$message="Hello ".$u->display_name."\r\n";
			        //$message="Mobile no: ".$mobile." attached to your account."."\r\n";



	function ps_ask_mobile()
	{

		global $wpdb,$twilio_sid,$twilio_token,$reg_errors,$twilio_from_no;
		$reg_errors = new WP_Error;

		$result=array('success'=>false,'msg'=>'');

		//We shall SQL escape all inputs
	    $mobile = $_POST['mobile'];
	    $login_user_id=$_POST['user_id'];

		//echo 'hello';
	    $u=get_user_by('ID',$login_user_id);

		//echo '<pre>';
	    //print_r($u);
	    //die();

	    $code = rand( 1000, 9999 );

	    if ( empty( $mobile )){
			//$reg_errors->add( 'mobile', 'Please provide mobile no.' );

			 $result['errors'][]="Please provide mobile no.";
		}
		else
		{

		    if($u)
		    {
		        try {

					$client = new Twilio\Rest\Client($twilio_sid, $twilio_token);

					$message = $client->messages->create(
				        //'+918871577510', // Text this number
				        $mobile, // Text this number
				        array(
				            'from' => $twilio_from_no, // From a valid Twilio number
				            'body' => 'Password is '.$code
				        )
					);

					wp_set_password( $code, $u->ID );
			        update_user_meta( $u->ID, 'mobile', $mobile );

	//echo '<pre>';
	//print_r($u);
			        $message="Hello ".$u->display_name."\r\n";
			        $message.="Mobile no: ".$mobile." attached to your account."."\r\n";

					//echo $message;
			        wp_mail($u->user_email, "Mobile no attached to your account", $message);

			        $result['success']=true;

				}
				//catch exception
				catch(Exception $e) {
				    //echo 'Message: ' .$e->getMessage();
				    if($e->getMessage()=="[HTTP 400] Unable to create record: The 'To' number  is not a valid phone number.")
			        {
			            $result['errors'][] = "Please provide valid phone number with country code.";
			        }
			        else
			        {
				        $result['errors'][] = $e->getMessage();
			        }
				}
		    }
		    else
		    {
		        $result['errors'][]="User not found. Please login again.";
		    }
	    }



		header('Content-type: application/json');
        echo json_encode($result);
		die();
	}

	add_action('wp_ajax_ask_mobile', 'ps_ask_mobile');
	add_action('wp_ajax_nopriv_ask_mobile', 'ps_ask_mobile');

	function ps_forgot_password()
	{
		global $wpdb,$twilio_sid,$twilio_token,$reg_errors,$twilio_from_no;
		$reg_errors = new WP_Error;

		$result=array('success'=>false,'msg'=>'');

		//We shall SQL escape all inputs
	    $mobile = $_POST['mobile'];
	    $code = rand( 1000, 9999 );

	    if ( empty( $mobile )){
			 $result['errors'][]="Please provide mobile no.";
		}
		else
		{
			//echo '<pre>';
			$u1=get_users(
		        array(
		            'meta_key' => 'mobile',
		            //'meta_value' => '+918871577510',
		            'meta_value' => $mobile,
		            'number' => 1,
		            'count_total' => false
		        )
		    );


			if(count($u1)>0)
			{
				$u = $u1[0];
				if ($u)
				{
					try {

						$client = new Twilio\Rest\Client($twilio_sid, $twilio_token);

						$message = $client->messages->create(
				            //'+918871577510', // Text this number
				            $mobile, // Text this number
				            array(
				                'from' => $twilio_from_no, // From a valid Twilio number
				                'body' => 'Password is '.$code
				            )
						);

						wp_set_password( $code, $u->ID );
				        update_user_meta( $u->ID, 'mobile', $mobile );

				        $result['success']=true;
						$result['ask_email']=false;
						//$result['mobile']=$mobile; //temporary

					}
					//catch exception
					catch(Exception $e) {
				        //echo 'Message: ' .$e->getMessage();
				        if($e->getMessage()=="[HTTP 400] Unable to create record: The 'To' number  is not a valid phone number.")
				        {
				            $result['errors'][] = "Please provide valid phone number with country code.";
				        }
				        else
				        {
					        $result['errors'][] = $e->getMessage();
				        }
					}
				}
			}
			else
			{
				$result['success']=true;
				$result['ask_email']=true;
				$result['mobile']=$mobile;
			}
		}

		header('Content-type: application/json');
        echo json_encode($result);
		die();
	}

	add_action('wp_ajax_ps_forgot_password', 'ps_forgot_password');
	add_action('wp_ajax_nopriv_ps_forgot_password', 'ps_forgot_password');


	function ps_ask_email()
	{
		global $wpdb,$twilio_sid,$twilio_token,$twilio_from_no;

		$result=array('success'=>false,'msg'=>'');

		//We shall SQL escape all inputs
	    $mobile = $_POST['mobile'];
	    $email=$_POST['email'];

	    $code = rand( 1000, 9999 );

	    if ( empty( $mobile )){
			 $result['errors'][]="Please try again.";
		}
		else if ( empty( $email )){
			 $result['errors'][]="Please provide email.";
		}
		else
		{
			$u=get_user_by('email',$email);

			if($u)
			{

				try {

					$client = new Twilio\Rest\Client($twilio_sid, $twilio_token);

					$message = $client->messages->create(
			            //'+918871577510', // Text this number
			            $mobile, // Text this number
			            //'918871577510',
			            array(
			                'from' => $twilio_from_no, // From a valid Twilio number
			                'body' => 'Password is '.$code
			            )
					);

					wp_set_password( $code, $u->ID );
			        update_user_meta( $u->ID, 'mobile', $mobile );

			        $result['success']=true;

				}
				//catch exception
				catch(Exception $e) {
			        //echo 'Message: ' .$e->getMessage();
			        //echo $e->getMessage();
			        if($e->getMessage()=="[HTTP 400] Unable to create record: The 'To' number  is not a valid phone number.")
			        {
			            $result['errors'][] = "Please provide valid phone number with country code.";
			        }
			        else
			        {
				        $result['errors'][] = $e->getMessage();
			        }
				}

			}
			else
			{
				 $result['errors'][]="Email not found. Please provide valid email.";
			}
			//echo '<pre>';
			//print_r($u);
		}

		header('Content-type: application/json');
        echo json_encode($result);
		die();

	}


	add_action('wp_ajax_ps_ask_email', 'ps_ask_email');
	add_action('wp_ajax_nopriv_ps_ask_email', 'ps_ask_email');
?>
