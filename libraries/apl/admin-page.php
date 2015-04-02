<?php
/**
 * APL_AdminPage
 * 
 * The APL_AdminPage class is a representation of a admin page in WordPress that will 
 * appear in the main admin menu.
 * 
 * @package    apl
 * @author     Crystal Barton <cbarto11@uncc.edu>
 */


if( !class_exists('APL_AdminPage') ):
abstract class APL_AdminPage
{
	
	protected $handler;			// The handler that controls the admin page.
	protected $menu;			// The parent admin menu's name (eg. "themes.php")
								// or APL_AdminMenu object.
	
	public $name;				// The name/slug of the page.
	public $page_title;			// The title of the page.
	public $menu_title; 		// The title show on the left menu.
	public $capability;			// The capability needed to displayed to the user.
	
	public $is_main_page;		// True if this page is the main menu page.
	public $is_current_page;	// True if this is current page being shown.
	
	protected $tabs;			// The page's tabs, if any.
	protected $tab_names;		// The names of the tabs with their index within the
								//   the $tabs array (for searching purposes).
	
	public $use_custom_settings;	// True if use the apl's custom Settings API.
	protected $settings;			// All settings that have been registered.
	
	public $display_page_tab_list;  // True if the tab list should be displayed.
	
	public $screen_options;
	public $selectable_columns;
	
	protected $ajax;
	
	protected $notices;
	protected $errors;

	
	/**
	 * Creates an APL_AdminPage object.
	 * @param  string  $name        The name/slug of the page.
	 * @param  string  $menu_title  The title shown on the left menu.
	 * @param  string  $page_title  The title shown on the top of the page.
	 * @param  string  $capability  The capability needed to displayed to the user.
	 */
	public function __construct( $name, $menu_title, $page_title, $capability = 'administrator' )
	{
		$this->handler = null;
		$this->menu = null;
		
		$this->name = $name;
		$this->menu_title = $menu_title;
		$this->page_title = $page_title;
		$this->capability = $capability;
		
		$this->is_main_page = false;
		$this->is_current_page = false;
		$this->current_tab = null;
		
		$this->tabs = array();
		$this->tab_names = array();
		
		if( is_network_admin() )
			$this->use_custom_settings = true;
		else
			$this->use_custom_settings = false;
		$this->settings = array();
		
		$this->display_page_tab_list = true;
		
		$this->screen_options = array();
		$this->selectable_columns = array();
		
		$this->ajax = array();
		
		$this->notices = array();
		$this->errors = array();
	}
	
	
	/**
	 * Initialize the admin page.  Called during "admin_init" action.
	 */
	public function init() { }
	
	
	/**
	 * Loads the admin page.  Called during "load-{page}" action.
	 */
	public function load() { }
	
	
	/**
	 * Adds the admin page to the main menu and sets up all values, actions and filters.
	 * Called during "admin_menu" or "network_admin_menu" action.
	 */
	public function admin_menu_setup()
	{
		$menu_name = $this->menu;
		if( $this->menu instanceof APL_AdminMenu ) $menu_name = $this->menu->name;
		
		if( $this->is_main_page )
		{
			$hook = add_menu_page(
				$this->page_title, 
				$this->menu_title,
				$this->capability,
				$this->get_name(),
				array( $this, 'display_page' )
			);
		}
		else
		{
			$hook = add_submenu_page(
				$menu_name,
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->get_name(),
				array( $this, 'display_page' )
			);
		}
		
		if( $this->handler->current_page !== $this ) return;

		if( $this->handler->controller )
		{
			add_action( "load-$hook", array($this->handler->controller, 'load') );
 			add_action( "load-$hook", array($this->handler->controller, 'setup_screen_options') );
		}
				
		$this->is_current_page = true;
		
		foreach( $this->tabs as $tab )
		{
			if( $tab instanceof APL_TabAdminPage ) $tab->admin_menu_setup();
 		}
	}
	
	
	/**
	 * Checks if this is the page that should perform an apl ajax request.  If the current
	 * page matches the request and a tab is not selected, then the ajax request is processed.
	 */
	public function perform_ajax_request()
	{
		$this->ajax_success();
		
		if( !isset($_POST['apl-ajax-action']) || !isset($_POST['input']) || !isset($_POST['nonce']) )
		{
			$this->ajax_failed( 'The submitted data is not complete.' );
			return;
		}
		
		if( !wp_verify_nonce($_POST['nonce'], $this->get_name().'-'.$_POST['apl-ajax-action'].'-ajax-request') )
		{
			$this->ajax_failed( 'The submitted data cannot be verified.' );
			return;
		}
		
		$this->output = array( 'ajax' => array() );
		$this->ajax_success();
		
		$this->ajax_request( $_POST['apl-ajax-action'], $_POST['input'], $_POST['count'], $_POST['total'] );
		
		$this->ajax_output();
		exit;
	}
	
	
	/**
	 * Sets the page's handler.
	 * @param  APL_Handler  $handler  The handler controlling the page.
	 */
	public function set_handler( $handler )
	{
		$this->handler = $handler;
		foreach( $this->tabs as $tab )
		{
			if( $tab instanceof APL_TabAdminPage ) { $tab->set_handler( $handler ); }
		}
	}	
	

	/**
	 * Sets the page's parent menu.
	 * @param  APL_AdminMenu  $menu  The parent menu of the page.
	 */
	public function set_menu( $menu )
	{
		$this->menu = $menu;
		foreach( $this->tabs as $tab )
		{
			if( $tab instanceof APL_TabAdminPage ) { $tab->set_menu( $menu ); }
		}
	}	

	
	/**
	 * Adds a tab to the page's tab list.  The tab can be an APL_TabAdminPage or APL_TabLink.
	 * @param  APL_TabAdminPage|APL_TabLink  $tab  The tab class object.
	 */
	public function add_tab( $tab )
	{
		if( $tab instanceof APL_TabAdminPage )
		{
			$tab->set_handler( $this->handler );
			$tab->set_menu( $this->menu );
			$tab->set_page( $this );
			$this->tab_names[$tab->name] = count($this->tabs);
		}
		$this->tabs[] = $tab;
	}
	
	
	/**
	 * Determines the default tab to be chosen when the tab isn't specified.
	 * @return  APL_TabAdminPage|null  The name of the tab, if found, else null. 
	 */
	public function get_default_tab()
	{
		$keys = array_keys($this->tab_names);
		if( count($keys) > 0 ) 
		{
			return $this->tabs[$this->tab_names[$keys[0]]];
		}
		return null;
	}
	
	
	/**
	 * Displays the tab list links.
	 */
	public function display_tab_list()
	{
		if( count($this->tabs) === 0 ) return;
		
		?><h2 class="admin-page nav-tab-wrapper"><?php

 		foreach( $this->tabs as $tab )
 		{
			$tab->display_tab();
 		}
		
		?></h2><?php
	}
	
	
	/**
	 * Retreives that APL_TabAdminPage object that matches the name.
	 * @param   string  $name  The name of the tab.
	 * @return  APL_TabAdminPage|null  The APL_TabAdminPage object if exists, otherwise null.
	 */
	public function get_tab_by_name( $name )
	{
		if( in_array($name, array_keys($this->tab_names)) )
		{
			return $this->tabs[$this->tab_names[$name]];
		}
		return null;
	}


	/**
	 * Add the screen options for the page.
	 */
	public function add_screen_options() { }
	

	/**
	 * Sets up the screen options for the admin page.
	 * Called during "load-{page}" action after the load function.
	 */
	public function setup_screen_options()
	{
		$this->add_screen_options();
		
		foreach( $this->screen_options as $so )
		{
			add_screen_option( $so['screen_option'], $so );
		}
		
		$screen = get_current_screen();
		add_filter( "manage_{$screen->id}_columns", array($this, 'get_selectable_columns') );
	}
	
	
	/**
	 * Adds the per_page option to the screen options list.  
	 * This option is used to alter number of items are shown in a list table.
	 * @param  string  $option   Unique name for option.
	 * @param  string  $label    The label/title to use when displaying.
	 * @param  string  $default  The default value.
	 */
	public function add_per_page_screen_option( $option, $label, $default )
	{
		$this->screen_options[$option] = array(
			'screen_option' => 'per_page',
			'option' => $option,
			'label' => $label,
			'default' => $default,
		);
	}
	
	
	/**
	 * Add the selectable columns to the screen options.
	 * @param  array  $columns  An array of columns that will be selectable.
	 */
	public function add_selectable_columns( $columns )
	{
		if( is_array($columns) )
		{
			$this->selectable_columns = array_merge( $this->selectable_columns, $columns );
		}
		else
		{
			$this->selectable_columns[]  = $columns;
		}
	}


	/**
	 * Gets the selectable columns for the list table.
	 * @return  array  The selectable columns array.
	 */
	public function get_selectable_columns()
	{
		return $this->selectable_columns;
	}
	
	
	/**
	 * Gets the default value of a screen option.
	 * @param   string  $option  The option's name.
	 * @return  string  The default value of the option.
	 */
	public function get_screen_option( $option )
	{
		if( !in_array($option, array_keys($this->screen_options)) ) return false;
		
		$value = get_user_option($option);
		if( $value === false && isset($this->screen_options[$option]['default']) )
			$value = $this->screen_options[$option]['default'];
			
		return $value;
	}
	
	
	/**
	 * Filter a screen option value before it is set.
	 * Called during "set-screen-option" filter.
	 * @param   bool    $status  
	 * @param   string  $option  The name of the option.
	 * @param   string  $value   The new value of the option.
	 * @return  string  The fitlered value of the option.
	 */
	public function save_screen_options( $status, $option, $value )
	{
		update_user_option( get_current_user_id(), $option, $value );
		return $value;
// 		apl_print(array_keys($this->screen_options), $option);
// 		if( !in_array($option, array_keys($this->screen_options)) ) return $status;
// 		
// 		update_user_option( get_current_user_id(), $option, $value );
// 		return $value;
	}
	
	
	/**
	 * Enqueues all the scripts or styles needed for the admin page. 
	 */
	public function enqueue_scripts() { }
	
	
	/**
	 * HTML/JavaScript to add to the <head> portion of the page. 
	 * Called during "admin_head" action.
	 */
	public function add_head_script() { }
	
	
	/**
	 * Register each individual settings for the Settings API.
	 */
	public function register_settings() { }
	
	
	/**
	 * Registers the settings key for the Settings API. The settings key is associated
	 * with an option key.  A filter is added for the option key, associated with
	 * process_settings function, which should be overwritten by child classes.
	 * @param  string  $option  The key for the data in the $_POST array, as well as the 
	 *                          key for the option in the options table.
	 * @param  bool    $merge   
	 */
	public function register_setting( $option, $merge = true )
	{
		if( !$this->use_custom_settings )
		{
			register_setting( $this->handler->get_page_name(), $option );
		}
	
		add_filter( 'sanitize_option_'.$option, array($this, 'process_settings'), 10, 2 );
		$this->settings[] = array( 'option' => $option, 'merge' => $merge );
	}
	
	
	/**
	 * Add the sections used for the Settings API. 
	 */
	public function add_settings_sections() { }
	
	
	/**
	 * Add the settings used for the Settings API. 
	 */
	public function add_settings_fields() { }
	
	
	/**
	 * Adds a "Settings API" section.
	 * @param  string  $name      The name/slug of the section.
	 * @param  string  $title     The title to display for the the section.
	 * @param  string  $callback  The function to call when displaying the section.
	 */
	public function add_section( $name, $title, $callback = null )
	{
		if( $callback === null ) $callback = 'no_echo';
		
		add_settings_section(
			$name, $title, array( $this, $callback ), $this->get_name().':'.$name
		);
	}
	

	/**
	 * Adds a "Settings API" field.
	 * @param  string  $section   The name/slug of the section.
	 * @param  string  $name      The name/slug of the field.
	 * @param  string  $title     The title to display for the the field.
	 * @param  string  $callback  The function to call when displaying the section.
	 * @param  array   $args      The arguments to pass to the callback function.
	 */
	public function add_field( $section, $name, $title, $callback = null, $args = array() )
	{
		if( $callback === null ) $callback = 'no_echo';
		
		add_settings_field( 
			$name, $title, array( $this, $callback ), $this->get_name().':'.$section, $section, $args
		);
	}
	
	
	/**
	 * 
	 */
	public function no_echo() { }
	
	
	
	/**
	 * Processes the current admin page or tab by checking the nonce, updating settings,
	 * and running the process function (which should be overloaded by child class).
	 */
	public function process_page()
	{
		if( !isset($_REQUEST['action']) && !isset($_REQUEST['action2']) ) return;
		
		if( !empty($_POST) )
		{
			if( !isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], $this->get_name().'-options') )
			{
				$this->set_error( 'The submitted data cannot be verified.' );
				return;
			}

			if( ($this->use_custom_settings) && ($_POST['action'] == 'update') )
			{
				foreach( $this->settings as $setting )
				{
					$option = $setting['key'];
					
					if( !isset($_POST[$option]) ) continue;
					$settings = $_POST[$option];
					
					if( $setting['merge'] === true )
						$settings = $this->merge_settings( $settings, $option );
					
					if( is_network_admin() )
					{
						update_site_option( $option, $settings );
					}
					else
					{
						update_option( $option, $settings );
					}
				}
			}
		}
		
		$this->process();
	}
	
	
	/**
	 * Displays the current admin page / tab. 
	 */
	public function display_page()
	{
		$menu_name = $this->menu;
		if( $this->menu instanceof APL_AdminMenu ) $menu_name = $this->menu->name;
		
		$remove = array( '.php' );
		$menu_name = str_replace( $remove, '', $menu_name );
		
		$dashes = array( '?', '=' );
		$menu_name = str_replace( $dashes, '-', $menu_name );
		
		$menu_name = sanitize_title($menu_name);
		?>
		<div class="wrap <?php echo $menu_name; ?> <?php echo $this->name; ?> <?php echo $this->get_name(); ?>">
	 
			<div id="icon-themes" class="icon32"></div>
			
			<?php
			if( $this->handler->current_tab && $this->handler->current_tab->page_title !== null ):
				?><h2><?php echo $this->handler->current_tab->page_title; ?></h2><?php
			else:
				?><h2><?php echo $this->page_title; ?></h2><?php
			endif;
			?>
			<?php settings_errors(); ?>
		 
		 	<?php 
		 	if( ($this->menu) && ($this->menu instanceof APL_AdminMenu) )
		 	{
		 		$this->menu->display_tab_list();
		 	}
		 	
		 	if( $this->display_page_tab_list )
		 	{
		 		$this->display_tab_list();
		 	}
		 	?>
		 	
		 	<div class="page-contents">
		 	
			<?php
			
			$this->display_notice();
			$this->display_error();
			
		 	if( $this->handler->current_tab ):
		 		$this->handler->current_tab->display();
		 	else:
		 		$this->display();
		 	endif;
		 	?>
		 	
		 	</div><!-- .page-contents -->
		 
		</div><!-- .wrap -->
		<?php
	}
	
	
	/**
	 * Processes the current admin page.  Only called when tab is not specified. 
	 */
	public function process() { }
	
	
	/**
	 * Processes the current admin page's Settings API input.
	 * @param   array   $settings  The inputted settings from the Settings API.
	 * @param   string  $option    The option key of the settings input array.
	 * @return  array   The resulted array to store in the db.
	 */
	public function process_settings( $settings, $option )
	{
		foreach( $this->settings as $setting )
		{
			if( !$setting['option'] === $option ) continue;
			if( $setting['merge'] !== true ) break;
			
			$settings = $this->merge_settings( $settings, $option );
		}
		
		return $settings;
	}
	
	
	/**
	 * Merges the settings of an option with the existing settings in the database.
	 * @param   array   $settings  The new settings for an option.
	 * @param   string  $option    The option name.
	 * @return  array   The merged settings.
	 */
	public function merge_settings( $settings, $option )
	{
		if( is_network_admin() )
		{
			$old_settings = get_site_option( $option, array() );
			$settings = array_merge( $old_settings, $settings );
		}
		else
		{
			$old_settings = get_option( $option, array() );
			$settings = array_merge( $old_settings, $settings );
		}
		
		return $settings;
	}


	/**
	 * Displays the current admin page.  Only called when tab is not specified. 
	 */
	public function display() { }
	
	
	/**
	 * Displays all of the Settings sections and fields for the page.
	 */
	public function print_settings()
	{
		global $wp_settings_sections;
		
		$this->form_start_settings_api();
		?>
		
			<div class="top-submit"><?php submit_button(); ?></div>
			<div style="clear:both"></div>
			<input type="hidden" name="page" value="<?php echo $this->handler->get_page_name(); ?>" />
			<input type="hidden" name="tab" value="<?php echo $this->handler->get_tab_name(); ?>" />
			
			<?php
			do_settings_sections( $this->get_name() );
			
			$tab_section = $this->get_name().':';
			foreach( array_keys($wp_settings_sections) as $section_name )
			{
				if( substr($section_name, 0, strlen($tab_section)) === $tab_section )
				{
					do_settings_sections( $section_name );
				}
			}
			?>
			
			<div style="clear:both"></div>
			<div class="bottom-submit"><?php submit_button(); ?></div>
			
		<?php
		$this->form_end();
	}
	
	
	
	/**
	 * Processes and displays the output of an ajax request.
	 * @param   string  $action  The AJAX action.
	 * @param   array   $input   The AJAX input array.
	 * @param   int     $count   When multiple AJAX calls are made, the current count.
	 * @param   int     $total   When multiple AJAX calls are made, the total count.
	 */
	public function ajax_request( $action, $input, $count, $total ) { }

	
	/**
	 * Displays the start of form when using the Settings API.
	 * @param  array|null  $attributes  Additional attributes to add to the start form tag.
	 */
	public function form_start_settings_api( $attributes = array() )
	{
		if( $this->use_custom_settings )
		{
			$this->form_start( null, $attributes, 'update' );
			return;
		}
		
		?>
		<form action="options.php" method="POST" 
		    <?php
		    foreach( $attributes as $key => $value ):
		      	echo $key.'="'.$value.'" ';
		    endforeach; ?>
		    >   
		<?php
		
		settings_fields( $this->handler->get_page_name() );
	}
	
	
	/**
	 * Displays the start form tag and mandatory fields for the start of a POST form.
	 * Most forms should be in this format.
	 * @param  string|null  $class       The class of the form.
	 * @param  array|null   $attributes  Additional attributes to add to the start form tag.
	 * @param  string|null  $action      The action the form will perform.
	 * @param  array|null   $query       Additional query args for the form url/action.
	 */
	public function form_start( $class = null, $attributes = array(), $action = null, $query = array() )
	{
		if( !is_array($attributes) ) $attributes = array();
		if( !is_array($query) ) $query = array();
		
		?>
		<form action="<?php echo $this->get_page_url( $query ); ?>" 
		      method="POST" 
		      class="<?php echo $class; ?>"
		      <?php foreach( $attributes as $key => $value ) { echo $key.'="'.$value.'" '; } ?>
		      >
		<input type="hidden" name="option_page" value="<?php echo $this->handler->get_page_name(); ?>" />

		<?php
		if( $action ):
			?>
			<input type="hidden" name="action" value="<?php echo $action; ?>" />
			<?php
		endif;
		
		wp_nonce_field( $this->get_name().'-options' );
	}
	
	
	/**
	 * Displays the start form tag and mandatory fields for the start of a GET form.
	 * The action and query variables are displayed as hidden tags.
	 * @param  string|null  $class       The class of the form.
	 * @param  array|null   $attributes  Additional attributes to add to the start form tag.
	 * @param  string|null  $action      The action the form will perform.
	 * @param  array|null   $query       Additional query args for the form url/action.
	 */
	public function form_start_get( $class = null, $attributes = array(), $action = null, $query = array() )
	{
		if( !is_array($attributes) ) $attributes = array();
		if( !is_array($query) ) $query = array();
		
		?>
		<form action="<?php echo apl_get_page_url( false ); ?>" 
		      class="<?php echo $class; ?>"
		      <?php foreach( $attributes as $key => $value ) { echo $key.'="'.$value.'" '; } ?>
		      >
		<?php
		$page = $this->handler->get_page_name();
		$tab = $this->handler->get_tab_name();
		
		if( isset($query['page']) ) $page = $query['page'];
		if( isset($query['tab']) ) $tab = $query['tab'];
		
		if( $page ):
			?>
			<input type="hidden" name="page" value="<?php echo $page; ?>" />
			<?php
		endif;

		if( $tab ):
			?>
			<input type="hidden" name="tab" value="<?php echo $tab; ?>" />
			<?php
		endif;
		
		if( $action ):
			?>
			<input type="hidden" name="action" value="<?php echo $action; ?>" />
			<?php
		endif;
		
		foreach( $query as $k => $v ):
			?>
			<input type="hidden" name="<?php echo $k; ?>" value="<?php echo $v; ?>" />
			<?php
		endforeach;
	}
	
	
	/**
	 * Gets the URL of the current admin page with new query arguments added.
	 * @param   array   $query  An array of key/value pairs for additional URL query items.
	 * @return  string  The generated URL with new query arguments.
	 */
	public function get_page_url( $query = array() )
	{
		$url = apl_get_page_url( false );
		
		if( is_string($this->menu) )
			$url .= '?'.parse_url($this->menu, PHP_URL_QUERY).'&';
		else
			$url .= '?';
		
		if( isset($query['page']) )
			$url .= 'page='.$query['page'];
		else
			$url .= 'page='.$this->handler->get_page_name();
		
		if( isset($query['tab']) )
			$url .= '&tab='.$query['tab'];
		elseif( $this->handler->get_tab_name() )
			$url .= '&tab='.$this->handler->get_tab_name();
		
		foreach( $query as $key => $value )
		{
			$url .= '&'.$key.'='.$value;
		}
		
		return $url;
	}
	
	
	/**
	 * Displays the end form tag.
	 */
	public function form_end()
	{
		?>
		</form>
		<?php
	}
	
	
	/**
	 * Displays a "Settings API" section that were added in "add_settings_sections".
	 * @param  string  $section_name  The name/slug of the section.
	 */
	public function print_section( $section_name )
	{
		do_settings_sections( $this->get_name().':'.$section_name );
	}
	
	
	/**
	 * Displays a button with the needed attributes to be used by the AplAjaxButton
	 * jQuery plugin for automated AJAX processing. 
	 * @param  string       $text           The text to display on the button.
	 * @param  string       $action         The action to send in the AJAX request.
	 * @param  array|null   $form_classes   The classes of the forms that should be 
	 *                                      processed via AJAX individually.  If no form
	 *                                      is given, then current form is assumed.
	 * @param  array|null   $input_names    The names of the form input values to process
	 *                                      via AJAX together.  If no input values are
	 *                                      given, then the entire form will be processed.
	 * @param  string|null  $cb_start       The JS function to call when processing
	 *                                      begins, before the first loop.
	 * @param  string|null  $cb_end         The JS function to call when processing
	 *                                      finishes, after the last loop.
	 * @param  string|null  $cb_loop_start  The JS function to call when each form
	 *                                      begins processing.
	 * @param  string|null  $cb_loop_end    The JS function to call when each formatOutput
	 *                                      finishes processing.
	 */
	public function create_ajax_submit_button( $text, $action, $form_classes, $input_names, $cb_start = null, $cb_end = null, $cb_loop_start = null, $cb_loop_end = null )
	{
		if( is_array($form_classes) ) $form_classes = implode( ',', $form_classes );
		if( is_array($input_names) ) $input_names = implode( ',', $input_names );
		$nonce = wp_create_nonce( $this->get_name().'-'.$action.'-ajax-request' );
		
		?>
		<button type="button" 
		        class="apl-ajax-button"
		        page="<?php echo $this->handler->get_page_name(); ?>"
		        tab="<?php echo $this->handler->get_tab_name(); ?>"
		        action="<?php echo $action; ?>"
		        form="<?php echo $form_classes; ?>"
		        input="<?php echo $input_names; ?>"
		        cb_start="<?php echo $cb_start; ?>"
		        cb_end="<?php echo $cb_end; ?>"
		        cb_loop_start="<?php echo $cb_loop_start; ?>"
		        cb_loop_end="<?php echo $cb_loop_end; ?>"
		        nonce="<?php echo $nonce; ?>">
		    <?php echo $text; ?>
		</button>
		<?php
	}
	
	
	/**
	 * Gets the name of the admin page.
	 * @return  string  The name of the admin page.
	 */
	public function get_name()
	{
		if( $this->menu && $this->menu instanceof APL_AdminMenu )
			return $this->menu->name.'-'.$this->name;
		
		return $this->name;
	}
	
	
	/**
	 * Set the error as the only error message for the page.
	 * @param   string  $error  The error message.
	 * @param   bool    $save   True to save the error data to the session.
	 */
	public function set_error( $error, $save = false )
	{
		if( !is_array($error) ) $error = array( $error );
		$this->errors = $error;
		if( $save ) $this->save_error();
	}
	
	
	/**
	 * Add a error to the page errors.
	 * @param   string  $error  The error message.
	 * @param   bool    $save    True to save the error data to the session.
	 */
	public function add_error( $error, $save = false )
	{
		$this->errors[] = $error;
		if( $save ) $this->save_error();
	}
	
	
	/**
	 * Save the errors in the session data.
	 */
	public function save_error()
	{
		$_SESSION['apl-error'] = json_encode(
			array(
				'page'		=> $this->get_name(),
				'messages'	=> $this->errors,
			)
		);
	}
	
	
	/**
	 * Clears any errors stored in the session.
	 */
	public function clear_error()
	{
		$this->errors = array();
		unset( $_SESSION['apl-error'] );
	}
	
	
	/**
	 * Gets the apl-error session data, if it exists and matches the current page.
	 * @return  array  The error messages for the current page.
	 */
	public function get_error()
	{
		if( !isset($_SESSION['apl-error']) ) return array();
		
		$e = json_decode( $_SESSION['apl-error'], true );
		
		if( !array_key_exists('page', $e) ) return array();
		if( $e['page'] !== $this->handler->get_name() ) return array();
		
		if( !array_key_exists('messages', $e) ) return array();
		return $e['messages'];
	}
	
	
	/**
	 * Displays any errors stored in the session.
	 */
	protected function display_error()
	{
		$this->errors = array_merge( $this->get_error(), $this->errors );
		?>
		
		<div class="page-errors">
		
		<?php foreach( $this->errors as $message ): ?>
			<div><?php echo $message; ?></div>
		<?php endforeach; ?>
		
		</div>
		
		<?php
		$this->clear_error();
	}
	
	
	/**
	 * Set the notice as the only notice message for the page.
	 * @param   string  $notice  The notice message.
	 * @param   bool    $save    True to save the notice data to the session.
	 */
	public function set_notice( $notice, $save = false )
	{
		if( !is_array($notice) ) $notice = array( $notice );
		$this->notices = $notice;
		if( $save ) $this->save_notice();
	}
	
	
	/**
	 * Add a notice to the page notices.
	 * @param   string  $notice  The notice message.
	 * @param   bool    $save    True to save the notice data to the session.
	 */
	public function add_notice( $notice, $save = false )
	{
		$this->notices[] = $notice;
		if( $save ) $this->save_notice();
	}
	
	
	/**
	 * Save the notices in the session data.
	 */
	public function save_notice()
	{
		$_SESSION['apl-notice'] = json_encode(
			array(
				'page'		=> $this->get_name(),
				'messages'	=> $this->notices,
			)
		);
	}
	
	
	/**
	 * Clears any notices stored in the session.
	 */
	public function clear_notice()
	{
		$this->notices = array();
		unset( $_SESSION['apl-notice'] );
	}
	
	
	/**
	 * Gets the apl-notice session data, if it exists and matches the current page.
	 * @return  array  The notice messages for the current page.
	 */
	public function get_notice()
	{
		if( !isset($_SESSION['apl-notice']) ) return array();
		
		$e = json_decode( $_SESSION['apl-notice'], true );
		
		if( !array_key_exists('page', $e) ) return array();
		if( $e['page'] !== $this->handler->get_name() ) return array();
		
		if( !array_key_exists('messages', $e) ) return array();
		return $e['messages'];
	}
	
	
	/**
	 * Displays any notices stored in the session.
	 */
	protected function display_notice()
	{
		$this->notices = array_merge( $this->get_notice(), $this->notices );
		?>
		
		<div class="page-notices">
		
		<?php foreach( $this->notices as $message ): ?>
			<div><?php echo $message; ?></div>
		<?php endforeach; ?>
		
		</div>
		
		<?php
		$this->clear_notice();
	}
	
	
	/**
	 * Sets the failure status and message that will be returned when AJAX call is complete.
	 * @param  string  $message  The failure message.
	 */
	public function ajax_failed( $message = '' )
	{
		$this->output['success'] = false;
		$this->output['message'] = $message;
	}
	
	
	/**
	 * Sets the success status and message that will be returned when AJAX call is complete.
	 * @param  string  $message  The success message.
	 */
	public function ajax_success( $message = '' )
	{
		$this->output['success'] = true;
		$this->output['message'] = $message;
	}
	
	
	/**
	 * Set a key/value pair tto the returning AJAX data.
	 * @param  string  $key    The key/name of the value.
	 * @param  string  $value  The value.
	 */
	public function ajax_set( $key, $value )
	{
		$this->output['ajax'][$key] = $value;
	}
	
	
	/**
	 * Remove a value from the returning AJAX data.
	 * @param  string  $key  The key/name of value.
	 */
	public function ajax_remove( $key )
	{
		unset( $this->output['ajax'][$key] );
	}
	
	
	/**
	 * Sets the return values needed to start a new AJAX loop.
	 * @param   string  $action         The new AJAX action.
	 * @param   array   $items          The items that will be subject of the new AJAX action.
	 * @param   string  $cb_start       The new start JS callback.
	 * @param   string  $cb_end         The new end JS callback.
	 * @param   string  $cb_loop_start  The new start loop JS callback.
	 * @param   string  $cb_loop_end    The new end loop JS callback.
	 */
	public function ajax_set_items( $action, $items, $cb_start = null, $cb_end = null, $cb_loop_start = null, $cb_loop_end = null )
	{
		$this->output['ajax']['page'] 			= $this->handler->get_page_name();
		$this->output['ajax']['tab'] 			= $this->handler->get_tab_name();
		$this->output['ajax']['action'] 		= $action;
		$this->output['ajax']['items'] 			= $items;
		$this->output['ajax']['cb_start'] 		= $cb_start;
		$this->output['ajax']['cb_end'] 		= $cb_end;
		$this->output['ajax']['cb_loop_start'] 	= $cb_loop_start;
		$this->output['ajax']['cb_loop_end'] 	= $cb_loop_end;
		$this->output['ajax']['nonce'] 			= wp_create_nonce( $this->get_name().'-'.$action.'-ajax-request' );
	}
	
	
	/**
	 * Display the AJAX data.
	 */
	public function ajax_output()
	{
		echo json_encode( $this->output );
	}

} // class APL_AdminPage
endif; // if( !class_exists('APL_AdminPage') ):

