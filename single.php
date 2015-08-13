<?php
/**
 * Displays a single page.
 *
 * @package variations-template-theme
 */

// vtt_print( 'PAGE:single.php' );
vtt_set_page_content_type( 'single' );
vtt_set_page_title( get_the_title() );
vtt_render_page();
