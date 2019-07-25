<?php

if ( ! has_action( 'login_enqueue_scripts', 'wp_print_styles' ) )
    add_action( 'login_enqueue_scripts', 'wp_print_styles', 11 );

add_filter( 'login_headerurl', 'custom_login_header_url' );

function custom_login_header_url($url) {
	
	return 'https://www.trainingdoulas.com/';
}

function nb_custom_styles() {
	$login_css = 'https://www.trainingdoulas.com/wp-content/plugins/doula-course/templates/login-styles.css';
	echo "<link href='//fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700' rel='stylesheet' type='text/css'>
	<link href='$login_css' rel='stylesheet' type='text/css'>";
}

add_action('login_head', 'nb_custom_styles');


function nb_no_login(){
	
	$request_uri = $_SERVER['REQUEST_URI'];
	
	//strings from URLS that don't require logins to access. 
	$no_login = array(
		'wp-login.php','wp-login' //Right now this is empty. 
	);
	
	foreach( $no_login as $val ){
		if( strpos( $request_uri, $val ) )
			return true;
	}
	return false;
}

function nbcs_force_login(){

	if(!is_user_logged_in()){
		if( !nb_no_login() ){
		
			//$script_url = urlencode( $_SERVER['REQUEST_URI'] );
			/*$go_url = get_bloginfo('url').'/wp-login.php';
			wp_redirect( $go_url ); exit; */
			
			 header('Location: /wp-login.php');
			 die();
		
		}
	}
	
	// Force User Login 
	
}

add_action('init','nbcs_force_login');

?>