<?PHP 
/*
people_crm\core\sub\NoticeTemplate

Notice Template - Sub Core Class for People_CRM Plugin
Last Updated 12 Jul 2019
-------------

Desription: This handles the retrieving of notice templates, building them, and returning them to the Notice class for use there. 


	usage: 
		
		$template = new Template( $this->template_slug );
			
			
			if( !$template->error ){
				
				$template->prepare( $this->message_vars );
				
				$this->subject = $template->get_subject();
				
				$this->content = $template->get_content();
				
			} else {
				
				//template not found. 
				$error = new \WP_Error( 'message template not found.' );
				
				return $error;
			}
		
---
*/
namespace people_crm\core\sub;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'NoticeTemplate' ) ){

	class NoticeTemplate{
		
		//Properties
		public 
			$slug = '',
			//$subject_vars = array(), //Varable extracted from subject
			//$content_vars = array(), //Varable extracted from content
			$source, //source data
			$subject = '',
			$content = '',
			$error = false;
		
		
		//Methods
		
			
	/*
		Name: __construct
		Description: 
	*/	
		
		public function __construct( $slug ){ 
			
			$this->init( $slug );
		}	
				
		
	/*
		Name: init
		Description: We need to know if the template exists. Sets Error to true if no template is found. 
	*/	
		
		private function init( $slug ){
			
			$this->slug = ( $this->retrieve( $slug ) )? $slug : '' ;
			
			$this->error = ( !empty( $this->slug ) && !empty( $this->content ) )? false : true ; 
		
		}	
		
		
			
	/*
		Name: retrieve
		Description: Checks to see if the template being requested is available. 
	*/	

		public function retrieve( $slug ){
			
			//Retrieve Post Type by Slug
			if ( $post = get_page_by_path( $slug , OBJECT, NN_PREFIX.'noticetemplate' ) ){
				$this->content = $post->post_content;
				$this->subject = $post->post_title;
			}
			return ( !empty( $this->content ) )? true : false; //true or false; 
			
		}		

							
	/*
		Name: prepare
		Description: This prepares the Notice Template by extracting available template variables from the content of the template and storing them in the 
	*/	
				
		
		public function prepare( $data ){
			
			$this->source = $data;
			
			//Build Subject Line
			$this->build( 'subject' );
			
			//Build Content with sent data
			$this->build( 'content' );
			
		}	
		
		
	/*
		Name: build
		Description: Builds the actual message to be sent to the user by replacing the template variables with source information, then returns it back to the main class for use. 
	*/	
				
		
		public function build( $what ){
			
			$out = $this->$what;
			
			$vars = $this->get_template_vars( $this->$what );
			
			//find and replace the short codes out of the template. (I actually think there is a better way to do this, back where things are first being parsed with regex.)
			foreach( $vars as $value )				
				$out = str_replace( "[nn_m $value]", $this->source[ $value ], $out );
			
			$this->$what = $out;
			
		}	
		

		
	/*
		Name: get_template_vars
		Description: 
	*/	
				
		
		public function get_template_vars( $content ){
			
			$pattern = get_shortcode_regex();
			
			preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches );

			$out = array();
			
			if( isset( $matches[2] ) ){
				foreach( $matches[2] as $key => $value ){
					if( 'nn_m' === $value )
						$out[] = trim( $matches[3][ $key ] );  
				}
			}
			return $out;
		}		
		
	
					
	/*
		Name: get_subject 
		Description: Get the notice template Subject
	*/	
				
		
		public function get_subject(){
			
			return $this->subject;
		}	
							
	/*
		Name: get_content
		Description: Gets the content of the subject. 
		
	*/	
				
		
		public function get_content(){
			
			return $this->content;
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