<?php
/**
 * The template for displaying all pages.
 *
 * @package variations-template-theme
 */

// vtt_print('PAGE:page.php');
vtt_set_page_content_type( 'single' );
vtt_set_page_title( get_the_title() );
vtt_render_page();
