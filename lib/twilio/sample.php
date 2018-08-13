<?php
	/*
	require('Twilio/Rest/Client.php');
	require('Twilio/Domain.php');
	require('Twilio/Version.php');
	require('Twilio/InstanceContext.php');
	require('Twilio/Rest/Api/V2010/AccountContext.php');
	require('Twilio/Rest/Api/V2010.php');
	require('Twilio/Rest/Api.php');
	require('Twilio/Http/Client.php');
	require('Twilio/Http/CurlClient.php');
	*/

	require_once("vendor/autoload.php");


	// Send an SMS using Twilio's REST API and PHP
	$sid = "ACbb3783881fad2f3d7a406149db1dc810"; // Your Account SID from www.twilio.com/console
	$token = "fad0b72c02ef32e65369ba6fff73b73e"; // Your Auth Token from www.twilio.com/console

	$client = new Twilio\Rest\Client($sid, $token);

	$message = $client->messages->create(
        '+918871577510', // Text this number
        array(
            'from' => '+19135215662', // From a valid Twilio number
            'body' => 'Hello from Twilio!'
        )
	);

	print $message->sid;