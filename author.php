<?php
/**
 * Displays the author page.
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// vtt_print( 'PAGE:author.php' );
global $wp_query, $vtt_config, $vtt_template_vars;

$vtt_template_vars = array();
$vtt_template_vars['content-type'] = 'author';
$vtt_template_vars['page-title'] = get_the_author_meta( 'display_name' );
$vtt_template_vars['listing-name'] = 'AUTHOR';

vtt_get_template_part( 'standard', 'page' );

