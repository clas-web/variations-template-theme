/**
 * 
 * @author Crystal Barton
 */
 

jQuery(document).ready( function()
{
	setup_sidebar( 'left-sidebar' );
	setup_sidebar( 'right-sidebar' );
	setup_sidebar( 'header-menu' );
	jQuery('#left-sidebar-button').click( function() { toggle_sidebar('left-sidebar'); } );
	jQuery('#right-sidebar-button').click( function() { toggle_sidebar('right-sidebar'); } );
	jQuery('#header-menu-button').click( function() { toggle_sidebar('header-menu'); } );
	jQuery('#overlay').click( function() { hide_all_sidebars(); } );
});


function setup_sidebar( id )
{
	var sidebar = jQuery('#'+id+'-wrapper');
	
	if( use_mobile_site )
	{
		jQuery('#content').removeClass('show-'+id);
		jQuery(sidebar).removeClass('show');
		jQuery('#content').addClass('hide-'+id);
		jQuery(sidebar).addClass('hide');
	}
	else
	{
		switch( sessionStorage.getItem(id) )
		{
			case 'false':
				jQuery('#content').removeClass('show-'+id);
				jQuery(sidebar).removeClass('show');
				jQuery('#content').addClass('hide-'+id);
				jQuery(sidebar).addClass('hide');
				break;

			default:
				sessionStorage.setItem( id, 'true' );
				jQuery('#content').removeClass('hide-'+id);
				jQuery(sidebar).removeClass('hide');
				jQuery('#content').addClass('show-'+id);
				jQuery(sidebar).addClass('show');
				break;
		}
	}
	
	var is_hidden = {
		'left-sidebar': jQuery('#left-sidebar-wrapper').hasClass('hide'),
		'right-sidebar': jQuery('#right-sidebar-wrapper').hasClass('hide'),
		'header-menu': jQuery('#header-menu-wrapper').hasClass('hide'),
	};
	
	for( var l in is_hidden )
	{
		if( is_hidden[l] )
		{
			jQuery('#'+l+'-button').removeClass('show');
			jQuery('#'+l+'-button').addClass('hide');
		}
		else
		{
			jQuery('#'+l+'-button').removeClass('hide');
			jQuery('#'+l+'-button').addClass('show');
		}
	}
}


function hide_all_sidebars()
{
	var sidebars = [ 'left-sidebar', 'right-sidebar', 'header-menu' ];
	
	for( var i in sidebars )
	{
		var id = sidebars[i];
		jQuery( '#'+id+'-wrapper' ).switchClass('show', 'hide', 200);
		jQuery( '#'+id+'-button').switchClass('show', 'hide', 0);
	}
	
	jQuery('#overlay').switchClass('show', 'hide', 0);
}


function toggle_sidebar( id )
{
	var sidebar = jQuery('#'+id+'-wrapper');
	
	var is_hidden = {
		'left-sidebar': jQuery('#left-sidebar-wrapper').hasClass('hide'),
		'right-sidebar': jQuery('#right-sidebar-wrapper').hasClass('hide'),
		'header-menu': jQuery('#header-menu-wrapper').hasClass('hide'),
	};
	
	if( use_mobile_site )
	{
		if( is_hidden[id] )
		{
			for( var l in is_hidden )
			{
				if( (l != id) && (!is_hidden[l]) )
				{
					jQuery('#'+l+'-wrapper').switchClass('show', 'hide', 0);
					jQuery('#'+l+'-button').switchClass('show', 'hide', 0);
				}
			}
			
			jQuery(sidebar).switchClass('hide', 'show', 200);
			jQuery('#'+id+'-button').switchClass('hide', 'show', 0);
			jQuery('#overlay').switchClass('hide', 'show', 200);
		}
		else
		{
			jQuery(sidebar).switchClass('show', 'hide', 200);
			jQuery('#'+id+'-button').switchClass('show', 'hide', 0);
			jQuery('#overlay').switchClass('show', 'hide', 0);
		}
	}
	else
	{
		if( is_hidden[id] )
		{
			jQuery('#content').switchClass('hide-'+id, 'show-'+id, 245);
			jQuery(sidebar).switchClass('hide', 'show', 250);
			jQuery('#'+id+'-button').switchClass('hide', 'show', 0);
			sessionStorage.setItem( id, 'true' );
		}
		else
		{
			jQuery('#content').switchClass('show-'+id, 'hide-'+id, 250);
			jQuery(sidebar).switchClass('show', 'hide', 245);
			jQuery('#'+id+'-button').switchClass('show', 'hide', 0);
			sessionStorage.setItem( id, 'false' );
		}
	}
}

