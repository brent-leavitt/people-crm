<?php 

/*
people_crm\data\stripe\Gate

Gate Class for Stripe in the People CRM Plugin
Last Updated 23 Oct 2019
-------------

Description: This is the gate class which creates objects for the gate database entries. 

THere are two ways to initate a gate: create() or load_by_id(). 
	
	-Create() - takes a batch of incoming data (a webhook) from the Stripe third party service and compiles into one group of data for use in the system's backend actions. 
	
	-Load_by_id() - takes and existing gate entry, referenced by its gate_id. 
	
	

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
	
namespace people_crm\data\Stripe;

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
			$status = 'new';//default is new
			/* $patron = 0,
			$service = '',
			$token = ''; */
			
	
		
		
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
			
			
	*/	
		
		private function init( ){
			

		}

		
	//STOPPED HERE, got some bugs. 	
	/*
		Name: setup
		Description: This sets up the gate incoming data. 
			Param: (array) $data 
	*/	
		
		public function setup( $data ){
			
			//Force to Array. 
			if( is_object( $data ) )
				$data = json_decode( json_encode( $data ), true );
			
			if( empty( $this->obj ) )
				$this->obj = $data;
			
			//Not sure why I'm calling this empty check . 
			if( empty( $this->reference_id ) )
				$this->reference_id = $data[ 'data' ][ 'reference_id' ];
			
			//Check if action needs to be updated. 
			if( strcmp( $this->action, $data[ 'action' ] ) !== 0 )
				$this->action = $data[ 'action' ];
			
			
			/* $props = [ 'patron', 'service', 'token' ];
			
			foreach( $props as $prop ){ 
				if( !empty( $data[ $prop ] ) ){
					$this->$prop = $data[ $prop ];
				}
			} */
			
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
					'status' => $this->status,
				];
			}		
			if( isset( $array[ 'data' ] ) )
				$array[ 'data' ] = json_encode( $array[ 'data' ] );
				
			return $array;
		}		

		
	/*
		Name: add
		Description: Sends gate info to database. 
	*/	
		
		public function add(){
			
			global $wpdb;
			
			$checks = [ 'timestamp', 'reference_id', 'action', 'data', 'status' ];
		
			$table = $wpdb->prefix."gate";
			
			//Timestamp: where is this coming from? Time it was insert: NOW. 
			$this->timestamp = date( 'Y-m-d H:i:s' );
			
			//Dev purposes only. 
			//if( NN_DEV )
				//$this->reference_id = 'in_0123456789a';
		
			$data = $this->prepare();
			
			//Check that all the above values are not empty. 
			
			foreach( $checks as $check ){
				if( empty( $data[ $check ] ) ){
					 return new \WP_Error( 'empty', __( "Attempting to send empty -{$check}- value to the database. Sorry!", PC_TD ) );
				}
			}
			
			$wpdb->insert( $table, $data );
			
			return $wpdb->insert_id;
			
		}		
		
		
	/*
		Name: save 
		Description: Take the gate object in its current state and saves it to the database. 
			Params: $args = the data to be updated. 
			
	*/	
		
		public function save( $args = [] ){
			global $wpdb;
			
			if( empty( $args ) )
				$args = $this->prepare();
			
			$table = $wpdb->prefix."gate";
			
			if( empty( $this->gate_id ) )
				return new \WP_Error( 'no_id', __( "Attempting to update a gate without an id. Sorry!", PC_TD ) );
			
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
			$this->gate_id = $this->add();
			
			//dump( __LINE__, __METHOD__, $stored );
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
			
			$this->status = $status;
			
			$args = $this->prepare( [
				'status' => $status
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
				"SELECT * FROM {$wpdb->prefix}gate WHERE id = '{$id}' LIMIT 1;" ,
				"ARRAY_A"
			);
			
			$gate = $results[0];
			
			$this->gate_id = $gate[ 'id' ];
			unset( $gate[ 'id' ] );
			
			//Set Data
			$this->obj = json_decode( $gate[ 'data' ] , true );
			unset( $gate[ 'data' ] );
			
			//dump( __LINE__, __METHOD__, $gate );
			
			//all remaining data coming from DB should have a corresponding property. 
			foreach( $gate as $key => $val )
				$this->$key = $val;
			
			
			
			//Finish setting up properties found in the data. 
			$this->setup( $this->obj );
		}


	/*
		Name: set_obj
		Description: 
	*/	
		
		public function set_obj( $obj ){
			
			if( !empty( $obj ) )
				$this->obj = (array) $obj;
			
		}

	/*
		Name: get_obj
		Description: Returns the 'obj' property forced as an array. 
	*/	
		
		public function get_obj( ){
			
			return (array) $this->obj;
			
		}


	/*
		Name: get_id
		Description: Retrieve gate ID. 
	*/	
		
		public function get_id( ){
			
			return $this->gate_id;
			
		}


	/*
		Name: get_status
		Description: 
	*/	
		
		public function get_status(){
			
			return $this->status;
		}

	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}


	}//end of class
}