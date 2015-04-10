<?php
/**
 * Displays the tag archive page.
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// vtt_print( 'PAGE:tag.php' );
global $vtt_config, $vtt_template_vars;

$vtt_template_vars = array();
$vtt_template_vars['content-type'] = 'listing';
$vtt_template_vars['page-title'] = single_tag_title( '', false );
$vtt_template_vars['description'] = tag_description( get_queried_object_id() );

vtt_get_template_part( 'standard', 'page' );

