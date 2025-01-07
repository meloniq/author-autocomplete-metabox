<?php
/*
Plugin Name: Author AutoComplete Metabox
Plugin URI: https://blog.meloniq.net/
Description: Replaces standard Author metabox with the one powered by AutoComplete feature.

Version: 0.2

Author: MELONIQ.NET
Author URI: https://www.meloniq.net
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
define( 'AAM_TD', 'author-autocomplete-metabox' );


/**
 * Setup plugin.
 *
 * @return void
 */
function aam_setup() {
	// Load Text-Domain
	load_plugin_textdomain( AAM_TD, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// Load Metabox
	require_once( 'aam-metabox.php' );
	new AAM_Author_Metabox();
}
add_action( 'plugins_loaded', 'aam_setup' );


/**
 * Generate an HTML tag. Atributes are escaped. Content is NOT escaped.
 *
 * @param string $tag
 *
 * @return string
 */
if ( ! function_exists( 'html' ) ):
function html( $tag ) {
	static $SELF_CLOSING_TAGS = array( 'area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta' );

	$args = func_get_args();

	$tag = array_shift( $args );

	if ( is_array( $args[0] ) ) {
		$closing = $tag;
		$attributes = array_shift( $args );
		foreach ( $attributes as $key => $value ) {
			if ( false === $value ) {
				continue;
			}

			if ( true === $value ) {
				$value = $key;
			}

			$tag .= ' ' . $key . '="' . esc_attr( $value ) . '"';
		}
	} else {
		list( $closing ) = explode( ' ', $tag, 2 );
	}

	if ( in_array( $closing, $SELF_CLOSING_TAGS ) ) {
		return "<{$tag} />";
	}

	$content = implode( '', $args );

	return "<{$tag}>{$content}</{$closing}>";
}
endif;

