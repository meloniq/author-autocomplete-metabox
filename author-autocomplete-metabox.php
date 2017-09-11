<?php
/*
Plugin Name: Author AutoComplete Metabox
Plugin URI: http://blog.meloniq.net/
Description: Replaces standard Author metabox with the one powered by AutoComplete feature.

Version: 0.1

Author: MELONIQ.NET
Author URI: http://www.meloniq.net
Text Domain: author-autocomplete-metabox
Domain Path: /languages

License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/


/**
 * Avoid calling file directly.
 */
if ( ! function_exists( 'add_action' ) ) {
	die( 'Whoops! You shouldn\'t be doing that.' );
}


/**
 * Plugin version and textdomain constants.
 */
define( 'AAM_VERSION', '0.1' );
define( 'AAM_TD', 'author-autocomplete-metabox' );


/**
 * Setup plugin.
 *
 * @return void
 */
function aam_setup() {
	// Load Text-Domain
	load_plugin_textdomain( AAM_TD, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

}
add_action( 'plugins_loaded', 'aam_setup' );


