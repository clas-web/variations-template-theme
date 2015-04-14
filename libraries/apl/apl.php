<?php
/**
 * Main file for the APL library that sets up the library for use in plugins or themes.
 * 
 * @package    apl
 * @author     Crystal Barton <cbarto11@uncc.edu>
 * @copyright  2014-2015.
 * @version    1.0
 */


// version of APL contained in this library.
$apl_version = '1.0';

// instantiate the apl libraries global variable.
global $apl_libraries;
if( empty($apl_libraries) ) $apl_libraries = array();

// store the files needed for this verison of the APL library.
$apl_libraries[$apl_version] = array(
	dirname(__FILE__).'/functions.php',
	dirname(__FILE__).'/admin-menu.php',
	dirname(__FILE__).'/admin-page.php',
	dirname(__FILE__).'/tab-admin-page.php',
	dirname(__FILE__).'/tab-link.php',
	dirname(__FILE__).'/handler.php',
);

// once all plugins are loaded, then load the APL libarary.
add_action( 'after_setup_theme', 'apl_load', 1 );


/**
 * Loads the most recent version of the APL library.
 */
if( !function_exists('apl_load') ):
function apl_load()
{
	// APL library is already loaded.
	if( defined('APL') ) return;
	
	// sort the available APL libraries by version.
	global $apl_libraries;
	ksort( $apl_libraries, SORT_STRING );
	
	// determine the most recent version of the APLl library.
	$library_versions = array_keys($apl_libraries);
	if( count($library_versions) == 0 )
	{
		die( 'No Admin Page Library available to load.' );
	}
	$version = $library_versions[ count($library_versions)-1 ];
	
	// set the APL and APL_VERSION global variables.
	define( 'APL', 'Admin Page Library' );
	define( 'APL_VERSION', $version );
	
	// load the most recent version of the APL library.
	foreach( $apl_libraries[$version] as $file )
	{
		require_once( $file );
	}
	
// 	apl_print( APL_VERSION );
}
endif;

