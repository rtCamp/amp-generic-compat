<?php
/**
 * AMP Generic compatibility plugin bootstrap.
 *
 * @package   Google\AMP_Generic_Compat
 * @author    Your Name, Google
 * @license   GPL-2.0-or-later
 * @copyright 2020 Google Inc.
 *
 * @wordpress-plugin
 * Plugin Name: AMP Generic Compat
 * Plugin URI: https://wpindia.co.in/
 * Description: Plugin to add <a href="https://wordpress.org/plugins/amp/">AMP</a> plugin compatibility for generic elements like navigation and search.
 * Version: 0.1
 * Author: milindmore22
 * Author URI: https://wpindia.co.in/
 * License: GNU General Public License v2 (or later)
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Google\AMP_Generic_Compat;

// Add Admin settings.
require_once __DIR__ . '/admin/amp-generic-settings.php';

/**
 * Whether the page is AMP.
 *
 * @return bool Is AMP.
 */
function is_amp() {
	return function_exists( 'is_amp_endpoint' ) && is_amp_endpoint();
}

/**
 * Run Hooks.
 */
function add_hooks() {

	/**
	 *  Keep this if you are using theme.
	 */
	if ( is_amp() ) {
		/**
		 *  Remove action which might add scripts or inline scripts.
		 *
		 * @see https://developer.wordpress.org/reference/functions/remove_action/
		 */
		remove_action( 'wp_head', 'enequeue_themes_scripts', 1 );

		/**
		 * The Action will override the scripts and styles.
		 *
		 * @see https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/
		 */
		add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\override_scripts_and_styles', 11 );

		$amp_compat_enable = get_option( 'amp_compat_enable' );

		if ( ! empty( $amp_compat_enable ) ) {
			/**
			 * Add sanitizers to convert non-AMP functions to AMP components.
			 *
			 * @see https://amp-wp.org/reference/hook/amp_content_sanitizers/
			 */
			add_filter( 'amp_content_sanitizers', __NAMESPACE__ . '\filter_sanitizers' );
		}
	}

}

add_action( 'wp', __NAMESPACE__ . '\add_hooks' );

/**
 * Remove enqueued JS.
 *
 * @see lovecraft_load_javascript_files()
 */
function override_scripts_and_styles() {

	wp_enqueue_style( 'amp-generic-compat', plugin_dir_url( __FILE__ ) . '/css/amp-style.css', '', rand() );

}

/**
 * Add sanitizer to fix up the markup.
 *
 * @param array $sanitizers Sanitizers.
 * @return array Sanitizers.
 */
function filter_sanitizers( $sanitizers ) {
	require_once __DIR__ . '/sanitizers/class-sanitizer.php';
	$sanitizers[ __NAMESPACE__ . '\Sanitizer' ] = array();
	return $sanitizers;
}

/**
 * Bonus improvement: add font-display:swap to the Google Fonts!
 *
 * @see https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 * @see https://developers.google.com/fonts/docs/getting_started
 * @see https://developer.wordpress.org/reference/hooks/style_loader_src/
 *
 * @param string $src    Stylesheet URL.
 * @param string $handle Style handle.
 * @return string Filtered stylesheet URL.
 */
function filter_font_style_loader_src( $src, $handle ) {
	if ( 'google-font-handle' === $handle ) {
		$src = add_query_arg( 'display', 'swap', $src );
	}
	return $src;
}

//add_filter( 'style_loader_src', __NAMESPACE__ . '\filter_font_style_loader_src', 10, 2 );
