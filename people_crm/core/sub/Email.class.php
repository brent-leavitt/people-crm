<?php 


/* 

people_crm\core\sub\Email

Email - Sub Core Class for People_CRM Plugin
Last Updated 3 Oct 2018
-------------

Description: Dependent upon the Notice Core Class, This houses actions that are specific to email messages only. 


---
	$data = array(
				'user_id' => 0,
				'address' => '',
				'from' => '',
				'subject' => '',
				'message' => '',
				'html' => (bool),
				'headers' => []
			);
			
	usage: 
		$email = new Email( $data );
		
		if( !$email->error )
			$result[] = $email->send();
		
		
		//set_user
		//set_message
		//set_address
		//
*/


namespace people_crm\core\sub;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'Email' ) ){

	class Email{
		
		//Properties
			
		public 
			$user_id = 0,
			$address = '',
			$from = '',
			$headers = [], 
			$subject = '',
			$message = '',
			$html = true,
			$error = false;
			
		//Methods
		
		
			
	/*
		Name: __construct
		Description: 
	*/	
				
		
		public function __construct( $data ){
			
			$this->init( $data );
		}	
				
		
	/*
		Name: init
		Description: 
	*/	
				
		
		public function init( $data ){
			
			$this->set_data( $data );
			
			//Maybe a data validation?
			//$this->validate_data();
			
		}	


		
	/*
		Name: set_data
		Description: 
	*/	
				
		
		public function set_data( $data ){
			
			foreach( $data as $key => $val ){
				
				if( !empty( $val ) )
					$this->$key = $val;
				
			}
			
			//set email address. 
			
			if( !empty( $this->user_id ) ){
				
				$user = get_userdata( $this->user_id );
				$this->address = $user->user_email;
				
			} 
			
			if( empty( $this->address ) )
				$this->error = true; //Can't send an email if address is not set. 
			
			
		}	
		
		
	/*
		Name: send 
		Description: Finally, this sends the email using WP_MAIL functionality, which allows a bunch of more stuff to happen behind the scenes. 
	*/	
				
		
		public function send(){
			
			return wp_mail( $this->address, $this->subject, $this->message, $this->headers );
			
		}	
		
		
	/*
		Name: validate_data
		Description: NEEDED? Not sure. 
	*/	
				
		
		public function validate_data(){
			
			
		}	
		
		
	/*
		Name: 
		Description: 
	*/	
				
		
		public function __(){
			
			
		}	
		
	}
}
?>