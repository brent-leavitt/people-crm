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
		
		//
		public 
			$patron = 0,
			$service = '',
			$token = '',
			$action = 'invoice',  //default is 'invoice'. 
			$data = [],
			$status = 'new'; //default is new
 
		private 
			$gate_id = 0,
			$reference_id = '',
			$obj;
			
		//Maybe we also need a date as a property? 
		
		
		private $data_map;						//(obj) Data map class 
		
		//Sets of action data needed to perform the requested action. 
		/*
		
			For example: if this is an invoice 
		
		
		
		*/
		
		/* private $default_data_map = [
		
		];			// 'nn_value' => '3rd_party_value'
		 */
		

		
		
		
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
		Name: prepare
		Description: 
	*/	
		
		public function prepare( $data ){
			
			$this->obj = $data
			$this->reference_id = $data[ 'data' ][ 'reference_id' ];
			
			$this->service = $data[ 'service' ];
			$this->token = $data[ 'token' ];
			
			
		}		

		
	/*
		Name: store
		Description: Sends gate info to database. 
	*/	
		
		public function store(){
			
			global $wpdb;
			
			$checks = [ 'timestamp', 'reference_id', 'action', 'obj', 'sent', 'table' ];
			
			/*
			
				'id' => 'mediumint(15) NOT NULL AUTO_INCREMENT',
				'timestamp' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
				'reference_id' => 'varchar(40) NOT NULL',
				'action' => 'varchar(10) NOT NULL',
				'data' => 'mediumtext NOT NULL',
				'sent' => 'varchar(15) NOT NULL',
			
			*/

			$table = $wpdb->prefix."gate";
			
			//Timestamp: where is this coming from? Time it was insert: NOW. 
			$timestamp = date( 'Y-m-d H:i:s' );
			
			//Reference ID: 
			$reference_id = $this->reference_id;
			
			//action: 
			$action = $this->action;
			
			//data obj
			$obj = json_encode( $this->obj );
			
			$sent = 'new'; //'new', 'hold', 'sent', or (ID of gate object that this belongs to)
			
			//$query = "SELECT * FROM {$table} WHERE event_id = {$id}";
			//if event has already been sent and stored: 
			//if( $wpdb->get_results( $query, OBJECT ) ) return;
			
			//Check that all the above values are not empty. 
			
			foreach( $checks as $check ){
				if( empty( $$check ) )
					 return new WP_Error( 'empty', __( "Attempting to send empty values to the database. Sorry!", PC_TD ) );
			}
			
			$data = [
				'timestamp' => $timestamp,
				'reference_id' => $reference_id,
				'action' => $action,
				'data' => $obj,
				'sent' => $sent,
			];
			
			return $wpdb->insert( $table, $data );
			
		}		
		
	
	/*
		Name: create
		Description: creates a new gate object from incoming data. 
	*/	
		
		public function create( $data ){
			
			//Prepare incoming data,  assign properties. 
			$this->prepare( $data );
			
			
			//store in database. 
			$stored = $this->store();
			
			
		}		

		
		
	/*
		Name: get_reference_id
		Description: 
	*/	
		
		public function get_reference_id(){
			
			return $this->reference_id;
			
		}


		
		
	/*
		Name: put_on_hold
		Description: This changes the status of the gate object in the database to "hold". 
	*/	
		
		public function put_on_hold(){
			
			
		}


		
		
	/*
		Name: get_gate_id
		Description: Returns the gate ID
	*/	
		
		public function get_gate_id(){
			
			return $this->gate_id;
		}
		
		
	/*
		Name: load_by_id
		Description: loads gate object by ID. 
	*/	
		
		public function load_by_id( $id ){
			global $wpdb;
			
			$table = $wpdb->prefix."gate";
			
			
			$query = "SELECT * FROM {$table} WHERE id = {$id} LIMIT 1;";
			
			$prepared = $wpdb->prepare( $query );
			
			$results = $wpdb->get_results( $prepared );
			
		}
		
		
	/*
		Name: save (same as "store" method?)
		Description: Take the gate object in its current state and saves it to the database. 
	*/	
		
		public function save(){
			global $wpdb;
			
			$query = array(
				'' => '',
				'' => '',
				'' => '',
				'' => '',
			);
			
			$wpdb->update( $query );

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