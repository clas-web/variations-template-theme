/**
 * jQuery for the Theme Customizer header position control.
 *
 * @package    variations-template-theme
 * @author     Crystal Barton <atrus1701@gmail.com>
 * @version    1.0
 */
jQuery(document).ready( function()
{
	/**
	 * Apply jQuery plugin to each #customize-control-vtt-header-title-position-control.
	 */
	jQuery('#customize-control-vtt-header-title-position-control').each( function()
	{
		var self = this;
		
		var select = jQuery(self).find('select');
		var position = jQuery(select).val();
		
		var position_controller = jQuery('<div class="position-controller"></div>');
		var above_container = jQuery('<div class="above-container"></div>');
		var container = jQuery('<div class="header-container"></div>');
		
		// Add a position box for each possible position for the header title.
		var v = [ 'vtop', 'vcenter', 'vbottom' ];
		var h = [ 'hleft', 'hcenter', 'hright' ];
		
		for( var c = 0; c < 3; c++ )
		{
			var pos = h[c]+' vabove';
			var cls = pos;
			if( position == pos ) cls += ' selected';
			jQuery(above_container).append('<div position="'+pos+'" class="pos '+cls+'"></div>');
		}
		jQuery(above_container).append('<br/>');
		jQuery(position_controller).append(above_container);
		
		for( var r = 0; r < 3; r++ )
		{
			for( var c = 0; c < 3; c++ )
			{
				var pos = h[c]+' '+v[r];
				var cls = pos;
				if( position == pos ) cls += ' selected';
				jQuery(container).append('<div position="'+pos+'" class="pos '+cls+'"></div>');
			}
			jQuery(container).append('<br/>');
		}
		jQuery(position_controller).append(container);
		
		// Add click event for each position box which changes the value of the select control.
		jQuery(position_controller).find('div.pos').click( function()
		{
			var new_position = jQuery( this ).attr('position');
			jQuery( position_controller ).find('div').removeClass('selected');
			jQuery( this ).addClass('selected');
			jQuery(select).val( new_position );
			jQuery(select).change();
		});
		

		// Add change event for the select control which changes the selected position box.
		jQuery(select).change( function()
		{
			var new_position = jQuery(select).val();
			jQuery( position_controller ).find('div').removeClass('selected');
			jQuery( self ).find( '.pos.'+(new_position.replace(' ','.')) ).addClass('selected');
		});


		// Append the created control to the theme customizer.
		jQuery(self).append(position_controller);
	});
	
});