

jQuery(document).ready(
	function()
	{
		// setup all List Table Inline Bulk Action rows.
		jQuery('table.list-table-inline-bulk-action').ListTableInlineBulkAction();
	}
);


( function( $ ) {
	
	/**
	 * ListTableInlineBulkAction
	 * 
	 * The ListTableInlineBulkAction jQuery plugin ...
	 * 
	 * @package    apl
	 * @author     Crystal Barton <cbarto11@uncc.edu>
	 */
	$.fn.ListTableInlineBulkAction = function( options )
	{
		/**
		 * Disables the bulk action lists (top and bottom).
		 */
		function disable_bulk_action_lists()
		{
			$('input#doaction').prop('disabled', true);
			$('input#doaction2').prop('disabled', true);
			$('#bulk-action-selector-top').prop('disabled', true);
			$('#bulk-action-selector-bottom').prop('disabled', true);
		}
		
		
		/**
		 * Enables the bulk action lists (top and bottom).
		 */
		function enable_bulk_action_lists()
		{
			$('input#doaction').prop('disabled', false);
			$('input#doaction2').prop('disabled', false);
			$('#bulk-action-selector-top').prop('disabled', false);
			$('#bulk-action-selector-bottom').prop('disabled', false);
		}
		
		
		/**
		 * Removes all inline bulk action rows which may be displaying.
		 * @param  array  settings  The settings for the plugin.
		 */
		function remove_all_inline_bulk_action_rows( settings )
		{
			$( settings.table ).find( 'tr.inline-bulk-action' ).remove();
		}
		
		
		/**
		 * Shows the inline bulk action row.
		 * @param  array  settings  The settings for the plugin.
		 */
		function show( settings )
		{
			remove_all_inline_bulk_action_rows( settings );
			$( settings.table ).prepend( $(settings.this).html() );
			
			$( settings.table ).find('button.bulk-save').click( 
				function(event) {
					save(settings);
			});
			$( settings.table ).find('button.bulk-cancel').click(
				function(event) {
					event.preventDefault();
					hide(settings);
			});
			disable_bulk_action_lists();
		}
		
		
		/**
		 * Hides the inline bulk action row.
		 * @param  array  settings  The settings for the plugin.
		 */
		function hide( settings )
		{
			remove_all_inline_bulk_action_rows( settings );
			enable_bulk_action_lists();
		}
		
		
		/**
		 * Saves the item in the inline bulk action row.
		 * @param  array  settings  The settings for the plugin.
		 */
		function save( settings )
		{
			// just let it submit...
		}
		
		
		/**
		 * Setup each List Table Inline Bulk Action.
		 */
		return this.each(function() {
			
			// create a compilation of the settings.
			var settings = {
				'this'     : this,
				'table'    : (($(this).attr('table')) ? $(this).attr('table') : null),
				'action'   : (($(this).attr('action')) ? $(this).attr('action') : null),
			};
			if(options) $.extend(settings, options);
			
			// need table and action values.
			if( !settings.table || !settings.action ) return;
			
			// need table class to be valid.
			settings.table = $('table.wp-list-table.'+settings.table+' #the-list');
			if( !settings.table ) return;

			// setup bulk action selection (top) to display the inline bulk action row.
			$('input#doaction').click( 
				function(event)
				{
					var action = $('#bulk-action-selector-top').val();
					if( action == settings.action )
					{
						event.preventDefault();
						show( settings );
					}
				}
			);
			
			// setup bulk action selection (bottom) to display the inline bulk action row.
			$('input#doaction2').click( 
				function(event)
				{
					var action = $('#bulk-action-selector-bottom').val();
					if( action == settings.action )
					{
						event.preventDefault();
						show( settings );
					}
				}
			);		
		});
	}

})( jQuery )

