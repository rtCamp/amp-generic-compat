<?php
/**
 * AMP Generic compatibility plugin bootstrap.
 *
 * @package   Google\AMP_Generic_Compat
 * @author    Your Name, Google
 * @license   GPL-2.0-or-later
 * @copyright 2020 Google Inc.
 */

namespace Google\AMP_Generic_Compat;

if ( is_admin() ) {
	add_action( 'admin_menu', __NAMESPACE__ . '\register_amp_compat_admin_menus' );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\register_amp_compat_admin_scripts' );
	add_action( 'admin_init', __NAMESPACE__ . '\amp_save_settings' );
}

/**
 * Register scripts.
 *
 * @param string $hook current hook.
 * @return null | void
 */
function register_amp_compat_admin_scripts( $hook ) {

	if ( ! in_array( $hook, array( 'toplevel_page_amp-compatibility-settings', 'amp-compatibility_page_amp-compatibility-settings-js', 'amp-compatibility_page_amp-compatibility-settings-css' ), true ) ) {
		return;
	}

	wp_enqueue_script( 'jquery' );

	$amp_settings = false;

	if ( 'amp-compatibility_page_amp-compatibility-settings-js' === $hook ) {
		$amp_settings = wp_enqueue_code_editor( array( 'type' => 'text/js' ) );
		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery( function() { wp.codeEditor.initialize( "amp-compat-js", %s ); } );',
				wp_json_encode( $amp_settings )
			)
		);
	}

	if ( 'amp-compatibility_page_amp-compatibility-settings-css' === $hook ) {
		$amp_settings = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery( function() { wp.codeEditor.initialize( "amp-compat-css", %s ); } );',
				wp_json_encode( $amp_settings )
			)
		);
	}

	// Bail if user disabled CodeMirror.
	if ( false === $amp_settings ) {
		return;
	}

	wp_enqueue_script( 'amp-generic-settings', plugin_dir_url( __FILE__ ) . 'js/amp-admin.js', array( 'jquery' ), rand(), true );
	wp_enqueue_style( 'amp-generic-settings', plugin_dir_url( __FILE__ ) . 'css/amp-admin.css', '', rand() );
}

/**
 * Add AMP javascript.
 */
function amp_compatibility_page_js() {
	$amp_compat_enable_js = get_option( 'amp_compat_js' );
	?>
	<div class="wrap">
		<h2><?php esc_html_e( 'Add Javascript to AMP pages' ); ?></h2>
		<div class="amp-compat-container">
			<form method="post">
				<table class="form-table">
					<tr>
						<th>
							<?php esc_html_e( 'Enable' ); ?>
						</th>
						<td>
							<input type="checkbox" value="1" name="amp_compat_enable_js" <?php checked( '1', $amp_compat_enable_js, true ); ?> />
						</td>
					</tr>
				</table>
				<table class="">
					<tr>
						<th>
							<?php esc_html_e( 'code box' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Rules' ); ?>
						</th>
					</tr>
					<tr>
						<td width="80%">
							<textarea name="amp_compat_js" id="amp-compat-js"></textarea>
						</td>
						<td width="20%">
							<ol>
								<li>Element getters like <code>getElementByName()</code>, <code>getElementsByClassName()</code>, <code>getElementsByTagName()</code>, <code>childNodes()</code>, <code>parentNode()</code>, and <code>lastChild()</code></li>
								<li>Mutators like <code>createTextNode()</code>, <code>appendChild()</code>, <code>insertBefore()</code>, <code>removeChild()</code>, and <code>replaceChild()</code></li>
								<li>Methods involving events like <code>addEventListener()</code>, <code>removeEventListener()</code>, and <code>createEvent()</code></li>
								<li>Property and attribute getters like <code>getAttribute()</code>, <code>hasAttribute()</code></li>
								<li>Event properties like <code>Event.target</code>, <code>Event.type</code>, and <code>Event.bubbles</code></li>
								<li>Element properties like <code>attributes</code>, <code>id</code>, <code>outerHTML</code>, <code>textContent</code>, <code>value</code>, <code>classList</code>, and <code>className</code></li>
								<li>And many more.</li>
							</ol>
						</td>
					</tr>
				</table>
				<div class="clearfix">
					<?php wp_nonce_field( 'amp_compat_save_settings_action', 'amp_compat_save_settings_action' ); ?>
					<button type="submit" class="button button-primary" name="save_amp_compat_settings" value="1"><?php esc_html_e( 'Save' ); ?></button>
				</div>
			</form>	
		</div>
	</div>
	<?php
}


/**
 * Add AMP javascript.
 */
function amp_compatibility_page_css() {
	$amp_compat_enable_js = get_option( 'amp_compat_css' );
	?>
	<div class="wrap">
		<h2><?php esc_html_e( 'Add CSS to AMP pages' ); ?></h2>
		<div class="amp-compat-container">
			<form method="post">
				<table class="form-table">
					<tr>
						<th>
							<?php esc_html_e( 'Enable' ); ?>
						</th>
						<td>
							<input type="checkbox" value="1" name="amp_compat_enable_css" <?php checked( '1', $amp_compat_enable_js, true ); ?> />
						</td>
					</tr>
				</table>
				<table class="">
					<tr>
						<th>
							<?php esc_html_e( 'code box' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Rules' ); ?>
						</th>
					</tr>
					<tr>
						<td width="80%">
							<textarea name="amp_compat_css" id="amp-compat-css"></textarea>
						</td>
						<td width="20%">
							<ol>
								<li>Element getters like <code>getElementByName()</code>, <code>getElementsByClassName()</code>, <code>getElementsByTagName()</code>, <code>childNodes()</code>, <code>parentNode()</code>, and <code>lastChild()</code></li>
								<li>Mutators like <code>createTextNode()</code>, <code>appendChild()</code>, <code>insertBefore()</code>, <code>removeChild()</code>, and <code>replaceChild()</code></li>
								<li>Methods involving events like <code>addEventListener()</code>, <code>removeEventListener()</code>, and <code>createEvent()</code></li>
								<li>Property and attribute getters like <code>getAttribute()</code>, <code>hasAttribute()</code></li>
								<li>Event properties like <code>Event.target</code>, <code>Event.type</code>, and <code>Event.bubbles</code></li>
								<li>Element properties like <code>attributes</code>, <code>id</code>, <code>outerHTML</code>, <code>textContent</code>, <code>value</code>, <code>classList</code>, and <code>className</code></li>
								<li>And many more.</li>
							</ol>
						</td>
					</tr>
				</table>
				<div class="clearfix">
					<?php wp_nonce_field( 'amp_compat_save_settings_action', 'amp_compat_save_settings_action' ); ?>
					<button type="submit" class="button button-primary" name="save_amp_compat_settings" value="1"><?php esc_html_e( 'Save' ); ?></button>
				</div>
			</form>	
		</div>
	</div>
	<?php
}

/**
 * Register Sub menu.
 */
function register_amp_compat_admin_menus() {
	add_menu_page( 'AMP Compatibility', 'AMP Compatibility', 'manage_options', 'amp-compatibility-settings', __NAMESPACE__ . '\amp_compatibility_page', 'dashicons-hammer' );
	add_submenu_page( 'amp-compatibility-settings', 'AMP Toggle', 'AMP Toggle', 'manage_options', 'amp-compatibility-settings', __NAMESPACE__ . '\amp_compatibility_page' );
	add_submenu_page( 'amp-compatibility-settings', 'Add JS', 'JS', 'manage_options', 'amp-compatibility-settings-js', __NAMESPACE__ . '\amp_compatibility_page_js' );
	add_submenu_page( 'amp-compatibility-settings', 'Add CSS', 'CSS', 'manage_options', 'amp-compatibility-settings-css', __NAMESPACE__ . '\amp_compatibility_page_css' );
}

/**
 * Add Compatibility Element.
 */
function amp_compatibility_page() {
	$amp_compat_settings = get_option( 'amp_compat_settings' );
	$amp_compat_enable   = get_option( 'amp_compat_enable' );
	?>
	<div class="wrap">
		<h2><?php esc_html_e( 'Add Compatablity Elements' ); ?></h2>
		<div class="amp-compat-container">
			<form method="post">
				<table class="form-table">
					<tr>
						<th>
							<?php esc_html_e( 'Enable' ); ?>
						</th>
						<td>
							<input type="checkbox" value="1" name="amp_compat_enable" <?php checked( '1', $amp_compat_enable, true ); ?> />
						</td>
					</tr>
				</table>
				<table class="form-table">
					<tr>
						<th>
							<?php esc_html_e( 'Element' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Element Class' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Element Toggle Class' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Action Element' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Action Element Class ' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Action Element Toggle Class' ); ?>
						</th>
						<th></th>
					</tr>
					<?php if ( ! empty( $amp_compat_settings ) ) : ?>

						<?php foreach ( $amp_compat_settings as $key => $amp_compat_setting ) : ?>
								<tr>
									<td>
										<input required="required" type="text" name="amp_compat_settings[<?php echo esc_attr( $key ); ?>][element]" placeholder="<?php esc_attr_e( 'div, nav, ul' ); ?>" value="<?php echo ! empty( $amp_compat_setting['element'] ) ? esc_attr( $amp_compat_setting['element'] ) : ''; ?>" />
									</td>
									<td>
										<input required="required" type="text" name="amp_compat_settings[<?php echo esc_attr( $key ); ?>][element_class]" placeholder="<?php esc_attr_e( 'menu, nav-menu' ); ?>" value="<?php echo ! empty( $amp_compat_setting['element_class'] ) ? esc_attr( $amp_compat_setting['element_class'] ) : ''; ?>" />
									</td>
									<td>
										<input required="required" type="text" name="amp_compat_settings[<?php echo esc_attr( $key ); ?>][element_toggle_class]" placeholder="<?php esc_attr_e( 'show, active' ); ?>" value="<?php echo ! empty( $amp_compat_setting['element_toggle_class'] ) ? esc_attr( $amp_compat_setting['element_toggle_class'] ) : ''; ?>" />
									</td>
									<td>
										<input required="required" type="text" name="amp_compat_settings[<?php echo esc_attr( $key ); ?>][action_element]" placeholder="<?php esc_attr_e( 'button, a' ); ?>" value="<?php echo ! empty( $amp_compat_setting['action_element'] ) ? esc_attr( $amp_compat_setting['action_element'] ) : ''; ?>" />
									</td>
									<td>
										<input required="required" type="text" name="amp_compat_settings[<?php echo esc_attr( $key ); ?>][action_element_class]" placeholder="<?php esc_attr_e( 'menu-toggle, toggle, nav-toggle' ); ?>" value="<?php echo ! empty( $amp_compat_setting['action_element_class'] ) ? esc_attr( $amp_compat_setting['action_element_class'] ) : ''; ?>" />
									</td>
									<td>
										<input required="required" type="text" name="amp_compat_settings[<?php echo esc_attr( $key ); ?>][action_element_toggle_class]" placeholder="<?php esc_attr_e( 'active, show' ); ?>" value="<?php echo ! empty( $amp_compat_setting['action_element_toggle_class'] ) ? esc_attr( $amp_compat_setting['action_element_toggle_class'] ) : ''; ?>" />
									</td>
									<td>
										<button class="button amp-compat-add-button" type="button">+</button>
										<button class="button amp-compat-remove-button" type="button">-</button>
									</td>
								</tr>
						<?php endforeach; ?>
					<?php else : ?>
					<tr>
						<td>
							<input required="required" type="text" name="amp_compat_settings[0][element]" placeholder="<?php esc_attr_e( 'div, nav, ul' ); ?>" />
						</td>
						<td>
							<input required="required" type="text" name="amp_compat_settings[0][element_class]" placeholder="<?php esc_attr_e( 'menu, nav-menu' ); ?>" />
						</td>
						<td>
							<input required="required" type="text" name="amp_compat_settings[0][element_toggle_class]" placeholder="<?php esc_attr_e( 'active, show' ); ?>" />
						</td>
						<td>
							<input required="required" type="text" name="amp_compat_settings[0][action_element]" placeholder="<?php esc_attr_e( 'button, a' ); ?>" />
						</td>
						<td>
							<input required="required" type="text" name="amp_compat_settings[0][action_element_class]" placeholder="<?php esc_attr_e( 'menu-toggle, toggle, nav-toggle' ); ?>" />
						</td>
						<td>
							<input required="required" type="text" name="amp_compat_settings[0][action_element_toggle_class]" placeholder="<?php esc_attr_e( 'active, show' ); ?>" />
						</td>
						<td>
							<button class="button amp-compat-add-button" type="button">+</button>
							<button class="button amp-compat-remove-button" type="button">-</button>
						</td>
					</tr>
					<?php endif; ?>
				</table>
				<div class="clearfix">
					<?php wp_nonce_field( 'amp_compat_save_settings_action', 'amp_compat_save_settings_action' ); ?>
					<button type="submit" class="button button-primary" name="save_amp_compat_settings" value="1"><?php esc_html_e( 'Save' ); ?></button>
				</div>
			</form>
		</div>	
	</div>
	<?php
}

/**
 * Save AMP Compat settings.
 */
function amp_save_settings() {
	$save_amp_compat_settings = filter_input( INPUT_POST, 'save_amp_compat_settings', FILTER_SANITIZE_STRING );
	if ( empty( $save_amp_compat_settings ) ) {
		return false;
	}

	$nonce = filter_input( INPUT_POST, 'amp_compat_save_settings_action', FILTER_SANITIZE_STRING );
	if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'amp_compat_save_settings_action' ) ) {
		wp_die( 'Sorry, your nonce did not verify.' );
	}

	$amp_compat_enable = filter_input( INPUT_POST, 'amp_compat_enable', FILTER_SANITIZE_NUMBER_INT );

	if ( ! empty( $amp_compat_enable ) ) {
		update_option( 'amp_compat_enable', $amp_compat_enable );
	} else {
		update_option( 'amp_compat_enable', '' );
	}

	$amp_compat_settings = filter_input( INPUT_POST, 'amp_compat_settings', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

	if ( ! empty( $amp_compat_settings ) ) {
		update_option( 'amp_compat_settings', $amp_compat_settings );
		add_action( 'admin_notices', __NAMESPACE__ . '\amp_compat_admin_notice__success' );
	} else {
		update_option( 'amp_compat_settings', '' );
		add_action( 'admin_notices', __NAMESPACE__ . '\amp_compat_admin_notice__error' );
	}
}

/**
 * Admin Notice Sucess.
 */
function amp_compat_admin_notice__success() {
	?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Settings Saved!' ); ?></p>
		</div>
	<?php
}

/**
 * Admin Notice failed.
 */
function amp_compat_admin_notice__error() {
	$class   = 'notice notice-error';
	$message = __( 'Settings Cleared!' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}
