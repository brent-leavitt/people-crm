<?php 

/* 
people_crm\data\Stripe\DataMap
DataMap Class for NBCS Network Plugin
Last Updated 9 Sept 2019
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
		//public $in; 			//This is the source JSON string of info.
		//public $data = [];		//This is the converted array of incoming JSON data. 
		
		public $data_set = array(
				'action' => '',
				'patron' => 0,
				'service' => '',
				'token' => '',
				'data' => array(),
				'source_data' => array(),
				
			);
		
		
		private $core_objects = [
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
			'enrollment' => 'metadata_enrollment', //
			'service' => 'metadata_service', //
			'stripe_customer_id' => 'customer',
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
			
			//Not sure if I want to send the whole source data object through the back end. Not secure either...
			//$this->data_set[ 'source_data' ] = $data; 
			
			//build an array of "core_objects" from the incoming data. 
			
			$set_data = $this->set_data( $data );
			
			dump( __LINE__, __METHOD__, $set_data);
			
			$this->data_set[ 'data' ] = $this->map_data( $set_data );
			
			dump( __LINE__, __METHOD__, $this->data_set);
			
			//set action
			
			
			//$this->to_array();
			
			
		}	
	
	/*
		Name: set_data
		Description: receives incoming data, and returns an array of informaiton for the data_set[ 'data' ] property. 
	*/	
		
		public function set_data( $in ){
			
			$arr = []; //final output is an array. 
			
			//add sets of data as arrays. 
			
			$in_arr = $in->__toArray(); 
			
			//remove nested data and return it as additional arrays. 
			
			$cores = $this->add_cores( $in_arr );
			
			foreach( $cores as $c_key => $c_val ){
				$new_core = $this->flatten( $c_val );
				unset( $cores[ $c_key ] );
				$cores[ $new_core[ 'object' ] ] = $new_core;
			}	
			
			$arr[ $in_arr[ 'object' ] ] = $this->simplify_arr( $in_arr );
		
			$arr = array_merge( $arr, $cores ); 
			
			return $arr;

		}		
		
		
		
	/*
		Name: map_data
		Description: 
	*/	
		
		public function map_data( $data ){
			
			$mapped = [];
			
			$keys = array_keys( $data );
			
			for( $i = 0; $i < count( $data ); $i++ ){
				$core =	$keys[ $i ];
				$mapped[ $core ] = $this->map_object( $core, $data[ $core ] );
			}
			
			return $mapped; 
		}		
		
		

	/*
		Name: map_object
		Description: This maps a specific set of core object data. 
	*/	
		
		public function map_object( $name , $arr ){
			
			$obj_name = $name."_data_map";
			$data_map = ( $this->$obj_name )?? NULL;
			
			if( empty( $data_map ) ) return false;
			
			$new_arr = [];
			
			foreach( $data_map as $key => $val )
				$new_arr[ $key ] = ( $arr[ $val ] )?? NULL ;
			
			//Drop empty values, but not zeros.
			$new_arr = array_filter( $new_arr, 'strlen' );
			
			return $new_arr;
		}	

		
		
		
		
	/*
		Name: simplify_arr
		Description: Receive the array, and cleans out anything that is nested as an object, and returns cleaned data. 
	*/	
		
		public function simplify_arr( $arr ){
			
			$add_val = [];
			
			foreach( $arr as $key => $val ){
				
				if( $this->is_core_object( $val ) || empty( $val ) ){
					//echo "THIS iteration is being skipped: $key -> $val \n\n";
					unset( $arr[ $key ] );
					continue;	
				}

				if( is_object( $val ) || is_array( $val ) ){
					$add_val[ $key ] = $val;
					unset( $arr[ $key ] );
				}	
			}
			
			//This will take anything that is nested but not core and add it to the array. 
			$arr = array_merge( $arr, $this->flatten( $add_val ) );
			
			return $arr;
		}		
	


	/*
		Name: add_cores
		Description: this processes all incoming data and pulls out additional core objects and attaches them as additional arrays. 
	*/	
		
		public function add_cores( $in ){
			
			$cores = [];
			
			$in_arr = ( is_object( $in ) )? $in->__toArray() : (array) $in; 
			
			foreach( $in_arr as $key => $val ){
				
				//look deeper for additional cores
				if( is_object( $val ) || is_array( $val ) ){
					if( $this->is_core_object( $val ) ){
						$cores[ $key ] = $val->__toArray(); 
					}					
					$cores = array_merge( $cores, $this->add_cores( $val ) );

				}
			}
			return $cores;
		}	

	

	/*
		Name: is_core_object
		Description: Checks to see if the tested object is in the core objects array
	*/	
		
		public function is_core_object( $obj ){
			
			
			$arr =( is_object( $obj ) )? $obj->__toArray() : (array)$obj ;
			
			if( isset( $arr[ 'object' ] ) ){
				if( in_array( $arr[ 'object' ] , $this->core_objects  ) ){
					return true;
				}
			}
			
			return false;
		}		
	
		
	/*
		Name: flatten
		Description: flattens multidemensional arrays, partner method to flatten_obj. 
	*/	
		
		public function flatten( $obj, $prefix = '' ){
			
				$new_arr = [];	
				
				$arr = ( is_object( $obj ) )? $obj->__toArray() : (array) $obj; 
					
				foreach( $arr as $k => $v ){
					
					if( empty( $v ) ) continue;
					
					$new_key = ( !empty( $prefix ) )? $prefix . '_' . $k : $k;
					
					//if this is not array or object:
					if( !is_array( $v ) && !is_object( $v ) ){
						$new_arr[ $new_key ] = $v;
						continue;
					}
					
					if( is_object( $v ) ){
						if( $this->is_core_object( $v ) ){ //if is core object, remove it.
							unset( $arr[ $k ] );
							continue;
						}
					}	
					
					$new_arr = array_merge( $new_arr, $this->flatten( $v, $new_key ) );
					
				}	
				
				//dump( __LINE__, __METHOD__, $new_arr );	
				
				return $new_arr;
		}	
	
		
	/*
		Name: get_data_map
		Description: returns the data_map property which is privately protected. 
	*/	
		
		public function get_mapped_data(){
			
			return $this->data_set;
			
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
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}		
	

	}//end of class
}

?>