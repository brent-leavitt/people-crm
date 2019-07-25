<?php
/*

people_crm\init\Listener

Listener - An Initializing Class for NN Network Plugin
Last Updated on 15 Jul 2019
-------------



*/

namespace people_crm\init;	

//use people_crm\data\Stripe\WebHook as WebHook;
/* use nn_network\data\Stripe\Collect as StripeCollect; */


// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
	
if( !class_exists( 'Listener' ) ){
	
	class Listener{

		//Properties: 
		//Maybe do a nested array for each value set so that you can do a foreach loop on each set: 
			
		public $listeners = array(
			
			'baah' => array( 
					'694' => 'people_crm\data\Stripe\WebHook'
				),		
			'email' => array( 
					'drive' => 'unknown'
				),		
				
			
			/* 
			'key' => array(
					'code' => 'action'
				), 	 
			*/	
		);
		
		//Methods
		public function __construct(){
			
			//This allows us to use wordpress to handle QueryVars requests. 
			add_action( 'template_redirect', array( $this, 'queryVarsListener' ) );
			add_filter( 'query_vars',  array( $this, 'queryVar' ) );
	
		}	
	
	
	
		public function queryVar( $query_vars ) {
			
			foreach( $this->listeners as $listener => $arr ){
				$query_vars[] = $listener;
			}
			
			
			/* $query_vars[] = 'collect'; // For Stripe Payment Collections */
			//$query_vars[] = 'baah'; //For Stripe Webhook Listener
			//$query_vars[] = 'email'; // For Email Collections
			
			/* $query_vars[] = 'odd'; // For Cron Job Access */
			return $query_vars;
		}

		public function queryVarsListener() {
			
			foreach( $this->listeners as $listener => $arr ){
				foreach( $arr as $key => $action ){
					
					//Check that the query var is set and is the correct value.					
					if (isset($_GET[ $listener ]) && $_GET[ $listener ] == $key ){
						
						//echo "The $action has been heard!";
						$$action = new $action();
						exit;
					}
						
				}
			}
			
			
			
			
			
			//For Webhook Processing
		/* 	if (isset($_GET['baah']) && $_GET['baah'] == '694'){//Check that the query var is set and is the correct value.
				
				echo "I am called! Baaah!";
				$webhook = new WebHook();
				exit;
			}
			
			if (isset($_GET['email']) && $_GET['email'] == 'drive'){

				require_once( PC_PATH . ( 'func/email_opt.php' ) );
				//Stop WordPress entirely
				exit;
			} */
			
		/* 	if(isset($_GET['odd']) && $_GET['odd'] == 517){
				//Run NB Cron Tasks Such as invoicing and scheduled registration invites. 
				include "nb_crons.php";
				//Stop the rest of Wordpress. 
				exit;
			} */
		}
	}
}

?>