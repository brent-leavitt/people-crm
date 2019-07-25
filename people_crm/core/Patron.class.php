<?php 
/*  
people_crm\core\Patron

Patron - Core Class for People_CRM Plugin
Last Updated 12 Jul 2019
-------------

Description: - Actions that are patron specific, relies heavily on pre-built WP core user funcitons, but also allows for user meta updates. 
		
extend the WP_User class?

- Is there a WP_UserMeta class? I don't think so. 
- Should user meta be seperate? No, because userMeta is not separate. 


---

-User Actions
	 (Needed, already a part of WP Core?)
	 - May need to call WP Core functions based on request.
	 
	 

-Usermeta Actions
	- Create Usermeta
	- Update Usermeta
	- Destroy Usermeta
	
	- Get, Set, and Unset functions (lower levels of abstraction for Create, Update, and Destroy)

*/

namespace people_crm\core;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'Patron' ) ){
	class Patron{
		
		
		//Properties
		public 	$data = []; //
		public 	$id = 0; //
		public 	$error = false;
		public 	$err_msg = '';
		
		private	$email = '';
		private $password = '';
		private $username = '';
		

		
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
			
			//Do I need to store all the data or do I just need to extract key information that I will need to perform patron actions. 
			
		}	
				
		
					
	/*
		Name: find
		Description: Finds a patron based on data submitted. 
	*/	
				
		
		public function find(){
			
			//What kind of input?
			if( !empty( $this->id = $this->data['patron'] ) ){
				
				$this->id = $this->data['patron'] ;
				return true;
				
			}
				
			
			
			//get user by email
			if( null !== ( $user = get_user_by( 'email', $this->data[ 'email' ] ) ) ){
				$this->id = $user->ID;	
				
			}else{
				
				//get user by paypal email address
				$pID = get_users( array( 
					'meta_key' => 'paypal_email',
					'meta_value' => $this->data[ 'patron_email' ],
					'number' => 1,
					'fields' => 'ids'
				) );
				
				//There may be other unique meta fields associate with this customer's data. 
									
				if( !empty( $pID ) )
					$this->id = $pID;		
				
			}
			
							
		
			//what other forms of data could be searched? 
			
			//returns user ID. 
			
			return ( !empty( $this->id ) )? true : false ;
		}	
				
/*
	Name: register
	Description: Registers a new patron account based on data submitted. This is 2nd Step registration. 
*/	
				
		
		public function register(){
						
			//Registration data. 
			if( $this->find() == false ){
				if( $this->reglite() == false  ){
					return false;
				}
			}
			
			//This registration will always be a second step registration after a payment or initial username/password has been setup. This sets up all user data that is required. Will data vary from site to site? Probably. 
			
			//INCOMPLETE
			/*
			
			$userdata = array(
				'ID'                    => 0,    //(int) User ID. If supplied, the user will be updated.
				'user_pass'             => '',   //(string) The plain-text user password.
				'user_login'            => '',   //(string) The user's login username.
				'user_nicename'         => '',   //(string) The URL-friendly user name.
				'user_url'              => '',   //(string) The user URL.
				'user_email'            => '',   //(string) The user email address.
				'display_name'          => '',   //(string) The user's display name. Default is the user's username.
				'nickname'              => '',   //(string) The user's nickname. Default is the user's username.
				'first_name'            => '',   //(string) The user's first name. For new users, will be used to build the first part of the user's display name if $display_name is not specified.
				'last_name'             => '',   //(string) The user's last name. For new users, will be used to build the second part of the user's display name if $display_name is not specified.
				'description'           => '',   //(string) The user's biographical description.
				'rich_editing'          => '',   //(string|bool) Whether to enable the rich-editor for the user. False if not empty.
				'syntax_highlighting'   => '',   //(string|bool) Whether to enable the rich code editor for the user. False if not empty.
				'comment_shortcuts'     => '',   //(string|bool) Whether to enable comment moderation keyboard shortcuts for the user. Default false.
				'admin_color'           => '',   //(string) Admin color scheme for the user. Default 'fresh'.
				'use_ssl'               => '',   //(bool) Whether the user should always access the admin over https. Default false.
				'user_registered'       => '',   //(string) Date the user registered. Format is 'Y-m-d H:i:s'.
				'show_admin_bar_front'  => '',   //(string|bool) Whether to display the Admin Bar for the user on the site's front end. Default true.
				'role'                  => '',   //(string) User's role.
				'locale'                => '',   //(string) User's locale. Default empty.
			 
			);

		*/
			
			//Return user ID. 
			
			return ( $this->id > 0 )? $this->id : false ;	
		}	
			
				
	/*
		Name: registerlite
		Description: Registers a new patron account based on data submitted. 1st Step registration. 
	*/			
		public function reglite(){
			
			/*
				Register UserName/email_address/password
			*/
			
			if( !empty( $this->email ) ){
		
				$email = $this->email;	
				$username = ( !empty( $this->username ) )? $this->username : $this->email ;
				$password = ( !empty( $this->password ) )? $this->password : '' ;
			
			} else {
				
				$this->error = true;
				$this->err_msg = 'empty_user_login';
				return false;
			}
			
			
			$pid = wp_create_user( $username, $password, $email );
			 
			 /*
			  wp_create_user returns a user ID or error as follows: 
				- 'empty_user_login', Cannot create a user with an empty login name.
				- 'existing_user_login', This username is already registered.
				- 'existing_user_email', This email address is already registered.
			 */
			 
			if( is_wp_error( $pid ) ){
				$this->error = true;
				$this->err_msg = $pid->get_error_message(); //Could also use get_error_messages() for multiple error messages. 
			}
				
			return ( $this->id > 0 )? $this->id : false ;	
		}
		
	}
}
?>