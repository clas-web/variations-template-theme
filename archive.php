<?php
/**
 * Displays the Event custom post type archive page.
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// vtt_print( 'PAGE:archive.php' );
global $wp_query, $vtt_config, $vtt_template_vars;

$vtt_template_vars = array();
$vtt_template_vars['content-type'] = 'listing';

if( is_day() ):
	$vtt_template_vars['page-title'] = sprintf( 'Daily Archives: %s', '<span>'.get_the_date().'</span>' );
elseif( is_month() ):
	$vtt_template_vars['page-title'] = sprintf( 'Monthly Archives: %s', '<span>'.get_the_date('F Y').'</span>' );
elseif( is_year() ):
	$vtt_template_vars['page-title'] = sprintf( 'Yearly Archives: %s', '<span>'.get_the_date('Y').'</span>' );
else:
	$vtt_template_vars['page-title'] = 'Archives';
endif;

vtt_get_template_part( 'standard', 'page' );

