<?php

/**
 * Plugin Name: WP Lucky Search
 * Plugin URI: http://harishchaudhari.com/projects/wp-lucky-search/
 * Description: Adds a Google like I'm feeling lucky button to the WordPress Search form, and redirects to the random post. It also has few settings.
 * Tags: Search, advance search, I'm feeling lucky, lucky, google, wordpress, random, post, page, seo, analytics, bounce rate
 * Version: 1.0
 * Author: Harish Chaudhari
 * Author URI: http://harishchaudhari.com/
 * Text Domain: wpls
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html 
 */

/**
 * Main file, contains the plugin constants and activation register
 *
 * @package    WP Lucky Search
 */
if ( ! defined( 'WPLS_PATH' ) ) {
    /**
     *  The server file system path to the plugin directory
     *
     */
    define( 'WPLS_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WPLS_ASSETS_URL' ) ) {
    /**
     *  URL to plugin folder
     *
     */
    define( 'WPLS_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/' );
}

class WP_Lucky_Search {
    
    function __construct() {
        
        /**
         * Includes library files
         */
        foreach( glob ( WPLS_PATH . "/lib/*.php" ) as $filename ) {
            require_once( $filename );
        }
        
        /**
         * Includes admin files
         */
        if ( is_admin() ) {
            foreach( glob ( WPLS_PATH . "lib/admin/*.php" ) as $filename ) {
               require_once( $filename );
            }
        }
    }
}

/**
 * Instantiate the plugin's main class
 */
if( class_exists( 'WP_Lucky_Search' ) ) {
    global $wpls_global;
    $wpls_global = new WP_Lucky_Search();
}

/**
 * Houston, we're in the magical world of WordPress! :)
 */