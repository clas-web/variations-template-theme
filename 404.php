<?php
/**
 * Displays when a requested page is not found (404 error).
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// uncc_print( 'PAGE:404.php' );
global $uncc_config, $uncc_template_vars;

$uncc_template_vars = array();
$uncc_template_vars['content-type'] = '404';
$uncc_template_vars['page-title'] = '404 Error: Page Not Found.';

uncc_get_template_part( 'standard', 'page' );

