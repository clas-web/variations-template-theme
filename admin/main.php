<?php


define( 'ADMIN_PATH', dirname(__FILE__) );


if( is_admin() ):

//----------------------------------------------------------------------------------------
// Setup the plugin's admin pages.
//----------------------------------------------------------------------------------------
add_action( 'admin_menu', array('UNCC_AdminMain', 'setup_admin_pages'), 10 );
add_action( 'admin_init', array('UNCC_AdminMain', 'setup_actions'), 5 );
add_action( 'admin_init', array('UNCC_AdminMain', 'register_settings'), 10 );

endif;


class UNCC_AdminMain
{
	
	private static $_page = null;
	
	
	public static function setup_admin_pages()
	{
		global $uncc_config;
	    
		add_submenu_page(
			'themes.php',
			'Theme Options',
			'Theme Options',
			'administrator',
			'theme-options',
			array( 'UNCC_AdminMain', 'show_admin_page' )
		);

		require_once( dirname(__FILE__).'/functions.php' );		
	}

	
	public static function init_page()
	{
		if( self::$_page !== null ) return true;
		
		global $pagenow;
		switch( $pagenow )
		{
			case 'options.php':
				$page = ( !empty($_POST['option_page']) ? $_POST['option_page'] : null );
				break;
			
			case 'admin.php':
			case 'themes.php':
				$page = ( !empty($_GET['page']) ? $_GET['page'] : null );
				break;
			
			default: return false; break;
		}
		
		if( $page !== 'theme-options' ) return false;
		
		$path = uncc_get_theme_file_path( 'admin/admin-page/theme-options.php' );
		if( $path === null ) return false;
		
		require_once( dirname(__FILE__).'/admin-page.php' );
		require_once( $path );
		
		self::$_page = call_user_func( array('NH_AdminPage_ThemeOptions', 'get_instance'), 'theme-options' );
		return true;
	}
	
	
	public static function setup_actions()
	{
		global $pagenow;
		switch( $pagenow )
		{
			case 'options.php':
				if( !self::init_page() ) return;
				break;
			
			case 'admin.php':
			case 'themes.php':
				if( !self::init_page() ) return;
				add_action( 'admin_enqueue_scripts', array(self::$_page, 'enqueue_scripts') );
				add_action( 'admin_head', array(self::$_page, 'add_head_script') );
				break;
			
			default: return; break;
		}
		
		add_action( self::$_page->slug.'-register-settings', array(self::$_page, 'register_settings') );
		add_action( self::$_page->slug.'-add-settings-sections', array(self::$_page, 'add_settings_sections') );
		add_action( self::$_page->slug.'-add-settings-fields', array(self::$_page, 'add_settings_fields') );
	}


	public static function register_settings()
	{
		if( !self::init_page() ) return;
		
		do_action( self::$_page->slug.'-register-settings' );
		do_action( self::$_page->slug.'-add-settings-sections' );
		do_action( self::$_page->slug.'-add-settings-fields' );
		
		register_setting( self::$_page->slug, 'uncc-options' );
		add_filter( 'sanitize_option_uncc-options', array(get_class(), 'process_input'), 10, 2 );
	}
	
	
	public static function process_input( $input, $option )
	{
		$page = $_POST['option_page'];
		$tab = ( !empty($_POST['tab']) ? $_POST['tab'] : null );
		$post = ( !empty($_POST[$option]) ? $_POST[$option] : null );
		$options = $uncc_config->get_options();
		
		if( $tab !== null )
		$options = apply_filters( $page.'-'.$tab.'-process-input', $options, $page, $tab, $option, $post );
		
		$options = apply_filters( $page.'-process-input', $options, $page, $tab, $option, $post );
		
		return $options;
	}
	
	public static function show_admin_page()
	{
		if( !self::init_page() ) return;
		self::$_page->show();
	}

}	


