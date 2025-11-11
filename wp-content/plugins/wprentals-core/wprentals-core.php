<?php
/*
Plugin Name: WpRentals -Theme Core Functionality
Plugin URI: https://themeforest.net/user/wpestate
Description: Adds functionality to WpRentals
Version: 3.14.1
Author: wpestate
Author URI: https://wpestate.org
License: GPL2
Text Domain: wprentals-core
Domain Path: /languages
*/


 /** 
 * Main plugin file that initializes all WPRentals functionality.
 * Handles constants definition, file loading, and basic plugin setup.
 *
 * @package     WPRentals
 * @subpackage  Core
 * @version     4.0
 * @author      wpestate
 * @copyright   Copyright (c) 2024, WPEstate
 * @license     GPL2+
 *
 * Required Core Files:
 * @uses includes/class-wprentals-loader.php     - Core loader class
 * @uses includes/core-functions.php             - Essential plugin functions
 * @uses includes/enqueue-scripts.php            - Script and style registration
 * @uses includes/init-redux.php                 - Redux framework initialization
 * @uses includes/helper-functions.php           - Utility functions
 * @uses includes/meta-functions.php             - Meta related functions
 *
 */


/**
 * Define plugin constants
 * These constants are used throughout the plugin for file paths and URLs
 */
// Base plugin URL
if (!defined('WPESTATE_PLUGIN_URL')) {
    define('WPESTATE_PLUGIN_URL', plugins_url());
}

// Plugin directory URL
if (!defined('WPESTATE_PLUGIN_DIR_URL')) {
    define('WPESTATE_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
}

// Plugin directory path
if (!defined('WPESTATE_PLUGIN_PATH')) {
    define('WPESTATE_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

// Plugin base name
if (!defined('WPESTATE_PLUGIN_BASE')) {
    define('WPESTATE_PLUGIN_BASE', plugin_basename(__FILE__));
}



/**
 * Load required core files
 * Each file contains specific functionality grouped by purpose
 */
$core_files = array(
    'includes/class-wprentals-loader.php',  // Core loader class
    'includes/core-functions.php',          // Essential functions
    'includes/enqueue-scripts.php',         // Script handling
    'includes/init-redux.php',              // Redux framework
    'includes/helper-functions.php',        // Utility functions
    'includes/meta-functions.php',          // Meta functions
    'shortcodes/shortcodes_install.php',          // Meta functions
);




foreach ($core_files as $file) {
    $file_path = WPESTATE_PLUGIN_PATH . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    } else {
        error_log(
            sprintf(
                /* translators: %s: The file path that is missing */
                esc_html__('WPRentals: Required file %s not found', 'wprentals-core'),
                $file_path
            )
        );
    }
}
// Initialize the loader - it will handle its own timing now
WpRentals_Loader::get_instance();
/**
 * Register core plugin hooks
 * These hooks initialize the plugin's basic functionality
 */


/**
 * Plugin activation handler
 * Triggers on plugin activation
 *
 * @since 4.0
 * @return void
 */

function wpestate_rentals_functionality_plugin_activated() {
    do_action('wpestate_rentals_plugin_activated');
}

/**
 * Plugin deactivation handler
 * Triggers on plugin deactivation
 *
 * @since 4.0
 * @return void
 */

function wpestate_rentals_deactivate() {
    do_action('wpestate_rentals_plugin_deactivate');
}




add_action('wp_enqueue_scripts', 'wpestate_rentals_enqueue_styles');
add_action('admin_enqueue_scripts', 'wpestate_rentals_enqueue_styles_admin');
add_action('plugins_loaded', 'wpestate_rentals_functionality_loaded');
register_activation_hook(__FILE__, 'wpestate_rentals_functionality_plugin_activated');
register_deactivation_hook(__FILE__, 'wpestate_rentals_deactivate');



