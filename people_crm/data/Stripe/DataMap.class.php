<?php 

/* 
people_crm\data\Stripe\DataMap
DataMap Class for NBCS Network Plugin
Last Updated 19 Dec 2018
-------------

Description: 


Dev Notes: 

you need a data map for each type of object that comes out of stripe. For example the invoice object, the subscription obj, the plan object, the source object, and the customer obj. Because the data will come differently from different objects, each needs to be mapped. 
		
	Types of Main Objects: 
		- charge
		- customer
		- invoice
		- source
		
	Additional Objects: 
		- subscription
		- plan
		- subscription_item
		- card
		- list
		
		- ?
		
*/
	
namespace people_crm\data\Stripe;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'DataMap' ) ){
	class DataMap{

	// PROPERTIES
		public $in; 			//This is the source JSON string of info.
		public $data = [];		//This is the converted array of incoming JSON data. 
		
		
		private $objects_arr = [
			'charge',
			'customer',
			'invoice',
			'source',
			'subscription',
			'subscription_item',
			'plan',
			'card',
		
		];
		
		//https://stripe.com/docs/api/charges/object
		private $charge_data_map = [ //'nn_value' => '3rd_party_value'
			'create_date' => 'created',
			'trans_type' => 'object',
			'trans_status' => 'status',
			'trans_descrip' => 'description',
			'currency' => 'currency',
/* 			'subtotal' => '',		 		//Subtotal before taxes
			'discount' => '',		 		//Discount on Subtotal
			'sales_tax' => '',		 		//Sales Tax */
			'gross_amount' => 'amount', 			//Transaction Gross Amount
/* 			'trans_fee' => '',  	 		//Transaction Fee
			'net_amount' => '',		 		//Amount Collected After Fees */
			'tp_id' => 'id', 		
			'full_name' => 'billing_details_name',			//
			'address' => 'billing_details_address_line1',	//	
			'address1' => 'billing_details_address_line2',	//	
			'city' => 'billing_details_address_city',		//	
			'state' => 'billing_details_address_state',		//	
			'zip' => 'billing_details_address_zip',			//	
			'country' => 'billing_details_country',			//	
			'email' => 'billing_details_email',				//	
			'phone' => 'billing_details_phone',			//	
			'on_behalf_of' => 'on_behalf_of',	
			'stripe_customer_id' => 'customer',
			
			//email_address 	
			
		
		];

		
		
		//https://stripe.com/docs/api/customers/object
		private $customer_data_map = [ //'nn_value' => '3rd_party_value'
			'create_date' 	=> 'created',
			'trans_type' 	=> 'object',
			'full_name' 	=> 'name',			//
			'address' 		=> 'address_line1',	//	
			'address1' 		=> 'address_line2',	//	
			'city' 			=> 'address_city',		//	
			'state'			=> 'address_state',		//	
			'zip' 			=> 'address_postal_code',			//	
			'country' 		=> 'address_country',			//	
			'email' 		=> 'email',				//	
			'phone' 		=> 'phone',			//	
			'' => '',
			
		
		];

		
		//INCOMPLETE 
		
		//https://stripe.com/docs/api/invoices
		private $invoice_data_map = [ //'nn_value' => '3rd_party_value'
			'create_date' => 'created',
			'trans_type' => 'object',
			'trans_status' => 'status',
			'trans_descrip' => 'description',
			'currency' => 'currency',
/* 			'subtotal' => '',		 		//Subtotal before taxes
			'discount' => '',		 		//Discount on Subtotal
			'sales_tax' => '',		 		//Sales Tax */
			'gross_amount' => 'amount', 			//Transaction Gross Amount
/* 			'trans_fee' => '',  	 		//Transaction Fee
			'net_amount' => '',		 		//Amount Collected After Fees */
			'tp_id' => 'id', 		
			'full_name' => 'source_name',			//
			'address' => 'source_address_line1',	//	
			'address1' => 'source_address_line2',	//	
			'city' => 'source_address_city',		//	
			'state' => 'source_address_state',		//	
			'zip' => 'source_address_zip',			//	
			'country' => 'source_country',			//	
			'email' => 'receipt_email',				//	
			'phone' => 'receipt_number',			//	
			'cc_type' => 'source_brand',			//paypal, visa, mastercard, etc. 
			'cc_card' => 'source_last4',			//last4 of 
			'cc_exp_month' => 'source_exp_month',	//expiration date. 
			'cc_exp_year' => 'source_exp_year',		//expiration date. 
			'on_behalf_of' => 'on_behalf_of',		//email_address 	
			
		
		];

		
		//INCOMPLETE
		
		//https://stripe.com/docs/api/subscription/object
		private $subscription_data_map = [ //'nn_value' => '3rd_party_value'
			'create_date' => 'created',
			'trans_type' => 'object',
			'trans_status' => 'status',
			'trans_descrip' => 'description',
			'currency' => 'currency',
/* 			'subtotal' => '',		 		//Subtotal before taxes
			'discount' => '',		 		//Discount on Subtotal
			'sales_tax' => '',		 		//Sales Tax */
			'gross_amount' => 'amount', 			//Transaction Gross Amount
/* 			'trans_fee' => '',  	 		//Transaction Fee
			'net_amount' => '',		 		//Amount Collected After Fees */
			'tp_id' => 'id', 		
			'full_name' => 'source_name',			//
			'address' => 'source_address_line1',	//	
			'address1' => 'source_address_line2',	//	
			'city' => 'source_address_city',		//	
			'state' => 'source_address_state',		//	
			'zip' => 'source_address_zip',			//	
			'country' => 'source_country',			//	
			'email' => 'receipt_email',				//	
			'phone' => 'receipt_number',			//	
			'cc_type' => 'source_brand',			//paypal, visa, mastercard, etc. 
			'cc_card' => 'source_last4',			//last4 of 
			'cc_exp_month' => 'source_exp_month',	//expiration date. 
			'cc_exp_year' => 'source_exp_year',		//expiration date. 
			'on_behalf_of' => 'on_behalf_of',		//email_address 	
			
		
		];
		

//INCOMPLETE
		
		//https://stripe.com/docs/api/subscription_item/object
		private $subscription_item_data_map = [ //'nn_value' => '3rd_party_value'
			'create_date' => 'created',
			'trans_type' => 'object',
			'trans_status' => 'status',
			'trans_descrip' => 'description',
			'currency' => 'currency',
/* 			'subtotal' => '',		 		//Subtotal before taxes
			'discount' => '',		 		//Discount on Subtotal
			'sales_tax' => '',		 		//Sales Tax */
			'gross_amount' => 'amount', 			//Transaction Gross Amount
/* 			'trans_fee' => '',  	 		//Transaction Fee
			'net_amount' => '',		 		//Amount Collected After Fees */
			'tp_id' => 'id', 		
			'full_name' => 'source_name',			//
			'address' => 'source_address_line1',	//	
			'address1' => 'source_address_line2',	//	
			'city' => 'source_address_city',		//	
			'state' => 'source_address_state',		//	
			'zip' => 'source_address_zip',			//	
			'country' => 'source_country',			//	
			'email' => 'receipt_email',				//	
			'phone' => 'receipt_number',			//	
			'cc_type' => 'source_brand',			//paypal, visa, mastercard, etc. 
			'cc_card' => 'source_last4',			//last4 of 
			'cc_exp_month' => 'source_exp_month',	//expiration date. 
			'cc_exp_year' => 'source_exp_year',		//expiration date. 
			'on_behalf_of' => 'on_behalf_of',		//email_address 	
			
		
		];


//INCOMPLETE		
		
		//https://stripe.com/docs/api/plan/object
		private $plan_data_map = [ //'nn_value' => '3rd_party_value'
			'create_date' => 'created',
			'trans_type' => 'object',
			'trans_status' => 'status',
			'trans_descrip' => 'description',
			'currency' => 'currency',
/* 			'subtotal' => '',		 		//Subtotal before taxes
			'discount' => '',		 		//Discount on Subtotal
			'sales_tax' => '',		 		//Sales Tax */
			'gross_amount' => 'amount', 			//Transaction Gross Amount
/* 			'trans_fee' => '',  	 		//Transaction Fee
			'net_amount' => '',		 		//Amount Collected After Fees */
			'tp_id' => 'id', 		
			'full_name' => 'source_name',			//
			'address' => 'source_address_line1',	//	
			'address1' => 'source_address_line2',	//	
			'city' => 'source_address_city',		//	
			'state' => 'source_address_state',		//	
			'zip' => 'source_address_zip',			//	
			'country' => 'source_country',			//	
			'email' => 'receipt_email',				//	
			'phone' => 'receipt_number',			//	
			'cc_type' => 'source_brand',			//paypal, visa, mastercard, etc. 
			'cc_card' => 'source_last4',			//last4 of 
			'cc_exp_month' => 'source_exp_month',	//expiration date. 
			'cc_exp_year' => 'source_exp_year',		//expiration date. 
			'on_behalf_of' => 'on_behalf_of',		//email_address 	
			
		
		];
		

//INCOMPLETE
		
		//https://stripe.com/docs/api/card/object
		private $card_data_map = [ //'nn_value' => '3rd_party_value'
			'create_date' => 'created',
			'trans_type' => 'object',
			'trans_status' => 'status',
			'trans_descrip' => 'description',
			'currency' => 'currency',
/* 			'subtotal' => '',		 		//Subtotal before taxes
			'discount' => '',		 		//Discount on Subtotal
			'sales_tax' => '',		 		//Sales Tax */
			'gross_amount' => 'amount', 			//Transaction Gross Amount
/* 			'trans_fee' => '',  	 		//Transaction Fee
			'net_amount' => '',		 		//Amount Collected After Fees */
			'tp_id' => 'id', 		
			'full_name' => 'source_name',			//
			'address' => 'source_address_line1',	//	
			'address1' => 'source_address_line2',	//	
			'city' => 'source_address_city',		//	
			'state' => 'source_address_state',		//	
			'zip' => 'source_address_zip',			//	
			'country' => 'source_country',			//	
			'email' => 'receipt_email',				//	
			'phone' => 'receipt_number',			//	
			'cc_type' => 'source_brand',			//paypal, visa, mastercard, etc. 
			'cc_card' => 'source_last4',			//last4 of 
			'cc_exp_month' => 'source_exp_month',	//expiration date. 
			'cc_exp_year' => 'source_exp_year',		//expiration date. 
			'on_behalf_of' => 'on_behalf_of',		//email_address 	
			
		
		];
		

//INCOMPLETE

		
		//https://stripe.com/docs/api/sources/object
		private $source_data_map = [ //'nn_value' => '3rd_party_value'
			'create_date' => 'created',
			'trans_type' => 'object',
			'trans_status' => 'status',
			'trans_descrip' => 'description',
			'currency' => 'currency',
/* 			'subtotal' => '',		 		//Subtotal before taxes
			'discount' => '',		 		//Discount on Subtotal
			'sales_tax' => '',		 		//Sales Tax */
			'gross_amount' => 'amount', 			//Transaction Gross Amount
/* 			'trans_fee' => '',  	 		//Transaction Fee
			'net_amount' => '',		 		//Amount Collected After Fees */
			'tp_id' => 'id', 		
			'full_name' => 'source_name',			//
			'address' => 'source_address_line1',	//	
			'address1' => 'source_address_line2',	//	
			'city' => 'source_address_city',		//	
			'state' => 'source_address_state',		//	
			'zip' => 'source_address_zip',			//	
			'country' => 'source_country',			//	
			'email' => 'receipt_email',				//	
			'phone' => 'receipt_number',			//	
			'cc_type' => 'source_brand',			//paypal, visa, mastercard, etc. 
			'cc_card' => 'source_last4',			//last4 of 
			'cc_exp_month' => 'source_exp_month',	//expiration date. 
			'cc_exp_year' => 'source_exp_year',		//expiration date. 
			'on_behalf_of' => 'on_behalf_of',		//email_address 	
			
		
		];

		
		
		
	// METHODS	

	/*
		Name: __Construct
		Description: 
	*/	
		
		public function __construct( $data ){
			
			$this->init( $data );
		}	
		

	/*
		Name: __Destruct
		Description: 
	*/	
		
		public function __destruct(){
			
			//What data should be unset? 
			unset()
			
		}	
		



	/*
		Name: Init
		Description: 
	*/	
		
		private function init( $data ){
			
			//$this->in = $data; 
			//$this->to_array();
		}	
		
		

	/*
		Name: to_array
		Description: Converts JSON Data to array and stores it in the "data" property.
	*/
		
		public function to_array(){
			
			
			//$array = (array) $this->in;
			
			$array = json_decode( json_encode( $this->in ), true );
			
			if( !empty( $array ) )
				$this->data = $array;
		}

		
	/*
		Name: get_data_map
		Description: returns the data_map property which is privately protected. 
	*/	
		
		public function get_data_map(){
			
			return $this->data_map;
			
		}	

		
	/*
		Name: get_action
		Description: 
	*/	
		
		/* 
		public function get_action(){
			
			//$action = $this->data[ 'custom' ];
			
			return $this->get_meta( 'action' );
		}
		*/
				
		
	/*
		Name: get_patron
		Description: 
	*/	
		
		public function get_patron(){
			
			
			//Is on_behalf_of set in the source data? 
			
			//Get 
			//$p_email = $this->data[ 'payer_email' ];
			
			//$patron = get_user_by( 'email', $p_email );
			
			//what is the payee email? 
			return $this->get_meta( 'patron' );
		}		
				
		
	/*
		Name: get_service
		Description: 
	*/	
		
		public function get_service(){
			
			//$service = $this->source->get_service();
			
			
			return $this->get_meta( 'service' );
		}		
				
		
	/*
		Name: get_token
		Description: 
	*/	
		
		public function get_token(){
			
			//$token = $this->source->get_token();
			
			return $this->get_meta( 'enrollment' ); //enrollment_token
			
		}		
				
		
	/*
		Name: get_meta
		Description:
	*/	
		
		public function get_meta( $value = '' ){
			
			if( !empty( $this->data[ 'metadata' ][ $value ] ) )
				$result = $this->data[ 'metadata' ][ $value ];
			
			return ( !empty( $result ) )? $result : false ;
		}		
		

				
		
	/*
		Name: get_
		Description: CAREFUL WITH THIS ONE. 
	*/	
		
		public function get__(){
			
			
			return 5;
		}		
		




		
	/*
		Name: add_post_data
		Description: 
	*/	
		
		public function add_post_data( $post ){
			
			//What information is available to add to the object
			
			//service_id, enrollment_type
			
			 $this->data[ 'metadata' ][ 'service' ] = $post['service_id'];
			 $this->data[ 'metadata' ][ 'enrollment' ] = $post['enrollment_type'];
			 //$this->data[ 'metadata' ][ 'action' ] = $post['action'];
			
			
		}	

		/*
		Name: flatten an object or array. 
		Description: 
	*/	
		
		public function flatten( $in, $prefix = ''){
			
		$array = (array) $in;
			$out = array();

			foreach( $array as $key => $value ){
				if( $key !== 'data' || $key !== 'object' )
					$new_key = $prefix . ( empty( $prefix ) ? '' : '_' ) . $key;
				else 
					$new_key = $prefix;
				
				
				if( is_array( $value ) || is_object( $value ) )
					$out = array_merge( $out, $this->flatten( $value, $new_key ) );
				else
					$out[ $new_key ] = $value;
			}
			return $out;
		}	
	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}		
	

	}//end of class
}

?>