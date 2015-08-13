<?php
/**
 * Displays the author page.
 *
 * @package variations-template-theme
 */

// vtt_print( 'PAGE:author.php' );
vtt_set_page_content_type( 'author' );
vtt_set_page_title( get_the_author_meta('display_name') );
vtt_set_page_listing_name( 'AUTHOR' );
vtt_render_page();
