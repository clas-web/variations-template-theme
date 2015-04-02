<?php
/**
 * APL_AdminMenu
 * 
 * The APL_AdminMenu class is a representation of a collection of admin pages in
 * WordPress that will appear together in the main admin menu.
 * 
 * @package    apl
 * @author     Crystal Barton <cbarto11@uncc.edu>
 */

if( !class_exists('APL_AdminMenu') ):
class APL_AdminMenu
{

	protected $handler;				// The handler controlling the menu.
		
	public $name;					// The name/slug of the menu.
	public $menu_title;				// The title show on the left menu.

	protected $pages;				// The admin pages found in the menu.
	
	public $display_menu_tab_list;	// True if a tab list of all of the page should be
									// displayed, otherwise False.
	
	/**
	 * Creates an APL_AdminMenu object.
	 * Sets up the values for the admin menu.
	 * @param  string  $name        The name/slug of the menu.
	 * @param  string  $menu_title  The title shown on the left menu.
	 */
	public function __construct( $name, $menu_title )
	{
		$this->handler = null;
		
		$this->name = $name;
		$this->menu_title = $menu_title;

		$this->pages = array();
		
		$this->display_menu_tab_list = false;
	}
	
	
	/**
	 * Sets the menu's handler.
	 * @param  APL_Handler  $handler  The handler controlling the menu.
	 */
	public function set_handler( $handler )
	{
		$this->handler = $handler;
		foreach( $this->pages as $page ) { $page->set_handler( $handler ); }
	}
	

	/**
	 * Add a child page to the admin menu.
	 * @param  APL_AdminPage  $page  A child admin page for the admin menu.
	 */
	public function add_page( $page )
	{
		$page->set_handler( $this->handler );
		$page->set_menu( $this );
		$this->pages[] = $page;
	}
	

	/**
	 * Adds the admin menu to the main menu and sets up all associated pages.
	 */
	public function admin_menu_setup()
	{
		add_menu_page(
			$this->menu_title, 
			$this->menu_title,
			'administrator',
			$this->name,
			null
		);
		
		foreach( $this->pages as $page )
		{
			$page->admin_menu_setup();
		}

		remove_submenu_page( $this->name, $this->name );
		unset($GLOBALS['submenu'][$this->name][0]);
	}
	
	
	/**
	 * Retrieves the page that name/slug matches the page name.
	 * @param   string  $page_name   The name/slug of the page.
	 * @return  APL_AdminPage|false  The APL_AdminPage object that matches the page name,
	 *                               otherwise False.
	 */
	public function get_page( $page_name )
	{
		foreach( $this->pages as $page )
		{
			if( $page_name === $page->get_name() )
				return $page;
		}
		
		return false;
	}
	
	
	/**
	 * Displays the tab list for the all the pages within the admin menu.
	 */
	public function display_tab_list()
	{
		if( !$this->display_menu_tab_list ) return;
		
		?><h2 class="admin-menu nav-tab-wrapper"><?php

 		foreach( $this->pages as $page )
 		{
 			?>
 			<a 
			 href="?page=<?php echo $page->get_name(); ?>"
			 class="nav-tab <?php if( $page->is_current_page ) echo 'active'; ?>">
				<?php echo $page->page_title; ?>
			</a>
			<?php
 		}
		
		?></h2><?php
	}
		
} // class APL_AdminMenu
endif; // if( !class_exists('APL_AdminMenu') ):

