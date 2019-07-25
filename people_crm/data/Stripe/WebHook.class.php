<?php

/*
people_crm\data\Stripe\WebHook

WebHook - Data Class for People CRM Plugin
Last Updated 19 Jul 2019

  Description: 


---
*/

namespace people_crm\data\Stripe;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'WebHook' ) ){
	class WebHook{

	// PROPERTIES
			
			
	// Methods
	
		
	/*
		Name: __construct
		Description: 
	*/	
		
		public function __construct(){
			$this->init();
			
		}
		
		
	/*
		Name: init
		Description: 
	*/	
		
		public function init(){
			
			//$post = @file_get_contents( 'php://input' );
			//$post =  = json_decode( $input );
			
			$payload = @file_get_contents('php://input');
			$event = null;
			
			// $post = json_encode( $_REQUEST );
			require( NN_PATH . 'lib/vendor/autoload.php' );
				
			try {
				$event = \Stripe\Event::constructFrom(
					json_decode($payload, true)
				);
				
				http_response_code(200); 
			} catch( \UnexpectedValueException $e) {
				// Invalid payload
				http_response_code(400);
				exit();
			} 
			 
			 
			// Handle the event
			switch ($event->type) {
				case 'payment_intent.succeeded':
					$paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
					//$this->PaymentIntentSucceeded( $paymentIntent );
					$this->testEventResponse( $paymentIntent );
					break;
				case 'payment_method.attached':
					$paymentMethod = $event->data->object; // contains a \Stripe\PaymentMethod
					//$this->PaymentMethodAttached( $paymentMethod );
					$this->testEventResponse( $paymentMethod );
					break;
				// ... handle other event types
				default:
					// Unexpected event type
					$otherEvent = $event->data->object; // contains a \Stripe\PaymentMethod
					$this->testEventResponse( $otherEvent );
					break;
					
					
					/* http_response_code(400);
					exit(); */
			} 
			 
			 
			/*  
			$timestamp = time();
			// Gather post data.
			$post_arr = array(
				'post_title'    => 'My post'.$timestamp,
				'post_content'  => 'This post was create at '.$timestamp .' Incoming Data is:'. $post,
				'post_status'   => 'draft',
				'post_type' =>'nn_receipt',
				'post_author'   => 1
			);
						
			wp_insert_post( $post_arr ); */
			
			//http_response_code(200); // PHP 5.4 or greater
			
		}
				
	
	/*
		Name: testEventResponse
		Description: 
	*/	
		
		public function testEventResponse( $obj ){
			
			$post = json_encode( $obj ); 
			
			$timestamp = time();
			// Gather post data.
			$post_arr = array(
				'post_title'    => 'My post'.$timestamp,
				'post_content'  => 'This post was create at '.$timestamp .' Incoming Data is:'. $post,
				'post_status'   => 'draft',
				'post_type' =>'nn_receipt',
				'post_author'   => 1
			);
						
			//wp_insert_post( $post_arr );
			
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