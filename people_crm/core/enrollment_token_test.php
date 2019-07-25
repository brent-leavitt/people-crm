<?php
echo "The token is set.";

use NNToken as Token; 

if( !class_exists( 'NNEnrollment' ) ){
	class NNEnrollment{
		
		//Properties
		
		public $patron_id = 0; 
		
		public $active_enrollments = [];
		
		public $inactive_enrollments = [];
		
		public $prefix = 'NN_';//NN_PREFIX;
		
		
		//Methods? 
		
				
	/*
		Name: __construct
		Description:
	*/			
		
		public function __construct( $patron_id ){
			
			$this->init( $patron_id );
			
			
		}
		
	/*
		Name: __destruct
		Description: 
	*/			
		private function __destruct( ){
			
			echo "I am deconstructing, see: " . __METHOD__ ;
			//first thing to do is to get the patron's current enrollments. 
			$prefix = $this->prefix;
			
			if( !empty( $this->active_enrollments  ) )
				$this->set( $prefix.'active_enrollments' );
				
			
			if( !empty( $this->inactive_enrollments  ) )
				$this->set( $prefix.'inactive_enrollments' );
			
		}
		
				
				
	/*
		Name: init
		Description: 
	*/
		
		private function init( $patron_id ){
			
			$this->patron_id = $patron_id; 
			
			//first thing to do is to get the patron's current enrollments. 
			$prefix = $this->prefix;
			
			if( !empty( $active_data = $this->get( $prefix.'active_enrollments' )  ) )
					$this->active_enrollments = $active_data;
			
			if( !empty( $inactive_data = $this->get( $prefix.'inactive_enrollments' )  ) )
					$this->inactive_enrollments = $inactive_data;  
			
		}
		
		
		
	/*
		Name: get
		Description: This gets enrollment tokens from the database. 
	*/	
		
		
		
		private function get( $meta_key ){
			
			$data_set = [];
			
			$data_set= get_user_meta( $this->patron, $meta_key, true );			
			
			return ( !empty( $data_set ) )? $data_set : false; 
			
		}	
		
	/*
		Name: set
		Description: This saves enrollment tokens back to the database. 
	*/	
				
		private function set( $meta_key ){
			
			echo "trying to stored to database: ". __METHOD__;
						
			//get data set based off meta_key. 
			$data_key = substr( $meta_key, strlen( $this->prefix ) );
			
			$data = $this->$data_key;
			
			update_user_meta( $this->patron_id, $meta_key, $data );
		}
		
	/*
		Name: add
		Description: This adds a new individual enrollment token 
		Params: $type, $service. 
	*/	
			
		
		public function add( $type, $service ){
			
			//Remove old tokens of the same type and service. 
			$this->retire( $type, $service );			
			
			//what checks need to happen for this. 
			if( $token = new Token() ){
				
				//build token
				$token->build( $type, $service );	
			
				$this->active_enrollments[] = $token;
				
				$prefix = $this->prefix;
			
				if( !empty( $this->active_enrollments  ) )
					$this->set( $prefix.'active_enrollments' );
				
				return true; // Should we return true or the ojbect?
			}
			return false;
			
		}
		
				
	/*
		Name: retire
		Description: Move token from active to inactive status. 
	*/
			
		
		public function retire( $type, $service ){
			
			//check if token is already set. 					
			foreach( $this->active_enrollments as $key => $token ){ //token is an object
				if( ( strpos( $token->type, $type ) === 0 ) && ( strpos( $token->service, $service ) === 0 ) ){
					
					//change status property to inactive
					$token->set_status( 'archived' );
					
					if( $token->status_is_( 'archived' ) ){
						
						//move to inactive enrollments. 
						$this->inactive_enrollments[] = $token;
						
						//check that it is there. 
						if( $this->is_token_set( $this->inactive_enrollments, $token ) ){
							
							unset( $this->active_enrollment[ $key ] );
						
							if( $check = $this->get_token_by( 'type', $type ) && ( strpos( $check->get( 'service' ), $service ) == 0 ) ){
								//recursion to clean out multiples of the same token. Should never happen, but here's the check. 
								$this->retire( $type, $service );
								
							} else {
								
								return true;
							}
							
						}	
					}
				}
			}//end foreach
			
			return false;
		}
		
							
	/*
		Name: is_token_set
		Description: Checks if a token is set within a given array. 
	*/
			
		
		private function is_token_set( $array, $token){
			
			foreach( $array as $check ){
				if( $check === $token )
					return true;
			}
			
			return false;
		}
	
	
	/*
		Name: get_token_by
		Description: Searchs in active enrollments for a given token based on type. 
		//Types = ID, type,
		Returns: only one token
	*/	
				
		
		public function get_token_by( $type, $var ){
			
			//check if type is 'id' or 'type'
			$check_arr = array( 'id', 'type' );
			
			
			if( !in_array( $type, $check_arr ))
				return $false;
			
			//echo __METHOD__." called!";
			
			foreach( $this->active_enrollments as $token ){
				
				//echo "in here<br/>";
				if( strpos( $token->get( $type ), $var ) == 0 )
					return $token;
				
			}
			
			return false; 
			
		}
		

	/*
		Name: get_tokens_by
		Description: 
		//Types = service, date, status
		Returns: an array of matching tokens
	*/
				
		
		public function get_tokens_by( $type, $var ){
			
			
			$check_arr = array( 'service', 'date', 'status' );
			
			if( !in_array( $type, $check_arr ))
				return $false;
			
			$final = [];
			
			foreach( $this->active_enrollments as $token ){
				
				if( strpos( $token->get( $type ), $var ) === 0 )
					$final[] = $token; 
				
			}

			return ( !empty( $final ) )? $final : false;
		}	

	/*
		Name: 
		Description: 
	*/	
				
		
		public function __(){
			
			
		}	
		
		
	}//end of class
}



if( !class_exists( 'NNToken' ) ){
	class NNToken{
		
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
				
		
		public function build( $type, $service ){
			
			//Set Type
			$this->set( 'type', $type );
			
			//Set Service
			$this->set( 'service', $service );
			
			//set date
			$this->tdate = microtime( true );
			
			//set status
			$this->status = 'active';
			
			//set id 
			$this->id = $this->random_id();//nn_random_id( 6 );
			
		}	
		
		
		
	/*
		Name: random_id
		Description: (MOVED TO NNDevTools, probably should be NNHelperTools)
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
			
			$args = ( 
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

echo "<br />Hello!";

$enroll = new NNEnrollment( 1 );

$enroll->add( 'subscription_free', 'CBL' );

$token1 = $enroll->get_token_by( 'type', 'subscription_free' );

echo "<pre>";
var_dump( $token1 );
echo "</pre>";
?>