<?php
/**
 * Displays the category archive page.
 *
 * @package variations-template-theme
*/

// vtt_print( 'PAGE:category.php' );
vtt_set_page_content_type( 'listing' );
vtt_set_page_title( single_cat_title('', false) );
vtt_set_page_description( category_description(get_queried_object_id()) );
vtt_render_page();
