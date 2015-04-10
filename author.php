<?php
/**
 * Displays the author page.
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// uncc_print( 'PAGE:author.php' );
global $wp_query, $uncc_config, $uncc_template_vars;

$uncc_template_vars = array();
$uncc_template_vars['content-type'] = 'author';
$uncc_template_vars['page-title'] = get_the_author_meta( 'display_name' );
$uncc_template_vars['listing-name'] = 'AUTHOR';

uncc_get_template_part( 'standard', 'page' );

