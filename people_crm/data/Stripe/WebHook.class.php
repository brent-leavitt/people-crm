<?php

/*
people_crm\data\Stripe\WebHook

WebHook - Data Class for People CRM Plugin
Last Updated 19 Jul 2019

  Description: 


---
*/

namespace people_crm\data\Stripe;

use \people_crm\data\Format as Format;
use \people_crm\core\Action as Action;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'WebHook' ) ){
	class WebHook{

	// PROPERTIES
			
		public $actionable_responses = array(
			'charge_succeeded',
			'charge_refunded',
			'charge_expired',
			'charge_failed',
			'customer_subscription_created',
			'customer_subscription_deleted',
			'customer_subscription_updated',
			'invoice_created',
			'invoice_payment_succeeded',
			'source_failed',
			//'',
			//'',
		);
		
		public $data_set = array(
				'action' => '',
				'patron' => 0,
				'service' => '',
				'token' => '',
				'webhook_data' => array()
			);
		
		
			
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
			
			//$action = "record"; This is set in the data being sent back from the specific event. 
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
			//Remove periods from event name. 
			$eventType = $event->type;		 
			$eventType = str_replace( '.', '_', $eventType );		 
			 
			//https://stripe.com/docs/api/events/types
			//Full list of all possible events to act upon!
			// Handle the event
			
			if( !in_array( $eventType, $this->actionable_responses, true ) )
				return false;
				
			switch( $eventType ) {
				/* case 'charge_succeeded':
						$action = 'receipt';
					break; */
					
			/* 	case 'invoice_created': */
				case 'invoice_payment_succeeded': 
						$action = 'invoice';
					break;
					
	
				// ... handle other event types
				default:
					// Unexpected event type
					
					break;
					
					
					/* http_response_code(400);
					exit(); */
			} 			
			
			$data =  $this->$eventType( $event );
			
			//$format = new Format( 'Stripe', $data ); // contains the data from a Stripe event.
			
			//$format->set_format();
			
			//$action = new Action( $format->out );
			
			
			
			$this->testEventResponse( $data, $eventType ); 
			 
			
			
			
			
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
		Params:  $evt = obj
				 $type = string
	*/	
		
		public function testEventResponse( $evt, $type ){
			
			
			
			$post = json_encode( $evt ); 
			
			$timestamp = time();
			// Gather post data.
			$post_arr = array(
				'post_title'    => $type .' '. $timestamp,
				'post_content'  => 'This post was create at '. $timestamp .' 
				Incoming Data is:'. $post,
				'post_status'   => 'published',
				'post_type' 	=> 'nn_receipt',
				'post_author'   => 1
			);
						
			//wp_insert_post( $post_arr );
			//wp_mail( 'brent@trainingdoulas.com', 'testEventResponse '. $type .' '.$timestamp , $post  );
			
			dump( __CLASS__, __METHOD__, $post );
		}
		
		
	/*
		Name: invoice_payment_succeeded
		Description: This will extract data from the specific event and prepare it for use in the system. 
	*/	
		
		public function invoice_payment_succeeded( $event ){
			
			//What do we need from the payload?
			// - patron_id
			$patron_cus_number = $event->data->object->customer;
			$patron_cus_email = $event->data->object->customer_email;
			
			$patron_id =  get_user_by_meta( 'stripe_customer_id', $patron_cus_number);
			
			if( empty( $patron_id ) ){
				$patron = get_user_by( 'email', $patron_cus_email );
				$patron_id = $patron->ID;
			}
		
			if( empty( $patron_id ) )
				$patron_id =  get_user_by_meta( 'stripe_customer_email', $patron_cus_email );
			
			//Service and Enrollment Tokens
			$metadata = $event->data->object->lines->data[0]->metadata;
			
			$enrollment_token = $metadata->enrollment;
			$service_id = $metadata->service;
			
			//Remainder of the data: 
			$data = $event->data;

			/* dump( __LINE__, __METHOD__, $metadata );
			var_dump( $metadata );
			// - enrollment_token */
			
			//Single out Stripe Cus ID and Email
			
			//Pull out non-empty meta data. 
			
			ECHO "Patron ID: $patron_id";
			echo "\n\r";
			ECHO "SERVICE ID: $servie_id";
			echo "\n\r";
			ECHO "Enrollment TOKEN: $enrollment_token";
				
				
			return $event;			
		}
		
		
		
		
		
			
		
	/*
		Name: get_user_by_meta
		Description:  returns a user ID for a matched key/value meta set. 
	*/	
		
		public function get_user_by_meta( $key, $val ){
			
			$result = get_users( array('meta_key' => $key, 'meta_value' => $val ) );
			
			$patron_id = $result[0]->ID;
			
			return $patron_id ?? NULL; 
			
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