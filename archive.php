<?php
/**
 * Displays the Event custom post type archive page.
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// uncc_print( 'PAGE:archive.php' );
global $wp_query, $uncc_config, $uncc_template_vars;

$uncc_template_vars = array();
$uncc_template_vars['content-type'] = 'listing';

if( is_day() ):
	$uncc_template_vars['page-title'] = sprintf( 'Daily Archives: %s', '<span>'.get_the_date().'</span>' );
elseif( is_month() ):
	$uncc_template_vars['page-title'] = sprintf( 'Monthly Archives: %s', '<span>'.get_the_date('F Y').'</span>' );
elseif( is_year() ):
	$uncc_template_vars['page-title'] = sprintf( 'Yearly Archives: %s', '<span>'.get_the_date('Y').'</span>' );
else:
	$uncc_template_vars['page-title'] = 'Archives';
endif;

uncc_get_template_part( 'standard', 'page' );

