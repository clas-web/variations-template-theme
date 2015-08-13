<?php
/**
 * Displays the tag archive page.
 *
 * @package variations-template-theme
 */

// vtt_print( 'PAGE:tag.php' );
vtt_set_page_content_type( 'listing' );
vtt_set_page_title( single_tag_title('', false) );
vtt_set_page_description( tag_description(get_queried_object_id()) );
vtt_render_page();


