<?php
/**
 * Displays the category archive page.
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// uncc_print( 'PAGE:mt-archive.php' );
global $uncc_config, $uncc_template_vars;

$uncc_template_vars = array();
$uncc_template_vars['content-type'] = 'listing';

$uncc_template_vars['page-title'] = 'Archives';

$uncc_template_vars['listing-name'] = ( mt_is_filtered_archive() ? 'Filtered Archive' : 'Combined Archive' );
$uncc_template_vars['description'] = '';

//global $nh_clas_search_term;
//$nh_clas_search_term = $nh_template_vars['page-title'];

uncc_get_template_part( 'standard', 'page' );





