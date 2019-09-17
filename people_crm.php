<?php
/*
Plugin Name: People CRM
Plugin URI: https://tech.trainingdoulas.com/
Description: The Client Relationship Management tool for educational based website. This handles all the billing and other aspects of client acccounts in a separate site away from the LMS or Library sites on the same network. This plugin is dependent upon the NN Network plugin. 
Version: 1.0
Author: Brent Leavitt
Author URI: https://tech.trainingdoulas.com/
License: GPLv2

*/

namespace people_crm;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !defined( 'PC_PATH' ) )
	define( 'PC_PATH', plugin_dir_path( __FILE__ )  );

if( !defined( 'PC_TD' ) )
	define( 'PC_TD', 'people_crm' );	//Plugin text domain. 


if( !class_exists( 'People_CRM' ) ){
	class People_CRM{
		
		public function __construct(){			
			
			$this->autoload_classes();
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'admin_init', array( $this, 'admin_init' ));
			add_action( 'admin_menu', array( $this, 'admin_menus' ));
			
		}
		
		
		public function init(){
		
			$listener	 = new init\Listener();		//Add Query Vars Listeners
			//$shortcodes	 = new init\ShortCodes();		//Shortcodes	
			/*	$email_settings = new init\Email();		//Email Settings
				 */
			
			$roles = new init\Roles();
			
			//setup Custom Post Types
			$this->set_cpts();
			
			//Setup Menus
			$menus = new init\Menu();
			
			
			//Crons schedule.
			//$cron = new init\Cron();
			//$cron->schedule();
			
			//setup activation and deactivation hooks
			register_activation_hook( __FILE__, array( $this, 'activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		
		}
		
		public function admin_init(){
			//$settings = new init\Settings(); //Add a settings page
			//$settings->init();
		}
		
		
		public function admin_menus(){
			
			//Define Menus to add. 
			$args = array(
			/* 		'education' => array(
						'current_submissions',
						'my_students',
						'assignments',
						'coaching_schedule',
						'certificate_generator',
					),
			*/
				'settings' => array( PC_TD )
			);
			
			//$menus = new init\AdminMenu( $args, 'add' ); //Add a settings page
			
		}
		
		//Add Custom Post Types
		public function set_cpts(){
			
			$cpts = new init\CPT();
			$cpts->setup();
			
		}

		//Remove Custom Post Types
		public function remove_cpts(){
		
			$cpts = new init\CPT();
			$cpts->remove();
		
		}
		
		public function set_caps(){
			$caps = new init\CPT();
			$caps->set_caps();
			
		}
		
		
		public function set_crons(){
			$cron = new init\Cron();
			$cron->init();
			
		}		
		public function stop_crons(){
			$cron = new init\Cron();
			$cron->remove_cron();
			
		}
		
		
		
		private function autoload_classes( ){
			
			//This loads all classes, as needed, automatically! Slick!
			
			spl_autoload_register( function( $class ){
				
				$path = substr( str_replace( '\\', '/', $class), 0 );
				$path = __DIR__ .'/'. $path . '.class.php';
				
				if (file_exists($path))
					require $path;
				
			} );
		}
		
		
		/* 
		
		//Are there any parameters that need to be setup on register or unregister hooks? 
		What about options tables for enrollment tokens? Or is this something that should be setup dynamically via an interface, per site? 
			- Maybe. 
		
		 */
		
		
		
			
		public function activation(){
		
			//Setup Custom Post Types
			$this->set_cpts();
					
			flush_rewrite_rules();	//Clear the permalinks after CPTs have been registered
		
		}
		
		
		public function deactivation(){
			
			//Stop Network Cron Jobs
			$this->stop_crons();
			
			//Clean up Post Types being removed. 
			$this->remove_cpts(); 	// unregister the post type, so the rules are no longer in memory
			flush_rewrite_rules();	// clear the permalinks to remove our post type's rules from the database
			
			//Remove tokens from all sites. 
			$tokens = new init\Token();
			//$tokens->remove();
			
		}
		
		

		
	}
}


$the_people = new People_CRM();




/* 


//Define Roles
require_once(  NB_CRM_PATH. ( 'func/nb_crm_roles.php' ) );

//Define Additional Capabilties for those Roles

//Define Custom Post Types
require_once( NB_CRM_PATH . ( 'func/nb_crm_cpt.php' ) );
//Build Custom Functions

//Build Custom Management Screens. 

require_once( NB_CRM_PATH . ( 'func/nb_crm_pages.php' ) );

//Grant Site Admins Access to user accounts: 

require_once( NB_CRM_PATH . ( 'func/nb_crm_access.php' ) );

//Secure Access with Forced Login
//require_once( NB_CRM_PATH . ( 'func/nb_crm_nod.php' ) );


//Listener File

require_once( NB_CRM_PATH . ( 'func/nb_crm_listener.php' ) );

register_activation_hook( __FILE__, 'nb_crm_register_roles' ); */

?>