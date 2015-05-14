
jQuery(document).ready( function()
{
	//
	// Variation change
	//
	jQuery('#customize-control-vtt-variation-control select').change( function()
	{
		vtt_send_request( 'change-variation', jQuery(this).val() );
	});
	
	//
	// Variation reset
	//
	jQuery('#customize-control-vtt-variation-control button').click( function()
	{
		vtt_send_request( 'reset', '' );
	});
	
	
	function vtt_send_request( action, value )
	{
		// setup up AJAX data.
		var data = {};
		data['action'] = 'vtt-variation-customizer-control';
		data['nonce'] = jQuery('#customize-control-vtt-variation-control input[name="vtt-variation-nonce"]').val();
		data['vtt-action'] = action;
		data['value'] = value;
		
		// perform the AJAX request.
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

