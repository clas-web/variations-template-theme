<?php

/**
 * APL_Handler
 * 
 * The APL_Handler class is the main class which controls or "handles" the admin menus
 * and pages created.
 * 
 * @package    apl
 * @author     Crystal Barton <cbarto11@uncc.edu>
 */
if( !class_exists('APL_Handler') ):
class APL_Handler
{
	protected $menus;			// A collection of menus with associated admin pages.
	protected $pages;			// Single main admin pages or admin pages that are
	                            // children of an existing page (ex. "themes.php").
	
	public $current_page;		// The APL_AdminPage object of the current page.
	public $current_tab;		// The APL_TabAdminPage object of the current tab.
	public $controller;         // The controlling APL_AdminPage or APL_TabAdminPage object.
	
	public $disable_redirect;   // False if we need to attempt to redirect when POST data
	                            // is present, otherwise True.
	public $force_redirect_url;	//
	
	public $is_network_admin;   // True if only show pages on the network admin menu,
	                            // otherwise False to only show on a site's admin menu.
	


	/**
	 * Creates an APL_Handler object.
	 * @param  bool  $is_network_admin  True if only show pages on the network admin menu,
	 *                                  otherwise False to only show on a site's admin menu.
	 */
	public function __construct( $is_network_admin = false )
	{
		apl_start_session();
		
		$this->menus = array();
		$this->pages = array();
		
		$this->current_page = null;
		$this->current_tab = null;
		
		$this->disable_redirect = true;
		$this->force_redirect_url = false;
		
		$this->use_settings_api = true;
		
		$this->is_network_admin = $is_network_admin;

		if( $is_network_admin )
		{
			add_action( 'network_admin_menu', array($this, 'admin_menu_setup'), 10 );
		}
		else
		{
			add_action( 'admin_menu', array($this, 'admin_menu_setup'), 10 );
		}

		add_action( 'wp_ajax_apl-ajax-action', array($this, 'perform_ajax_request') );
		add_action( 'admin_init', array($this, 'possible_redirect'), 99999 );
	}
	

	/**
	 * Add a menu to the main admin menu.
	 * @param  APL_AdminMenu  $menu  An admin menu to be displayed in the main admin menu.
	 */
	public function add_menu( $menu )
	{
		$menu->set_handler( $this );
		$this->menus[] = $menu;
	}
	

	/**
	 * Add a page to the main admin menu.
	 * @param  APL_AdminPage  $page    Admin page to be displayed in the main admin menu.
	 * @param  string         $parent  The parent page's name/slug.
	 */
	public function add_page( $page, $parent = null )
	{
		if( $parent === null )
		{
			$parent = $page->name;
			$page->is_main_page = true;
			$this->pages[$parent] = array();
		}
		elseif( !in_array($parent, array_keys($this->pages)) )
		{
			$this->pages[$parent] = array();
		}
		
		$page->set_handler( $this );
		$page->set_menu( $parent );
		$this->pages[$parent][] = $page;
	}
	
	
	/**
	 * Setups the current admin page/tab, then sets up the needed hooks.
	 */
	public function setup()
	{
		$this->set_current_page();
		
		if( defined('DOING_AJAX') && DOING_AJAX ) return;
		
		global $pagenow;
		switch( $pagenow )
		{
			case 'options.php': break;
			
			default:
				if( $this->current_page )
				{
					add_action( 'admin_enqueue_scripts', array($this->current_page, 'enqueue_scripts') );
					add_action( 'admin_head', array($this->current_page, 'add_head_script') );
				}
				if( $this->current_tab )
				{
					add_action( 'admin_enqueue_scripts', array($this->current_tab, 'enqueue_scripts') );
					add_action( 'admin_head', array($this->current_tab, 'add_head_script') );
				}
				break;
		}
		
		if( $this->controller )
		{
			add_action( 'admin_init', array($this->controller, 'init') );
			
			add_action( 'admin_init', array($this->controller, 'register_settings') );
			add_action( 'admin_init', array($this->controller, 'add_settings_sections') );
			add_action( 'admin_init', array($this->controller, 'add_settings_fields') );
			
			add_action( 'admin_init', array($this->controller, 'process_page') );
		
			add_filter( 'set-screen-option', array($this->controller, 'save_screen_options'), 10, 3);
		}		
	}
	
	
	/**
	 * Sets up all the admin menus and pages.
	 */
	public function admin_menu_setup()
	{
		foreach( $this->menus as $menu )
		{
			$menu->admin_menu_setup();
		}
		
		foreach( $this->pages as $pagetree )
		{
			foreach( $pagetree as $page )
			{
				$page->admin_menu_setup();
			}
		}
	}
	
	
	/**
	 * Performs an AJAX request on each of the menus and admin pages.
	 */
	public function perform_ajax_request()
	{
		$this->set_current_page();
		
		if( $this->current_tab )
		{
			$this->current_tab->perform_ajax_request();
		}
		
		if( $this->current_page )
		{
			$this->current_page->perform_ajax_request();
		}
	}
	

	/**
	 * Determines the current page and tab being shown.
	 */
	protected function set_current_page()
	{
		global $pagenow;
		switch( $pagenow )
		{
			case 'options.php':
				$this->current_page = ( !empty($_POST['option_page']) ? $_POST['option_page'] : null );
				$this->current_tab = ( isset($_POST['tab']) ? $_POST['tab'] : null );
				$this->disable_redirect = true;
				break;
			
			case 'admin-ajax.php':
				$this->current_page = ( !empty($_POST['admin-page']) ? $_POST['admin-page'] : null );
				$this->current_tab = ( !empty($_POST['admin-tab']) ? $_POST['admin-tab'] : null );
				$this->disable_redirect = true;
				break;
			
			case 'admin.php':
			default:
				$this->current_page = ( !empty($_GET['page']) ? $_GET['page'] : null );
				$this->current_tab = ( isset($_GET['tab']) ? $_GET['tab'] : null );
				$this->disable_redirect = false;
				break;
		}
		
		if( $this->current_page )
		{
			$this->current_page = $this->get_page( $this->current_page );
			
			if( $this->current_page )
			{
				if( $this->current_tab )
					$this->current_tab = $this->current_page->get_tab_by_name($this->current_tab);	
				
				if( !$this->current_tab )
					$this->current_tab = $this->current_page->get_default_tab();
			}
		}

		$this->controller = null;
		if( !$this->current_page )
		{
			$this->current_page = null;
			$this->current_tab = null;
		}
		elseif( $this->current_tab )
		{
			$this->controller = $this->current_tab;
		}
		elseif( $this->current_page )
		{
			$this->controller = $this->current_page;
		}
		
		if( !$this->controller )
		{
			$this->disable_redirect = true;
		}
	}
	

	/**
	 * Retrieves the page that name/slug matches the page name.
	 * @param   string  $page_name   The name/slug of the page.
	 * @return  APL_AdminPage|false  The APL_AdminPage object that matches the page name,
	 *                               otherwise False.
	 */
	protected function get_page( $page_name )
	{
		foreach( $this->menus as $menu )
		{
			if( $p = $menu->get_page($page_name) )
				return $p;
		}
		
		foreach( $this->pages as $pagetree )
		{
			foreach( $pagetree as $page )
			{
				if( $page_name === $page->get_name() )
					return $page;
			}
		}

		return false;
	}
	
		
	/**
	 * Determines if the page has POST data and needs to redirect to a "clean" page.
	 * If a redirect is deemed necessary, then page to redirect to is determine and
	 * redirected to.
	 */
	public function possible_redirect()
	{
		if( (!$this->force_redirect_url) && ((empty($_POST)) || ($this->disable_redirect)) ) return;
		
		unset($_POST);
		$this->redirect();
	}
	
	
	/**
	 * Redirects to the http referer, if exists, else the current page.
	 */
	public function redirect()
	{
		if( $this->controller )
		{
			$this->controller->save_notice();
			$this->controller->save_error();
		}
		
		$redirect_url = ( $this->force_redirect_url ? $this->force_redirect_url : 
			( !empty($_REQUEST['_wp_http_referer']) ? $_REQUEST['_wp_http_referer'] : apl_get_page_url() )
		);
		
		wp_redirect( $redirect_url );
		exit;
	}
	
	
	/**
	 * Returns the name of the current page.
	 * @return  string|null  The name of the current page, if exists, otherwise null.
	 */
	public function get_page_name()
	{
		if( $this->current_page ) return $this->current_page->get_name();
		return null;
	}
	

	/**
	 * Returns the name of the current tab.
	 * @return  string|null  The name of the current tab, if exists, otherwise null.
	 */
	public function get_tab_name()
	{
		if( $this->current_tab ) return $this->current_tab->name;
		return null;
	}
	
	
	/**
	 * Returns the name of the current controller (page or tab).
	 * @return  string|null  The name of the current controller, if exists, otherwise null.
	 */
	public function get_name()
	{
		if( $this->controller ) return $this->controller->get_name();
		return null;
	}
	
} // class APL_Handler
endif; // if( !class_exists('APL_Handler') ):

