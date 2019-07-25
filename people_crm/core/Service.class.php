<?php 

/* 
people_crm/core/Service

Services - Core Class for People_CRM Plugin
Last Updated 12 Jul 2019
-------------

Desription: - Actions related to service are housed here. 


---
- This really does initiate services, so for example, when a new certificate service get's registered, in the Certs LMS, funcitons need to be run that setup a Cert CPT/ service.  

- Some services are much light weight, like a newsletter subscription. 
	
- Service Actions


	- Services could be certification or subscription. (One-time or recurring)
		- Doula Certificates
		- Library Subscriptions
		- Newsletter, Email Subscriptions
		- Any Other Service that we decided to offer. 
		
	- Service is unique to each Patron, in that a patron can only subscribe to one instance of a given service.
		- For example, a Patron can subscribe to multiple types of Doula Certificates (BDC, FDC, PDC), but can only subscribe to one of each, and never more than one of each type. 
		- 
	- Based on incoming data, this class needs to be able to target a specific instance of a service and modify it, or if not available, create it. Can this happen alone? I think not. 
	
	- Use a find method to locate existing service instances. 
	
	
	- Most of the service functionality is handle within the Network Plugin. Only unique functionality should be handled via action hooks. 
		
		
	- Create a service
		- what service, what user
		
	- Update a service
		- what service, what update, what user
		
	- Suspend a Service 
		- what service, what user
		
	- Create, Update, or Suspend a Service on the Network
		- what action, what service, what site, what change?
		
	
	- Get, Set, and Unset functions (lower levels of abstraction for Create, Update, or Suspsend)
	
	Should the incoming data expressly state what is needing to happen, or should it be implied by the context of data being sent? 
	
	Much of what happens in here is based on the status of the service. 
	There are two types of services (lite( or access-based services) and training or certificate-based services)
	
	Access Based
		- Subscriptions
		- Newsletter
		- Coaching
		Access-based services have only on/off (active/expired) status.
	
	Certificate Based
		- Doula Training
		Status is more Involved: 
			- active 
			- inactive
			- completed
			- issued
			- expired
			- TBD (?)
			
	Hence, based on the nature of the service (access or certificate / recurring or one-time) more status actions could be performed if available. Should all be available here or only the simple ones? I think all, but still unsure. 
	
	
	This class will also need to be able to perform a quick check to see if a service has already been registered for by the user. 
	
	Maybe the "find" method already does that or can do that? 
	
		
*/

namespace people_crm/core;

//use Token as Token;


if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'Service' ) ){

	class Service{
		
	//PROPERTIES
		public $patron = 0;
		public $service = '';	
		public $service_id = 0;	
		public $status = 'active';
		/* public $token = ''; */
		//public $date = ''; //Needed? Why?
		
		private $process = '';
		private $cpt_handler;
		private $actions = [];
		
		
		//private $site = 0; 	//If '0', then current. 
		//private $type = 'access'; //access or certificate, needed? Instead of trying to figure this out here. send an action hook to the individual service for extra fields. 
		
		
	//METHODS
		  
		
	/*
		Name: __construct
		Description: 
	*/	
			
		
		public function __construct( $data ){
			
			//Check that both patron ID and Service are set. 
			//What action is to be taken on the service? 
			
			$this->init( $data );
			
		}
		
		
		
			
	/*
		Name: init
		Description: A call to setup a service for a given user based on parameters set. 
		
	*/	
			
		
		private function init( $data ){
			
			//Process incoming data for service details. 
			//Set Patron
			$this->patron = $data[ 'patron' ];
			
			//Set Service
			$this->service = $data[ 'service' ];
			
			//Set CPT Handler
			$this->cpt_handler = $this->get_cpt_handler();
			
			//Set process (do action)
			$this->process = ( strpos( $data[ 'action' ], 'enrollment' ) === 0  )? $data[ 'enrollment' ]['type'] : 'add' ;
			
			
			//Set Token 
			/* $this->token = $data[ 'token' ]; */
			
			
			/* 
			$init_array = [
				'site_id' => get_current_blog_id(),
				'service' => $this->service,
				'cpt_handler' => $this->get_cpt_handler(), //Is there a way to get this without a filter? 
			];
			
			
			$init_array = apply_filter( 'Service_init', $init_array ); 
			
			if( !empty( $init_array[ 'site_id' ] ) )
				$this->site_id =  $init_array[ 'site_id' ];
			
			if( !empty( $init_array[ 'cpt_handler' ] ) )
				$this->cpt_handler =  $init_array[ 'cpt_handler' ]; */
			
			//Get Site ID. 
			
			//run a filter of arrays here that sets parameters for site specific information. 
			/*
				like: 
					- what is the CPT handler for the service in question? 
					- what is the SITE ID for the service in question? 
					- 
			
			*/
			
		}	

		
		
	/*
		Name: find
		Description: This method allows inidividual site plugins to find a specific service based on the parameters sent, and send back an id or false on fail. 
	*/	
			
		
		public function find(){
			
			//Check that patron is already set. It may not be depending upon how it is accessed. 
			//What other checks need to be performed? 
			
			$author = $this->patron;
			$post_type = $this->cpt_handler; //CPT
			$meta_key = NN_PREFIX.'service'; //Metakey
			$meta_value = $this->service; //or whatever service we're checking
			//Is post status required to be 'active'? Not so!
			$post_status = $this->status;
			
			$find_query =  "author=$author&post_type=$post_type&post_status=$post_status&meta_key=$meta_key&meta_value=$meta_value&fields=ids";
			
			$find_query = apply_filters( 'Service_Find_Query', $find_query );	
			
			//ep( "The value of FIND_QUERY in the find function in the Service class is: <br> $find_query " );
			//What is the format of the returned value? An array of numbers. We want the first result, so key = 0. 
			$found_id = get_posts( $find_query );
			
			
			//Hard query to perform because it is dependent upon the database which is site specific.
		
			
			//Returns post ID or False on fail.
			//$service = apply_filter( 'Service_Find', $data );	
			
			$this->service_id = ( !empty( $found_id[ 0 ] ) )? $found_id[ 0 ] : 0 ;
			$this->status = get_post_status( $this->service_id );
			
			dump( __LINE__, __METHOD__, get_object_vars( $this ) );
			
			return ( !empty( $this->service_id ) )? true : false;
			
		}
		
		
		
	/*
		Name: create
		Description: Create a Service. 
		
		Is the creation of a service the same between access and certificate? 
		
		How is a service created? 
			Services are created based on CPT's that are specific to a site. 
				- for example, in the Certs LMS, a certificate CPT (a custom post) is the service that is to be created.
				- similarly, in the CBLibrary, a subscription CPT is the service that is to be created. 
				- in the People CRM, a newsletter CPT is another service that could be created. 
				(It is the service that allows access to content.)
				- What are the unifying features of these CPT across the network? 
					- All have a 'service_id' meta_data, such as 'CBL' or 'NWS' or 'BDC'
					
				- What are the unique features per site? 
					- Certs LMS ->certificates CPT have some custom meta fields like: 
						- number of payments
						- billing type
						- recert_number
						- recert_date
						- extensions
						
				
				
	*/	
			
		
		public function create( $status = 'active' ){
			
			//add_user_meta( $patron, $service, $date );
			
			//Build a Descriptive Title for the Service, for example: Birth Doula Certificate for Jane Smith 
			$post_title = $this->build_title(); 
			
			//build a descriptive slug for service, for example:  birth-doula-certificate-jane-smith
			$post_slug = strtolower( str_replace( ' ', '-', $post_title ) ); 
			
			
			$post_content = '';//Store history of the service. 
			
			$post_arr = [
				'post_author' => $this->patron,
				'post_title' => $post_title,
				'post_name' => $post_slug,
				'post_content' => $this->log_action( $status, 'created' ),
				'post_status' => $status,
				'post_type' => $this->cpt_handler,
				'meta_input' => array(
					NN_PREFIX.'service' => $this->service,
					//'(key)' => '(value)',
				),
				/* 
				'' => '',
				'' => '', 
				*/
				
			];
			
			//If this is a certificate (vs. access) add more meta_input vars. 
			
			$post_arr = apply_filters( 'Service_Create_Pre_Insert', $post_arr );
			
			$this->service_id = $service_id = wp_insert_post( $post_arr );
			
			//$result = apply_filters( 'Service_Create_Result', $this->service );
			
			do_action( 'Service_Create',  $service_id );
			
			array_push( $this->actions, 'do_role', 'do_notice' );
			
			return ( !empty( $service_id ) )? $service_id :  false ; //information about the created service. 
			
		}
				
			
	/*
		Name: update
		Description: This determines what action was taken with the most recent token, and how that should affect the status of the service in question. 
	*/	

		public function update( $status = '' ){
			
			//Service ID will be updated to reflect CPT ID if there has been an update to the service. 
			$service_id = 0;
			
			//if status is not expressly set, look at available values. 
			if( empty( $status ) ){
				
				//needed variables: current_status, enrollment_action = $this->process
				$current_status = $this->status;
				$do_action = $this->process;
				
				switch( [ $current_status , $do_action ] ){
					//$current_status_arr = [ 'active', 'inactive', 'completed', 'issued', 'expired' ];
					//$do_arr  = [ 'add', 'expire', 'retire', 'annul' ];
					
					case [ 'active', 'add' ]:
					case [ 'inactive', 'add' ]:
						$status = 'active';
						break;
					
					case [ 'active', 'expire' ]:
					case [ 'active', 'annul' ]:
					case [ 'inactive', 'expire' ]:
					case [ 'inactive', 'annul' ]:
						$status = 'inactive';
						break;
						
					case [ 'expired', 'add' ]:
					case [ 'issued', 'add' ]:
						$status = 'issued';
						break;
					
					case [ 'expired', 'expire' ]:
					case [ 'expired', 'annul' ]:
					case [ 'issued', 'expire' ]:
					case [ 'issued', 'annul' ]:
						$status = 'expired';
						break;

				}
			}
			
			if( !empty( $status ) ){
				$service_id = $this->update_status( $status );
				$this->status = $status;
			}
			
			//Add additional functionality
			do_action( 'Service_Update',  $service_id );

			//If successful, we'll return Service ID affected that was updated, else return false. 
			return ( $service_id !== 0 )? $service_id : false;
		}		
				
		
		
	/*
		Name: update_status
		Description: Updates the status of a service.
	*/	
		
		public function update_status( $status ){
			
			$status_arr = [ 'active', 'expired', 'training', 'inactive', 'completed', 'issued' ];
			
			//you may add a line to the post_content explaining the change. 
			if(  in_array( $status, $status_arr ) && !$this->check_status( $status ) ){
				
				$post_arr = array(
					'ID' => $this->service_id,
					'post_content' => $this->log_action( $status, 'updated' ),
					'post_status' => $status	
				);
				
				$service_id = wp_update_post( $post_arr );
				
				$uc_status = ucfirst( $status );
				
				do_action( "Service_Update_{$uc_status}", $service_id );
				
				array_push( $this->actions, 'do_role', 'do_notice' );
			}
			
			return ( isset( $service_id ) && !empty( $service_id ) )? $service_id : 0 ; //was the update request successfully? True or False.
		}
		
	/*
		Name: check_status
		Description: Checks the status of a service. If submitted status is the same as status set in the DB then return true. Else false. 
	*/	
			
		
		public function check_status( $status ){
			
			return ( strpos( $status, get_post_status( $this->service_id ) ) === 0 )? true : false ; 
		}


	/*

		Name: get_actions
		Description: 
	*/	
			
		
		public function get_actions(){
			
			return ( !empty( $this->actions ) )? $this->actions : false ;
			
		}		
		
		
			
	/*

		Name: log_action
		Description: This generates messages to be stored in the post_content field. This can be expanded to include multiple types of messages. 
		params: 
			$status = //whatever available status. 
			$action = //created, updated
		
	*/	
			
		
		public function log_action( $status, $action ){
			
			$content = ( $post = get_post( $this->service_id ) )? $post->post_content : '' ;
			
			$date = date( 'j-M-Y H:i:s' );
			$change = "<p>The service has been $action, and its status is set to '$status' on $date.</p>";
			
			$new_content = $content . $change; 
			
			return $new_content;
			
		}
			
		
			
	/*

		Name: build_title
		Description: This builds the title for the Service, a required element for saving a post. 

	*/	
			
		
		public function build_title(){
			
			$title = '';
			
			//Service Name
			$serv_opts = get_option( 'People_CRM_Services' );
			$s_name = ucwords( $serv_opts[ $this->service ] );
			$title .=  $s_name .' for ';
			
			//Patron Name
			$p = get_userdata( $this->patron );
			$p_name = $p->first_name. ' ' . $p->last_name;
			$patron_name .= ( !empty( $p_name ) )? $p_name : $p->username ; 
			$title .= ucwords( trim( $patron_name ) );
			
			return ( strlen( $title ) > 5 )? $title : 'Service' ;
		}
			
			
	/*

		Name: get_cpt_handler
		Description: This looks in the options table for a Custom Post Type (CPT) associate with the service being queried, so the 'service_cpt' option must be set first. 
		
		Option Table Var is 
		'service_cpts' = array(
			'(CPT_handler)' => array( '(service_handler)', '', '' ),
		);

	*/	

		public function get_cpt_handler(){
			
			$service_cpts = get_option( 'service_cpt' );
			$service = $this->service;
			
			foreach( $service_cpts as $cpt => $srvc_arr ){
				
				if( in_array( $service, $srvc_arr ) ){
					return $cpt; //Return the CPT (Custom Post Type). 
				}
			}
		}		
				
	/*

		Name: get_all
		Description: 
		returns an array of all services assigned to that user. 

	*/	
			
		
		public function get_all_status(){
			
			$args = array(
				'author'        =>  $this->patron,
				'post_type'     =>  $this->cpt_handler,
			);
			
			$s_arr = get_posts( $args );
			$final = [];
			foreach( $s_arr as $srvc )
				$final[ $srvc->ID ] = $srvc->post_status;
				
			return ( !empty( $final ) )? $final : NULL;
		}
		
		
	/*

		Name: 
		Description: 

	*/	
			
		
		public function __(){
			
			
		}
			

				
	/*
		Name: expire
		Description: When an enrollment token expires, so does its service unless it is renewed. 
		
			
		
		public function expire(){
			
			$service_id = $this->update_status( 'expired' );
			
			do_action( 'Service_Expire', $service_id );
			
			//$result = apply_filters( 'Service_Expire', $result );
			
			return; //Was the suspension successful? True or False. 
		}
	*/
				
	/*
		Name: renew
		Description: Some services can be suspended, or placed on hold. 
		
			
		
		public function renew(){
			
			$service_id = $this->update_status( 'active' );
			
			
			
			return ( !empty( $service_id ) )? $service_id : 0 ; //Was the status changed?
		}
	*/
		
	}

}

?>