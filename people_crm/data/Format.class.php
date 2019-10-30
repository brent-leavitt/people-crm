<?php 

/*
people_crm\data\Format

DataFormat Class for NBCS Network Plugin
Last Updated 15 Jul 2019
-------------

Description: This takes all incoming data from internal and third party transactions and turns into a universal formatted data set for use in the backend processes. 

Question: Shouldn't internal functions be correctly formatted before they get here? Yes. 

This doesn't call any backend functionality. This is simply a prepretory class. All data regardless of the source should be properly formatted after passing through this class. 

		
*/
	
namespace people_crm\data;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'Format' ) ){
	class Format{

	// PROPERTIES
		
		// 
		private $in;							//Incoming Data String
		private $source;						//Source of incoming data. 
		private $action; 						//Action to be taken. 
		private $out; 							//Outgoing Data Array
		
		private $data_map;						//(obj) Data map class 
		
		private $output_format = array(
			'action' => '',						//Primary Action 
			'patron' => '', 					//
			'service' =>  '', 					//
			'token' => '', 						//
			'data' => array(					//The data key may be replaced with the name of the Primary Action. 
				'create_date' => '', 			//Create Date or issue Date
				'currency' => '',				//Currency (only accepting USD)
				'discount' => '',		 		//Discount on Subtotal
				'due_date' => '', 				//Due Date
				'gross_amount' => '', 			//Transaction Gross Amount
				'line_items' => array(
					array(
						'li_id' => '', 			//Item ID
						'li_descrip' => '',		//Description
						'li_qty' => '', 		//Qty
						'unit_price' => '',		//Unit Price
						'li_discount' => '', 	//Discout
						'account' => '', 		//Account
						'li_amount' => '', 		//Amount
					),
					//etc...
				),
				'net_amount' => '',		 		//Amount Collected After Fees
				'payee' => array(
					'full_name' => '',			//
					'user_name' => '', 			//
					'display_name' => '', 		//
					'first_name' => '',			//
					'last_name' => '',			//
					'address' => '',			//	
					'address1' => '',			//	
					'city' => '',				//	
					'state' => '',				//	
					'zip' => '',				//	
					'country' => '',			//	
					'email' => '',				//	
					'phone' => '',				//	
					'cc_type' => '',				//paypal, visa, mastercard, etc. 
					'cc_four' => '',				//last4 of 
					'cc_exp_mo' => '',				//expiration month. 
					'cc_exp_yr' => '',				//expiration year. 
					'on_behalf_of' => '',		//email_address 
					'password' => '',			//?
				),
				'reference_id' => '',	 		//Reference ID
				'reference_type' => '',			//Reference Type
				'sales_tax' => '',		 		//Sales Tax
				'src_data' => '',				//JSON String of Transactional Source Data. 
				'subtotal' => '',		 		//Subtotal before taxes
				'template' => 0, 				//what notice (template) is being sent?
				'template_vars' => array(),
				'tp_id' => '', 					//ThirdParty Transaction ID
				'tp_name' => '', 				//ThirdParty Name, like "stripe" or PayPal 
				'tp_type' => '', 				//ThirdParty Transaction Type, like "charge", "payment", "refund", etc. 
				'tp_user_id' => '', 				//ThirdParty User ID
				'trans_status' => '',			//Transaction Status
				'trans_descrip' => '',			//Description of the Transaction
				'trans_fee' => '',  	 		//Transaction Fee
				
				'' => '',						// (what else)?
			)
		);
		
		/* private $default_data_map = [
		
		];			// 'nn_value' => '3rd_party_value'
		 */
		

		
		
		
	// METHODS	

	/*
		Name: __Construct
		Description: 
	*/	
		
		public function __construct( $data, $source, $action ){
			
			$this->init( $data, $source, $action );
			
		}	
		

	/*
		Name: __Destruct
		Description: 
	*/	
		
		public function __destruct(){
			
			
			
		}	

	/*
		Name: Init
		Description: 
			$data is the incoming data
			$source is a string
			$action is a string, and is determined in the webhook. 
			
	*/	
		
		private function init( $data, $source, $action ){
			
			$this->in 		= $data;		//Recieve the data to be formatted from external sources. 
			$this->source 	= $source;		//Set the source of incoming data.
			$this->action 	= $action;
			
			$this->out = $this->map_data();
			
			//Add integrity check
			
			//Add clean 
			
			
			//End result is: mapped data ready to be taken for use. 
			//$this->get_formatted_data();
			
			//dump( __LINE__, __METHOD__, $this->out );
		}

	
		
	/*
		Name: map_data
		Description: The master method for taking third party data and prepare it to be inserted into the "mapped_data" property. 
	*/	
		
		public function map_data(){
			
			if( empty( $this->in ) || empty( $this->source ) || empty( $this->action ) )
				return false;
			
			$arr = $this->output_format;
			
			$source = ucfirst( strtolower( $this->source ) );
			$data_map_class = 'people_crm\\data\\'.$source.'\\DataMap';
			$this->data_map = new $data_map_class( $this->in );
			
			//Set PRIMARY values
			
			$arr[ 'action' ] 		= $this->action;
			$arr[ 'patron' ] 		= $this->get_patron();
			$arr[ 'token' ] 		= $this->get_enrollment();
			$arr[ 'service' ] 		= $this->get_service();
			
			
			//Set DATA values
						
			foreach( $arr[ 'data' ] as $key => $val ){
				if( is_array( $val ) ) continue;
				$arr[ 'data' ][ $key ] = $this->get_data( $key );
			}
			
			//set payee
			$arr[ 'data' ][ 'payee' ] = $this->get_data_array( $arr[ 'data' ][ 'payee' ] );
			
			//Because our system only processes single line item transactions, this works. If ever to process multiple. This would need to be updated. This, along with a bunch of other code. 
			//set line items
			$arr[ 'data' ][ 'line_items' ][0] = $this->get_data_array( $arr[ 'data' ][ 'line_items' ][0] );
			
			
			//Set ThirdParty
			$arr[ 'data' ][ 'tp_name' ] = $this->source;
			
			//Set Source data
			$arr[ 'data' ][ 'src_data' ] = $this->in;
			
			
			return $arr;
		}		
		
		
	/*
		Name: get_patron
		Description: How to get PATRON for whom this transaction is set? 
			
	*/	
		
		public function get_patron(){
		
			$patron = $this->data_map->get_patron();
				
			return ( is_numeric( $patron ) )? $patron : -1 ;
		}		
				
		
	/*
		Name: get_service
		Description: 
	*/	
		
		public function get_service(){
			
			return $this->data_map->get_service();
			
		}		
				
		
	/*
		Name: get_enrollment
		Description: 
	*/	
		
		public function get_enrollment(){
			
			return  $this->data_map->get_enrollment();
			
		}		
	
		
	/*
		Name: get_data
		Description: this looks for a data value in the source that matches the key. 
	*/	
		
		public function get_data( $key ){
			
			return $this->data_map->get_data( $key );
		}		
		
		
	/*
		Name: get_data_array
		Description: this looks for a data value in the source that matches the key. 
	*/	
		
		public function get_data_array( $arr ){
			
			return $this->data_map->get_data_array( $arr );
		}		
		
		
	/*
		Name: integrity
		Description: Checks the integrity of the outputted data. If key fields are in place then return true. 
	*/	
		
		public function integrity( $data ){
			
			$fields = [ 'action', 'service', 'patron', 'token' ];
			
			foreach( $fields as $field ){
				if( empty( $data[ $field ] ) )
					return false;					
			}
			
			return true; 
		}
		
		
	/*
		Name: clean
		Description: This cleans up the data and removes empty fields. 
	*/	
		
		public function clean( $data ){
			
			foreach( $data as $key => $val ){
				if( !is_array( $data[ $key ] ) ){
					//unset if empty		
					if( empty( $val ) )
						unset( $data[ $key ] );
				}else{
					$data[ $key ] = $this->clean( $data[ $key ] );
				}
				
			}
			return $data;
		}	
		

		
	/*
		Name: get_output_format
		Description: 
	*/	
		
		public function get_output_format(){
			
			return $this->output_format;
		}
		
		
	/*
		Name:get_out 
		Description: Returns the out property value. 
	*/	
		
		public function get_out(  ){
			
			return $this->out;
		}		
		
		
		
	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}


	}//end of class
}