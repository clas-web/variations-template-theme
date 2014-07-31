<?php
/**
 * Displays a single page.
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// uncc_print( 'PAGE:single.php' );
global $uncc_config, $uncc_template_vars;

$uncc_template_vars = array();
$uncc_template_vars['content-type'] = 'single';
$uncc_template_vars['page-title'] = get_the_title();

uncc_get_template_part( 'standard', 'page' );

