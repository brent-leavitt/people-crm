<?php 

/* 
people_crm\core\sub\Transaction

Transaction - Sub Core Class for People_CRM Plugin
Last Updated 12 Jul 2019
-------------

Desription: Service the similar interests of the Invoice and Receipt Classes

To Do: Store Transactions for printing purposes. 
Maybe move the source data to the post_exceprt field. 
Maybe move all other data from post_meta to post_content as JSON String? 

---

*/

namespace people_crm\core\sub;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'Transaction' ) ){

	class Transaction extends PostData{
		
		
		//Properties

		//What properties are universal to both receipts and invoices, only map those items that have a direct correlation to a post field. Everything else goes to meta, with same key name as the property name. 
		public 
			$Transaction_data_map = array(
				'trans_id' 		=> 'post_name',
				'trans_date'	=> 'post_date',
				'trans_status'	=> 'post_status',
				'trans_type'	=> '', 
				'amount_paid' 	=> '',
				'currency' 		=> '',
				'sales_tax' 	=> '',
				'subtotal' 		=> '',
				'net_amount' 	=> '',
				'discount' 		=> '',
				'payee_first_name' 	=> '',
				'payee_last_name' 	=> '',
				'payee_address' 	=> '',
				'payee_address1' 	=> '',
				'payee_city' 		=> '',
				'payee_state' 		=> '',
				'payee_zip' 		=> '',
				'payee_country' 	=> '',
				'payee_email' 		=> '',
				'payee_phone' 		=> '',
				'src_data' 		=> '',
			); 
			
		protected $meta_key = 'TransData';
					
		public $trans_id,
			$trans_date,
			$trans_status,
			$trans_type, 
			$amount_paid,
			$currency,
			$sales_tax,
			$subtotal,
			$net_amount,
			$discount,
			$payee_first_name, 	
			$payee_last_name, 	
			$payee_address, 	
			$payee_address1, 	
			$payee_city,
			$payee_state,
			$payee_zip,
			$payee_country,	
			$payee_email,
			$payee_phone,
			$src_data;
			
			
		//Methods
		
		
	/*
		Name: __construct
		Description: Commented out because this is the same as the parent class. 

				
		
		public function __construct( $data ){
			
			$this->init( $data );
		}	
	*/				
		
	/*
		Name: init
		Description: 
	*/	
				
		
		public function init( $data ){
			
			//Adds the transaction data map to the postData data map. 
			//We just need the next parent class. 
			$this->extend_data_map( key( class_parents( $this ) ) );
			//Then the current class (which will be Receipt or invoice)
			$this->extend_data_map( get_class( $this ) );
			
			
			
			//Assign incoming data to the data property for access. 
			//$this->data = $data; 
			
			//Asssign incoming data to respective and available properties. 
			$this->set_data( $data );
			
			if( method_exists( $this, 'set_src_data' ) ) $this->set_src_data();
			
			//If a post ID is set, retrieve the post. 
			if( !empty( $this->ID ) ){
				$this->retrieve();
			}
			
			//dump( __LINE__, __METHOD__, get_object_vars( $this ) );
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