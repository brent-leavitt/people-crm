<?php 
/*
people_crm\core\Enrollment

Enrollment  - Core Class for People_CRM Plugin
Last Updated 12 Jul 2019
-------------

Description: The purpose of the enrollment class is handle the collection of tokens assigned to an individual user.  

	
	//Additional functions to add?
	
	- get_token_by( $type, $var )
		//Types = ID, type,
		//only returns one token
	
	- get_tokens_by( $type, $var )
		//Types = service, date, status
		//returns an array of tokens
		
		
		
	- get_date
	
	When setting Tokens: 
	
		- See if the nb_service_tokens user meta is set? 
		- When Retiring tokens see if the nb_old_service_tokens is set first. 
		
		
		//what happens if a token is double registered for the same service? It should never be double registered? 
			- The same token for the same service will never exist at the same time for a user, based sole on those two bits of data. 
	
	What extra data is needed to create a new token?
		- token type, service,
		- Make New Token?
			
		
	When retrieving all exisitng tokens? 
		- Get All Tokens
		
	Check for Specific Token?
		- token_type, service
		
	WHen retiring a token? 
		- ID only?
		
		
---

*/


namespace people_crm\core;

use people_crm\core\sub\Token as Token;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'Enrollment' ) ){
	class Enrollment{
		
		//Properties
		
		public $patron_id = 0; 
		
		public $active_enrollments = [];
	
		public $inactive_enrollments = [];
		
		private $actions = []; //actions to be taken as a result of enrollments
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
		public function __destruct( ){
			
			//echo "I am deconstructing, see: " . __METHOD__ ;
			//first thing to do is to get the patron's current enrollments. 
			
			if( !empty( $this->active_enrollments  ) )
				$this->set( NN_PREFIX.'active_enrollments' );
				
			
			if( !empty( $this->inactive_enrollments  ) )
				$this->set( NN_PREFIX.'inactive_enrollments' );
			
		}
		
				
				
	/*
		Name: init
		Description: 
	*/
		
		private function init( $patron_id ){
			
			$this->patron_id = $patron_id; 
			
			//first thing to do is to get the patron's current enrollments. 
			
			
			if( !empty( $active_data = $this->get( NN_PREFIX.'active_enrollments' )  ) )
				$this->active_enrollments = $active_data;					
			
			if( !empty( $inactive_data = $this->get( NN_PREFIX.'inactive_enrollments' )  ) )
					$this->inactive_enrollments = $inactive_data;  
			
		}
		
		
		
	/*
		Name: get
		Description: This gets enrollment tokens from the database. 
	*/	
		
		
		
		private function get( $meta_key ){
			
			$data_set = [];
			
			$data_set = get_user_meta( $this->patron_id, $meta_key, true );			
			
			return ( !empty( $data_set ) )? $data_set : false; 
			
		}	
		
	/*
		Name: set
		Description: This saves enrollment tokens back to the database. 
	*/	
				
		private function set( $meta_key ){
			
			//echo "trying to store to database: ". __METHOD__;
						
			//get data set based off meta_key. 
			$data_key = substr( $meta_key, strlen( NN_PREFIX ) );
			
			$data = $this->$data_key;
			
			//print_pre( $data );
			update_user_meta( $this->patron_id, $meta_key, $data );
		}
		
	/*
		Name: add
		Description: This adds a new individual enrollment token 
		Params: $type, $service. 
	*/	
			
		
		public function add( $type, $service, $tdate ){
			
			//Remove old tokens of the same type and service. 
			//Also assessing if needs to be added or if it is the same. 
			$retired = $this->retire( $type, $service, $tdate );			
			
			//$retired can be 1 or -1, but not zero. If zero, it's the same as an existing token and is already set. 
			if( $retired !== 0 ){
				//what checks need to happen for this. 
				if( $token = new Token() ){
					
					//build token
					$token->build( $type, $service, $tdate );	
				
					$this->active_enrollments[] = $token;
					
					/* 
				
					if( !empty( $this->active_enrollments  ) )
						$this->set( NN_PREFIX.'active_enrollments' ); */
					
					return true; // Should we return true or the ojbect?
				}
			}
			return false;
			
		}
		
				
	/*
		Name: retire
		Description: Move token from active to inactive status. 
		
		return: 
			1 = retired successfully;
			0 = same, not retired; 
		   -1 = not set, not retired; 
	*/
			
		
		public function retire( $type, $service, $tdate ){
			
			/* $set = array( 
				'type' 		=> $type,
				'service' 	=> $service,
				'tdate' 	=> $tdate
			);
			 */
			
			//check if token is already set. 					
			foreach( $this->active_enrollments as $tkey => $token ){ //token is an object
				
				/* 
				foreach( $set as $skey => $sval )
					$continue = ( strpos( $token->get( 'type' ), $type ) === 0 )? true : false; */
				
				
				//if both are true token with the same type and service, exists. 
				if( ( strpos( $token->get( 'type' ), $type ) === 0 ) && ( strpos( $token->get( 'service' ), $service ) === 0 ) ){
					
					//If date is not the same as the requesting token... then proceed, else return 0. 
					if( strpos( $token->get( 'tdate' ), $tdate ) !== 0 ){
						
						//change status property to inactive
						$token->set_status( 'archived' );
						
						if( $token->status_is_( 'archived' ) ){
							
							//move to inactive enrollments. 
							$this->inactive_enrollments[] = $token;
							
							//check that it is there. 
							if( $this->is_token_set( $this->inactive_enrollments, $token ) ){
								
								unset( $this->active_enrollments[ $tkey ] );
								
								//Does this really need to be called again. Included just as a precautionary cleanup? 
								$this->retire( $type, $service, $tdate );
									
								return 1; //retired successfully;
								
							}	
						}
					}
					
					return 0; //same, not retired (doesn't need to be added either).
					
				}
			}//end foreach
			
			return -1; //not set, not retired
		}
	

	
	/*
		Name: expire
		Description: Set an active token to expired status. Expired tokens remain in active_enrolllments collection, until replaced with a new token. 
		
		Notes: not doing anyting to check the date for expiration purposes. May be helpful to do so. but would trans_date be the date of the original transaction or the expiration date. If expiration date, it would not match. 
	*/	
				
		
		public function expire( $type, $service, $tdate ){
			
			
			foreach( $this->active_enrollments as $key => $token ){ //token is an object
				if( ( strpos( $token->get( 'type' ), $type ) === 0 ) && ( strpos( $token->get( 'service' ), $service ) === 0 ) ){
					
					$token->set_status( 'expired' );
					
					if( $token->status_is_( 'expired' ) ) return true;
						
					
				}
			}	
			
			return false;
		}	
			
		
				
	/*
		Name: annul
		Description: To void a token. 
	*/	
				
		
		public function annul( $type, $service, $tdate ){
			
			foreach( $this->active_enrollments as $key => $token ){ //token is an object
				if( ( strpos( $token->get( 'type' ), $type ) === 0 ) && ( strpos( $token->get( 'service' ), $service ) === 0 ) ){
					
					$token->set_status( 'void' );
					
					if( $token->status_is_( 'void' ) ){
						
						//move to inactive enrollments. 
						$this->inactive_enrollments[] = $token;
						
						//check that it is there. 
						if( $this->is_token_set( $this->inactive_enrollments, $token ) ){
							
							unset( $this->active_enrollments[ $key ] );
							
							//$this->annul( $type, $service ); No recursion needed here. 
								
							return true;
							
						}	
						
					}
				}
			}	
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
		Name: get_actions
		Description: This is called in the Action::clean_up method. 
	*/	
				
		
		public function get_actions(){
			
			return ( !empty( $this->actions ) )? $this->actions : false ;
			
		}	
		
	/*
		Name: process
		Description: What needs to be done from enrollments with the data that has been received? 
	*/	
				
		
		public function process( $data ){
			
			//Quick check of data. 
			if( empty( $data ) ) return false;
			
			//dump( __LINE__, __METHOD__, $data );
			//DO = add, expire, retire, annul, add is default
			$do_action = ( strpos( $data[ 'action' ], 'enrollment' )  === 0  )? $data[ 'enrollment' ]['type'] : 'add' ;
			//ep( "The value of DO_ACTION is: ".$do_action );

			$result = $this->$do_action( $data[ 'token' ], $data[ 'service' ], $data[ 'trans_date' ] );
			
			//After thourough consideration, the only instance where do_service should not be called after do_enrollment is when a token is being retired. All other enrollment actions should require an update of service.
			if( ( $result != false ) && ( $do_action != 'retire' ) )
				$this->actions[] = 'do_service';
		
			return ( !empty( $result ) )? $result : false;
			
		}	
	

	
	/*
		//Get existing token sets
			//Based on submitted action from data, perform action
			//What checks need to be performed, per action? 
			
				//action is a payment, so probably going to add token. 
				//What additional checks to be performed? 
					
					- When a token is added. (Payment has been received.) 
					- Does user have expected services? 
					- Does user have expected role? 
					- Does user receive a notice? 
					
				
				
				
				- When a token is expired. (Expired tokens remain in active enrollments until replaced by new active token of the same value.)
					- Has connected service been modified to inactive
					- Does user role change because of the expired token status? 
					- Does user receive a notice because of this? 
					
				- When a token is retired. (Replaced by a newly added token)
					- ??? 
					
					
				- When a token is voided. (Refunds, Resets, So a token would be voided when it was prematurely terminated. Some tokens, such as newsletter subscriptions, when terminated will be voided. TOkens may or may not be replaced when voided, but there is usually not a payment associated with a voided token. Yet other transactions could cause a voided token.)
					- Similar to Expired?
						- Has a connected service been modified? 
						- Does user role change because of the expired token status? 
						- Does user receive a notice because of this? 
	*/
			
			
			
	/*
		Name: set_actions (DISABLED)
		Description: There are up to three actions which can be set: do_service, do_role, and do_notice. This function determines if each needs to be set. 
		
				
		
		public function set_actions( $info ){
			
			$actions = [];
			
			//DO_SERVICE? 
			$do_service = false;
			
			//if do = add, expire, or void, the service may need to be added or updated. 
				//Expire or Void necessitate a do_service call
				//Add is a call only if the service doesn't already exist. NOT SO. 
					//Meta connected to a service should be updated, like number of payment received. 
					
				//Does_Patron_Have_Service(), which needs action?
					 //Does the user have a post (service CPT) with with this meta data, and is it active? 
					
					
			$author = $this->patron;
			$post_type = 'Service'; //CPT
			$meta_key = 'Service'; //Metakey
			$meta_value = $service; //or whatever service we're checking
			$post_status = 'active';
			
			$myQuery = new WP_Query( "author=$author&post_type=$post_type&post_status=$post_status&meta_key=$meta_key&meta_value=$meta_value&fields=ids" );
			//Hard query to perform because it is dependent upon the database which is site specific. 
			
			//DO_ROLE?
			
			//if do = add, expire, or void, the role may need to be added or updated. 
				//what are the variables? Role only changes if service changes? 
				//Does role ever change if service does not change? if no, role is dependent upon service. 
			
			$do_role = false;
			
			
			//DO_NOTICE?
				//Initial check should be whether the action is of high enough priority to merit a notice. 
				//TO be assessed from here? Yes, only if do_service or do_role, is not added. 
			
			$do_notice = false;
			
			
		}	
	*/


		
	/*
		Name: 
		Description: 
	*/	
				
		
		public function __(){
			
			
		}	
		
		
	}//end of class
}
?>