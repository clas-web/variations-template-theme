<?php
/**
 * The template for displaying all pages.
 *
 * @package variations-template-theme
 */

// vtt_print( 'PAGE:index.php' );

if( is_singular() ) $content_type = 'single';
else                $content_type = 'listing';

if( is_home() )
{
	if( is_singular() ):
		global $post;
		$page_title = $post->post_title;
	else:
		$page_title = 'Home';
	endif;
}
else
{
	$page_title = 'Index Page';
}

vtt_set_page_content_type( $content_type );
vtt_set_page_title( $page_title );
vtt_render_page();
