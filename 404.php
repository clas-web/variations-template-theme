<?php
/**
 * Displays when a requested page is not found (404 error).
 *
 * @package variations-template-theme
 */

// vtt_print( 'PAGE:404.php' );
vtt_set_page_content_type( '404' );
vtt_set_page_title( '404 Error: Page Not Found.' );
vtt_render_page();
