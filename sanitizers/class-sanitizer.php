<?php
/**
 * Sanitizer
 *
 * @package Google\AMP_Generic_Compat
 */

namespace Google\AMP_Generic_Compat;

use AMP_Base_Sanitizer;
use DOMElement;
use DOMXPath;

/**
 * Class Sanitizer
 */
class Sanitizer extends AMP_Base_Sanitizer {

	/**
	 * Sanitize.
	 */
	public function sanitize() {

		$amp_compat_settings = get_option( 'amp_compat_settings' );

		if ( ! empty( $amp_compat_settings ) ) {

			foreach ( $amp_compat_settings as $amp_compat_setting ) {

				$unique_id                   = wp_unique_id( 'amp_toggle_' );
				$element                     = $amp_compat_setting['element'];
				$element_class               = $amp_compat_setting['element_class'];
				$element_toggle_class        = $amp_compat_setting['element_toggle_class'];
				$action_element              = $amp_compat_setting['action_element'];
				$action_element_class        = $amp_compat_setting['action_element_class'];
				$action_element_toggle_class = $amp_compat_setting['action_element_toggle_class'];

				$xpath = new DOMXPath( $this->dom );

				// setup element.
				$main_element        = $xpath->query( '//' . $element . '[contains(@class,"' . $element_class . '")]' )->item( 0 );
				$main_action_element = $xpath->query( '//' . $action_element . '[contains(@class,"' . $action_element_class . '")]' )->item( 0 );

				if ( $main_element instanceof DOMElement && $main_action_element instanceof DOMElement ) {

					$main_action_element->parentNode->insertBefore(
						$this->create_amp_state( $unique_id, false ),
						$main_action_element
					);

					// Added role and tabindex.
					if ( ! in_array( $action_element, array( 'a', 'button' ), true ) ) {
						$main_action_element->setAttribute( 'role', 'button' );
						$main_action_element->setAttribute( 'tabindex', '0' );
					}

					$main_action_element->setAttribute( 'on', 'tap:AMP.setState( { ' . $unique_id . ': ! ' . $unique_id . ' } )' );

					$main_action_element->setAttribute(
						'data-amp-bind-class',
						sprintf( '%1$s + ( ' . $unique_id . ' ? " %2$s" : "" )', wp_json_encode( $main_action_element->getAttribute( 'class' ) ), $element_toggle_class )
					);

					$main_element->setAttribute(
						'data-amp-bind-class',
						sprintf( '%1$s + ( ' . $unique_id . ' ? " %2$s" : "" )', wp_json_encode( $main_element->getAttribute( 'class' ) ), $action_element_toggle_class )
					);
				}
			}
		}

	}

	/**
	 * Create AMP state.
	 *
	 * @param string $id    State ID.
	 * @param mixed  $value State value.
	 * @return DOMElement An amp-state element.
	 */
	private function create_amp_state( $id, $value ) {
		$amp_state = $this->dom->createElement( 'amp-state' );
		$amp_state->setAttribute( 'id', $id );
		$script = $this->dom->createElement( 'script' );
		$script->setAttribute( 'type', 'application/json' );
		$script->appendChild( $this->dom->createTextNode( wp_json_encode( $value ) ) );
		return $amp_state;
	}
}
