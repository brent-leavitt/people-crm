<?php 

/*
people_crm\data\stripe\GateHandler

Gate Class for Stripe in the People CRM Plugin
Last Updated 16 Oct 2019
-------------

Description: This takes a batch of incoming data (a webhook) from the Stripe third party service and handles the logic of creating, merging, and preparing a gate object to be sent to the backend systems. 

//Steps: 

//Create Gate and then send back it's reference_ID. 

//Search for gates with similar reference_IDs. 

//Assess if this is the lowest ID. 

//If yes, does gate object have enough info to send? 
//If no, do nothing,
	//check if lower object is waiting for more data, marked as "hold"

//If not enough info, are there more objects that match? 

//If yes, attempt to merge those gate objects. 
//If no, mark as "hold".

//Merge two gate objects. 

//Mark merged object with primary objects id in the "status" field. 
//Check if primary object now has enough data to send.

		
*/
	

namespace people_crm\data\Stripe;

use \people_crm\core\Action as Action;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'GateHandler' ) ){
	class GateHandler{

	// PROPERTIES
		
		//
		private $gate_ids = [],
			$reference_id = '',
			$primary,
			$second;
		
		
		
	// METHODS	

	/*
		Name: __Construct
		Description: Incoming data has already been formatted according to the systems needs. Only thing lackin will be completeness of data. May be empty values. 
	*/	
		
		public function __construct( $data ){
			
			$this->init( $data );
			
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
		
		private function init( $data ){
			
			//Create Gate and then send back it's reference_ID. 
			$primary = new Gate();
			$primary->create( $data );
			
			//dump( __CLASS__, __METHOD__, $primary );
			
			$this->reference_id = $primary->get_reference_id();
			
			//Search for gates with similar reference_IDs. Returns a boolean which answer: are there other gate ids? 
			if( ! $this->search_by_reference_id() ){
				if( ! $this->is_ready( $primary ) )
					$primary->set_status( 'hold' );
			} 
				
			
			//Assess if this is the lowest ID.
			//Is Primary ID the lowest ID? 
			if( !$this->is_lowest_id( $primary->get_gate_id() ) ){
				
				$second = new Gate();
				$second->load_by_id( min( $this->gate_ids ) );
				
				//Merge data together.
				$this->merge_gates( $second, $primary );
				
				//Check if gate is ready to send
				if( $this->is_ready( $second ) )
					$this->send( $second );
				else
					$second->save();
				
			} else {
				//This is the lowest id. 
				
				if( ! $this->is_ready( $primary ) ){
					//attempt to merge with other gates. 
					$this->merge_all_gates( $primary );
					
				}else{
					$this->send( $primary );
					
				}
				
			}
			//Attempt send???
				
		}

		
	/*
		Name: search_by_reference_id
		Description: This looks for all transactions that have an identical reference_id and then adds them to the "gate_ids" property, and returns true if there are any other gate_ids
	*/	
		
		public function search_by_reference_id(){
			global $wpdb;
			
			$id = $this->reference_id;
			
			//Do database stuff. NEEDS TO BE TESTED. 
			
			$results = $wpdb->get_results( 
				"SELECT id FROM {$wpdb->prefix}gate WHERE reference_id = '{$id}';" 
			);
			
			$ids = [];
			
			foreach( $results as $result )
				$ids[] = $result->id;
				
			//if not empty send results (array of IDs) to gate_ids.
			$this->gate_ids = ( $ids )?? [];
		
			return !empty( $this->gate_ids ); //boolean;
			
		}	

		
	/*
		Name: is_lowest_id
		Description: checks to see if submitted ID is the lowest of all gate ids loaded to the object. 
	*/	
		
		public function is_lowest_id( $in ){
			
			if( !empty( $this->gate_ids ) )
				$lowest = min( $this->gate_ids );
			else
				return true;//assume lowest if nothing to compare. 
			
			return ( $in <= $lowest );//boolean
		}

		
	/*
		Name: merge_gates
		Description: takes two gate objects, merges data to the first submitted object, updates both objects with correct "sent" statuses. 
	*/	
		
		public function merge_gates( &$low, &$high ){
		
			//$h_arr =  $high->get_obj();
			
			
			$new_obj =  array_merge( $low->get_obj(), $high->get_obj() );
			$low->set_obj( $new_obj );			
			
			//Merge Top Level 'obj' data from the two submitted gate objects 
			/* $low_obj = $low->get_obj();
			
			foreach($low_obj  as $key => $val ){
				if( empty( $val ) ){
					$new_val =  $high->get_obj( $key );
					$low->set_obj( $new_val );
				}
			}
			
			//Merge Payee information
			foreach( $low_obj->data as $ )
			*/
			
			//$low->set_obj( array_merge( $low->get_obj(), $h_arr ) );
			
			//IMPORTANT: Assign the low gate's id to the high gate status, indicating that high gate is now contributed its data to the low gate id. 
			if( !empty(  $low_id = $low->get_id() ) )
				$high->set_status( $low_id );
			
			//Save higher gate, because it will not be altered furtehr. 
			$high->save(); 
		}

		
	/*
		Name: merge_all_gates
		Description: This takes the primary gate and calls all available gates to build out data. 
	*/	
		
		public function merge_all_gates( &$gate ){
			$available = $this->gate_ids;
			
			foreach( $available as $g_id ){
				$high = new Gate();
				$high->load_by_id( $g_id );
				
				$this->merge_gates( $gate, $high );
				
				if( $this->is_ready( $gate ) ){
					$this->send( $gate );	
					return;
				}
			}
			
			//if not sent, mark gate as on hold, and save it. 
			$gate->set_status( 'hold' );
			$gate->save();
			
		}
	
	
	/*
		Name: is_ready
		Description: This checks to see if the submitted gate object has enough data to be correctly processed on the back end. 
	*/	
		
		public function is_ready( $gate ){
			
			dump( __LINE__, __METHOD__, $gate );
			
			//Check if gate has patron, service, and token set. 
			$checks = [ 'patron', 'service', 'token' ];
			
			foreach( $checks as $check ){
				if( empty( $gate->$check ) ) 
					return false;
			}
				
			
			//Two arrays to check: the data array, and the nested payee array. 
			$data_checks = [
				'create_date',
				'currency',
				'gross_amount',
				'net_amount',
				'payee',
				'reference_id',
				'reference_type',
				'tp_id',
				'tp_name',
				'tp_type',
				'tp_user_id',
				'trans_status',
				'trans_descrip',
				'trans_fee',
			];
			
			$data = $gate->obj['data'];
			
			if( ! $this->checker( $data, $data_checks ) )
				return false; 
			
			$payee_checks = [
				'full_name',
				'first_name',
				'last_name',
				'address',
				'city',
				'state',
				'zip',
				'country',
				'email',
				'phone',
				'cc_type',
			];
			
			if( ! $this->checker( $payee, $payee_checks ) )
				return false;
			
			return true; //(boolean)
			
		}		
	
	
	/*
		Name: send
		Description: Sends the submitted gate object to the back end for processing. 
	*/	
		
		public function send( $gate ){
			
			$gate->save();
			$action = new Action( $gate->obj );
			
		}
	
	
	/*
		Name: checker
		Description: This checks that array submitted has the checks and that those checks are not empty. Returns boolean. 
	*/	
		
		public function checker( $arr, $checks ){
			
			foreach( $checks as $check ){
				if( empty( $arr[ $check ] ) ){
					dump( __LINE__, __METHOD__, $check. ' check is empty in this array: ' .$arr );	
					return false;
				}
			}
			
			return true;
		}	
		
	
	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}


	}//end of class
}