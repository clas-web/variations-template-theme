<?php
/**
 * Displays the category archive page.
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// uncc_print( 'PAGE:category.php' );
global $uncc_config, $uncc_template_vars;

$uncc_template_vars = array();
$uncc_template_vars['content-type'] = 'listing';
$uncc_template_vars['page-title'] = single_cat_title( '', false );
$uncc_template_vars['description'] = category_description( get_queried_object_id() );

uncc_get_template_part( 'standard', 'page' );

