<?php
//Add custom pages



/** Step 2 (from text above). */
add_action( 'admin_menu', 'nb_crm_pages' );

/** Step 1. */
function nb_crm_pages() {
	
	//Add a new sub page. 
	add_submenu_page( NULL, 'CRM Overview', NULL, 'edit_posts', 'crm-overview', 'crm_overview_callback' );
	add_submenu_page( 'tools.php', 'User Import', 'User Import Tool', 'edit_posts', 'nb_user_migrate', 'nb_user_migrate_cb' );
		
}

/** Step 3. */
function crm_overview_callback() {
	if ( !current_user_can( 'edit_posts' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	
	$uid = $_REQUEST[ 'uid' ];
	$user = get_userdata( $uid );
	$u_name = $user->first_name .' '. $user->last_name;
	
	if( $u_name === " " )
			$u_name = $user->display_name;
	$u_cap = key($user->caps);
		
	echo "<div class='wrap'>";
	
	echo "<h1>Relationships Overview: <i><a href='/wp-admin/user-edit.php?user_id=$uid' title='Edit User Profile for $u_name'>$u_name</a></i>  <small>[ $u_cap ]</small></h1>";
	
	echo "<h2 class='wp-heading-inline'>Upcoming Actions </h2>  <a href='/wp-admin/post-new.php?post_type=action' class='page-title-action'>Add New</a>";
	nb_crm_list_query('action', $uid);

	echo "<h2 class='wp-heading-inline'>Posts History</h2>  <a href='/wp-admin/post-new.php' class='page-title-action'>Add New</a>";	
	nb_crm_list_query('post', $uid);
	
	
	echo '</div>';
}




function nb_crm_list_query( $type, $uid ){
		
	global $wpdb;
	
	$querystr = "
    SELECT $wpdb->posts.* 
    FROM $wpdb->posts, $wpdb->postmeta
    WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
    AND $wpdb->postmeta.meta_key = 'nbcs-user' 
    AND $wpdb->postmeta.meta_value = $uid 
    AND $wpdb->posts.post_status = 'publish' 
    AND $wpdb->posts.post_type = '$type'
    AND $wpdb->posts.post_date < NOW()
    ORDER BY $wpdb->posts.post_date DESC
	 ";
	
	$pageposts = $wpdb->get_results($querystr, OBJECT);
	
	$i = 1;
	
	foreach( $pageposts as $post ){
		echo '<hr />';

		//var_dump( $post );
		
		//Post title and link
		$post_title = $post->post_title;
		$post_id = $post->ID;
		$post_url = "/wp-admin/post.php?post=$post_id&action=edit";
		
		//post date
		$post_date = $post->post_date;	
		
		//post author
		$a_id = $post->post_author;
		$user_data = get_userdata( $a_id );
		$post_author = $user_data->first_name .' '. $user_data->last_name;
		
		if( $post_author === " " )
			$post_author = $user_data->display_name;
		
		echo "$i. <a href='$post_url'>$post_title</a> &emsp; $post_author &emsp; $post_date";
		
		$i++;
	}
	echo "<hr />";
	
	
	echo "<a href='/wp-admin/users.php'>&laquo;- Return to Users Overview</a>";
	
}




//Code to make these pages accessable: 
//credit: https://wordpress.stackexchange.com/questions/160422/add-custom-column-to-users-admin-panel

function nb_crm_modify_user_table( $column ) {
    $column['manage_crm'] = 'CRM';
    return $column;
}
add_filter( 'manage_users_columns', 'nb_crm_modify_user_table' );



function nb_crm_modify_user_table_row( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'manage_crm' :
            return "<a href='/wp-admin/admin.php?page=crm-overview&uid=$user_id'>Manage</a>";
            break;
        default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'nb_crm_modify_user_table_row', 10, 3 );





//Then some clean up. 

function crm_remove_menus(){
	
	if( !current_user_can( 'manage_sites' ) ){
		remove_menu_page( 'jetpack' );
		remove_menu_page( 'themes.php' );
		remove_menu_page( 'plugins.php' );
		remove_menu_page( 'tools.php' );
		remove_menu_page( 'options-general.php' );
		remove_menu_page( 'googleanalytics' );
	}
	
}

add_action( 'admin_menu', 'crm_remove_menus', 999 );


//add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null )


function nb_user_migrate_cb (){
	
	//'certificates-lms/view/admin/'.$slug.'.php';
		global $plugin_page, $title;
		
		echo "<div class='wrap'>
		<h1 id='wp-heading-inline'>$title</h1>";
		
		$path = NB_CRM_PATH.'func/'.$plugin_page.'.php';
		
		if (file_exists($path))
				require $path;
		else
			echo "$path not found! ";
		
		echo"</div>";
	
}



?>