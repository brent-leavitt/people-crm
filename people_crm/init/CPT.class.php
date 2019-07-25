<?php 

/*
people_crm\init\CPT

  - Core Class Psuedo Code for NBCS Network Plugin
Last Updated  Oct 2018
-------------

Desription: 


---

*/
namespace people_crm\init;


use \nn_network\modl\CPT as nCPT;

// Exit if accessed directly 
if ( !defined('ABSPATH')) exit;

if( !class_exists( 'CPT' ) ){
	class CPT{
		
		public $post_types = array(
		/* 	'guide', */
			'receipt',
			'invoice',
			'notice',
			'noticetemplate',
			'record',
		);
		
		public function __construct( ){
			//$this->setup();
			//This is important is it needs to be hooked into the init action hook. 
			add_action( 'init', array( $this, 'setup' ) );
		}
		
		public function setup(){
			//Define specific CPTs for use across the network. 
			//Most of these CPTs should only be active on the NN_BASESITE
			
			//if network and site_id equals NN_BASESITE then declare these CPTS. 
			
			
			//Guide
			/* $guide	= new CPT( 
				array( 
					'post_type'=>'guide',
					'description'=>'enrollment actions or services used for assigning behaviours to tokens',
					'menu_icon'=>'index-card', 
					'hierarchical' => false,
					'exclude_from_search' => true,
					'supports' => array( 
						'title', 
						'editor', 
						'comments', 
						'author', 
						'revisions' 
					)
				) 
			); */
			
			$receipt =  new nCPT( 
				array( 
					'post_type'=>'receipt',
					'description'=>'receipt of transactions processed',
					/* 'menu_pos'=>53,*/
					'menu_icon'=>'cart', 
					'hierarchical' => false,
					'exclude_from_search' => false,
					'supports' => array( 
						'title', 
						'editor', 
						'revisions' 
					)
				) 
			);
			
			$receipt->set_status( [ 'complete' ] );
			
			$invoice =  new nCPT( 
				array( 
					'post_type'=>'invoice',
					'description'=>'finacial transactions to be received',
					/* 'menu_pos'=>53,*/
					'menu_icon'=>'media-spreadsheet', 
					'hierarchical' => false,
					'exclude_from_search' => true,
					'supports' => array( 
						'title', 
						'editor', 
						'comments', 
						'author', 
						'revisions' 
					)
				) 
			);
			
			$invoice->set_status( [ 'issued' ] );
			
			$notice =  new nCPT( 
				array( 
					'post_type'=>'notice',
					'description'=>'',
					/* 'menu_pos'=>53,*/
					'menu_icon'=>'email', 
					'hierarchical' => false,
					'exclude_from_search' => true,
					'supports' => array( 
						'title', 
						'editor', 
						'comments', 
						'author', 
						'revisions' 
					)
				) 
			);

			$notice->set_status( [ 'sent', 'posted', 'delivered', 'opened', 'read', 'clicked' ] );
			
			$noticetemplate =  new nCPT( 
				array( 
					'post_type'=>'noticetemplate',
					'description'=>'',
					/* 'menu_pos'=>53,*/
					'menu_icon'=>'admin-page', 
					'hierarchical' => false,
					'exclude_from_search' => true,
					'supports' => array( 
						'title', 
						'editor', 
						'comments', 
						'author', 
						'revisions' 
					)
				) 
			);	
			
			$record =  new nCPT( 
				array( 
					'post_type'=>'record',
					'description'=>'',
					/* 'menu_pos'=>53,*/
					'menu_icon'=>'backup', 
					'hierarchical' => false,
					'exclude_from_search' => true,
					'supports' => array( 
						'title', 
						'editor', 
						'comments', 
						'author', 
						'revisions' 
					)
				) 
			);		
			$record->set_status( [ 'recorded' ] );
			
			//metaBoxes are temporarily disabled.
			//add_filter( 'rwmb_meta_boxes', array( $this, 'set_meta_boxes') );
		}
		
		
		private function set_meta_boxes( $meta_boxes ) {
			$prefix = NN_PREFIX;
			
			//For Guides(
			//(Replace below code when ready)
			/* [OLD]
				$meta_boxes[] = array(
				'id' => 'crm_posts',
				'title' => esc_html__( 'User', 'nbcs-crm' ),
				'post_types' => array( 'post' ),
				'context' => 'side',
				'priority' => 'high',
				'autosave' => true,
				'fields' => array(
					array(
						'id' => $prefix . 'user',
						'type' => 'user',
						'field_type' => 'select_advanced',
					),
					array(
						'id' => $prefix . 'contact',
						'name' => esc_html__( 'Type of Contact', 'nbcs-crm' ),
						'type' => 'select',
						'placeholder' => esc_html__( 'Select an Item', 'nbcs-crm' ),
						'options' => array(
							'form_inquiry' => 'Form Inquiry',
							'chat' => 'Chat',
							'email_sent' => 'Email Sent',
							'email_received' => 'Email Received',
							'phone_sent' => 'Phone Call Made',
							'phone_received' => 'Phone Call Received',
							'admin_note' => 'Admin Note',
							'automated' => 'Automated',
							
						),
					),
					
				),
			); */
			

			return $meta_boxes;
		}

		
		public function set_caps(){
			
			foreach( $this->post_types as $pt ){
				
				//I need a list of all the capabilities to add to the admin. 
				$cpt = new nCPT( array( 'post_type' => $pt ) );
				
				
				
				//Then I need to add_caps to the admin: 
				 $admin = get_role( 'administrator' );
				 
				 foreach( $caps as $cap ){
					 
					 $admin->add_cap( $cap );
				 }

			}
			
			
			
		}
		
		public function remove(){
			
			$types = $this->post_types;
			foreach( $types as $type )
				unregister_post_type( $type );
			
		}
	}
}


?>