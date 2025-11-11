<?php 
/**
 * WPRentals Updates function
 * This file handles functions used in data updates betwen versions
 */

   
/**
 * Hook the migration to plugin update
 */
function wprentals_check_version_and_migrate() {
    $current_version = get_option('wprentals_version');

    if ($current_version && version_compare($current_version, '3.13', '<')) {
        wprentals_migrate_user_roles_to_313();
    }
    // Update version after migration
    update_option('wprentals_version', '3.13');
}

add_action('admin_init', 'wprentals_migrate_user_roles_to_313',9999);



 /**
 * One-time migration for user roles based on user_type meta
 * Run during plugin update to version 3.13
 * 
 * @return void
 */
function wprentals_migrate_user_roles_to_313() {

    // Verify admin privileges
    if (!current_user_can('manage_options')) {
        return;
    }
    // Check if migration already ran
    if (get_option('wprentals_role_migration_313_completed')) {
        return;
    }
     // Create roles if needed
    if (!get_role(WPRENTALS_ROLE_OWNER) || !get_role(WPRENTALS_ROLE_RENTER)) {
        wprentals_create_custom_roles();
    }

    // Get all users with 'user_type' meta
    $users = get_users(array(
        'meta_key' => 'user_type',
        'compare' => 'EXISTS',
        'fields' => array('ID'),
        'role__not_in' => array('administrator', 'editor', 'author', 'contributor'),
    ));

    foreach ($users as $user) {
        
        $user_type = intval(get_user_meta($user->ID, 'user_type', true));
        $user_obj = new WP_User($user->ID);

        // Determine role based on user_type
        $new_role = ($user_type === 0) ? WPRENTALS_ROLE_OWNER : WPRENTALS_ROLE_RENTER;
          
        // Store existing roles except administrator
        $existing_roles = array_diff($user_obj->roles, array('administrator', 'editor', 'author', 'contributor'));

        // Add new role without removing existing ones
        $user_obj->add_role($new_role);


        error_log( $user->ID. ' new role '.$new_role.'</br>') ;
          // Log migration
          error_log(sprintf(
            'WPRentals Migration: User %d assigned role %s while preserving roles: %s',
            $user->ID,
            $new_role,
            implode(', ', $existing_roles)
        ));
    }
    
    // Mark migration as complete
    update_option('wprentals_role_migration_313_completed', true, false);
}