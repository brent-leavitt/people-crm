<?php 

/*  
people_crm\core\Receipt

Receipt - Core Class for People_CRM Plugin
Last Updated 12 Jul 2019
-------------

Description: - Receipt issuing is controlled here. Extends Transaction Sub Class


*/

namespace people_crm\core;

use people_crm\core\sub\Transaction;

if( !class_exists( 'Receipt' ) ){
	class Receipt extends Transaction{
		
		//Properties	
		
		public $post_type = NN_PREFIX.'receipt';
		
		public $Receipt_data_map = array(
			'invoice_id'		=> 'post_parent',
			'trans_fee' 		=> '', 
			'reference_type' 	=> '',
			'tp_name' 			=> '',
			'tp_id' 			=> '',
			'payee_type' 		=> '',
			'payee_card' 		=> '',
			'payee_exp' 		=> '',	
		);
		
		public $invoice_id,			$trans_fee,			$reference_type, 	
			$tp_name,			$tp_id,			$payee_type,			$payee_card,			$payee_exp;
				//Methods
		
		
	/*  MAY NOT BE NEEDED IF IT IS THE SAME AS PARENT CLASS

		Name: __construct
		Description: 
		
				
		
		public function __construct( $data ){
			
			$this->init( $data );
		}	
				
	*/	
	/*  
		Name: init
		Description: 
		
				
		//Is this needed, or does the parent class cover the setup. 
		public function init( $data ){
			//copy from parent class. 
			$this->set_data( $data );
			
			
			//Unique to Receipt class.
			$this->post_type = 'Receipt';
		}	
				
	*/	
					
	/*
		Name: issue
		Description: This generated a Receipt CPT for storage in the database as a receipt. What is unique to Receipts from Invoices? 
		
	*/	
				
		
		public function issue(){
			
			$result = $this->insert();
			//Necessary? Yes. 
			
			//set additional actions only if the receipt was successfully added. 
			if( $result != false )
				$this->set_actions( [ 'do_enrollment', 'do_role', 'do_notice' ] );
			
			return ( $result )? $this->ID : false ; //Returns the receipt CPT ID. 
		}	
		

		
	/*
		Name: set_src_data
		Description: 
	*/	
				
		
		public function set_src_data(){
			dump( __LINE__, __METHOD__, $this->data );
			$this->src_data = $this->data[ 'src_data' ]?? false;
			if( $this->src_data !== false )
				unset( $this->data[ 'src_data' ] );
			//dump( __LINE__, __METHOD__, $this->src_data );
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