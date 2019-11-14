<?php 

/* 
people_crm\core\Action

Actions Automation - Code Plan for NBCS
Last Updated 12 Jul 2019
-------------

Description: This is the Action Automation business logic for the Network Plugin

A class that takes direction from listeners or users as to what system actions to take.
	-It figures out if the command was initiated by a user or automated response. 
	-It performes what kind of actions?

Action is the master class of all core classes. 
	- As such, it records and reports to the record class. 
		- Starts Recording actions in the constructor method
		- Ends recording and reports to the record class in the destruct method.
		
*/
	
namespace people_crm\core;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'Action' ) ){
	class Action{

	// PROPERTIES
		
		//This is to record the action steps taken. 
		public $record = array();
		
		public $patron = 0; 

		public $data = array();
		
		private $actions = array();
		
		public $error = array();

	// METHODS	

	/*
		Name: __Construct
		Description: Incoming data has been formated by the /data/Format class. If is thirdparty data it is also gated before being sent to the backend.
	*/	
		
		public function __construct( $data ){
			
			$this->init( $data );
			
		}	
		

	/*
		Name: __Destruct
		Description: 
	*/	
		
		public function __destruct(){
			
			//The last thing this does is send action data to the record keeper. 
			$this->do_record();
			
		}	
		



	/*
		Name: Init
		Description: 
	*/	
		
		private function init( $data ){
			
			//set data to class property
			if( $this->check_data( $data ) ){
				
				//Set super action: 
				//Is the action that is being called set?
				if( isset( $data[ "action" ] ) /* && is_callable( array( $this, $data[ "action" ] )  ) */){
					$super = $data[ "action" ];
					
					//A simple registration must be processed before any payments can be accepted on our website. Do automatically if not explicit.
					
					//Is this a registration?
					if( strpos( 'register', $super ) === 0 ){						
						$this->register();
						return;
					} 
					
					//If not registration, get patron from submitted data. If no patron is found, then we register. 
					if( $this->do_patron( 'find' ) ){
						if( $this->patron === 0 )
							$this->register();
					}
					
					//As long as a patron ID has been set at this point, we're good to continue. 
					if( $this->patron != 0 ){
						//Set the primary action to be taken. 
						$this->actions[] = 'do_'.$super;
						
						//Process the actions. 
						$this->actions();
						return;
					} 
				} 
				
				//if Not registration and no patron is registered, 
				$this->do_admin_warning( 'The user is not on file. May be illegal.' );	
				
			}
		}	
		


	/*
		Name: actions
		Description: This is a very powerful step. This loops through all set actions in the action paramater, and processes each accordingly. 
	*/	
		
		public function actions(){
			
			$actions = [ 'invoice', 'receipt', 'enrollment', 'service', 'role', 'notice' ];
			$record = array();
			
			foreach( $actions as $action ){
				
				$do = 'do_'.$action;
				
				if( in_array( $do, $this->actions ) )	
					$record[ $do ] = $this->$do();		
			}			
			
			$this->record_step( __METHOD__ , $record);
			
		}	
			
	/*
		Name: register
		Description: 
	*/	
		
		public function register(){
			
			$record = array();
			
			//do register
			$record[ 'register' ] = $this->do_patron( 'register' );
			
			//do notice
			if( in_array( 'do_notice', $this->actions ) )
				$record['do_notice'] = $this->do_notice();
			
			$this->record_step( __METHOD__ , $record );
		}
		
	/*
		Name: Check Data
		Description: Validates the data being passed. This requires that the data array being sent has 'action' key as first element. If this element is not set, data is seen as invalid. 
		
		That data that is being "returned" to the '->data' property is a flattened array of data for the specfic action to be taken. At this point, we are not preparing the data to insert to the database, not yet. Got to make it uniform first. 
		
		//Do we want to do a real filter/sanitize? 
	*/	
		
		private function check_data( $data ){
			
			//Data is set and is made uniform.
			$data_set = new sub\Data( $data );
			

			if( $check = $data_set->valid ){
				$this->data = $data_set->get();//Returns an array (not object)
				//dump( __LINE__, __METHOD__, $this->data );
			}		
			return $check;

		}	
	
		
		
		
	/*
		Name: clean_up
		Description: Ever do_(action) had two clean_up steps at the end of the function: record action taken, and add additional actions. We are doing these things here. 
	*/	
		
		public function clean_up( $func , $record = array(), $obj ){
			
			//Possibly add a timestamp here or in "do_record"
			
			//Store actions taken and their results. 
			$this->record_step( $func , $record );
			
			//What additional actions need to be taken? Ask the object if they have anything else to do. Then merge with existing list of to dos. 
			if( method_exists( $obj, 'get_actions' ) ){
				if( ( $obj_actions = $obj->get_actions()  ) !== false ){
					$this->actions = array_unique( array_merge( $this->actions, $obj_actions ) );
				}
				
			}
				
			
			//dump( __LINE__, __METHOD__, $this->actions );
		}	
		

	/*
		Name: Record Step
		Description: This is separate from clean_up function because it is also called elsewhere. 
	*/	
		
		public function record_step( $func , $data = array() ){
			
			//This could end up being quite bloated. 
			$this->record[] = array( $func => $data ); 
			
			//dump( __LINE__, __METHOD__, $this->record );
		}	
		
		
		
	// CORE ACTIONS	

	/*
		Name: do_patron
		Description: Process a patron action and then return the patron ID. 
		param: $action = 'find', 'register', 'reglite'
		//THIS NEEDS WORK.
		returns: true or false
	*/	
		
		public function do_patron( $action ){
			
			//The Patron is an extension of WP_User, so 
			
			$patron = new Patron( $this->data );
			
			if( ( $patron->$action() ) != false ){
				
				$this->patron = $patron->id;
				
			} elseif( $patron->error != false ) {
				
				//process error messages. 
				$this->error[ 'patron' ] = $patron->err_msg;
				
			}
			
			$record = 'The patron ID is: '. $this->patron . ', and the action performed was '. $action ;
			
			$this->clean_up( __METHOD__ , $record, $patron );
		
			return ( $this->patron > 0 )? true : false ;
			
		}	
		
		
	/*
		Name: do_invoice
		Description: This takes in invoice data and processes it accordingly. It needs to assess whether this is a create, update, delete, or remind. 
	*/	
		
		public function do_invoice(){
		
			$record = array();
			
			//Let's make a data handler object. 
			//This may not be needed now. This is preprocssed. 
			/* $data = new Data( $this->data );
			$invoice_data = $data->get_invoice_data(); */
			
			$invoice = new Invoice( $this->data );
			
			dump( __LINE__, __METHOD__, $invoice );
					
			//Check that action is set?
			if( !empty( $invoice->action ) )
				$record[ 'invoice_process' ] = $invoice->process();
		
			$this->clean_up( __METHOD__ , $record, $invoice );
			
			return ( !empty( $record ) )? true : false;
		}	
		
	/*
		Name: do_receipt
		Description: 
	*/	
		
		public function do_receipt(){
			
			$record = array();
			
			$receipt = new Receipt( $this->data );
			
			$record['receipt_issue'] = $receipt->issue(); //Returns a post ID for the receipt generated. 
			
			//if( !empty( $record[ 'receipt_id' ] ) )
				//$record[] = $this->do_notice( $record[ 'receipt_id' ] );
			dump( __LINE__, __METHOD__, $record );
			
			$this->clean_up( __METHOD__ , $record, $receipt );
			
			return ( !empty( $record ) )? true : false;
		}	

		
	/*
		Name: do_enrollment
		Description: Sets or updates an enrollment token. 
	*/	
		
		public function do_enrollment(){
			
			$record = array();
			
			$enrollment = new Enrollment( $this->patron );			
			
			//add, expire, annul, or retire enrollment token. Anything else? 
			$record[ 'do_enrollment_process' ] = $enrollment->process( $this->data );
		
			
			$this->clean_up( __METHOD__ , $record, $enrollment );
			
			return ( !empty( $record ) )? true : false;
		}	
		
	/*
		Name: do_service
		Description: Sets or updates a service. Services Can be called to cancel as well. 
		
	*/	
		
		public function do_service(){
			
			$record = array();
			
			//Receives Data with instruction on what to do. 
			$service = new Service( $this->data );
			
			//if service doesn't exist, let's create it. 
			if( !$service->find() ){
				
				$record[ 'do_service_create' ] = $service->create();
				
			//else if the service exists this must be an update to the service. 
			}elseif( isset( $service->service_id ) ){
				
				$record[ 'do_service_update' ] = $service->update();
				
			}
			 
			do_action( 'Action_do_service', $service );
			
			$this->clean_up( __METHOD__ , $record, $service );
				
			return ( !empty( $record ) )? true : false;
		}	

		
	/*	
		Name: do_role
		Description: 
	*/	
		
		public function do_role(){
			
			$record = array();
			
			$role = new Role( $this->data );
			
			//Send the role object to the the respective service to adjust rules according to each service. 
			do_action( 'Action_Do_Role', $role );
			
			
			/* 	
			
			/Available Role Actions: add, remove, set
			$actions_arr = [ 'add', 'remove', 'set' ];
			
			if( in_array( $action, $actions_arr ) ){
				
				$role_action = $action. '_role';
				$record[ 'role_action' ] = $patron->$role_action( $role );
			} */
			
			//After individual sites have used Role object to update patron roles, post a report to the record.
			$record[ 'do_role_report' ] = $role->report();
			
			$this->clean_up( __METHOD__ , $record, $role );
			
			return ( !empty( $record ) )? true : false;
		}	
		
		
	/*
		Name: do_notice
		Description: This allows for multiple notices to be sent from the results array. 
	*/	
		
		public function do_notice(){
			
			//What information needs to be sent for a notice to be effectively processed?
			
			$record = [];
			
			//Let's make a data handler object. 
			/* 
			
			$data = new Data( $this->data );
			$notice_data = $data->get_notice_data(); 
			$notices = $data->get_notices(); //returns an array of all notices to be sent. 
			
			*/
			 
			 
			//$notice = new Notice();
			//$notice->send_data( $this->data );
			
			/* 
			
			foreach( $notices as $message_slug ){
				$notice = new Notice( $notice_data );
				$record[] = $notice->send( $message_slug );
			}
			
			*/
			 
			$notice = new Notice( $this->data );
				
			$record['notice_send'] = $notice->send();
			 
			$this->clean_up( __METHOD__ , $record, $notice );
			
			return ( !empty( $record ) )? true : false ; //true or false for success status. 
		}	
		
		
	/*
		Name: Do Record
		Description: 
	*/	
		
		public function do_record(){
			
			$record = new Record( $this->patron, $this->record );
			
			//Send admin warning if fails to record: 
			
			if( is_wp_error( $record ) )
				$this->do_admin_warning( $msg );
			
		}
		


	/*
		Name: do_admin_warning
		Description: This will only print to screen when in development mode. 
	*/	
		
		public function do_admin_warning( $msg ){
			
			//Add DevTool for sending admin notices.
			nn_admin_notice( $msg );
		}
		

	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}
		
	}
}

?>
