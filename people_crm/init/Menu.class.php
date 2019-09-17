<?php

/*
people_crm\init\Menu 

(Admin) Menu  -  Menu Class for People CRM 
Last Updated 16 Sept 2019
-------------
	
Description: 

		
*/

namespace people_crm\init;

use \nn_network\init\AdminMenu as AdminMenu;
 
Class Menu{
	
	public $remove_menus = array(
		'posts',
		'media',
		'pages',
		'comments',
		'appearance',
		'plugins',
		'tools',
		'users',
		'settings',
	), 
	$add_menus = array(

		'company' => array(
			'icon'  => 'admin-site',
			'sub'  => [
				'company_overview',
				'stats',	
				'help',
			]
		),
		'patrons' => array(
			'icon'  => 'heart',
			'sub'  => [
				'all_patrons',
				'new_patron',
				'instructor_student_tool',
				'new_account'
			]
		),
		'communications' => array(
			'icon'  => 'format-chat',
			'sub'  => [
				'messaging_tool',
				'automated_messages',
				'message_templates',
				'messaging_logs',
			]
		),
		'finance' => array( 
			'icon'  => 'chart-line',
			'sub'  => [
				'invoices',
				'transactions',
				'new_financal',
				'payment_logic',
			]
		),
		'reports' => array(
			'icon'  => 'chart-pie',
			'sub'  => [
				'financial_reports',
				'educational_reports',
				'misc_reports',
				'report_settings',
			]
		),
		'utilities' => array(
			'icon'  => 'admin-settings',
			'sub'  => [
				'people_crm',			
				'payment_gateways', 
				'WP_default_menus',
			]
		)
	);
	
	
	
	public function __construct(){
		
		add_action('admin_menu', array( $this, 'process_menus' ), 99);
		
	}
	
	public function process_menus( ){
				
		$this->add_menus();
		$this->remove_menus();
		
	}
	
	
	public function add_menus(){
		
		$menu = new AdminMenu( $this->add_menus , 'add' );
		
		/* $menu = new AdminMenu();
		$menus_added = $menu->add_menu( $this->add_menus ); */
		
	}
	
	
	public function remove_menus(){
		
		$menu = new AdminMenu( $this->remove_menus , 'remove' );
		
		/* $menu = new AdminMenu();
		$menus_removed = $menu->remove_menu( $this->remove_menus ); */
		
	}
	
	
	
	
}



?>