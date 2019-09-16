<?php 

/* 
people_crm\data\Stripe\DataMap
Stripe DataMap Class for People CRM 
Last Updated 9 Sept 2019
-------------

Description: 



		
*/
	
namespace people_crm\data\Stripe;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'DataMap' ) ){
	class DataMap{

		// PROPERTIES
	
		public $data_set = array();
		
		
		private $core_objects = [
			'charge',
			'customer',
			'invoice',
			'line_item',
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
			'reference_id' 	=> 'invoice', 		
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
			'tp_user_id' => 'customer',
			
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
			'tp_user_id' => 'customer',
			
			'' => '', //
		
		];
		
		//https://stripe.com/docs/api/subscription/object
		private $subscription_data_map = [ //'nn_value' => '3rd_party_value'
			'subscription_id' => 'id', //
			'subscription_status' => 'status', //
			'enrollment' => 'metadata_enrollment', //
			'service' => 'metadata_service', //
			'tp_user_id' => 'customer',
			'invoice_id' => 'last_invoice', //
			
		
		];
				
		//https://stripe.com/docs/api/subscription_item/object
		private $subscription_item_data_map = [ //'nn_value' => '3rd_party_value'
			'subscription_item_id' => 'id', //
			'subscription_plan' => 'plan', //
			'' => '', //
			
		
		];		
		//https://stripe.com/docs/api/line_item/object
		private $line_item_data_map = [ //'nn_value' => '3rd_party_value'
			'line_item_id' => 'id', //
			'enrollment' => 'metadata_enrollment', //
			'service' => 'metadata_service', //
			'' => '', //
			
		
		];

		
		//https://stripe.com/docs/api/plan/object
		private $plan_data_map = [ //'nn_value' => '3rd_party_value'
			'subscription_plan_id'	=> 'id', //		
			'gross_amount'	=> 'amount', //	
			'currency' => 'currency', //
			'trans_descrip' => 'nickname', //
			'' => '', //
			'' => '', //
			
		
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
			'cc_four' 		=> 'last4',	 //last4 of 
			'cc_exp_month' 	=> 'exp_month', //expiration date. 
			'cc_exp_year'	 => 'exp_year',		//expiration date. 
			'tp_user_id' => 'customer'
		
		];
		
		
		//https://stripe.com/docs/api/sources/object
		//Presently contains no data that we need. Maybe in the future
		private $source_data_map = [ //'nn_value' => '3rd_party_value'
			'city' => 'address_city', //
			'country' => 'address_country', //
			'address' => 'address_line1', //
			'address1' => 'address_line2', //
			'state' => 'address_state', //
			'zip' => 'address_zip', //
			'cc_type' => 'brand', //
			'cc_four' => 'last4', //
			'cc_exp_mo' => 'exp_month',	//expiration month. 
			'cc_exp_yr' => 'exp_year',
			'full_name' => 'name', //
			'' => '', //
			
		
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
	
			
			//build an array of "core_objects" from the incoming data. 
			$set_data = $this->set_data( $data );
			
			//Map "core_objects" to the data_set for use by the "Format" class. 
			$this->data_set = $this->map_data( $set_data );
			
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
		Name: search_data_map
		Description: 
	*/	
		
		public function search_data_map( $key ){
			
			$value = '';
			
			if( empty( $this->data_set ) ) return '';
			
			$dm_keys = array_keys( $this->data_set );
			
			for( $i = 0; $i < count( $this->data_set ); $i++ ){
				$data_map_key = $dm_keys[ $i ];
				foreach( $this->data_set[ $data_map_key ] as $k => $val ){
					if( $k === $key )
						return $val;
				}
			}	
			return ''; 
		}

		
	/*
		Name: get_patron
		Description: 
	*/	
		
		public function get_patron(){
			
			
			
			$patron_id = 0;
			
			$patron_cus_number = $this->search_data_map( 'tp_user_id' );
			$patron_cus_email = $this->search_data_map( 'email' );
			
			$patron_id =  get_user_id_by_meta( 'stripe_user_id', $patron_cus_number);
			
			if( empty( $patron_id ) && !empty( $patron_cus_email ) ){
				$patron = get_user_by( 'email', $patron_cus_email );
				$patron_id = $patron->ID;
			}
		
			if( empty( $patron_id ) )
				$patron_id =  get_user_id_by_meta( 'stripe_customer_email', $patron_cus_email );
				
			return $patron_id ?? 0;
		}		
				
		
	/*
		Name: get_service
		Description: 
	*/	
		
		public function get_service(){
			
			return $this->search_data_map( 'service' );
		}		
				
		
	/*
		Name: get_token
		Description: 
	*/	
		
		public function get_enrollment(){
			
			return $this->search_data_map( 'enrollment' );
			
		}		
				
	

	/*
		Name: get_data
		Description: searchs for an element by key
	*/	
		
		public function get_data( $key ){
			
			$result = $this->search_data_map( $key );
			
			//dump( __LINE__, __METHOD__, $key .' -> '. $result );
			return $result;
		}		

	/*
		Name: get_data_array 
		Description: Receives an array of data to look for. Also returns the same array with data values loaded. 
	*/	
		
		public function get_data_array( $arr ){
			
			foreach( $arr as $key => $val )
				$arr[ $key ] = $this->search_data_map( $key );
			
			return $arr;
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