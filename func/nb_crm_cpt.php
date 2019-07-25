<?php
//Register Custom Post Type

function nb_crm_cpt() {

	/*
		Action CPT
	*/
	  
	$nb_action_labels = array(
		'name' => _x('Actions', 'post type general name', 'nbcs-crm'),
		'singular_name' => _x('Action', 'post type singular name', 'nbcs-crm'),
		'add_new' => _x('Add New', 'action', 'nbcs-crm'),
		'add_new_item' => __('Add New Action', 'nbcs-crm'),
		'edit_item' => __('Edit Action', 'nbcs-crm'),
		'new_item' => __('New Action', 'nbcs-crm'),
		'all_items' => __('All Actions', 'nbcs-crm'),
		'view_item' => __('View Action', 'nbcs-crm'),
		'search_items' => __('Search Actions', 'nbcs-crm'),
		'not_found' =>  __('No actions found', 'nbcs-crm'),
		'not_found_in_trash' => __('No actions found in Trash', 'nbcs-crm'), 
		'parent_item_colon' => '',
		'menu_name' => __('Actions', 'nbcs-crm')
	);

	$nb_action_args = array(
		'labels' => $nb_action_labels,
		'public' => true,
		'publicly_queryable' => false,
		'query_var' => true,
		'has_archive' => true, 
		'hierarchical' => true,
		'menu_position' => 4,
		'menu_icon' => 'dashicons-clock',
		'supports' => array( 'title', 'editor', 'comments', 'author', 'revisions', 'excerpt' ), 
		
	); 
	  
	  register_post_type('action', $nb_action_args); 
}

add_action( 'init', 'nb_crm_cpt', 10, 0 ); 


//Registering Custom Post Status for Action post types.
function nb_asmt_post_status(){
	register_post_status( 'submitted', array(
		'label'                     => _x( 'Submitted', 'action' ),
		'public'                   => true,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Submitted <span class="count">(%s)</span>', 'Submitted <span class="count">(%s)</span>' ),
	) );
	register_post_status( 'incomplete', array(
		'label'                     => _x( 'Incomplete', 'action' ),
		'public'                   => true,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Incomplete <span class="count">(%s)</span>', 'Incomplete <span class="count">(%s)</span>' ),
	) );
	register_post_status( 'resubmitted', array(
		'label'                     => _x( 'Resubmitted', 'action' ),
		'public'                   => true,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Resubmitted <span class="count">(%s)</span>', 'Resubmitted <span class="count">(%s)</span>' ),
	) );
	register_post_status( 'completed', array(
		'label'                     => _x( 'Completed', 'action' ),
		'public'                   => true,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>' ),
	) );
	
}
add_action( 'init', 'nb_asmt_post_status', 10, 0 );


function nbcs_crm_get_user_meta_box( $meta_boxes ) {
	$prefix = 'nbcs-';
	
	//For Posts
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
	);
	
	//For actions
	$meta_boxes[] = array(
		'id' => 'crm_actions',
		'title' => esc_html__( 'User', 'nbcs-crm' ),
		'post_types' => array( 'action' ),
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
				'id' => $prefix . 'follow_up',
				'type' => 'datetime',
				'name' => esc_html__( 'Scheduled Follow-Up', 'nbcs-crm' ),
			),
			array(
				'id' => $prefix . 'action_type',
				'name' => esc_html__( 'Type of Action', 'nbcs-crm' ),
				'type' => 'select',
				'placeholder' => esc_html__( 'Select an Item', 'nbcs-crm' ),
				'options' => array(
					'email_send' => 'Send Email',
					'phone_send' => 'Make Call',
					'admin_action' => 'Admin Action',
					'automated' => 'Automated',
					'other' => 'Other',
				),
			),
			
		),
	);

	return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'nbcs_crm_get_user_meta_box' );

	
?>