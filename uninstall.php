<?php
//PEOPLE CRM UNINSTALL SCRIPT

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

//Delete Options installed by plugin

$option_name = 'people_crm_db_version';
 
delete_option($option_name);
 
// for site options in Multisite
delete_site_option($option_name);
 
 
//Delete Tables Installed Plugin
 
// drop a custom database table
global $wpdb;

$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}webhooks");

$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}gate");


?>