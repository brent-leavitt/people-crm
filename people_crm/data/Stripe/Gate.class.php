<?php 

/*
people_crm\data\stripe\Gate

Gate Class for Stripe in the People CRM Plugin
Last Updated 8 Oct 2019
-------------

Description: This takes a batch of incoming data (a webhook) from the Stripe third party service and compiles into one group of data for use in the system's backend actions. 

//Steps for building a gate object. 

//Collect incoming data. 

//Assess if this matches (or if the information is a part of) an existing gate object. 
	-- what are key matching elements? 
		- invoice_id 
		- cus_numbers
		- email_address
		- time_stamp 

//If no matching gate objects, create a new gate object. 
	
	

//	

		
*/
	
namespace people_crm\data\Stripe\;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'Gate' ) ){
	class Gate{

	// PROPERTIES
		
		//public 

		private 
			$gate_id = 0,
			$timestamp = NULL,
			$reference_id = '',
			$obj,
			$action = 'invoice',  //default is 'invoice'. 
			$status = 'new',//default is new
			$patron = 0,
			$service = '',
			$token = '';
			
	
		
		
	// METHODS	

	/*
		Name: __Construct
		Description: 
	*/	
		
		public function __construct( ){
			
		
		}	
		

	/*
		Name: __Destruct
		Description: 
			- Save Gate Object to Database on Destruction.
		
	*/	
		
		public function __destruct(){
			
			$this->save();
			
			
		}	

	/*
		Name: Init (deprecated?)
		Description: 
			$data is the incoming data
			$source is a string
			$action is a string, and is determined in the webhook. 
			
	*/	
		
		private function init( ){
			
			//check for matching reference_ids, returns an array of gate_ids that match 
			if( !empty( $stored ) )
				$gate_ids = $this->find_matching_reference_ids();
			
			//
			if( !empty( $gate_ids ) )
				$is_lowest = $this->is_lowest_gate_id( $gate_ids );
			//End result is: mapped data ready to be taken for use. 
			//$this->get_formatted_data();
			
			//dump( __LINE__, __METHOD__, $this->out );
		}

		
		
	/*
		Name: setup
		Description: This sets up the gate incoming data.  
	*/	
		
		public function setup( $data ){
			
			if( empty( $this->obj ) )
				$this->obj = $data;
			
			if( empty( $this->obj ) )
				$this->reference_id = $data[ 'data' ][ 'reference_id' ];
			
			$props = [ 'patron', 'service', 'token' ]
			
			foreach( $props as $prop ){
				if( !empty( $data[ $prop ] ) )
					$this->$prop = $data[ $prop ];
			}
			
		}		
		
		
	/*
		Name: prepare
		Description: This prepares data to be inserted into the database. 
			array of [field=>values] to prepare (optional) - if not set, prepare all. 
			
			Returns an array of data for insertion. 

	*/	
		
		public function prepare( $array = [] ){
			
			if( empty( $array ) ){
				$array = [
					'timestamp' => $this->timestamp,
					'reference_id' => $this->reference_id,
					'action' => $this->action,
					'data' => $this->obj,
					'sent' => $this->sent,
				];
			}		
				
			return $array;
		}		

		
	/*
		Name: add
		Description: Sends gate info to database. 
	*/	
		
		public function add(){
			
			global $wpdb;
			
			$checks = [ 'timestamp', 'reference_id', 'action', 'obj', 'sent', 'table' ];
		
			$table = $wpdb->prefix."gate";
			
			//Timestamp: where is this coming from? Time it was insert: NOW. 
			$this->timestamp = date( 'Y-m-d H:i:s' );
		
			$data = $this->prepare();
		
			//IMPORTANT: but needs to happen after this->prepare(), data obj 
			$data[ 'obj' ] = json_encode( $data[  'obj' ] );
			
			//Check that all the above values are not empty. 
			
			foreach( $checks as $check ){
				if( empty( $$check ) )
					 return new WP_Error( 'empty', __( "Attempting to send empty values to the database. Sorry!", PC_TD ) );
			}
			
			return $wpdb->insert( $table, $data );
			
		}		
		
		
	/*
		Name: save 
		Description: Take the gate object in its current state and saves it to the database. 
			Params: $args = the data to be updated. 
			
	*/	
		
		public function save( $args ){
			global $wpdb;
			
			$table = $wpdb->prefix."gate";
			
			if( empty( $this->gate_id ) )
				return new WP_Error( 'empty', __( "Attempting to update a gate without an id. Sorry!", PC_TD ) );
			
			$where = [
				'id' => $this->gate_id, 
			];
			
			return $wpdb->update( $table, $args, $where );

		}		
	
	
	/*
		Name: create
		Description: creates a new gate object from incoming data. 
	*/	
		
		public function create( $data ){
			
			//Prepare incoming data,  assign properties. 
			$this->setup( $data );
			
			//store in database. 
			$stored = $this->add();
			
		}		


		
	/*
		Name: get_gate_id
		Description: Returns the gate ID
	*/	
		
		public function get_gate_id(){
			
			return $this->gate_id;
		}
		
		
	/*
		Name: get_reference_id
		Description: 
	*/	
		
		public function get_reference_id(){
			
			return $this->reference_id;
			
		}	
		
		
	/*
		Name: set_status
		Description: This changes the status of the gate object in the database to inputted value. 
	*/	
		
		public function set_status( $status ){
			
			$args = $this->prepare( [
				'sent' => $status;
			] );
			
			$saved = $this->save( $args );
			
			return $saved;
		}


		

		
	/*
		Name: load_by_id
		Description: loads gate object by ID. 
	*/	
		
		public function load_by_id( $id ){
			global $wpdb;
			
			$results = $wpdb->get_results( 
				"SELECT * FROM {$wpdb->prefix}gate WHERE id = {$id} LIMIT 1;" 
			);
			
			$gate = $results[0];

			$this->gate_id = $gate[ 'id' ];
			unset( $gate[ 'id' ] );
			
			//Set Data
			$this->obj = json_decode( $gate[ 'data' ] );
			unset( $gate[ 'data' ] );
			
			//all remaining data coming from DB should have a corresponding property. 
			foreach( $gate as $key => $val )
				$this->$key = $val
			
			//Finish setting up properties found in the data. 
			$this->setup( $this->obj );
		}

		
	/*
		Name: send
		Description: Send this gate object to the back end for processing. 
	*/	
		
		public function send(){
			
			
		}
		
	/*
		Name: is_ready
		Description: This checks to see if the gate object has enough data to be correctly processed on the back end. 
	*/	
		
		public function is_ready(){
			
			

		}
		
	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}


	}//end of class
}