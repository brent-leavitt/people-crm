<?php 

/*
people_crm\core\sub\Data

Data - Sub Core for People_CRM Plugin
Last Updated 12 Jul 2019
-------------

Description: This takes all data send to the backend and send back only that part of the data needed for complete particular functions. 


---

*/

namespace people_crm\core\sub;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'Data' ) ){

	class Data{
		
			//Properties
		
		public $data = array();  		//storage for raw data
		
		public $valid = false; 			//Whether or not the submitted data has been validated and it is safe for use. 
		
		public $action = ''; 			//What specific action is this data being sent to perform.
		
		public $data_set = array();		 //The cleaned, finalized data set prepared for use with the action. 
		
		
		/*
			These properties are data_sets for specific actions. 
		*/
		
				
		private $enrollment_set = array(
				'action',
				'service',
				'token',
				'patron',
				'enrollment',
				'type',
			);
			
			
		private $invoice_set = array(
				'action',
				'service',
				'token',
				'patron',
				'issue_date',
				'due_date', 		
				'trans_descrip',		//Description of the Transaction
				'currency',			//Currency (only accepting USD)
				'amount', 			//Transaction Gross Amount
				'subtotal',		 	//Subtotal before taxes
				'sales_tax',		 	//Sales Tax
				'net_amount',		 	//Amount Collected After Fees
				'reference_ID',	 	//Reference ID
				'reference_type',		//Reference Type
				'tp_name', 			//ThirdParty Name, like "stripe" or PayPal 
				'tp_id', 				//ThirdParty Transaction ID
				'line_items',
				'src_data',
			);
				
			
		private $notice_set = array(
				'action',
				'service',
				'token',
				'patron',
				'type',
				'template',
				'template_vars',
			);
			
			
		private $patron_set = array(
				'action',
				'slug',
				'email',
				'login',
				'alt_email',
				'last_name',
			);

			
		private $receipt_set = array(
				'action',
				'service',
				'patron',
				'token',
				'trans_type',
				'trans_date',
				'trans_status',
				'trans_descrip',
				'currency',
				'amount',
				'trans_fee',
				'subtotal',
				'sales_tax',
				'net_amount',
				'reference_ID',
				'reference_type',
				'tp_name',
				'tp_id',
				'line_items',
				'payee_full_name',
				'payee_first_name',	
				'payee_last_name',		
				'payee_address',		
				'payee_address1',		
				'payee_city',		
				'payee_state',		
				'payee_zip',		
				'payee_country',		
				'payee_email',		
				'payee_phone',		
				'payee_type',		
				'payee_card',		
				'payee_exp',		
				'on_behalf_of',		
				'src_data',
			);

			
		private	$register_set= array(
				'action',
				'service',
				'token',
				'patron',
				'patron_full_name',
				'patron_first_name',		
				'patron_last_name',		
				'patron_address',		
				'patron_address1',		
				'patron_city',		
				'patron_state',		
				'patron_zip',		
				'patron_country',		
				'patron_email',		
				'patron_phone',		
				'patron_',		
			);
			
			//Data sets: payment, register, subscribe, invoice, patron, service, reminder
		
		//Methods
		
				
		
	/*
		Name: __construct
		Description: 
	*/	
				
		
		public function __construct( $data ){
			
			$this->init( $data );
		}	
				
		
	/*
		Name: init
		Description: 
	*/	
				
		
		public function init( $data ){
			
			$this->data = $data;
			
			//If first position array entry is init?
			$action = key( $data );
			
			if( ( strpos( $action, 'action' ) === 0 ) &&  !empty( $data[ $action ] ) ){
				
				//
				$this->action =  $data[ $action ];
				
				$isset = $this->set();
							
				//Is Data Valid, more checks? 
				if( $isset )
					$this->valid = true;
			}
			
			
			//return false;
		}	



	/*
		Name: set
		Description: propogates the data_set with its relevant action data. 
		
	*/	
		
		public function set(){
			//Get incoming data		
			$data = $this->data;
			
			//what action are we taking?
			$action = $this->action;
			
			//What data set are we taking action on. 
			$action_set = $action. '_set';
			
			//load keys from that data set. 
			$keys = $this->$action_set;
			//dump( __LINE__, __METHOD__, $data ); 
			
			//Now we start to build. 
			foreach( $keys as $key ){
				if( !empty( $data[ $key ] ) )
					$this->data_set[ $key ] = $data[ $key ];
				elseif( !empty( $data[ 'data' ] ) ){
					//looking deeper into the source arrays for data that matches the requesting field.
		
					//first check if field is available in top level of nested array. 
					if( !empty( $data[ 'data' ][ $key ] ) ){
						$this->data_set[ $key ] = $data[ 'data' ][ $key ];
						
					} else {
						
						//else look deeper by referencing the first word of the key to find it's associated array.  
						$pos = strpos( $key, '_'  );
						$sub_arr = substr( $key, 0, $pos );
						$sub_key = substr( $key, $pos );
						$sub_val = $data[ 'data' ][ $sub_arr ][ $sub_key ] ?? '';

						if( !empty( $sub_val ) )
							$this->data_set[ $key ] = $sub_val;

					}
				} else {
					$this->data_set[ $key ] = '';
					
				}
			}	
			//The final data set is specific to the initiating action being performed 
			//$this->data_set = $this->$action();
			
			return true;
		}
		




	/*
		Name: get
		Description: get the set data if it is not empty. 
	*/	
		
		public function get(){
			
			if( !empty( $this->data_set ) )
				return $this->data_set;
			
			return false;
		}



	/*
		Name: 
		Description: 
	*/	
		
		public function __(){
			
			
		}
		
	}
}	
	
/*TEST Data

$data = new NNData( ['action' => 'register',		//Register super action call. 
			'service' =>  'BDC', //
			'token' => 'subscription_free', //? Is there a token set?
			'register' => array(
				'patron' => array(
					'first_name' => 'Bob',			//
					'last_name' => 'Jones',			//
					'full_name' => 'Bob Jones',			//
					'email' => 'bob.jones@example.com',				//	
				)
			)] );
var_dump( $data->data_set );
echo $data->valid;


*/


?>