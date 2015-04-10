<?php
/**
 * VTT_ThemeOptionsAdminPage
 * 
 * This class controls the admin page "Theme Options".
 * 
 * @package    variations-template-theme
 * @subpackage admin-pages/pages
 * @author     Crystal Barton <cbarto11@uncc.edu>
 */

if( !class_exists('VTT_ThemeOptionsAdminPage') ):
class VTT_ThemeOptionsAdminPage extends APL_AdminPage
{
	
	/**
	 * Creates an VTT_ThemeOptionsAdminPage object.
	 */
	public function __construct(
		$name = 'theme-options',
		$menu_title = 'Theme Options',
		$page_title = 'Theme Options',
		$capability = 'administrator' )
	{
		parent::__construct( $name, $menu_title, $page_title, $capability );
		
		$this->add_tab( new VTT_ThemeOptionsVariationsTabAdminPage($this) );
		$this->add_tab( new VTT_ThemeOptionsAdminPageHeaderTabAdminPage($this) );
	}
	
} // class VTT_ThemeOptionsAdminPage extends APL_AdminPage
endif; // if( !class_exists('VTT_ThemeOptionsAdminPage') )

