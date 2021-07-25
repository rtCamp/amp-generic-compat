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
 * Version: 0.2
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
	return function_exists( 'amp_is_request' ) && amp_is_request();
}

/**
 * Run Hooks.
 */
function add_hooks() {

	/**
	 *  Keep this if you are using theme.
	 */
	if ( is_amp() ) {

		$amp_compat_enable_css = get_option( 'amp_compat_enable_css' );

		if ( ! empty( $amp_compat_enable_css ) ) {
			add_action( 'wp_head', __NAMESPACE__ . '\amp_custom_style' );
			add_action( 'amp_post_template_css', __NAMESPACE__ . '\amp_custom_style' );
		}

		$amp_compat_enable_js = get_option( 'amp_compat_enable_js' );

		if ( ! empty( $amp_compat_enable_js ) ) {
			if ( function_exists( 'amp_is_legacy' ) && amp_is_legacy() ) {
				add_action( 'amp_post_template_head', __NAMESPACE__ . '\amp_script_hash' );
				add_action( 'amp_post_template_body_open', __NAMESPACE__ . '\amp_script_open', PHP_INT_MAX );
				add_action( 'amp_post_template_footer', __NAMESPACE__ . '\amp_script_close', PHP_INT_MIN );
			} else {
				add_action( 'wp_head', __NAMESPACE__ . '\amp_script_hash' );
				add_action( 'wp_body_open', __NAMESPACE__ . '\amp_script_open', PHP_INT_MAX );
				add_action( 'wp_footer', __NAMESPACE__ . '\amp_script_close', PHP_INT_MIN );
			}
		}

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
 * Add AMP Scipt has meta.
 */
function amp_script_hash() {
	$amp_compat_js_hash = get_option( 'amp_compat_js_hash' );

	if ( empty( $amp_compat_js_hash ) ) {
		return;
	}

	printf( '<meta name="amp-script-src" content="%s">', esc_attr( $amp_compat_js_hash ) );
}

/**
 * AMP Script Open tag.
 */
function amp_script_open() {

	echo '<amp-script layout="container" script="amp_compat_js">';

}


/**
 * AMP Script close.
 */
function amp_script_close() {
	$amp_compat_js = get_option( 'amp_compat_js' );
	echo '</amp-script>';
	echo '<script id="amp_compat_js" type="text/plain" target="amp-script"> ' . $amp_compat_js . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Add AMP custom style.
 */
function amp_custom_style() {

	$amp_compat_css  = get_option( 'amp_compat_css' );
	$amp_compat_css .= 'amp-script { opacity:1};';

	if ( function_exists( 'amp_is_legacy' ) && amp_is_legacy() ) {

		echo $amp_compat_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	} else {
		?>
		<style type="text/css">
			<?php echo $amp_compat_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</style>
		<?php
	}
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
