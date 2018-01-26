/**
 * jQuery script for the Responsive mobile menu.
 *
 * @package    variations-template-theme
 * @author     Crystal Barton <atrus1701@gmail.com>
 * @version    1.0
 */


// Last saved scroll position of the window.
var window_scroll_position = 0;


jQuery(document).ready( function()
{
	// Copy the full menu into the responsive menu area.
	jQuery('body').prepend('<div id="responsive-menu"></div>');
	//jQuery('#responsive-menu').append( jQuery('#full-menu').html() ).addClass('hide');
	//jQuery('#full-menu').addClass('hide');
    jQuery('#responsive-menu').addClass('hide');

	// Add menu button to the responsive title.
	jQuery('#responsive-title .relative-wrapper').append('<div class="menu-button icon-button"></div>');
	jQuery('#responsive-title .menu-button').click( function()
	{	
		jQuery('#full-menu').detach().appendTo( jQuery('#responsive-menu') )
		jQuery('#full-menu').removeClass('hide');
		window_scroll_position = jQuery(window).scrollTop();
		jQuery('#responsive-menu').removeClass('hide');
		jQuery(window).scrollTop(0);
		jQuery('#site-outside-wrapper')
			.animate({
				'left':'100%'
			},
			'fast',
			function() {
				jQuery('#site-outside-wrapper').addClass('hide');
			});
		return false;
	});


	// Add menu close button to the admin bar.
	jQuery('#wp-admin-bar-close-menu-button').click( function()
	{
		jQuery('#full-menu').addClass('hide');
		jQuery('#full-menu').detach().prependTo( jQuery('#main') )
		jQuery('#site-outside-wrapper').removeClass('hide');
		jQuery('#site-outside-wrapper')
			.animate({
				'left':'0'
			},
			'fast',
			function() {
				jQuery('#responsive-menu').addClass('hide');
				jQuery(window).scrollTop(window_scroll_position);
			});
		return false;
	});
});

