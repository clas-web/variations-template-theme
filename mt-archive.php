<?php
/**
 * Displays the category archive page.
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// vtt_print( 'PAGE:mt-archive.php' );
global $vtt_config, $vtt_template_vars;

$vtt_template_vars = array();
$vtt_template_vars['content-type'] = 'listing';

$vtt_template_vars['page-title'] = 'Archives';

$vtt_template_vars['listing-name'] = ( mt_is_filtered_archive() ? 'Filtered Archive' : 'Combined Archive' );
$vtt_template_vars['description'] = '';

//global $nh_clas_search_term;
//$nh_clas_search_term = $nh_template_vars['page-title'];

vtt_get_template_part( 'standard', 'page' );





