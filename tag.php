<?php
/**
 * Displays the tag archive page.
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// uncc_print( 'PAGE:tag.php' );
global $uncc_config, $uncc_template_vars;

$uncc_template_vars = array();
$uncc_template_vars['content-type'] = 'listing';
$uncc_template_vars['page-title'] = single_tag_title( '', false );
$uncc_template_vars['description'] = tag_description( get_queried_object_id() );

uncc_get_template_part( 'standard', 'page' );

