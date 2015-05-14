
jQuery(document).ready( function()
{
	//
	// Variation change
	//
	jQuery('#customize-control-vtt-variation-control select').change( function()
	{
		// send new variation via ajax.
		location.reload();
	});
	
	//
	// Variation reset
	//
	jQuery('#customize-control-vtt-variation-control button').click( function()
	{
		// send reset request via ajax.
		location.reload();
	});
	
});

