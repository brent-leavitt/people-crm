<?PHP

/*
people_crm\core\Record

Record Automation - Code Plan for NBCS
Last Updated 12 Jul 2019
-------------

Desription: This is the record keeping class. 

	The last step in any action taken by the system or a user is to record it in the system database. It requires a listener to be set at the beginning of any action to collect data as the processes are being executed in order to create an effective paper trail. :| how to do this? 

	Collected Data stored in the record can be a numeric array (indicating each main step in the process) with nested associated arrays detailing key information about each step in the process. 
	
		$args = array(
			'do_action' => array(
				'step1' => true,
				'step2' => true
			),
			'do_another' => array(
				'step1' => true,
				'step2' => true
			)
		);
	
	Records and Post in the CRM should be combined into one Record CPT, because both record actions taken on a user's account. 
		
		
---

*/
	
namespace people_crm\core;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'Record' ) ){
	class Record{

	// Variables
		
		public $patron = 0; 
		public $record = array();
		


	// Method
					
	/*
		Name: __construct
		Description: 
	*/	

		public function __construct( $patron, $record ){
			
			$this->patron = $patron;
			$this->record = $record;
			return $this->init();
			
		}
					
	/*
		Name: init
		Description: 
	*/		
		public function init(){
			//Take data, process it into a <pre> formatted text for human readibility. 
			//Store the same data as an exerpt 
			
			//Check that properties are set. 
			foreach( get_object_vars( $this ) as $prop ){
				if( empty( $prop ))
					return false;
			}
			//dump( __LINE__, __METHOD__, get_object_vars( $this ) );
			return $this->save();
			
		}
	
	/*
		Name: save
		Description: 
	*/	
			
		public function save(){
			
			$post_record = array(
				'post_title' => $this->record_title(),
				'post_author' => $this->patron,
				'post_content' => $this->pre_format(),
				'post_status' => 'recorded',
				'post_excerpt' => serialize( $this->record ),
				'post_type' => NN_PREFIX.'record',
				
			);
				
			//Save only to the BASE SITE if set. Is this needed now? This code only resides in the base site plugin.  
			nn_switch_to_base_blog();

			$result = wp_insert_post( $post_record );
			
			nn_return_from_base_blog();
			
			return $result;
			
		}	

		
	/*
		Name: pre_format
		Description: 
	*/		
		
		public function pre_format(){
			
			$output = "<pre>";
			$output .= var_export( $this->record, true );
			$output .= "</pre>";
			
			return $output; 
		}	
				
			
	/*
		Name: record_title
		Description: 
	*/	
			
		public function record_title(){
			
			$date = date( 'y-m-d H:i:s' );
			$patron = $this->patron;
			$record = $this->record;
			
			//A little traversing to find the initiating method. 
			end( $record );
			$last_key =  key( $record );
			$nested_last_key = key( $record[ $last_key ] );
			$first_method = key( $record[ $last_key ][ $nested_last_key ] );
			//Then a little clean up. 			
			$first_method = (  'do_' === substr( $first_method , 0 , 3)  )? substr( $first_method , 3 ): $first_method ;

			$method = ucfirst( $first_method ); //get the key of the last array which is the initiating method. 
			$title = "$method Record, Patron $patron on $date ";
			
			return $title;
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
	