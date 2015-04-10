<?php
/**
 * The template for displaying all pages.
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// vtt_print('PAGE:page.php');
global $vtt_config, $vtt_template_vars;

$vtt_template_vars = array();
$vtt_template_vars['content-type'] = 'single';
$vtt_template_vars['page-title'] = get_the_title();

vtt_get_template_part( 'standard', 'page' );

