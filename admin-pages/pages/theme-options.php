<?php
/**
 * UNCC_ThemeOptionsAdminPage
 * 
 * This class controls the admin page "Theme Options".
 * 
 * @package    uncc
 * @subpackage admin-pages/pages
 * @author     Crystal Barton <cbarto11@uncc.edu>
 */

if( !class_exists('UNCC_ThemeOptionsAdminPage') ):
class UNCC_ThemeOptionsAdminPage extends APL_AdminPage
{
	
	/**
	 * Creates an UNCC_ThemeOptionsAdminPage object.
	 */
	public function __construct(
		$name = 'theme-options',
		$menu_title = 'Theme Options',
		$page_title = 'Theme Options',
		$capability = 'administrator' )
	{
		parent::__construct( $name, $menu_title, $page_title, $capability );
		
		$this->add_tab( new UNCC_ThemeOptionsVariationsTabAdminPage($this) );
		$this->add_tab( new UNCC_ThemeOptionsAdminPageHeaderTabAdminPage($this) );
	}
	
} // class UNCC_ThemeOptionsAdminPage extends APL_AdminPage
endif; // if( !class_exists('UNCC_ThemeOptionsAdminPage') )

