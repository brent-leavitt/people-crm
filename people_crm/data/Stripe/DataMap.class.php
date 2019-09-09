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
		
		public $data_set = array(
				'action' => '',
				'patron' => 0,
				'service' => '',
				'token' => '',
				'data' => array()
			);
		
		
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
			'create_date' 	=> 'created',
			'trans_type' 	=> 'object',
			'trans_status' 	=> 'status',
			'trans_descrip' => 'description',
			'currency' 		=> 'currency',
/* 			'subtotal' 		=> '',		 		//Subtotal before taxes
			'discount' 		=> '',		 		//Discount on Subtotal
			'sales_tax' 	=> '',		 		//Sales Tax */
			'gross_amount' 	=> 'amount', 			//Transaction Gross Amount
/* 			'trans_fee'		=> '',  	 		//Transaction Fee
			'net_amount' 	=> '',		 		//Amount Collected After Fees */
			'tp_id' 		=> 'id', 		
			'full_name' 	=> 'billing_details_name',			//
			'address' 		=> 'billing_details_address_line1',	//	
			'address1' 		=> 'billing_details_address_line2',	//	
			'city' 			=> 'billing_details_address_city',		//	
			'state' 		=> 'billing_details_address_state',		//	
			'zip' 			=> 'billing_details_address_zip',			//	
			'country' 		=> 'billing_details_country',			//	
			'email' 		=> 'billing_details_email',				//	
			'phone' 		=> 'billing_details_phone',			//	
			'on_behalf_of' 	=> 'on_behalf_of',	
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

		
		//https://stripe.com/docs/api/invoices
		private $invoice_data_map = [ //'nn_value' => '3rd_party_value'
			'invoice_id' => 'id', //
			'create_date' 	=> 'created',
			'currency' 		=> 'currency',
			'trans_status' 	=> 'status', // draft, open, paid, uncollectible, or void
			'invoice_paid' => 'paid',
			'gross_amount' 	=> 'amount_paid', //Invoice Gross Amount			//
			'full_name' 	=> 'customer_name',	//	
			'address' 		=> 'customer_address_line1',	//	
			'address1' 		=> 'customer_address_line2',	//	
			'city' 			=> 'customer_address_city',		//	
			'state' 		=> 'customer_address_state',		//	
			'zip' 			=> 'customer_address_postal_code',			//	
			'country' 		=> 'customer_adddres_country',			//	
			'email' 		=> 'customer_email',				//	
			'phone' 		=> 'customer_phone',			//	
			'stripe_customer_id' => 'customer',
			
			'' => '', //
			'' => '', //
		
		];
		
		//https://stripe.com/docs/api/subscription/object
		private $subscription_data_map = [ //'nn_value' => '3rd_party_value'
			'subscription_id' => 'id', //
			'subscription_status' => 'status', //
			'' => '', //
			
		
		];
				
		//https://stripe.com/docs/api/subscription_item/object
		private $subscription_item_data_map = [ //'nn_value' => '3rd_party_value'
			'subscription_item_id' => 'id', //
			'subscription_plan' => 'plan', //
			'' => '', //
			
		
		];

		
		//https://stripe.com/docs/api/plan/object
		private $plan_data_map = [ //'nn_value' => '3rd_party_value'
			'subscription_plan_id'	=> 'id', //		
			''	=> '', //		
			
		
		];
		
		
		//https://stripe.com/docs/api/card/object
		private $card_data_map = [ //'nn_value' => '3rd_party_val
			'address' 		=> 'address_line1',	//	
			'address1'		=> 'address_line2',	//	
			'city' 			=> 'address_city',	//	
			'state' 		=> 'address_state',	//	
			'zip' 			=> 'address_zip',	//	
			'country' 		=> 'address_country',	//	
			'cc_type' 		=> 'brand',	//paypal, visa, mastercard, etc. 
			'cc_card' 		=> 'last4',	 //last4 of 
			'cc_exp_month' 	=> 'exp_month', //expiration date. 
			'cc_exp_year'	 => 'exp_year',		//expiration date. 
			'stripe_customer_id' => 'customer'
		
		];
		
		
		//https://stripe.com/docs/api/sources/object
		//Presently contains no data that we need. Maybe in the future
		private $source_data_map = [ //'nn_value' => '3rd_party_value'
			
		
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
			//unset()
			
		}	
		



	/*
		Name: Init
		Description: 
	*/	
		
		private function init( $data ){
			
			$this->in = $data; 
			
			//build an array of "objects" from the incoming data. 
			$final = $this->build_objs_array( $this->in );
			dump( __LINE__, __METHOD__, $final);
			
			//filter out only those objects that contain pertinent data. ($objects_arr)
			
			//treat each object separately, then map available data for each object. 
			
			//dump all data from processed objects into the $this->data_set[ 'data' ] array. 
			
			
			
			//put object name in key. if nested objects, value is another array. 
			
			/* 
			
			$objs = [ 
				'obj_1'=> [
					'obj_2' => '',
					'obj_3' => '',
					'obj_4' => [
						'obj_5' => ''
					]
				],
				'obj_6' => [
					'obj_2' => '',
					'obj_3' => '',
				]
				//etc.
			]; 
			
			*/
			
			
			//$this->to_array();
			
			
		}	
		
	/*
		Name: build_objs_array
		Description: builds an array of objects only
	*/	
		
		public function build_objs_array( $in, $i = 0, $key_name = ''){
			//$i++;
			$out = [];
			//dump( __LINE__, __METHOD__, $in );
			
			
			$new_key = ( is_object( $in ) && empty( $key_name ) )? get_class( $in ) : $key_name;
			
			$new_value = [];
			
			
			//convert to array for iteration. 
			$in_arr = ( is_object( $in ) )?  $in->__toArray() : $in ;
			
			if( isset( $in_arr[ 'object' ] ) )
				echo "\r\n this is a {$in_arr[ 'object' ]} object!!!  \r\n";
			
			dump( __LINE__, __METHOD__, $in_arr );
			
			foreach( $in_arr as $k => $v ){
				//we need to look in here for more object, but not do anything else. 
				if( is_array( $v ) ){
					$rsult = $this->build_objs_array( $v, $i );
					//dump( __LINE__, __METHOD__, $i );
					//dump( __LINE__, __METHOD__, $rsult);
					if( !empty( $rsult ) ){
						$new_value[ $k ] = $rsult;
					}
				}
				
				if( is_object( $v ) ){
					
					$key_name = get_class( $v );
					$rsult = $this->build_objs_array( $v, $i, $key_name  );
					$r_key = key( $rsult );
					if( array_key_exists( $r_key, $new_value ) ){
						//dump( __LINE__, __METHOD__, $rsult);
						$i++;
						$rsult[ $r_key.$i ] = $rsult[ $r_key ];
					}
					$new_value  =  $new_value + $rsult;
					
					//dump( __LINE__, __METHOD__, $i );
					//dump( __LINE__, __METHOD__, $new_value);
				}	
				
				
			}
			
			
			if( is_object( $in ) )
				$out[ $new_key ] = $new_value ?? '';
			
			
			//dump( __LINE__, __METHOD__, $out);
			return $out; 
		}				
	
	/*
		Name: to_array
		Description: Converts JSON Data to array and stores it in the "data" property.
	*/
		
		public function to_array( $in ){
			
			
			//$array = (array) $this->in;
			
			dump( __LINE__, __METHOD__, $this->in );
			
			$array = json_decode( json_encode( $this->in ), true );
			
			if( !empty( $array ) )
				$this->data = $array;
			
			return $out;
			//dump( __LINE__, __METHOD__, $array );
		}

		
	/*
		Name: get_data_map
		Description: returns the data_map property which is privately protected. 
	*/	
		
		public function get_mapped_data(){
			
			return $this->data;
			
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