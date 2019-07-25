<?php
//Grant Site Admins Access to User Accounts: 
//credit: https://wordpress.stackexchange.com/questions/21334/how-to-enable-a-site-administrator-to-edit-users-in-a-wordpress-network-multisi

function mc_admin_users_caps( $caps, $cap, $user_id, $args ){

    foreach( $caps as $key => $capability ){

        if( $capability != 'do_not_allow' )
            continue;

        switch( $cap ) {
            case 'edit_user':
            case 'edit_users':
                $caps[$key] = 'edit_users';
                break;
            case 'delete_user':
            case 'delete_users':
                $caps[$key] = 'delete_users';
                break;
            case 'create_users':
                $caps[$key] = $cap;
                break;
        }
    }

    return $caps;
}
add_filter( 'map_meta_cap', 'mc_admin_users_caps', 1, 4 );
remove_all_filters( 'enable_edit_any_user_configuration' );
add_filter( 'enable_edit_any_user_configuration', '__return_true');

/*
 * hide admin from user list
 */
add_action('pre_user_query','isa_pre_user_query');
function isa_pre_user_query($user_search) {
  $user = wp_get_current_user();
  if ($user->ID!=1) { // Is not administrator, remove administrator
    global $wpdb;
    $user_search->query_where = str_replace('WHERE 1=1',
      "WHERE 1=1 AND {$wpdb->users}.ID<>1",$user_search->query_where);
  }
}

?>