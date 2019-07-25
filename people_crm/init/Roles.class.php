<?php
/*

people_crm\init\Roles

Roles - An Initializing Class for People_CRM Plugin
Last Updated on 18 Jul 2019
-------------

  Description: Roles and Caps Management




*/

namespace people_crm\init;	

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
	
if( !class_exists( 'Roles' ) ){
	
	class Roles{

	
		public $roles = array(
			'inactive', 		// Not interested in any services, may have requested no contact
			'reader', 			// Newsletter Signups start here. 
			'learner',			// Starting Level for Library
			'mother',			// Expectant mother - interest level
			'supporter',		// Family or Friend wanting to provide support
			'birther',			// Birth Professionals
			'inquirer',			// Prospective Student, made inquiry about doula training.
			'student',			// Has been a student, but account has gone inactive
			'student_active',	// Currently progressing student
			'alumnus',			// Certified, but certification period has lapsed
			'alumnus_active',	// Active Certification or actively working on additional certs. 
			'resource',			// Other business contacts
			'trainer'			// Special role that allows to interact with system.

		);

		
		//These default roles are to be removed from the CRM.
		public $defaults = array(
			'subscriber',
			'contributor',
			'author',
			'editor'
		);
		
		
		public function __construct(){
			
			$roles = array_reverse( $this->roles ); 
			
			//All roles to be removed.
			$remove_roles = array_merge( $roles, $this->defaults  );
			$this->remove_roles( $remove_roles );
			
			//Add CRM roles. 
			$this->add_roles( $roles );

			//Set Default Role: 
			update_option( 'default_role', 'reader' );
			
		}


		public function add_roles( $roles ){
			global $admin_notices;

			foreach ($roles as $role ){		
				
				$result = add_role( $role, ucfirst( $role ) );
				
				if ( null !== $result ) {
					$admin_notices .= " New role '$role' created! <br />";
				}
				else {
					$admin_notices .= "Oh... the '$role' role already exists. <br />";
				}
			}
			
		}


		public function remove_roles( $roles ){
			
			foreach( $roles as $role ){
				if( get_role( $role ) ){
					  remove_role( $role );
				}
			}
			
		}


	
	}
	
}



?>