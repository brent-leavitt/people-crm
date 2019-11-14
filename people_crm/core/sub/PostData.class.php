<?php 

/* 
people_crm\core\sub\PostData

PostData - Sub Core Class for People_CRM Plugin
Last Updated 12 Jul 2019
-------------

Desription: This is the core class for using the data that is stored multiple Post Type classes used acrossed the network. Namely,  Receipt, Invoice (grouped together under the Transaction Parent Class),  Notice,and Record. 

The PostData class performs fundamental functions that are useful for the CPTs created specifically for the network.  These CPT are stored exclusively in the CRM database and as such are only initialized therein. 


---

*/

namespace people_crm\core\sub;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'PostData' ) ){
	
	class PostData{
		
		
		//Properties

		public 
			$ID = 0,
			$patron = 0,
			$post_type = '', //
			//$data = array(),
			$data_map = array(
				'patron' => 'post_author'
			); //
		
		//This is what get's sent to WordPress
		public $post_arr = array( 
				//'ID' => 0, //add if this an update. 
				'post_author' => 0,
				'post_date' => '',
				'post_content' => '',
				'post_content_filtered' => '',
				'post_title' => '',
				'post_name' => '',
				'post_excerpt' => '',
				'post_status' => 'draft',
				'post_type' => 'post',
				'post_parent' => 0,
				
				/* 'comment_status' => '',
				'ping_status' => '',
				'post_password' => '',
				'to_ping' =>  '',
				'pinged' => '',
				'menu_order' => 0,
				'guid' => '',
				'import_id' => 0,
				'context' => '', */
				
				'meta_input' => array(), //this can also be added to send meta data
			);
			
		protected $meta_key = 'PostData';

		protected $actions = []; //actions to be taken as a result of enrollments
			//Methods? 

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

			//Storing source data into the class. Needed. For right now, yes. 
			//$this->data = $data; 
		
			$this->set_data( $data );
			
			//If a post ID is set, retrieve the post. 
			if( !empty( $this->ID ) ){
				$this->retrieve();
			}
		}	
				
		
		
	/*
		Name: set_data
		Description: This is the initial data to properties setup. If there is an initiating value sent in the data assign it to its respective property. 
	*/	
				
		
		public function set_data( $data ){
			
			dump( __LINE__, __METHOD__, $data );
			
			$data = array_merge( $data, $data[ 'data' ] );
			
			foreach( $data[ 'payee' ] as  $pkey => $pval ){
				$payee_key = 'payee_'.$pkey;
				$data[ $payee_key ] = $pval;
			}
			
			unset( $data[ 'data' ] );
			unset( $data[ 'payee' ] );
			
			
			foreach ( get_object_vars( $this ) as $key => $value ){
				if( isset( $new_data[ $key ] ) && !empty( $data[ $key ] ) ){
					$this->$key = $data[ $key ];
				}
			}	
			
			//
		}	
		
		
		
	/*
		Name: prepare
		Description: Prepares data being sent in via the data array for insertion into WPDB

				
		
		public function prepare(){
			
			
		}	
	*/			
					
	/*
		Name: retrieve
		Description: This pulls a custom post type from the database and then assigns it to the requesting class
	*/	
				
		
		public function retrieve(){
			
			$post = array();
			$meta = array();
			
			if( isset( $this->ID ) ){
				
				//This information is stored in the CRM/MasterSite
				if( defined( 'NN_BASESITE' ) && is_multisite() )
					switch_to_blog( NN_BASESITE );
				
				$post = get_post(  $this->ID, 'ARRAY_A'  );
				//This only returns post data, not meta. 
				$meta = get_post_meta( $this->ID, '', true);
					
				//Return back to current space. 
				if( defined( 'NN_BASESITE' ) && is_multisite() )
					restore_current_blog();
				
				//Clean up and condense meta data returned from WPDB. 
				$meta = $this->clean_retreived_meta( $meta );
								
				//Tuck in the cleaned up meta data into the meta_input space in the post data. 
				$post = array_merge( $post, ['meta_input' => $meta] );

				//$post = array_merge( $post, $meta );
			}
			
			
			$this->post_arr = array_merge( $this->post_arr, $post );
			
			//Assign values to class properties. 
			$this->reverse_map();
			
		}

			
	/*
		Name: insert
		Description: Inserts submitted data into the WPDB. This is the final step in submitting data. 
	*/			
		
		public function insert(){
			
			//Map data values to the post_arr
			$this->map();
			
			//$this->add_meta(); //NOT NEEDED, Duplicate action from $this->map(). 
			
			//Array_filter drops empty fields if no callback function is provided. 
			$this->post_arr = array_filter( $this->post_arr /*,$callback_function missing*/ );
			
			//dump( __LINE__, __METHOD__, get_object_vars( $this ) );
			
			//Prepare Data for Insertion
			$this->prepare();
			
			
			//Has this post already been inserted? 
			//Setup to assess if post exists:
			foreach( [ 'title', 'content', 'date' ] as $exists )
				$$exists = ( $this->post_arr[ "post_$exists" ] ) ?? '';
			
			//Now we're checking if post exists. 
			require_once ABSPATH . '/wp-admin/includes/post.php';
			if( post_exists( $title, $content, $date ) === 0 ){//It doesn't exists
			
				//dump( __LINE__, __METHOD__, $this->post_arr );

				//This information is stored in the CRM/MasterSite
				nn_switch_to_base_blog();
				
				$result = wp_insert_post( $this->post_arr );
				
				//Return back to current space. 
				nn_return_from_base_blog();
			
				if( !is_wp_error( $result ) ){
					
					$this->ID = $result;
					return true;
				}
				
				return $result;
				
			}
			
			return false;
		}

		
		
	/*
	
		Name: add_meta
		Description: Adds Metadata to the main post_arr array. 
		
		NOTES: Where is this called? 
		
	*/	
		
		public function add_meta(){
			
			//Remove empty values, if any. 
			$this->post_meta = array_filter( $this->post_meta );
			
			if( !empty( $this->post_meta ) ){
				
				$this->post_arr[ 'meta_input' ] = $this->post_meta;
				
				return true;
			}
			
			return false;
		}

		
	/*
		Name: extend_data_map
		Description: This takes the data_map of the extending class and adds it to the core data map. 
		params: $str;

	*/			
		
		public function extend_data_map( $str ){
			 
					
			$class_name = $this->class_name_only( $str );
			
			$t_data_map = $class_name.'_data_map';
			
			$this->data_map =  array_merge( $this->data_map, $this->$t_data_map );
			
			//Dispose of after merge. 
			unset( $this->t_data_map );
			
		}		
		
			
		
	/*
		Name: class_name_only
		Description: Isolates the class from its namespace. 
	*/			
		
		public function class_name_only( $str ){
			
			$name = urlencode( $str );
			
			$class_name = substr( $name , strrpos( $name, '%5C' ) +3 );
			
			return $class_name;
			
		}		
		
		
	/*
		Name: map
		Description: This takes all data that is sent in via the $data array var, and maps it to the respective field for insertion in the WPDB. 
	*/				
		
		public function map() {
			
			$post = $this->post_arr;
			$map = $this->data_map;
			
			//dump( __LINE__, __METHOD__, $post );
			//dump( __LINE__, __METHOD__, $this->data );
			
			$mapped = array();
			$meta = array();
			
			foreach( $map as $key => $val ){
				
				
				//checking if this class has the requesting property set. 
				if( !empty( $this->data[ $key ] ) ){ //This is going through the properties of the class to connect them to the post_arr value. 
					//ep( "The key is $key and the data for this key is: ".$this->data[ $key ]. " The value is $val. " );
					
					//Now we're checking if the post_arr has the requesting key set.
					if( array_key_exists( $val, $post ) ){
						//ep( "Array Key exists for $val in POST." );
						$mapped[ $val ] =  $this->data[ $key ];
						
					//If not, add val to meta array that we'll tack on at the end.
					} else {
						$meta[ $key ] = $this->data[ $key ];
					}	
				}
			
			}
			//Put uncategorized data into the post_content field. 
			$mapped[ 'post_content' ] = json_encode( $meta ); //Add the meta data to the array.
			
			//put source data, if it exists, into the post_excerpt field. 
			if( property_exists( $this, 'src_data' ) ) $mapped[ 'post_excerpt' ] = $this->src_data;
			
			//Checking if any properties are matched to the actual post_arr keys.
			foreach( $post as $key => $val ){
				if( isset( $this->$key ) )
					$mapped[ $key ] = $this->$key;
			}
			
			//This will merge any duplicate keys with the later vallues of mapped overriding the existing. 
			$this->post_arr = array_merge( $this->post_arr, $mapped );
			
			
			//dump( __LINE__, __METHOD__, $this->post_arr );
			//needed? 
			return $mapped;
		}
		
		
		
	/*
		Name: reverse_map
		Description: Takes data from a WP_POST call and maps it to class values. 
		Does not process Meta_data yet. 
		
	*/			
		
		
		public function reverse_map(){
			
			$post = $this->post_arr;
			$map = $this->data_map;
			
			$mapped = array();
			
			foreach( $map as $key => $val ){
				
				//Look at post data. 
				if( !is_string( $val ) )
					continue;
				
				if( !empty( $post[ $val ] )  ){
					
					//Is there a mapped propertied for this line of post data?
					if( isset( $this->$key ) ){
						
						$this->$key = $post[ $val ];
					}
				}
			}
			
			foreach( get_object_vars( $this )  as $key => $val ){
				if( isset( $post[ $key ] )  ){
					
					$this->$key = $post[ $key ];
					
				}
			}
			
		}


		
		
	/*
		Name: clean_retreived_meta
		Description: This cleans metadata that has been retrieved from the WPDB into a simpler format to work with. This removes unnecessary array groupings with only 1 or 0 arrays. Where multiple values are returned for a single meta_key, the nested array remains in tack. 
	*/			
		
		private function clean_retreived_meta( $meta ){
			
			foreach( $meta as $mkey => $mval ){
					$meta_count = count( $mval );
					
					//if not more than one value is set, remove from array wrapper
					if( $meta_count < 1 )
						unset( $meta[ $mkey ] );
					elseif( $meta_count == 1 )
						$meta[ $mkey ] = $mval[0];
					
					//if value is empty remove from array. 
					if( empty( $meta[ $mkey ] ) )
						unset( $meta[ $mkey ] );
					
				}
				
			return $meta;
		}	
			
		
	/*
		Name: get_actions
		Description: This is called in the Action::clean_up method. 
	*/	
					
			
			public function get_actions(){
				
				return ( !empty( $this->actions ) )? $this->actions : false ;
				
			}	
			
					
	/*
		Name: set_actions
		Description: This allows child objects to send actions to the private $actions array. 
	*/	
				
		
		public function set_actions( $actions ){

			foreach( $actions as $action )
				$this->actions[] = $action;
			
			
			//dump( __LINE__, __METHOD__, $this->actions );
		}	
				


		
	/*
		Name: prepare
		Description: Prepare data for insertion into database. 
		
		NOTES: This isn't a postData method. It belongs in Transaction. 
	*/	
				
		
		public function prepare(){
			
			//Check title 
			if( empty( $this->post_arr[ 'post_title' ] ) ){
				$trans = ( $this->data[ 'trans_type' ] ) ?? '';
				$trans_id = ( $this->data[ 'tp_id' ] ) ?? '';
				$tp = ( $this->data[ 'tp_name' ] ) ?? '';
				$patron = $this->patron;
				
				$this->post_arr[ 'post_title' ] = "$trans #$trans_id from $tp for Account #$patron";
			}
			
			//check name
			
			
		}	

		
	/*
		Name: 
		Description: 
	*/	
				
		
		public function __(){
			
			
		}	
		
	}
}


/*	
//DEV TEST CODE

$data = array( 'ID' => 23 );

$cpt = new CPT( $data );
echo "<br />". $cpt->ID;
echo "<br /><pre>";
var_dump( $cpt );
echo "</pre>";
*/


?>