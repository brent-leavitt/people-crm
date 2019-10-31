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
			$reference_id = '';
		
		
		
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
			
			$this->reference_id = $primary->get_reference_id();
			
			//Loads all related IDs. 
			$this->search_by_reference_id();
			
			
			//Assess if this is the lowest ID.
			//Is Primary ID the lowest ID? 
			if( !$this->is_lowest_id( $primary->get_gate_id() ) ){
 
				$second = new Gate();
				$second->load_by_id( min( $this->gate_ids ) );
				
				//Merge data together.
				$this->merge_gates( $second, $primary );
				
				//See if you can send lowest. 
				if( $this->is_ready( $second ) ){
					dump( __LINE__, __METHOD__, "Second is ready! prepping to send." );
					$this->send( $second );
					return;
					
				}else{
					//If not, save primary and secondary. 
					
					dump(  __LINE__, __METHOD__, "SECOND is not ready! Setting status to hold." );
					$second->set_status( "hold" );
					return;
				}
			}
			
			
				
			if( ! $this->is_ready( $primary ) ){
				
				//attempt to merge with other gates. 
				dump(  __LINE__, __METHOD__, "Primary is not ready! ...Saving." );
				$primary->save();

				//$this->merge_all_gates( $primary );
				
			}else{
				
				dump(  __LINE__, __METHOD__, "Primary is ready!" );
				$this->send( $primary );
				
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
			
			if( !empty( $this->gate_ids ) ) //This should never be empty. 
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
			
			//check if low gate status is already sent. 
			$low_status = $low->get_status();
			
			if( $low_status !== 'sent' ){
				
				$low_obj = $low->get_obj();
				$high_obj = $high->get_obj();
			
				$new_obj =  $this->merge_data( $low_obj, $high_obj );
				$low->set_obj( $new_obj );		
					
			}
			
			//IMPORTANT: Assign the low gate's id to the high gate status, indicating that high gate is now contributed its data to the low gate id. 
			if( !empty(  $low_id = $low->get_id() ) )
				$high->set_status( $low_id );
			
		}
	
	/*
		Name: is_ready
		Description: This checks to see if the submitted gate object has enough data to be correctly processed on the back end. 
	*/	
		
		public function is_ready( $gate ){
			
			$obj = $gate->get_obj();
			
			//Check if gate has patron, service, and token set. 
			$checks = [ 'patron', 'service', 'token' ];
			
			foreach( $checks as $check ){
				if( empty( $obj[ $check ] ) ) {
					/* dump( __LINE__, __METHOD__, "failed at first check: $check " );
					 */
					return false;
				}
			}
				
			
			//Two arrays to check: the data array, and the nested payee array. 
			$data_checks = [
				'create_date',
				'currency',
				'gross_amount',
				'payee',
				'reference_id',
				//'reference_type',
				'tp_id',
				'tp_name',
				//'tp_type',
				'tp_user_id',
				'trans_status',
				//'trans_descrip',
			];
			
			if( ! $this->checker( $obj[ 'data' ], $data_checks ) ){
/* 				dump( __LINE__, __METHOD__, "failed at 2nd check." );
				dump( __LINE__, __METHOD__, $gate ); */
				return false; 
			}
			
			$payee_checks = [
				'full_name',
				//'first_name',
				//'last_name',
				'address',
				'city',
				'state',
				'zip',
				'country',
				'email',
				//'phone',
				'cc_type',
			];
			
			$payee = $obj[ 'data' ][ 'payee' ]; //Payee array. 
			
			if( ! $this->checker( $payee, $payee_checks ) ){
/* 				dump( __LINE__, __METHOD__, "failed at 3rd check." );
				dump( __LINE__, __METHOD__, $gate ); */
				return false;
			}
			
			return true; //(boolean)
			
		}		
	
	
	/*
		Name: send
		Description: Sends the submitted gate object to the back end for processing. 
	*/	
		
		public function send( &$gate ){
			
			$status = $gate->get_status();
			
			if( $status !== 'sent' ){
				
				$gate->set_status( 'sent' );
				$gate->save();
				$action = new Action( $gate->get_obj() );
				
			}
			
		}
	
	
	/*
		Name: checker
		Description: This checks that array submitted has the checks and that those checks are not empty. Returns boolean. 
	*/	
		
		public function checker( $arr, $checks ){
			
			foreach( $checks as $check ){
				if( empty( $arr[ $check ] ) ){
					dump( __LINE__, __METHOD__, $check );	
					return false;
				}
			}
			
			return true;
		}	
		
	
	/*
		Name: merge_data
		Description: This merges data from two sets, ignoring already set and empty values. Method is recursive. 
	*/	
		
		public function merge_data( $low, $high ){
	
			foreach( $low as $key => $val ){
				if( !is_array( $val ) ){
					if( empty( $val ) && !empty( $high[ $key ] ) )
						$low[ $key ] = $high[ $key ];
				}else{
					$low[ $key ] = $this->merge_data( $low[ $key ], $high[ $key ] );
				}
				
			}
			
			return $low;
		}
		
		
		
	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}


	}//end of class
}