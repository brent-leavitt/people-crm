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

//Mark merged object with primary objects id in the "sent" catagery. 
//Check if primary object now has enough data to send.

		
*/
	
namespace people_crm\data\Stripe\;

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
			
			$this->reference_id = $primary->get_reference_id();
			
			//Search for gates with similar reference_IDs. Returns a boolean. 
			if( ! $this->search_by_reference_id() ){
				if( ! $primary->is_ready() )
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
				if( $second->is_ready() )
					$second->send();
				else
					$second->save();
				
			} else {
				//This is the lowest id. 
				
				if( ! $primary->is_ready() ){
					//attempt to merge with other gates. 
					$this->merge_all_gates( $primary );
					
				}else{
					$primary->send();
					
				}
				
			}
			//Attempt send???
				
		}

	

		
	/*
		Name: search_by_reference_id
		Description: 
	*/	
		
		public function search_by_reference_id(){
			global $wpdb;
			
			$id = $this->reference_id;
			
			//Do database stuff. 
		
			//if not empty send results (array of IDs) to gate_ids.
			$this->gate_ids = ( $results )?? [];
			
			
		
			return !empty( $this->gate_ids ); //boolean;
			
			
		}	



		
	/*
		Name: is_lowest_id
		Description: checks to see if submitted ID is the lowest of all gate ids loaded to the object. 
	*/	
		
		public function is_lowest_id( $in ){
			
			$lowest = min( $this->gate_ids );
			
			return ( $in <= $lowest );//boolean
		}

		
	/*
		Name: merge_gates
		Description: takes two gate objects, merges data to the first submitted object, updates both objects with correct "sent" statuses. 
	*/	
		
		public function merge_gates( &$low, &$high ){
			
			$low->data = array_merge( $low->data, $high->data );
			
			//IMPORTANT: Assign the low gate's id to the high gate status, indicating that high gate is now contributed its data to the low gate id. 
			$high->status = $low->id;
			
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
				
				if( $gate->is_ready() )
					$gate->send();	
			}
			
			//if not sent, mark gate as on hold, and save it. 
			$gate->set_status( 'hold' );
			$gate->save();
			
		}

		
	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}


	}//end of class
}