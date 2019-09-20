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
			
		public $action = 'record'; //Default is to at least make a record. 
			
		public $actionable_responses = array(
			'charge_succeeded' 				=> 'receipt',
			'charge_refunded' 				=> 'receipt',
			'charge_expired' 				=> 'receipt',
			'charge_failed'					=> 'receipt',
			'customer_subscription_created' => 'enrollment',
			'customer_subscription_deleted' => 'enrollment',
			'customer_subscription_updated' => 'enrollment',
			'invoice_created' 				=> 'invoice',
			'invoice_payment_succeeded' 	=> 'invoice',
			'source_failed' 				=> 'enrollment',
			
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
			
			if( !array_key_exists( $eventType, $this->actionable_responses ) || empty( $event->data ) )
				return false;
				
			//Set primary action
			$this->action = $this->actionable_responses[ $eventType ]; //
			
			$format = new Format( $event->data[ "object" ] , 'Stripe', $this->action ); // contains the data from a Stripe event.
		
			$action = new Action( $format->out );
			
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
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}
		
		
		
		
		
	}
}

?>