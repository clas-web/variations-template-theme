<?php
/**
 * Displays the search results.
 *
 * @package variations-template-theme
 */

// vtt_print( 'PAGE:search.php' );
vtt_set_page_content_type( 'search' );
vtt_set_page_title( 'Search for: '.get_search_query() );
vtt_set_page_listing_name( 'Full-Text Search' );
vtt_render_page();
