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
			
		//public $action = 'record'; //Default is to at least make a record. 
			
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
			
		);
		
		
		public $test_data = '';
			
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
		
		private function init(){
			
			$payload = @file_get_contents('php://input');
			
			if( !$payload )	die( 'Hmm... Are \'ya lost?' );
			
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
			
			if( !in_array( $eventType, $this->actionable_responses ) || empty( $event->data ) )
				return false;
				
			$format = new Format( $event->data[ "object" ] , 'Stripe', 'receipt' ); // contains the data from a Stripe event.

			//Send to Gate Handler
			$handler = new GateHandler( $format->get_out() );	
			
		}
				
				
	/*
		Name: testEventResponse
		Description: 
		Params:  $evt = obj
				 $type = string
	*/	
		
		public function testEventResponse( $evt, $type ){
			
			
			
			//$post = json_encode( $evt ); 
			//$post = $evt;
			
			$timestamp = time();
			// Gather post data.
			
			wp_mail( 'brent@trainingdoulas.com', 'testEventResponse '. $type .' '.$timestamp , $evt );
			
			//dump( __CLASS__, __METHOD__, $post );
		}
		
					
	/*
		Name: send_to_db
		Description: 
	*/	
		
		public function send_to_db( $event ){
			global $wpdb;
			
			
			$id = $event->id;
			$timestamp = date( 'Y-m-d H:i:s', $event->created );
			$table = $wpdb->prefix."webhooks";
			$query = "SELECT * FROM {$table} WHERE event_id = '{$id}'";
			
			//if event has already been sent and stored: 
			if( $wpdb->get_results( $query, OBJECT ) ) return;
			
			$data = [
				'event_id' => $id,
				'timestamp' => $timestamp,
				'data' => json_encode( $event ),
			];
			
			$wpdb->insert( $table, $data );
			
			dump( __CLASS__, __METHOD__, $wpdb );
			
			//NO INSERT ID being sent, because the database doesn't have an ID row. 
			return $wpdb->insert_id;
			
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