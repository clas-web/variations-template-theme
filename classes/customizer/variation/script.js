/**
 * jQuery script for the Theme Customizer variation selector.
 *
 * @package    variations-template-theme
 * @author     Crystal Barton <atrus1701@gmail.com>
 */

jQuery(document).ready( function()
{
	 /* === Checkbox Multiple Control === */

    jQuery( '#customize-control-vtt-variation-choices-control input[type="checkbox"]' ).on(
        'change',
        function() {

            checkbox_values = jQuery( this ).parents( '.customize-control' ).find( 'input[type="checkbox"]:checked' ).map(
                function() {
                    return this.value;
                }
            ).get().join( ',' );

            jQuery( this ).parents( '.customize-control' ).find( 'input[type="hidden"]' ).val( checkbox_values ).trigger( 'change' );
        }
    );
	
	
	// Variation change event
	jQuery('#customize-control-vtt-variation-control select').change( function()
	{
		vtt_send_request( 'change-variation', jQuery(this).val() );
	});
	
	// Variation reset event
	jQuery('#customize-control-vtt-variation-control button').click( function()
	{
		vtt_send_request( 'reset', '' );
	});
	
	
	/**
	 * Send a request to the server to change the variation.
	 * @param  string  action  The action to send to the server.
	 * @param  string  value  The value associated with the action.
	 */
	function vtt_send_request( action, value )
	{
		// Setup up AJAX data.
		var data = {};
		data['action'] = 'vtt-variation-customizer-control';
		data['nonce'] = jQuery('#customize-control-vtt-variation-control input[name="vtt-variation-nonce"]').val();
		data['vtt-action'] = action;
		data['value'] = value;
		
		// Perform the AJAX request.
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: data,
			dataType: 'json'
		})
		.done(function( data )
		{
			if( data.status )
			{
				location.reload();
				return;
			}
			
			alert( "Error processing request: "+data.message );
		})
		.fail(function( jqXHR, textStatus )
		{
			alert( "Error processing request: "+jqXHR.responseText+': '+textStatus );
		});
	}
});

