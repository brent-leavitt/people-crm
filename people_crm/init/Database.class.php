<?php 
/* 
people_crm\init\Database
Database - Class for Database Initalization in the People CRM Plugin
Last Updated  7 October 2019
-------------

Desription: This setups or remove additional database fields needed for the People CRM Plugin


---

*/

namespace people_crm\init\;


if ( ! defined( 'ABSPATH' ) ) { exit; }

if( !class_exists( 'Database' ) ){
	class Database{
		
		
		//Properties
		
		public $people_crm_db_version = '1.0';
		
		public $tables = [
					[ 
						'table_name' => 'webhooks', 
						'query' => [
							'event_id' => 'varchar(40) NOT NULL',
							'timestamp' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
							'data' => 'mediumtext NOT NULL',
						], 
						'primary_key' => 'event_id'
					],
					[ 
						'table_name' => 'gate', 
						'query' => [
							'id' => 'mediumint(15) NOT NULL AUTO_INCREMENT',
							'timestamp' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
							'reference_id' => 'varchar(40) NOT NULL',
							'action' => 'varchar(10) NOT NULL',
							'data' => 'mediumtext NOT NULL',
							'status' => 'varchar(15) NOT NULL',
						],
						'primary_key' => 'id'
					]
					
			];
			
		
		//Methods
		
		
	/*
		Name: __construct
		Description: 
	*/	
				
		
		public function __construct(){
			
			$this->init( );
		}	
				
		
	/*
		Name: init
		Description: 
	*/	
				
		
		public function init( ){
			
			global $people_crm_db_version;
			
			$people_crm_db_version = $this->people_crm_db_version;
						
		}	
				
						
	/*
		Name: setup
		Description: 
	*/	
				
		
		public function setup(){
			
			global $wpdb, $people_crm_db_version;
			
			add_option( 'people_crm_db_version', $this->people_crm_db_version );
			//character set:
			$charset_collate = $wpdb->get_charset_collate();

			//Needed for dbDelta function. 
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			
			foreach( $this->tables as $table ){
				
				$table_name = $wpdb->prefix . $table[ 'table_name' ];
				
				$sql = "CREATE TABLE $table_name (
				";
				
				foreach( $table[ 'query' ] as $key => $val ){
					$sql .= "{$key} {$val},
					";	
				}
				$sql .= "PRIMARY KEY \x20({$table[ 'primary_key' ]})
				) {$charset_collate};";
				
				//echo $sql;
				
				dbDelta( $sql );
			}
			
		
		}	
	}
}
?>