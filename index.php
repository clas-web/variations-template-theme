<?php
/**
 * The template for displaying all pages.
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// uncc_print( 'PAGE:index.php' );
global $uncc_config, $uncc_template_vars;

$uncc_template_vars = array();
if( is_singular() ):
	$uncc_template_vars['content-type'] = 'single';
else:
	$uncc_template_vars['content-type'] = 'listing';
endif;
if( is_home() ):
	if( is_singular() ):
		global $post;
		$uncc_template_vars['page-title'] = $post->post_title;
	else:
		$uncc_template_vars['page-title'] = 'Home';
	endif;
else:
	$uncc_template_vars['page-title'] = 'Index Page';
endif;

uncc_get_template_part( 'standard', 'page' );

