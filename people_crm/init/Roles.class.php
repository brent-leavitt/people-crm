<?php
/*

people_crm\init\Roles

Roles - An Initializing Class for People_CRM Plugin
Last Updated on 2 Oct 2019
-------------

  Description: Roles and Caps Management




*/

namespace people_crm\init;	

use \nn_network\init\Roles as nRoles;

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
	
	
if( !class_exists( 'Roles' ) ){
	
	class Roles extends nRoles {

		
		// Properties

		public $add = array(
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
		public $remove = array(
			'subscriber',
			'contributor',
			'author',
			'editor'
		);
		
		public $default_role = 'reader';
		
		
		
		//Methods

		/*
			No new methods added in this class. All methods are the same as the parent class. 
		*/
	
	}
	
}



?>