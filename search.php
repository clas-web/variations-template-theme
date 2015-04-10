<?php
/**
 * Displays the search results.
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// vtt_print( 'PAGE:search.php' );
global $vtt_config, $vtt_template_vars;

$vtt_template_vars = array();
$vtt_template_vars['content-type'] = 'search';
$vtt_template_vars['page-title'] = get_search_query();
$vtt_template_vars['listing-name'] = 'Full-Text Search';

vtt_get_template_part( 'standard', 'page' );

