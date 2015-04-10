<?php
/**
 * Displays the search results.
 *
 * @package WordPress
 * @subpackage unc-charlotte-theme
 */

// uncc_print( 'PAGE:search.php' );
global $uncc_config, $uncc_template_vars;

$uncc_template_vars = array();
$uncc_template_vars['content-type'] = 'search';
$uncc_template_vars['page-title'] = get_search_query();
$uncc_template_vars['listing-name'] = 'Full-Text Search';

uncc_get_template_part( 'standard', 'page' );

