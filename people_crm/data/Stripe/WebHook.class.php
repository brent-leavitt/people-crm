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
		
		
		
		public $test_data = 
'{
  "id": "evt_1FHXlSEY0jlqbLN4xbAKONOC",
  "object": "event",
  "api_version": "2018-08-23",
  "created": 1568215789,
  "data": {
    "object": {
      "id": "in_1FHXlQEY0jlqbLN4ivg1f6a4",
      "object": "invoice",
      "account_country": "US",
      "account_name": "New Beginning Childbirth Services",
      "amount_due": 500,
      "amount_paid": 500,
      "amount_remaining": 0,
      "application_fee": null,
      "attempt_count": 1,
      "attempted": true,
      "auto_advance": false,
      "billing": "charge_automatically",
      "billing_reason": "subscription_update",
      "charge": "ch_1FHXlREY0jlqbLN4RuQLyi6i",
      "closed": true,
      "collection_method": "charge_automatically",
      "created": 1568215788,
      "currency": "usd",
      "custom_fields": null,
      "customer": "cus_Fn81ASGJ2s6h8S",
      "customer_address": null,
      "customer_email": "tu346@trainingdoulas.com",
      "customer_name": null,
      "customer_phone": null,
      "customer_shipping": null,
      "customer_tax_exempt": "none",
      "customer_tax_ids": [

      ],
      "date": 1568215788,
      "default_payment_method": null,
      "default_source": null,
      "default_tax_rates": [

      ],
      "description": "",
      "discount": null,
      "due_date": 1570807789,
      "ending_balance": 0,
      "finalized_at": 1568215789,
      "footer": null,
      "forgiven": false,
      "hosted_invoice_url": "https://pay.stripe.com/invoice/invst_GVNx3jAJ0HnayRUnQD94IkUjkr",
      "invoice_pdf": "https://pay.stripe.com/invoice/invst_GVNx3jAJ0HnayRUnQD94IkUjkr/pdf",
      "lines": {
        "object": "list",
        "data": [
          {
            "id": "sli_443a282390fc05",
            "object": "line_item",
            "amount": 500,
            "currency": "usd",
            "description": "1 subscription × NB Childbirth Library (at $5.00 / month)",
            "discountable": true,
            "livemode": false,
            "metadata": {
              "service": "CBL",
              "enrollment": "library_month"
            },
            "period": {
              "end": 1570807788,
              "start": 1568215788
            },
            "plan": {
              "id": "plan_F6dP7S9MilOACx",
              "object": "plan",
              "active": true,
              "aggregate_usage": null,
              "amount": 500,
              "amount_decimal": "500",
              "billing_scheme": "per_unit",
              "created": 1558415029,
              "currency": "usd",
              "interval": "month",
              "interval_count": 1,
              "livemode": false,
              "metadata": {
              },
              "nickname": "Childbirth Library Monthly Subscription",
              "product": "prod_DXuvzA0BGIwaGm",
              "tiers": null,
              "tiers_mode": null,
              "transform_usage": null,
              "trial_period_days": null,
              "usage_type": "licensed"
            },
            "proration": false,
            "quantity": 1,
            "subscription": "sub_Fn8145Qd9OlsXh",
            "subscription_item": "si_Fn81jAMpTmzuxf",
            "tax_amounts": [

            ],
            "tax_rates": [

            ],
            "type": "subscription"
          }
        ],
        "has_more": false,
        "total_count": 1,
        "url": "/v1/invoices/in_1FHXlQEY0jlqbLN4ivg1f6a4/lines"
      },
      "livemode": false,
      "metadata": {
      },
      "next_payment_attempt": null,
      "number": "57088487-0001",
      "paid": true,
      "payment_intent": "pi_1FHXlREY0jlqbLN479gvo6iX",
      "period_end": 1568215788,
      "period_start": 1568215788,
      "post_payment_credit_notes_amount": 0,
      "pre_payment_credit_notes_amount": 0,
      "receipt_number": null,
      "starting_balance": 0,
      "statement_descriptor": null,
      "status": "paid",
      "status_transitions": {
        "finalized_at": 1568215789,
        "marked_uncollectible_at": null,
        "paid_at": 1568215789,
        "voided_at": null
      },
      "subscription": "sub_Fn8145Qd9OlsXh",
      "subtotal": 500,
      "tax": null,
      "tax_percent": null,
      "total": 500,
      "total_tax_amounts": [

      ],
      "webhooks_delivered_at": null
    }
  },
  "livemode": false,
  "pending_webhooks": 1,
  "request": {
    "id": "req_VVAtgURj15yjMV",
    "idempotency_key": null
  },
  "type": "invoice.created"
}';
			
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
			
			if( !$payload )	die( 'Hmm... Are \'ya lost?' );
			
			$event = null;
			
			// $post = json_encode( $_REQUEST );
			require( NN_PATH . 'lib/vendor/autoload.php' );
				
			try {
				$event = \Stripe\Event::constructFrom(
					//json_decode($payload, true)
					json_decode($this->test_data, true)
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
			
			//Should this happen by default?
			//$format->set_format();
		
			//$action = new Action( $format->out );
			
			//RETURN TO IF STATEMENT WHEN DONE TESTING. 
			
			//We're just using test data right now for faster development. 
			//$this->testEventResponse( $payload, $this->action ); 
			 
			
			
			
			
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
		Name: invoice_payment_succeeded (deprecated)
		Description: This will set  
	*/	
		
		public function invoice_payment_succeeded( $event ){
			
			$result = true;
			
			//Five things needed: 1) Action, 2) Patron, 3) Service, 4)Enrollment, 5)webhook data
			
			$this->data_set[ 'action' ] = 'invoice';
			
			//What do we need from the payload?
			// - patron_id
			$patron_cus_number = $event->data->object->customer;
			$patron_cus_email = $event->data->object->customer_email;
			
			$patron_id =  get_user_id_by_meta( 'stripe_customer_id', $patron_cus_number);
			
			if( empty( $patron_id ) ){
				$patron = get_user_by( 'email', $patron_cus_email );
				$patron_id = $patron->ID;
			}
		
			if( empty( $patron_id ) )
				$patron_id =  get_user_id_by_meta( 'stripe_customer_email', $patron_cus_email );
			
			$this->data_set[ 'patron' ] = $patron_id ?? 0;
			
			
			//Service and Enrollment Tokens
			$metadata = $event->data->object->lines->data[0]->metadata;
			
			$this->data_set[ 'token' ] = $metadata->enrollment;
			$this->data_set[ 'service' ] = $metadata->service;
			
			//Remainder of the data: 
			$this->data_set[ 'data' ]  = $event->data;

			//dump( __LINE__, __METHOD__, $this->data_set );
			
			foreach( $this->data_set as $val ){
				if( empty( $val ) ){
					$result = false;
					break;
				}
			}	
				
			return $result;			
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