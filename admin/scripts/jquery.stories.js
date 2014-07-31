

/**
 *
 */
jQuery(document).ready(function()
{
	jQuery('.story-selector').JqueryStorySelector();
});
	

/**
 *
 */
( function( $ ) {
			
	$.fn.JqueryStorySelector = function() {


		//--------------------------------------------------------------------------------
		// 
		//--------------------------------------------------------------------------------
		function preg_quote(str)
		{
			return (str + '').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, "\\$1");
		}

		//--------------------------------------------------------------------------------
		// 
		//--------------------------------------------------------------------------------
		function wrap(data, search, before, after)
		{
			return data.replace( new RegExp( preg_quote( search ), 'gi' ), before + search + after );
		}

		//--------------------------------------------------------------------------------
		// 
		//--------------------------------------------------------------------------------
		function get_nonce()
		{
			var nonce = $('input[name="nh-stories-options-nonce"]').val();
			return nonce;
		}

		//--------------------------------------------------------------------------------
		// 
		//--------------------------------------------------------------------------------
		function update_search_results( wrapper, search_text, section, selected_item, search_results_div )
		{
   			if( search_text.length < 3 )
   			{
   				$(search_results_div).html('<div class="enter-more">Enter at least 3 characters to search...</div>');
   				return;
   			}
   			
   			$(search_results_div).html('<div class="searching">Searching...</div>');
   			
   			var data = {};
			data['action'] = 'nh-stories-options';
			data['nonce'] = get_nonce();
			data['ajax-action'] = 'get-search-results';
			data['search_text'] = search_text;
			data['section'] = section;
			
			$.ajax( {
				type: 'POST',
				url: ajaxurl,
				data: data,
				dataType: 'json'
			})
			.done(function( data ) {
				if( data['status'] == false )
				{
					alert( "Failed: " + data['message'] );
				}
				else
				{
// 					console.log( data ); return;

					if( data['output'].length == 0 )
					{
						$(search_results_div).html('<div class="no-results">No results found.</div>');
						return;
					}

					$(search_results_div).html('');
					
					var row = 'odd';
					
					for( i in data['output'] )
					{
						var item_id = data['output'][i]['id'];
						var item_title = data['output'][i]['title'];
						//var formatted_title = item_title.replace(search_text,"<strong>"+search_text+"</strong>");
						var formatted_title = wrap(item_title, search_text, '<strong>', '</strong>');
						
						var hidden_item = $('<input type="hidden" />')
							.attr('value', item_id)
							.attr('post_title', item_title);
						var result_div = $('<div class="result '+row+'" />')
							.append(hidden_item)
							.append(formatted_title)
							.click(function( event )
							{
								var hidden_item = $(event.currentTarget).children('input[type="hidden"]');
								var item_id = $(hidden_item).val();
								var item_title = $(hidden_item).attr('post_title');
								display_selected_item( wrapper, selected_item, item_id, item_title );
							})
							.appendTo(search_results_div);
							
						if( row == 'odd' ) row = 'even'; else row = 'odd';
					}
				}
			})
			.fail(function( jqXHR, textStatus ) {
				alert( "Request failed: " + jqXHR.responseText + " - " + textStatus );
			});
		}
		
		//--------------------------------------------------------------------------------
		// 
		//--------------------------------------------------------------------------------
		function clear_selected_item( wrapper, selected_item )
		{
			$(wrapper).find('input[type="hidden"][class="story-selector"]')
				.attr('value', '-1')
				.attr('post_title', '');
			$(selected_item).html('<span class="latest-post"><< Latest Post >></span>');
		}

		//--------------------------------------------------------------------------------
		// 
		//--------------------------------------------------------------------------------
		function display_selected_item( wrapper, selected_item, item_id, item_title )
		{
			$(wrapper).find('input[type="hidden"][class="story-selector"]')
				.attr('value', item_id)
				.attr('post_title', item_title);
			$(selected_item).html('<span class="selected-post">'+item_title+'</span>');
		}

		//--------------------------------------------------------------------------------
		// 
		//--------------------------------------------------------------------------------
		function setup( item_tag )
		{
			
			//----------------------------------------------------------------------------
			// get section of the current stories section.
			//----------------------------------------------------------------------------
			var section = $(item_tag).attr('section');
			if( !section ) section = '';
			if( section == 'all' ) section = '';
			
			//----------------------------------------------------------------------------
			// wrapper for object.
			//----------------------------------------------------------------------------
			$(item_tag).wrap('<div class="selected-story-wrapper" />');
			var wrapper = $(item_tag).parent().addClass('clearfix');
			
			//----------------------------------------------------------------------------
			// The item that is currently selected.
			//----------------------------------------------------------------------------
			var selected_item = $('<div class="selected-item" />');
			var item_id = parseInt( $(item_tag).attr('value') );
			if( item_id == -1 )
			{
				clear_selected_item( wrapper, selected_item );
			}
			else
			{
				var item_title = $(item_tag).attr('post_title');
				display_selected_item( wrapper, selected_item, item_id, item_title );
			}
			
			//----------------------------------------------------------------------------
			// The searching object.
			//----------------------------------------------------------------------------
			var search_posts_div = $('<div class="search-posts" />');
			var search_results_div = $('<div class="search-results" />').hide();
			var search_textbox = $('<input type="text" />')
				.keyup(function( event )
				{
					var value = $(search_textbox).val();
					if( value.length > 0 )
					{
						$(search_results_div).show();
					}
					else
					{
						$(search_results_div).hide();
						return;
					}
					update_search_results( wrapper, value, section, selected_item, search_results_div );
				});

			//----------------------------------------------------------------------------
			// Reset selected item to "<< Latest Posts >>" object.
			//----------------------------------------------------------------------------
			var reset_selected_item = $('<div class="reset-item" />')
				.click(function( event )
				{
					clear_selected_item( wrapper, selected_item );
				});

			//----------------------------------------------------------------------------
			// Append objects into wrapper.
			//----------------------------------------------------------------------------
			$(wrapper).append(search_textbox);
			$(wrapper).append(reset_selected_item);
			$(wrapper).append(selected_item);
			$(wrapper).append(search_results_div);
		}
		
		return this.each( function() { setup(this); } );
	}
	
})( jQuery );

