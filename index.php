<?php
/**
 * The template for displaying all pages.
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// vtt_print( 'PAGE:index.php' );
global $vtt_config, $vtt_template_vars;

$vtt_template_vars = array();
if( is_singular() ):
	$vtt_template_vars['content-type'] = 'single';
else:
	$vtt_template_vars['content-type'] = 'listing';
endif;
if( is_home() ):
	if( is_singular() ):
		global $post;
		$vtt_template_vars['page-title'] = $post->post_title;
	else:
		$vtt_template_vars['page-title'] = 'Home';
	endif;
else:
	$vtt_template_vars['page-title'] = 'Index Page';
endif;

vtt_get_template_part( 'standard', 'page' );

