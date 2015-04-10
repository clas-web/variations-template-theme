<?php
/**
 * Displays the category archive page.
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// vtt_print( 'PAGE:category.php' );
global $vtt_config, $vtt_template_vars;

$vtt_template_vars = array();
$vtt_template_vars['content-type'] = 'listing';
$vtt_template_vars['page-title'] = single_cat_title( '', false );
$vtt_template_vars['description'] = category_description( get_queried_object_id() );

vtt_get_template_part( 'standard', 'page' );

