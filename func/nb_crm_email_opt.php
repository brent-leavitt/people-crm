<?php

/*
* 	Email Opt  
*   Created on 22 Aug 2018
*/
	
	
	// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

//mailer log error function copied from: https://developer.wordpress.org/reference/functions/wp_mail/

add_action('wp_mail_failed', 'log_mailer_errors', 10, 1);

function log_mailer_errors(){
	$fn = NB_CRM_PATH . '/mail.log'; // say you've got a mail.log file in your server root
	$fp = fopen($fn, 'a');
	fputs($fp, "Mailer Error: " . $mailer->ErrorInfo ."\n");
	fclose($fp);
}

//Set Variables from Form: 
	//Wordpress Nonce
	//
	
	$opt_campaign = 'email-opt_'. $_POST[ 'campaign' ];
	
	if( !wp_verify_nonce( $_POST['nb_nonce'], $opt_campaign ) ){ //Stopped Here. 
		echo $_POST['nb_nonce'];
		echo ',  $opt_campaign: '.$opt_campaign;
		var_dump( $_POST );
		wp_die();	
	}
	
	if( isset( $POST[ 'user_email' ] ) && ( is_email( $POST[ 'user_email' ] ) !== false ) )
		$opt_email = $_POST[ 'user_email' ];
	else	
		nb_crm_go_back();

	$opt_action = $_POST[ 'action' ];
	$opt_return =  $_POST[ 'return' ];



//Search For existing User based on email. 

$user = get_user_by( 'email', $opt_email );


//if not user, add new user. 

if( $user == false ){
	$username = $opt_email;
	
	$password = wp_generate_password();
	
	//$user_return =  wp_create_user( $username, $password, $opt_email );
	
	//Use wp_insert_user();
}
	


//Set Meta Data for Request Campaign. 
/*  
	meta_key 		meta_value
	--------		----------
	email_campaigns	array( 
						(campaign_name) => array( //campaign_name is a unique identifier
											'subscribe_date' => '',
											'status' => '',
											('unsubscribe_date' => '') //optional
										)
					)
	user meta = array(
		'subscribe_date' => '',
		'status' => '',
		'' => '',
		'' => '',
	);

*/



//Send Confirmation of Email List Subscription. 




//Return to Page. 
  


ob_start();
var_dump($_POST);
$debug = ob_get_clean();



//wp_mail( string|array $to, string $subject, string $message, string|array $headers = '', string|array $attachments = array() )

$to 		= //$user_email; //String or array
$to 		= 'brent@trainingdoulas.com'; //String or array
$subject	= 'Test from CRM Email OPT';
$message 	= 'Test Message from CRM Email Opt file';
$message 	.= "<br /><pre>". $debug ."</pre>";  //$debug = var_export($_REQUEST, true);
$headers 	= array(); //string or array.

$headers[]	= 'From: New Beginnings <rachel@trainingdoulas.com>';
$headers[]	= 'Content-Type: text/html; charset=UTF-8';
$headers[]	= 'Bcc: Brent Leavitt <brent@trainingdoulas.com>';
$attach 	= NULL; //string or array.



$mail_sent = wp_mail( $to, $subject, $message, $headers, $attach );

if( $mail_sent ){
	wp_redirect( $_POST['return'] );
	
} else {
	
	nb_crm_go_back();
}

function nb_crm_go_back(){
	
	global $_SERVER;
	
	wp_redirect( $_SERVER['HTTP_REFERER'] );	
	
}



/*
	nb_opt_in_form_func
	
	attrs: 
		- campaign
		- action
		- return

*/

function nb_opt_in_form_func( $atts ){

	$atts = shortcode_atts( array(
		'campaign' => 'newsletter',
		'action' => 'subscribe',
		'return' => 'email-thank-you'
	), $atts, 'nb_opt_in_form' );


	extract( $atts, EXTR_PREFIX_ALL, "nb" );
	
	
	$action_url = "https://ppldev.childbirthlibrary.org/";
	$return_url = get_site_url( NULL, $nb_return, 'https');
	
	$nonce_action = "email-opt_$nb_campaign";
	$nonce_name = "nb_nonce";
	
	$form = "
	<form action='$action_url?email=drive' method='post'>";
	$form .=  wp_nonce_field( $nonce_action, $nonce_name, true, false );

	$form .="	<input type='hidden' name='campaign' value='$nb_campaign' />
		<input type='hidden' name='action' value='$nb_action' />
		<input type='hidden' name='return' value='$return_url' />
		
		<input type='email' name='user_email' value='' placeholder='email@example.com' />
		<input type='submit' name='send_email' value='Send' />

	</form>";
	
	return $form;
	
}

add_shortcode( 'nb_opt_in_form', 'nb_opt_in_form_func' );


?>