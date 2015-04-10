<?php
/**
 * APL_TabAdminPage
 * 
 * The APL_TabAdminPage class is the representation of a tab within an admin page in 
 * WordPress that will appear as part of an admin page, but not in the main admin menu.
 * 
 * @package    apl
 * @author     Crystal Barton <cbarto11@uncc.edu>
 */

if( !class_exists('APL_TabAdminPage') ):
abstract class APL_TabAdminPage extends APL_AdminPage
{

	protected $page;			// The parent admin page that contains the tab.
	protected $is_current_tab;	// True if this is current tab being shown.
	
	public    $display_tab;     // True if tab should appear in tab list, else False.
	
	
	/**
	 * Creates an APL_TabAdminPage object.
	 * @param  APL_AdminPage  $page        The parent admin page that contains the tab.
	 * @param  string         $name        The name/slug of the tab.
	 * @param  string         $tab_title   The title of the tab.
	 * @param  string         $page_title  The title of the page when tab is active.
	 * @param  string         $capability  The capability needed to displayed to the user.
	 */
	public function __construct( $page, $name, $tab_title, $page_title = null, $capability = 'administrator' )
	{
		$this->page = $page;
		$this->display_tab = true;

		parent::__construct( $name, $tab_title, $page_title, $capability );
	}
	
	
	/**
	 * Sets up the current tab-admin page.
	 */
	public function admin_menu_setup()
	{
		if( $this->handler->current_tab !== $this ) return;
		
		$this->is_current_tab = true;

		global $pagenow;
		switch( $pagenow )
		{
			case 'options.php':
				break;
			
			default:
				add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts') );
				add_action( 'admin_head', array($this, 'add_head_script') );
				break;
		}
	}
		

	/**
	 * Sets the tab's parent admin page.
	 * @param  APL_AdminPage  $page  The parent page the contains the tab.
	 */
	public function set_page( $page )
	{
		$this->page = $page;

		if( !$this->page ) apl_print($this, 'no parent?? set_page');
	}	


	/**
	 * Displays the tab link for the this tab.
	 */
	public function display_tab()
	{
		if( !$this->display_tab ) return;
		?>
		
		<a href="?page=<?php echo $this->page->get_name(); ?>&tab=<?php echo $this->name; ?>"
		   class="nav-tab <?php if( $this == $this->handler->current_tab ) echo 'active'; ?>">
			<?php echo $this->menu_title; ?>
		</a>
		
		<?php
	}
	
	
	/**
	 * Gets the name of the tab-admin page.
	 * @return  string  The name of the tab-admin page.
	 */
	public function get_name()
	{
		return $this->page->get_name().'-'.$this->name;
	}

} // class APL_TabAdminPage
endif; // if( !class_exists('APL_TabAdminPage') ):

