<?php 

/*
people_crm\data\stripe\Gate

Gate Class for Stripe in the People CRM Plugin
Last Updated 8 Oct 2019
-------------

Description: This takes a batch of incoming data from the Stripe third party service and compiles into one group of data for use in the system's backend actions. 

//Steps for building a gate object. 

//Collect incoming data. 

//Assess if this matches (or if the information is a part of) an existing gate object. 
	-- what are key matching elements? 
		- invoice_id 
		- cus_numbers
		- email_address
		- time_stamp 

//If no matching gate objects, create a new gate object. 

// -- The challenge is to not allow the gate object to be pulled more than once. --//
	-- 
	
	
//lock the gate object. 
	-- if not locked, lock, Then check for lock again. If locked and expected to be locked. Pull for edit. 
	-- if locked, and expected to be unlocked (first access) don't touch the gated object. Die. 

//Work on the gate object. 
	-- Add needed relevant information.
	-- record webhook_id to gate_object. 
	
//Assess if required information is sufficient to send the gate object. 
	If sufficient, 
		-send gate object.-
		- Record as sent. 
		- Sent back to database. 
	if not sufficient, 
		-check for other available data. (Because it's possible that other data was sent while working on the current data.)
		-unlock and store gate object. 

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
			$action = '';		
		
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
			
			
			
			//End result is: mapped data ready to be taken for use. 
			//$this->get_formatted_data();
			
			//dump( __LINE__, __METHOD__, $this->out );
		}

	

		
	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}


	}//end of class
}