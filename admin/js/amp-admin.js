/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(function( $ ) {

    jQuery.fn.copy = function (options) {
        var $self    = this;
        var $_cloned = null;

        var _defaults = {
            attributes: ['id', 'name'],
            copyEvents: false,
        };
        var settings = jQuery.extend(_defaults, options);
        // Main
        var obj     = $(this);
        $_cloned    = jQuery(obj).clone(settings.copyEvents);
        var _childrens  = $_cloned.find('*');
        jQuery.each(_childrens, function (i, e) {
            var _oldId      = true;
            var _oldClass   = false;
            $e              = $(e);
            // Cerco indici nei seguenti attributi
            jQuery.each(settings.attributes, function (i, attrName) {
                try {   // Incremento indice numerico +1
                    var _content = $e.attr(attrName);
                    if (_content.match(/\d+/)) {
                        var _newContent = _content.replace(/\d+/i, function (index) {
                            return parseInt(index) + 1
                        });
                        $e.attr(attrName, _newContent);
			$e.val( '' );
                    }
                }catch(e){};
            });
        });
        // Chainable
        return $_cloned.each(function () {});
    }

    /**
     * Add Button.
     */
    $( document ).on( 'click', '.amp-compat-add-button', function( e ) {
	e.preventDefault();
	// Current table row.
	var $tr    = $(this).closest( 'tr' );

	// Copy and insert it after.
	$tr.copy().insertAfter( $tr );

    });
    
    /**
     * Remove Button.
     */
    $( document ).on( 'click', '.amp-compat-remove-button', function( e ) {
	e.preventDefault();
	// Current table row.
	var $tr    = $(this).closest( 'tr' );
	
	// Count All table rows.
	var $alltr  = $(this).closest( 'table' ).find( 'tr' );

	// Keep last element.
	if ( ( $alltr.length -=1) === 1 ) {
	    $tr.find( ':text' ).val( '' );
	    return false;
	}
	
	// Remove if not last element.
	$tr.remove();
    });
} );