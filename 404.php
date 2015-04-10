<?php
/**
 * Displays when a requested page is not found (404 error).
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// vtt_print( 'PAGE:404.php' );
global $vtt_config, $vtt_template_vars;

$vtt_template_vars = array();
$vtt_template_vars['content-type'] = '404';
$vtt_template_vars['page-title'] = '404 Error: Page Not Found.';

vtt_get_template_part( 'standard', 'page' );

