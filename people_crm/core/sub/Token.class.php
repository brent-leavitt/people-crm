<?php 
/*
people_crm\core\sub\Token

Token - Sub Core Class for People_CRM Plugin
Last Updated 12 Jul 2019
-------------

Description: 
	
		//This handles the formatting of service tokens. 
		
		
		//add new token - 
		

	
	Does this handle just one token at a time?
		- Set Current Token
			- set properties for current token
		
	
	Definintions: 
		- token_set = the unique individual token stored for a particular user for a particular service. Formatted as an array with a unique ID and nested array which contains all relative data. 
		- token_id = the unique ID assigned to a token_set. 
		- token_type = the identifying string name for a type of token. This is the string value that is set in the 'name' field of the token_set. 
		- data = the collection of token_sets saved to either one of the meta_keys for tokens
		- meta_key = the string name of the meta_key field related to token storage, either 'nb_service_tokens' or 'nb_old_service_tokens'. 
		- service = the service associated with the token. 

		
		
		//MOVED FROM ENROLLMENT CLASS'
		- is_( $status );
			- is_active? - Is an enrollment token active? 
			- is_expired? - Is an enrollment token expired? 
				(If a token is not active, does that automatically make it expired? Visa Versa?)
				
			- is_archived? - Is a particular token archived? 
			
		- check_enrollment_status = get( 'status' ) fulfills this. 
		- 
		
		
		Enrollment Class is dependent upon Site-specific variables. 
		- The enrollment types defined for a site. 
		
		- expiration_date -> this is based on the site specific duration of the token_type and only exists in this space. 
		- 

		
		- Where is this needed?
	The token class would have the properties of a token: 
		- ID
		- Patron ID
		- Type
		- Date
		- Service
		- Status
		(additional properties for inactive tokens?)
	Methods: 
		- get
		- set
		- set_date
		- set_service
		- set_status
		- set_id


		

---

*/

namespace people_crm\core\sub; 

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'Token' ) ){
	class Token{
		
			
		private $id = 0;
		//public $patron = 0; //Maybe not needed.
		private $type = '';
		private $tdate = '';
		private $service = '';
		private $status = '';
		
		/* public $token_set_tmplt = array(
			0 => array(
				'type' => '',
				'date' => NULL,
				'service' => '',
				'status' => 'active'
			)
		); */
				
	/*
		Name: __construct
		Description: 
		
	*/			
		
		public function __construct(){
			
			$this->init();
		}	
			
				
	/*
		Name: init
		Description: 
	*/	
				
		
		public function init(){
			
			//$this->(); 
			 
		}	
		
		
	//SUPER ACTIONS 	
		
		
	/*
		Name: get
		Description: Get's a token's property if specified, otherwise returns Token ID. 
	*/		
		
		public function get( $prop = '' ){
			
			$value = $this->id;
			//get_token_set from active tokens.
			
			if( property_exists( $this, $prop ) )
					$value = $this->$prop;
			
			return $value;
		}	
		

		
	/*
		Name: set 
		Description: set a token property. 
	*/	
				
		
		private function set( $prop, $value ){
			
			if( property_exists( $this, $prop ) )
				$this->$prop = $value;	
			
			if( ( isset( $this->$prop ) ) && ( strpos( $this->$prop, $value ) === 0 ) ){
				
				return true;
			}
			
			return false;
		}	

		
	// END SUPER ACTIONS 


			
		
		
		
	/*
		Name: status_is_
		Description: Checks if a token has status and returns true or false. 
	*/	
				
		
		public function status_is_( $status ){
			
			if( strpos( $status, $this->status ) === 0 ){
				
				return true;
			}
			
			return false;
		}	
				
		
		
	/*
		Name: build
		Description: Propogates the new token with all needed property values. 
	*/	
				
		
		public function build( $type, $service, $tdate ){
			
			//Set Type
			$this->set( 'type', $type );
			
			//Set Service
			$this->set( 'service', $service );
			
			//set date
			$this->set( 'tdate', $tdate );
			//$this->tdate = microtime( true ); //old code as of 24 jan 2019
			
			//set status
			$this->status = 'active';
			
			//set id 
			$this->id = $this->random_id();//nn_random_id( 6 );
			
		}	
		
		
		
	/*
		Name: random_id
		Description: (MOVED TO DevTools, probably should be HelperTools)
	*/
				
		
		private function random_id( $lenght = 6, $prefix = 'NN_' ) {
				// uniqid gives 13 chars, but you could adjust it to your needs.
			if ( function_exists( "random_bytes" ) ){
				$bytes = random_bytes( ceil( $lenght / 2 ) );
			} elseif ( function_exists( "openssl_random_pseudo_bytes" ) ) {
				$bytes = openssl_random_pseudo_bytes( ceil( $lenght / 2 ) );
			} else {
				throw new Exception( "no cryptographically secure random function available" );
			} 
			return $prefix . substr( bin2hex( $bytes ), 0, $lenght );
		}
				
	
//HELPER FUNCTIONS
		
	/*
		Name: get_timestamp
		Description: Get formatted timestamp;
	*/	
				
		
		public function get_datetime(){
			
			$time = $this->tdate;
			
			return date("Y-m-d H:i:s", $time);
		}


		
	/*
		Name:  set_status
		Description: 
	*/
				
		
		public function set_status( $status ){
			
			$args = array( 
				'active',
				'expired',
				'archived',
			);
			
			if( in_array( $status, $args ) ){
				
				$result = $this->set( 'status', $status );
				
				return $result;
			}
			
			return false;
			
		}	
	}//end class
}







?>