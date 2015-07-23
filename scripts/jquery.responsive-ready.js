
	var window_scroll_position = 0;
	
	jQuery(document).ready( function()
	{
		jQuery('#responsive-menu').append( jQuery('#full-menu').html() );
		jQuery('#full-menu').addClass('hide');

		jQuery('#responsive-title .relative-wrapper').append( '<div class="menu-button icon-button"></div>' );
		jQuery('#responsive-title .menu-button').click( function()
		{
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

		// adding close button to admin bar.
		//jQuery('#responsive-menu').prepend( '<div class="close-menu-button icon-button"></div>' );
		//jQuery('#responsive-menu .close-menu-button').click( function()
		jQuery('#wp-admin-bar-close-menu-button').click( function()
		{
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

	