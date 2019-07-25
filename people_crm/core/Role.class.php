<?php 

/*  
people_crm\core\Role

Role (User Roles) - Core Class for People_CRM Plugin
Last Updated 12 Jul 2019
-------------

Description: - Actions regarding updates to user roles are controlled here. 
		
---

- This is a class that get's used in the individual sites but is relative to all sites. How to call? 

- This class gets sent as an object via the do_action hook in the do_role method of the Action class. 
	- what methods could be employed to 

- User Role Actions
	- Grant User Access to User Role
		- What user, what role
	- Revoke User Access to User Role
		- what user, what role revoked?
	- Change User Role
		- What user, what changed role? 
		
	- Grant, Change, or Revoke user role on network site
		- what action, what user, what site, what role
	
	- Get, Set, and Unset functions (lower levels of abstraction for Grant, Revoke, and Change)
	
*/

namespace people_crm\core;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'Role' ) ){
	class Role{
		
		//PROPERTIES
		public $patron = 0;
		public $role = '';	
		public $old_role;
		/* public $token = ''; */
		//public $date = ''; //Needed? Why?
		
		private $service;
		private $change = false;
		private $actions = [];
		
		
		
		//Methods
			
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
			
			//Set Patron
			$this->patron = $data[ 'patron' ];
			
			//Set Role 
			$patron_data = get_userdata( $this->patron );
			$this->role = implode( ', ', $patron_data->roles ) ; //returns current user role	
			
			//Set Service Object
			$this->service = new Service( $data );
			//The code below belongs in a separate function that get's called by the 
			
						
		}
		
			
	/*

		Name: site_roles
		Description: Returns all site roles that are available 

	*/	
			
		
		public function site_roles(){
		
			global $wpdb;
			$user_roles = $wpdb->prefix .'user_roles';

			$options = get_option( $user_roles );

			$roles = [];

			foreach( $options as $role => $stuff  ){
				$roles[] = $role;
			}
			
			return $roles;
			
		}	
			
			
	/*

		Name: set
		Description: this sets the role when an updated role is determined to be needed. 
		parameters: $role, $priority 
	*/	
			
		
		public function set( $role, $priority = 0 ){
			
			$patron = new WP_User( $this->patron );
			
			//if database role is different than this object's role, set the role. 
			if( strpos( $role, $patron->roles[0] ) != 0 ){
				$this->old_role = $this->role;
				$patron->set_role( $role );
				
				//How to get priority for current site? ?
				$site_id = get_current_blog_id();
				$this->set_priority( $site_id, $priority );
			}
			
			
			//This is a checking mechinism that says the role has been updated. if updated, update the object->role and object->change values to reflect change. 
			if( strpos( $role, $patron->roles[0] ) == 0 ){
				$this->role = $role;
				$this->change = true; //
				return true;
			}
			return false;
		}
				
			
	/*

		Name: update_network
		Description: updates network based on changes to requesting site.
		
		Because changes can only be made to database, sets of rules need to be set for what changes affect what sites across the network.
		
		Is every case different? Almost. 
		
		The challenge here is that the set of rules should be manage as an options table. 
		What would be the abstraction of that? 
		
		Check for options effected by 
		
		Cases: 
			LMS:
				- User goes from being active alumnus in the LMS to inactive. 
					- Revoke library access
					- update CRM status. 			
					
				- User goes from being active student in the LMS to inactive. 
					- Revoke library access
					- move CRM status to inactive student also. 

				- User goes from being inactive student in the LMS back to active. 
					- add library status back. 
					- update CRM status. 
					
				- User goes from being inactive alumnus in the LMS back to active. 
					- add library status
					- update CRM status. 
		
				
			CMS(library): 
				- New paying user get assigned active/learner on library
					- CRM record gets created to learner. 
			
				- User goes from being inactive( non-paying ) on Library to returning to activity
					- move CRM status back to former level (where is that stored?).
					
				- User goes from being active/learner(paying) on Library to reader (non-paying). 
					- move CRM status from learner (or higher) to reader. 
					
			CRM: 
				- Users go from being actively subscribed to an email service to inactive. 
					- Does not affect other sites. 
				
				- User requests to moved to inactive status, but have active subscription or enrollment on other service (probable case?) 
					
				-  
			---
			Does this change require a network update? 
			 - how do I answer this question. 
			 
			 - Rules are set per site as to what other sites are afffected by changes to roles on the currrent site. 
			
			$role_changes = [
				0 => array( 'old_role', 'new_role', 0(network_role_change_id)),
				1 => array( 'old_role', 'new_role' ),
				2 => array( 'old_role', 'new_role' ),
			];
			
			
			$network_role_change = [
				0 => array(
					[ 0 (site_id), 'updated_role', 0 (priority) ],
					[ 0 (site_id), 'updated_role', 0 (priority) ],
				),
				1 => array(
					[ 0 (site_id), 'updated_role', 0 (priority) ],
					[ 0 (site_id), 'updated_role', 0 (priority) ],
				),
				2 => array(
					[ 0 (site_id), 'updated_role', 0 (priority) ],
					[ 0 (site_id), 'updated_role', 0 (priority) ],
				)
			];
			
			
			
			or combined: 
			
			
			$role_changes = [
				0 => array( 'old_role', 'new_role' 
					array(
						[ 0 (site_id), 'updated_role', 0 (priority) ],
						[ 0 (site_id), 'updated_role', 0 (priority) ],
					)
				),
				1 => array( 'old_role', 'new_role'  
					array(
						[ 0 (site_id), 'updated_role', 0 (priority) ],
						[ 0 (site_id), 'updated_role', 0 (priority) ],
					)
				),
				2 => array( 'old_role', 'new_role'  
					array(
						[ 0 (site_id), 'updated_role', 0 (priority) ],
						[ 0 (site_id), 'updated_role', 0 (priority) ],
					)
				),
			];
			
			or a set of IDS between different groups of changes. 
			
			
			
			//This needs to throw an error message if the change at the network level cannot be completed, and the reason why? How? 
			
			
			
			//What constraints are in place to check if a change should be allowed on from a remote site? 
				Are constraints needed beyond a case by case basis? 
				Examples? 
				There would be no USER input or back and forth exchange of data in this instance. Flow of data is only one way. 
				
				But yet are there still instances where a network change of role should be prohibited. 
				Examples? 
				
				If someone is paying for a library subscription and then they sign up for doula training. The library subscription is automatically voided, and the doula training subscription takes affect. 
				
				Everything gets controlled from the initiating domain, so there shouldn't be a conflict of domain actions, like updating role changes. It change is happening at library, should not be a user at the LMS. If lms change registers a user, the library user is upgraded or added. When cancelled or inactivated, library role is demoted.  
			
			
			//Priority level of changes. 
				
**** LMS ****
			//Site ID: 6
			
			$role_changes = [
				0 => array( '', 'student_active', 0), 					//New student registration
				1 => array( 'student_active', 'student', 1 ),			//move to inactive status
				2 => array( 'student', 'student_active', 0 ), 			//move to reactivated status
				3 => array( 'student_active', 'alumnus_active', 2 ),	//moved to certified status
				4 => array( 'alumnus_active', 'alumnus', 3 ),			//deactivated alumnus status
				5 => array( 'alumnus', 'alumnus_active', 2 ),			//renewed alumnus status after deactivated. 
				6 => array( 'alumnus_active', 'student_active', 0 ),	//case scenario?	
				//7 => array( 'old_role', 'new_role', 0 (network_role_change_id) ),
			];
			
			
			//Network Sites: 
				Library = 1
				CRM = 2
				WWW = 4
				
			$network_role_change = [
				0 => array( //New student registration && //move to reactivated status
					[ 1, 'subscriber', 9 ],
					[ 2, 'student_active', 9 ],
				),
				1 => array( //move to inactive status
					[ 1, 'visitor', 9 ],
					[ 2, 'student', 9 ],
				),
				2 => array( 
					[ 1, 'subscriber', 9 ],
					[ 2, 'alumnus_active', 9 ],
				),
				3 => array(
					[ 1 , 'visitor', 9 ], 
					[ 2 , 'alumnus', 9 ],
					
				)
			];
			
				
**** LIBRARY ****
			//Site ID: 1
			
			$role_changes = [
				0 => array( '', 'visitor', 0), 					//
				1 => array( '', 'subscriber', 1),				//
				2 => array( 'visitor', 'subscriber', 1),		//
				3 => array( 'subscriber', 'visitor', 0),		//
				//? => array( 'old_role', 'new_role', 0 (network_role_change_id) ),
			];
			
			
			//Network Sites: 
				CRM = 2
				WWW = 4
				LMS = 6
				
			$network_role_change = [
				0 => array( //New visitor
					[ 2, 'reader', 1 ],
				),
				1 => array( //move to inactive status
					[ 2, 'learner', 2 ],
				)
			];
			
**** CRM ****
			//Site ID: 2
			
			$role_changes = [
				0 => array( '', 'reader', 0), 						//
				1 => array( '', 'inquirer', 0), 					//
				2 => array( 'reader', 'inactive', 1), 							//
				//? => array( 'old_role', 'new_role', 0 (network_role_change_id) ),
			];
			
			
			//Network Sites: 
				Library = 1
				WWW = 4
				LMS = 6
				
			$network_role_change = [
				0 => array( //
					[ 1, 'visitor', 1 ],
				),
				1 => array( //move to inactive status
					[ 1, '', 9 ],					
				)
			];			
			
		params: uses $this->old_role, and $this->role to assess what changes need to be made to the network. 
	*/
			
		
		public function update_network(){
			
			$network_updated = [];
			
			//These two options are set in the options table. 
			$role_changes = get_option( 'nn_role_changes' );
			$network_role_changes = get_option( 'nn_network_role_changes' );
			
			//Assess what role change is being performed to know what network change need be accessed. 
			foreach( $role_change as $change ){
				
				list( $old_role, $new_role, $network_role_change ) = $change;
				if( strpos( $old_role, $this->old_role ) == 0 && strpos( $new_role, $this->role ) == 0 )
					$change_id = $network_role_change;
			}
			
			if( isset( $change_id ) ){
				
				foreach( $network_role_changes[ $change_id ] as $site_change ){
					
					//$site_change = array( 0(site_id) , '(role)', 0(priority) );
					$network_updated[ $site_change[0] ] = update_network_role( $site_change );
					//returns the updated role for the site ID. 
				}
			}
			
		}


		
		
	/*

		Name: update_network_role
		Description: Receives an array with a site id, role to update, and a priority level.
		params: $change = array( 0(site_id), '(role_name)', 0(priority) )
	*/	
			
		
		public function update_network_role( $change ){
			
			if( $this->is_priority( $change ) ){
				
				list( $site_id, $role,  ) = $change;
				$patron_id = $this->patron;
				
				//If no value set, drop patron from site. 	
				if( empty( $role ) ){
					
					remove_user_from_blog( $patron_id, $site_id );
					
				//Otherwise	add new user role to site.
				} else {
					
					add_user_to_blog( $site_id, $patron_id, $role );
				}
				
				return $role;
			}	
				
			
			return false; 
			
		}
				
		
			
	/*

		Name: is_diff 
		Description: if new role is the same as existing role, return true;

	*/	
			
		
		public function is_diff( $new_role ){
			
			return ( strpos( $new_role, $this->role ) == 0 )? false : true; 
			
		}

		
	/*

		Name: report
		Description: reports on what has happened in the role class, because most actions will be handled by other plugins. 

	*/	
			
		
		public function report(){
			
			return ( $this->change )? 'The role was changed to '. $this->status .'.' :  'No change to patron role.' ;
			
		}
		
		
	/*

		Name: is_priority
		Description: 
		
		returns: true or false; 

	*/	
			
		
		public function is_priority( $change ){
			
			$role_priority_key = 'nn_role_priority';
			$role_priority = get_user_meta($this->patron, $role_priority_key, true );
			
			//If no priority is set, this will override, and thus is true. 
			if( empty( $role_priority ) )				
				return true;

			list( $site_id, $new_role, $priority ) = $change;
			
			$current_priority = $role_priority[ $site_id ];
			
			//If current role prioirity is higher than the requesting priority, return false, no action needed. 
			if( $current_priority > $priority )				
				return false;
			
			//But if not false, update the prioirty and send it back to the user databse as well. 
			$role_priority[ $site_id ] = $priority;
			update_user_meta( $this->patron, $role_priority_key, $role_priority ); 

			return true ;
				
		}		
		
		
	/*

		Name: set_priority
		Description: This sets a role's priority so that it can be upheld or overriden across the network. ranges from 0(lowest) - 9(highest).

	*/	
			
		
		public function set_priority( $site_id, $priority ){
			
			$role_priority_key = 'nn_role_priority';
			
			$role_priority = get_user_meta($this->patron, $role_priority_key, true );
			
			$role_priority[ $site_id ] = $priority;
			
			update_user_meta( $this->patron, $role_priority_key, $role_priority, $prev_value ); 
			
		}		
		
		
		
	/*

		Name: 
		Description: 

	*/	
			
		
		public function __(){
			
			
		}
		
		
	}//End Class
}


/* 
	IN Separate PLUGIN Create something like this:  

	End result is role for site is updated. 
	Roles across network are also updated. 
	
*/


//FOR USE IN THE CERTS LMS 
function process_role_certs_lms( $role ){
	
	//This retrieves all status from all services associated with the patron in question. 
	$services = $role->service->get_all_status();	
		
	$permissions = array(
		'issued' 	=> 'alumnus_active',
		'active' 	=> 'student_active',
		'expired'	=> 'alumnus',
		'inactive' 	=> 'student',
	);
			
	//CODE FOR CERTS LMS
	foreach( $persmissions as $srvc_status => $role ){	
		if( in_array( $srvc_status, $services ) ){
			$new_role = $role;
			continue;
		}	 	
	}
	
	//Priority? 
	
	//if role is different, set new role.
	if( $role->is_diff( $new_role ) )
		$role_set = $role->set( $new_role /*, $priority*/ );
	
	//if new role is set, update the network. 
	if( $role_set )
		$role->update_network();
}

//add_action( 'Action_Do_Role', 'process_role_certs_lms', 10, 1 );




//FOR USE IN THE CBL
function process_role_cbl( $role ){
	
	//new role is based on whether the status of the service is active or not. 	
	$new_role = ( strpos( 'active',  $role->service->status ) == 0 )? 'subscriber' : 'visitor';	
	
	//priority? 
	
	//if role is different, set new role.
	if( $role->is_diff( $new_role ) )
		$role_set = $role->set( $new_role /*, $priority*/ );
	
	//if new role is set, update the network. 
	if( $role_set )
		$role->update_network();
}

//add_action( 'Action_Do_Role', 'process_role_cbl', 10, 1 );



//FOR USE IN THE PEOPLE CRM 
function process_role_people_crm( $role ){
		
	//$role is an object of the Role class. 
	$current_role = $role->role; 
	
	$site_roles = $role->site_roles();
	
	//new role is based on whether the status of the service is active or not. 	
	$new_role = ( strpos( 'active',  $role->service->status ) == 0 )? 'reader' : 'inactive';	
	
	//Three base options: learner (paying), reader (non-paying), inactive (requested).

	//If new role is reader, and current role is higher than reader, then ignore user update. 
	if( $new_role = 'reader' ){
		
		//INCOMPLETE
		
	}elseif( $new_role = 'inactive' ){
		
		//INCOMPLETE
		
	}
		
	//Priority? 
	
	//If role is greater than inquirer (?), then abort. 
	
	//Check permissions for user first. If current user role is above a certain level, ignore the need to update. 
	//What level? 
	
	//What happens if a user requests to be moved to inactive status? 
	//Then anyone from inquirer on down can be knocked down, but if any student or alumni status, this needs to be processed differently. Status at the network level needs to be assessed first. A user cannot request to go inactive and have an active or inactive sudent account, or be an alumni. 
	
	
	//This should be loaded from options table? 
	
	
	$crm_roles = array(
		'administrator',
		'trainer',
		'resource',
		'alumnus_active',
		'alumnus',
		'student_active',
		'student',
		'inquirer',
		'birther',
		'supporter',
		'mother',
		'learner',
		'reader',
		'inactive',
	);
	
	
	
	//A patron may have a service established in the PEOPLE CRM, like a newsletter subscription or some other campaign. These will not be the primary influencer services for PEOPLE CRM, but this function only get's triggered if they subscribe to one of these services. 
	
	
	//This retrieves all status from all services associated with the patron in question. 
	$services = $role->service->get_all_status();	
	
	
	

			
	//CODE FOR CERTS LMS
	foreach( $persmissions as $role ){	
		if( in_array( $srvc_status, $services ) ){
			$new_role = $role;
			continue;
		}	 	
	}
	
	//if role is different, set new role.
	if( $role->is_diff( $new_role ) )
		$role_set = $role->set( $new_role );
	
	//if new role is set, update the network. 
	if( $role_set )
		$role->update_network();
}

//add_action( 'Action_Do_Role', 'process_role_people_crm', 10, 1 );

	
	
	//Current user role is already set at this point
	//Service that may effect the change to user role is also loaded. 
	
	//$role->role current isset
	//$role->service->status updated isset
	
	//What are all the services on this site that would affect a user's role? 
		//collect them into an array 
		
		/* 
		$services = $role->service->get_all_status();	
		
		$services = [
			'(service_id)' => '(service_status)', 
			'(service_id)' => '(service_status)', 
			'(service_id)' => '(service_status)', 
			'(service_id)' => '(service_status)', 
		 ] */
		 
		 
		 
		
		/*
		
			
		
		$roles = $this->site_roles(); //Returns an array of all current roles on the site. 
		
		
		<?php



			$blogs_ids = get_sites();

			foreach( $blogs_ids as $b ){

				switch_to_blog( $b->blog_id  );
				
				global $wpdb;
				$user_roles = $wpdb->prefix .'user_roles';
				
				$options = get_option( $user_roles );
				
				restore_current_blog();
				
				$roles = [];
				
				foreach( $options as $role => $stuff  ){
					$roles[] = $role;
				}
				
				echo "<pre>";
				var_export( $roles );
				echo "</pre>";
			}	



		
		//SITE 1
		array (
		  0 => 'administrator',
		  1 => 'editor',
		  2 => 'author',
		  3 => 'contributor',
		  4 => 'subscriber',
		  5 => 'customer',
		  6 => 'shop_manager',
		  7 => 'wpseo_manager',
		  8 => 'wpseo_editor',
		)
		
		//SITE 2
		array (
		  0 => 'administrator',
		  1 => 'trainer',
		  2 => 'resource',
		  3 => 'alumnus_active',
		  4 => 'alumnus',
		  5 => 'student_active',
		  6 => 'student',
		  7 => 'inquirer',
		  8 => 'birther',
		  9 => 'supporter',
		  10 => 'mother',
		  11 => 'learner',
		  12 => 'reader',
		  13 => 'inactive',
		)

		//SITE 4
		array (
		  0 => 'administrator',
		  1 => 'editor',
		  2 => 'author',
		  3 => 'contributor',
		  4 => 'subscriber',
		  5 => 'wpseo_manager',
		  6 => 'wpseo_editor',
		)

		//SITE 5
		array (
		  0 => 'administrator',
		  1 => 'other',
		  2 => 'inactive',
		  3 => 'student',
		  4 => 'alumnus',
		)
		
		//SITE 6
		array (
		  0 => 'administrator',
		  1 => 'alumnus_active',
		  2 => 'alumnus',
		  3 => 'student_active',
		  4 => 'student',
		 
		)
		
		*/
	
		//What rules affect the decision making process of a role to be assigned? 	
			//Ever site has different user permissions and different rules governing those permissions. How do I abstract that? 
		
		//If patron has a service with this status, grant this role. 
			//foreach service assigned to a patron, 

			//What is the highest service status? These are not hierarchical. 
			
			//Childbirth Library = roles are subscriber or visitor (paid/free), based on service status
			//PPL CRM = roles are many and represent progress and are mostly externally influenced. 
			//Certs LMS = roles based on service status, but are not heirarchical per se. 
			
		/*
			PERMISSIONS for CERTS LMS: 
			- if at least one service is issued, role is alumnus_active. 
			- if at least one service is active but not issued, role is student_active.
			- if at least one service is expired but not issued or active, role is alumnus.
			- if at least one service is inactive but not issued, acitve, or expired, role is student. 
			- if none of these are set, roles are revoked. (This should never happen. 
			
			$permissions = array(
				'issued' => 'alumnus_active',
				'active' => 'student_active',
				'expired' => 'alumnus',
				'inactive' => 'student',
			);
			
			
			//CODE FOR CERTS LMS
			
			foreach( $persmissions as $srvc_status => $role ){	
				if( in_array( $srvc_status, $services ) )
					return $role->set( $role );		
			}
		
			
			PERMISSIONS for PPL CRM: 
			- if role exists for Certs LMS, it trumps any other network role, except for resource (in PPL CRM). 
			- if no account on Cert LMS, and no local services have been added, look for roles on CBL. 
			
			
			
			PERMISSION for CBL: 
			- this is simply dependent upon the active/inactive status of their subscription to the library. 
			- 
			
		
		*/