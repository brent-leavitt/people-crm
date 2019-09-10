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
				'data' => array()
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
			
			$this->data_set[ 'data' ] = $this->set_data();
			
			
			//$final = $this->build_objs_array( $this->in );
			dump( __LINE__, __METHOD__, $this->data_set);
			
			
			
			
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
			
			$arr[ $in_arr[ 'object' ] ] = array_merge( $in_arr, $this->simplify_arr( $in_arr ) );
			
			
			$arr = array_merge( $arr, $this->add_cores( $in_arr ) ); 
			
			return $arr;

		}		
		
		
	/*
		Name: simplify_arr
		Description: Receive the array, and cleans out anything that is nested as an object, and returns cleaned data. 
	*/	
		
		public function simplify_arr( $arr ){
			
			$add_val = [];
			
			foreach( $arr as $key => $val ){
				
				//
				if( is_core_object( $val ) )
					unset( $arr[ $key ] );

				if( is_object( $val ) || is_array( $val ) ){
					$add_val[ $key ] = $val;
					unset( $arr[ $key ] );
				}	
			}
			
			//This will take anything that is nested but not core and add it to the array. 
			$arr = array_merge( $arr, $this->flatten( $add_val ) );
			
			return $arr
		}		
	

	/*
		Name: add_val (NOT NEEDED) 
		Description: Incoming is an array of arrays and objects. This will take anything that is nested but not core and add it to the array. Outputs a simple flattened array. 
	*/	
		
		public function add_val( $in ){
			
			$arr = [];
			
			$in_arr = ( is_object( $in ) )?  $in->__toArray : $in ;
				
			
			foreach( $in_arr as $key => $val  ){
				
				//if core, ignore:
				if( is_core_object( $val ) )
					continue;	
				
				//if value is obj: flatten_obj
				if( is_object( $val ) )
					$arr = array_merge( $arr, $this->flatten_obj( $val, $key ) );
				
				//if value is array: flatten_arr
				if( is_array( $val ) )
					$arr = array_merge( $arr, $this->flatten_arr( $val, $key ) );
				
			}
			
			return $arr;
		}	
	

	/*
		Name: add_cores
		Description: this processes all incoming data and pulls out additional core objects and attaches them as additional arrays. 
	*/	
		
		public function add_cores( $in ){
			
			
		}	

		
		
		/* 
		
		OLD CODE 
		
			
			$in = ( empty( $in ) && ( $i == 0  ) )?  $this->in : in ;
			
			$i++;
			
			
			//convert to array for iteration. 
			$in_arr = ( is_object( $in ) )?   : $in ;
			
			$obj_name = ( $in_arr[ 'object' ] ) ?? '';
			
			//if this is a good egg, let's eat it! This is a core data object
			if( $this->is_core_object( $obj_name ) ){
				
				//Now I want to take everything that is not an object or array and set it inside here. 
						//Move all this to another method?
				foreach( $in_arr  as $key => $val ){
					
					if( is_array( $val ) ){
						
						$new_arr = $this->flatten_arr( $val );
						
						$arr = $arr + $new_arr;
						
					}
					
					if( is_object( $val )  ){
						
						//This will return either a core_object or an array of values
						$sub_data = $this->sub_data_loop( $val, $key );
						
						dump( __LINE__, __METHOD__, $sub_data );
						if( $this->is_core_object( $sub_data[ 'object' ] ) ){
							
							echo "We are calling the build_data METHOD inside itself! \r";
							$this->set_data( $sub_data, $i );
							
						} else {
							
							//$arr = array_merge( $arr, $sub_data );
							$new_arr = $this->sub_data_loop( $sub_data );
							
							$arr = array_merge( $arr, $new_arr );
							
						}
						
						//else it needs to be processed further. if it is core data, it can be run through this loop again. $val could be a core object. 
						continue;
					}	
					
					$arr[ $key ] = $val;
					
				}
				
				
			} else {
				
				//This is not an core object, but another type of array or object, we need to loop through this and then tack it on as well. 
				
				//$arr[ $key ] = $val;
			} */
	

	/*
		Name: is_core_object
		Description: Checks to see if the tested object is in the core objects array
	*/	
		
		public function is_core_object( $obj ){
			
			$arr =( is_object( $obj ) )? $obj->__toArray() : (array)$obj ;
			
			if( isset( $arr[ 'object' ] ) ){
				if( in_array( $arr[ 'object' ] , $this->core_objects  ) )
					return true;
			}
			
			return false;
		}		
	
		
	/*
		Name: flatten
		Description: flattens multidemensional arrays, partner method to flatten_obj. 
	*/	
		
		public function flatten( $arr, $prefix = '' ){
			
				$new_arr = [];	
				
				$arr = ( is_object( $obj ) )? $obj->__toArray() : (array) $obj; 
					
				foreach( $arr as $k => $v ){
					$new_key = ( !empty( $prefix ) )? $prefix . '_' . $k : $k;
					
					//if this is array:
					if( !is_array( $v ) && !is_object( $v ) ){
						$new_arr[ $new_key ] = $v;
						continue;
						
					//if this is an object:	
					}elseif( is_object( $v )){
								
						if( $this->is_core_object( $v ) ){
							echo "Core object found in flatten_arr. We are removing it! \r";
							dump( __LINE__, __METHOD__, $v );
							unset( $arr[ $k ] );
							continue;
						}
					}	
					
					$new_arr = $this->flatten( $v, $new_key );
					
				}	
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