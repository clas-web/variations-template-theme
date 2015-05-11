<?php

/**
 * functions.php
 * 
 * The main functions for the Variations Template Theme.
 * 
 * @package    variations-template-theme
 * @author     Crystal Barton <cbarto11@uncc.edu>
 */

//========================================================================================
//======================================================================== Constants =====

if( !defined('VTT') ):

define( 'VTT', 'Variations Template Theme' );

define( 'VTT_DEBUG', true );

define( 'VTT_PATH', dirname(__FILE__) );
define( 'VTT_URL', get_template_directory_uri() );

define( 'VTT_VERSION', '1.0.0' );
define( 'VTT_DB_VERSION', '1.0' );

define( 'VTT_VERSION_OPTION', 'vtt-version' );
define( 'VTT_VARIATION_OPTION', 'vtt-variation' );
define( 'VTT_DB_VERSION_OPTION', 'vtt-db-version' );

define( 'VTT_OPTIONS', 'vtt-options' );

define( 'VTT_HEADER_TITLE_POSITION', 'header-title-position' );
define( 'VTT_HEADER_TITLE_HIDE', 'header-title-hide' );
define( 'VTT_FEATURED_IMAGE_POSITION', 'featured-image-position' );

endif;


//========================================================================================
//======================================================================= Main setup =====

global $vtt_mobile_support, $vtt_config;

// Setup mobile support.
require_once( get_template_directory().'/classes/mobile-support.php' );
$vtt_mobile_support = new Mobile_Support;

// Setup the config information.
require_once( get_template_directory().'/classes/config.php' );
$vtt_config = new VTT_Config;

// Set blog name.
define( 'VTT_BLOG_NAME', trim( preg_replace("/[^A-Za-z0-9 ]/", '-', get_blog_details()->path), '-' ) );


//========================================================================================
//====================================================== Default filters and actions =====

// Include the admin backend. 
if( is_admin() ):
	require_once( dirname(__FILE__).'/libraries/apl/apl.php' );
	add_action( 'wp_loaded', 'vtt_load_apl_admin' );
endif;

if( is_customize_preview() ):
	require_once( dirname(__FILE__).'/classes/customizer/header-position.php' );
endif;


// Admin Bar
add_filter( 'show_admin_bar', 'vtt_show_admin_bar', 10 );
add_action( 'admin_bar_menu', 'vtt_setup_admin_bar' );

// Theme setup
add_action( 'after_setup_theme', 'vtt_theme_setup', 1 );
add_action( 'init', 'vtt_setup_widget_areas' );
add_action( 'init', 'vtt_register_menus' );
add_action( 'wp_enqueue_scripts', 'vtt_enqueue_scripts', 0 );

// Embeded content
add_filter( 'embed_oembed_html', 'vtt_embed_html', 10, 3 );
add_filter( 'video_embed_html', 'vtt_embed_html' );

// Theme Customizer
add_action( 'customize_register', 'vtt_customize_register' );
add_action( 'customize_save_after', 'vtt_customize_save' );
add_action( 'update_option_'.VTT_VARIATION_OPTION, 'vtt_customize_update_variation', 99, 2 );
add_action( 'update_option_'.VTT_OPTIONS, 'vtt_customize_update_options', 99, 2 );

// Comments
add_filter( 'comments_template', 'vtt_find_comments_template_part', 999 );

// Categories Widget
add_action( 'init', 'vtt_setup_categories_walker' );
add_filter( 'widget_categories_args', 'vtt_alter_categories_widget_args' );

// Add / Remove MIME types
add_filter( 'upload_mimes', 'vtt_add_custom_mime_types' );

// Enable Links subpanel
add_filter( 'pre_option_link_manager_enabled', '__return_true' );

// Post Content
add_filter( 'the_content_more_link', 'vtt_read_more_link' );

// Add Home to Pages menu.
add_filter( 'wp_page_menu_args', 'vtt_add_home_pages_menu_item' );


//========================================================================================
//======================================================================== Functions =====


/**
 * 
 */
if( !function_exists('vtt_theme_setup') ):
function vtt_theme_setup()
{
	global $vtt_mobile_support, $vtt_config;
	
	// load config.
	$vtt_config->load_config();

	// include variation's functions.php
	$vtt_config->load_variations_files( 'functions.php' );	

	// add theme support.
	vtt_add_featured_image_support();
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-background' );
	
	// add editor styles.
	add_editor_style( 'editor-style.css' );
}
endif; 


/**
 *
 */
if( !function_exists('vtt_load_apl_admin') ):
function vtt_load_apl_admin()
{
	// child admin main.
	if( is_child_theme() && file_exists(get_stylesheet_directory().'/admin-pages/require.php') ):
	require_once( get_stylesheet_directory().'/admin-pages/require.php' );
	endif;
	
	// parent admin main.
	require_once( get_template_directory().'/admin-pages/require.php' );
		
	// Site admin page.
	$vtt_pages = new APL_Handler( false );
	
	$vtt_pages->add_page( new VTT_ThemeOptionsAdminPage(), 'themes.php' );
	$vtt_pages = apply_filters( 'vtt-theme-admin-populate', $vtt_pages );
	
	$vtt_pages->setup();
}
endif;


// TODO: what is this for????
/**
 * 
 */
if( !function_exists('vtt_return_nothing') ):
function vtt_return_nothing()
{
	return '';
}
endif; 


/**
 * 
 */
if( !function_exists('vtt_alter_categories_widget_args') ):
function vtt_alter_categories_widget_args( $args )
{
	$args['walker'] = new VTT_Categories_Walker;
	return $args;
}
endif; 


/**
 * 
 */
if( !function_exists('vtt_setup_categories_walker') ):
function vtt_setup_categories_walker()
{
	$filepath = vtt_get_theme_file_path( 'classes/categories-walker.php' );
	if( $filepath ) require_once( $filepath );
}
endif;


/**
 * 
 */
if( !function_exists('vtt_embed_html') ):
function vtt_embed_html( $html )
{
	return '<div class="video-container">' . $html . '</div>';
}
endif; 


/**
 * 
 */
if( !function_exists('vtt_show_admin_bar') ):
function vtt_show_admin_bar( $show_admin_bar )
{
	return true;
}
endif;


/**
 * 
 */
if( !function_exists('vtt_register_menus') ):
function vtt_register_menus()
{
	register_nav_menus(
		array(
			'header-navigation' => __( 'Header Menu' ),
		)
	);
}
endif;


/**
 * 
 */
if( !function_exists('vtt_setup_admin_bar') ):
function vtt_setup_admin_bar( $wp_admin_bar )
{
	if( !is_user_logged_in() ):
		$wp_admin_bar->add_menu(
			array(
				'title' => __( 'Log In' ),
				'href' => wp_login_url()
			)
		);
	endif;
}
endif;


/**
 * Sets up the widget areas.
 */
if( !function_exists('vtt_setup_widget_areas') ):
function vtt_setup_widget_areas()
{
	global $vtt_config;
	
	$widgets = array(
		array(
			'id'   => 'vtt-left-sidebar',
			'name' => 'Left Sidebar',
		),
		array(
			'id'   => 'vtt-right-sidebar',
			'name' => 'Right Sidebar',
		),
		array(
			'id'   => 'vtt-footer-1',
			'name' => 'Footer Column 1',
		),
		array(
			'id'   => 'vtt-footer-2',
			'name' => 'Footer Column 2',
		),
		array(
			'id'   => 'vtt-footer-3',
			'name' => 'Footer Column 3',
		),
		array(
			'id'   => 'vtt-footer-4',
			'name' => 'Footer Column 4',
		),
	);
	
	$widgets = apply_filters( 'vtt-widget-areas', $widgets );
	
	$widget_area = array();
	$widget_area['before_widget'] = '<div id="%1$s" class="widget %2$s">';
	$widget_area['after_widget'] = '</div>';
	$widget_area['before_title'] = '<h2 class="widget-title">';
	$widget_area['after_title'] = '</h2>';

	foreach( $widgets as $widget )
	{
		$widget_area['name'] = $widget['name'];
		$widget_area['id'] = $widget['id'];
		register_sidebar( $widget_area );
	}
}
endif;



/**
 * Enqueue any needed css or javascript files.
 */
if( !function_exists('vtt_enqueue_scripts') ):
function vtt_enqueue_scripts()
{
	global $vtt_mobile_support;
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui', '//code.jquery.com/ui/1.11.0/jquery-ui.js' );
	vtt_enqueue_files( 'style', 'main-style', 'style.css', array(), '1.0.0' );
	
	if( $vtt_mobile_support->use_mobile_site )
	{
		vtt_enqueue_files( 'style', 'mobile-site', 'styles/mobile-site.css');
	}
	else
	{
		vtt_enqueue_files( 'style', 'full-site', 'styles/full-site.css');
	}
	
	vtt_enqueue_file( 'script', 'vtt_toggle_sidebar', 'scripts/jquery.toggle-sidebars.js' );
}
endif;



/**
 * Enqueues the theme version of the the file specified. 
 * @param	$type		string		The type of file to enqueue (script or style).
 * @param	$name		string		The name to give te file.
 * @param	$filepath	string		The relative path to filename.
 */
if( !function_exists('vtt_enqueue_files') ):
function vtt_enqueue_files( $type, $name, $filepath, $dependents = array(), $version = false  )
{
	global $vtt_config;
	
	if( $type !== 'script' && $type !== 'style' ) return;

	$directories = $vtt_config->get_all_directories( false );
	
	foreach( $directories as $key => $directory )
	{
		if( file_exists($directory.'/'.$filepath) )
		{
			$url = vtt_path_to_url( $directory.'/'.$filepath );
			call_user_func( 'wp_register_'.$type, $name.'-'.$key, $url, $dependents, $version );
			call_user_func( 'wp_enqueue_'.$type, $name.'-'.$key );
		}
	}
}
endif;


/**
 * Enqueues the theme version of the the file specified.
 * @param	$type		string		The type of file to enqueue (script or style).
 * @param	$name		string		The name to give te file.
 * @param	$filepath	string		The relative path to filename.
 */
if( !function_exists('vtt_enqueue_file') ):
function vtt_enqueue_file( $type, $name, $filepath, $dependents = array(), $version = false )
{
	if( $type !== 'script' && $type !== 'style' ) return;
	
	$url = vtt_get_theme_file_url($filepath);
	
	if( $url !== null )
	{
		call_user_func( 'wp_register_'.$type, $name, $url, $dependents, $version );
		call_user_func( 'wp_enqueue_'.$type, $name );
	}
}
endif;


/**
 * 
 */
if( !function_exists('vtt_admin_preview_callback') ):
function vtt_admin_preview_callback()
{
	vtt_get_template_part( 'header', 'part' );
}
endif;


/**
 * 
 */
if( !function_exists('vtt_admin_head_callback') ):
function vtt_admin_head_callback()
{
	vtt_enqueue_files( 'style', 'header-style', 'styles/admin-header.css' );
}
endif;


/**
 * Adds support for featured images.
 */
if( !function_exists('vtt_add_featured_image_support') ):
function vtt_add_featured_image_support()
{
	global $vtt_config;
	
	add_theme_support( 'custom-header',
		array( 
			'width' 					=> 950, 
			'height'					=> 200,
			'flex-width'				=> false,
			'flex-height'				=> true,
			'random-default' 			=> true,
			'admin-head-callback' 		=> 'vtt_admin_head_callback',
			'admin-preview-callback' 	=> 'vtt_admin_preview_callback',
			'header-text'				=> false,
		)
	);
	
	$all_directories = $vtt_config->get_all_directories( false );
	$all_directories = apply_filters( 'vtt-headers-directories', $all_directories );
	
	$images = array();
	foreach( $all_directories as $directory )
	{
		if( !is_dir($directory.'/images/headers') ) continue;
		if( !is_dir($directory.'/images/headers/full') ) continue;
		if( !is_dir($directory.'/images/headers/thumbnail') ) continue;
		
		$url = vtt_path_to_url( $directory );
		$files = scandir( $directory.'/images/headers/full' );
		foreach( $files as $file )
		{
			if( $file[0] == '.' ) continue;
			if( is_dir($directory.'/images/headers/full/'.$file) ) continue;
			if( !file_exists($directory.'/images/headers/thumbnail/'.$file) ) continue;
			if( is_dir($directory.'/images/headers/thumbnail/'.$file) ) continue;
			
			$basename = basename( $file );
			$images[$basename]['url'] = $url.'/images/headers/full/'.$file;
			$images[$basename]['thumbnail_url'] = $url.'/images/headers/thumbnail/'.$file;
			$images[$basename]['description'] = $url;
		}
	}
	
	register_default_headers( $images );	
}
endif;


/**
 * 
 */
if( !function_exists('vtt_get_header_image') ):
function vtt_get_header_image()
{
	$header_url = get_header_image();
	if( !$header_url ) $header_url = get_random_header_image();

	$header_path = '';
	if( $header_url ) $header_path = vtt_url_to_path($header_url);
		
	if( !$header_path )
	{
		$header_url = '';
		$header_width = 0;
		$header_height = 0;
	}
	else
	{
		list( $header_width, $header_height ) = getimagesize( $header_path );
	}
	
	return array(
		'url' 		=> $header_url,
		'width' 	=> $header_width,
		'height' 	=> $header_height,
	);
}
endif;


/**
 * 
 */
if( !function_exists('vtt_url_to_path') ):
function vtt_url_to_path( $url )
{
	$url_parts = parse_url($url);
	$url_path = $url_parts['host'].$url_parts['path'];
	
	$uploads_info = wp_upload_dir();
	$uploads_info['baseurl'] = str_replace( 'http://', 'https://', $uploads_info['baseurl'] );
	
	if( strpos($url, $uploads_info['baseurl']) !== false )
	{
		return str_replace( $uploads_info['baseurl'], $uploads_info['basedir'], $url );
	}

	if( strpos($url, home_url()) !== false )
	{
		return str_replace( home_url().'/', ABSPATH, $url );
	}
	

	$template_parts = parse_url(get_template_directory_uri());
	$template_path = $template_parts['host'].$template_parts['path'];
	
	if( is_child_theme() )
	{
		$stylesheet_parts = parse_url(get_stylesheet_directory_uri());
		$stylesheet_path = $stylesheet_parts['host'].$stylesheet_parts['path'];
		
		if( strpos($url_path, $stylesheet_path) !== false )
		{
			$path = str_replace( $stylesheet_path, get_stylesheet_directory(), $url_path );
			if( file_exists($path) ) return $path;

			$path = str_replace( $stylesheet_path, get_template_directory(), $url_path );
			if( file_exists($path) ) return $path;
			
			return '';
		}
		
		if( strpos($url_path, $template_path) !== false )
		{
			$path = str_replace( $template_path, get_stylesheet_directory(), $url_path );
			if( file_exists($path) ) return $path;

			$path = str_replace( $template_path, get_template_directory(), $url_path );
			if( file_exists($path) ) return $path;
			
			return '';
		}
	}
	else
	{
		if( strpos($url_path, $template_path) !== false )
		{
			$path = str_replace( $template_path, get_template_directory(), $url_path );
			if( file_exists($path) ) return $path;
			
			return '';
		}
	}

	$upload = wp_upload_dir();
	$upload_parts = parse_url($upload['baseurl']);
	$upload_path = $upload_parts['host'].$upload_parts['path'];
	
	if( strpos($url_path, $upload_path) !== false )
	{
		$path = str_replace( $upload_path, $upload['basedir'], $url_path );
		if( file_exists($path) ) return $path;
		
		return '';
	}
	
	return '';
}
endif;


/**
 * 
 */
if( !function_exists('vtt_path_to_url') ):
function vtt_path_to_url( $path )
{
	if( !file_exists($path) ) return '';

	$uploads_info = wp_upload_dir();
	$uploads_info['baseurl'] = str_replace( 'http://', 'https://', $uploads_info['baseurl'] );
	
	if( strpos($path, $uploads_info['basedir']) !== false )
	{
		return str_replace( $uploads_info['basedir'], $uploads_info['baseurl'].'/', $path );
	}
	
	if( strpos($path, ABSPATH) !== false )
	{
		return str_replace( ABSPATH, home_url().'/', $path );
	}
	
	$upload = wp_upload_dir();
	if( strpos($path, $upload['basedir']) !== false )
	{
		return str_replace( $upload['basedir'], $upload['baseurl'], $path );
	}

	return plugins_url( $path );
	
	return '';
}
endif;


/**
 * Writes an object to the page with <pre> tags.
 * @param	$var		mixed		An object to var_dump.
 */
if( !function_exists('vtt_print') ):
function vtt_print( $var, $label = '' )
{
	echo '<pre style="display:block; clear:both;">';
	if( $label !== '' ) echo '<b>'.$label.":</b> \r\n";
	var_dump($var);
	echo '</pre>';
}
endif;



/**
 * Retreives the absolute path to a file within the theme.
 * @param	$filepath	string		The relative path within the theme to the file.
 * @return				string|null	The absolute path to the file in the theme.
 */
if( !function_exists('vtt_get_theme_file_path') ):
function vtt_get_theme_file_path( $filepath, $search_type = 'both', $return_null = true )
{
	global $vtt_config;
	
	if( (strlen($filepath) > 0) && ($filepath[0] === '/') ) $filepath = substr( $filepath, 1 );
	
	if( $search_type === 'both' || $search_type === 'variation' ):
	
	$directories = $vtt_config->get_variation_all_directories( true );
	foreach( $directories as $directory )
	{
		if( file_exists($directory.'/'.$filepath) )
			return $directory.'/'.$filepath;
	}
	
	endif;
	
	if( $search_type === 'both' || $search_type === 'theme' ):
	
	$directories = $vtt_config->get_theme_directories( true );
	foreach( $directories as $directory )
	{
		if( file_exists($directory.'/'.$filepath) )
			return $directory.'/'.$filepath;
	}

	endif;
		
	if( $return_null ) return null;
	return '';
}
endif;


/**
 * Retreives the absolute url to a file within the theme. 
 * @param	$filepath	string		The relative path within the theme to the file.
 * @return				string|null	The absolute path to the file in the theme.
 */
if( !function_exists('vtt_get_theme_file_url') ):
function vtt_get_theme_file_url( $filepath, $search_type = 'both', $return_null = true )
{
	global $vtt_config;
	
	if( (strlen($filepath) > 0) && ($filepath[0] === '/') ) $filepath = substr( $filepath, 1 );
	
	$filepath = vtt_get_theme_file_path( $filepath, $search_type, true );
	
	if( $filepath ) 
		return vtt_path_to_url( $filepath );
	
	if( $return_null ) return null;
	return '';
}
endif;



/**
 * @param	$filepath	string		The relative path within the theme to the file.
 */
if( !function_exists('vtt_include_files') ):
function vtt_include_files( $filepath )
{
	$directories = $vtt_config->get_theme_directories( true );
	foreach( $directories as $directory )
	{
		if( file_exists($directory.'/'.$filepath) )
			include_once( get_stylesheet_directory().'/'.$filepath );
	}
}
endif;



/**
 * Find, then includes the template part.
 * @param	$name		string		The name of the template part.
 */
if( !function_exists('vtt_get_template_part') ):
function vtt_get_template_part( $name, $folder = '', $key = '' )
{
	global $vtt_config;
	if( $folder ) $folder = 'templates/'.$folder.'/'; else $folder = 'templates/';
	
	$filepath = null;
	if( $key )
		$filepath = vtt_get_theme_file_path( $folder.$name.'-'.$key.'.php' );
	
	if( $filepath === null )
		$filepath = vtt_get_theme_file_path( $folder.$name.'.php' );
	
	if( $filepath !== null )
	{
		include( $filepath );
		return true;
	}
	
	return false;
}
endif;



/**
 * Retreives a tag object based on the slug.
 * @param	$slug		string		The slug/name of the tag.
 * @return				mixed		Term Row (array) or false if not found.
 */
if( !function_exists('vtt_get_tag_by_slug') ):
function vtt_get_tag_by_slug( $slug )
{
	return get_term_by( 'slug', $slug, 'post_tag' );
}
endif;



 /**
 * Creates the HTML for the an anchor.  If contents are provided, then the anchor will
 * wrap the contents, else only the beginning anchor tag will be returned.
 * @param	$url		string		The url of the anchor.
 * @param	$title		string		The title for the anchor.
 * @param	$class		string|null	The class for the anchor, if any.
 * @param	$contents	string|null	The contents wrapped by the anchor.
 * @return				string		The created anchor tag.
 */
if( !function_exists('vtt_get_anchor') ):
function vtt_get_anchor( $url, $title, $class = null, $contents = null )
{
	if( empty($url) ) return $contents;
	
	$anchor = '<a href="'.$url.'" title="'.htmlentities($title).'"';
	if( $class ) $anchor .= ' class="'.$class.'"';
	$anchor .= '>';

	if( $contents !== null )
		$anchor .= $contents.'</a>';

	return $anchor;
}
endif;



/**
 * Gets the current datetime for the current timezone.
 * @return  DateTime  The current datetime.
 */
if( !function_exists('vtt_get_current_datetime') ):
function vtt_get_current_datetime()
{
	global $vtt_config;
	$timezone = $vtt_config->get_timezone();
	date_default_timezone_set($timezone);
	return ( new Datetime() );
}
endif;



/**
 * 
 */
if( !function_exists('vtt_use_widget') ):
function vtt_use_widget( $part, $placement )
{
	global $vtt_config;
	
	if( !function_exists('dynamic_sidebar') ) return;
	
	if( $vtt_config->use_widget($part, $placement) ) dynamic_sidebar( $part.'-'.$placement );
}
endif;



/**
 * 
 */
if( !function_exists('vtt_image') ):
function vtt_image( $image_info, $echo = true )
{
	global $vtt_mobile_support;

	if( empty($image_info) ) return;
	
	if( !isset($image_info['selection-type']) ) $image_info['selection-type'] = 'relative';
	if( !isset($image_info['use-site-link']) ) $image_info['use-site-link'] = false;
	
	switch( $image_info['selection-type'] )
	{
		case 'relative':
			$image_info['path'] = vtt_get_theme_file_url( $image_info['path'] );
			break;

		case 'media':
			$image_info['path'] = wp_get_attachment_url( $image_info['attachment-id'] );
			break;
		
		default: return;
	}
	
	$html = '<img src="'.$image_info['path'].'" alt="'.$image_info['title'].'" class="'.$image_info['class'].'" />';

	if( isset($image_info['use-site-link']) && ($image_info['use-site-link'] === true) )
	{
		$image_info['link'] = get_home_url();
	}
	
	if( !empty($image_info['link']) )
		$html = vtt_get_anchor( $image_info['link'], $image_info['title'], $image_info['class'], $html );

	if( $echo ) echo $html;
	else return $html;
}
endif;



/**
 * Retreives an image's url.
 * @param	$path		string		The absolute or relative path to the image.
 * @return				string|null	The absolute url to the image.
 */
if( !function_exists('vtt_get_image_url') ):
function vtt_get_image_url( $path )
{
	global $vtt_mobile_support;
	
	if( is_array($path) ) $path = $path['url'];
	
	if( $vtt_mobile_support->use_mobile_site )
	{
		$pathinfo = pathinfo( $path );
		$mobile_path = vtt_get_theme_file_url( $pathinfo['dirname'].'/'.$pathinfo['filename'].'-mobile.'.$pathinfo['extension'] );
		if( $mobile_path ) return $mobile_path;
	}
	
	return vtt_get_theme_file_url($path);
}
endif;



/**
 * 
 */
if( !function_exists('vtt_get_image_info') ):
function vtt_get_image_info( $image_info )
{
	global $vtt_mobile_support;
	
	if( !$image_info ) return $image_info;
	
	$image_info['height'] = 'auto';
	$image_info['width'] = 'auto';

	$pathinfo = pathinfo( $image_info['path'] );
	if( !$pathinfo ) return $image_info;	
	
	$full_path = ''; $path = ''; $url = '';
	if( $vtt_mobile_support->use_mobile_site )
	{
		$path = $pathinfo['dirname'].'/'.$pathinfo['filename'].'-mobile.'.$pathinfo['extension'];
		$full_path = vtt_get_theme_file_path( $path );
		
		if( $path !== null ) 
			$url = vtt_get_theme_file_url( $path );
	}

	if( !$url )
	{
		$path = $pathinfo['dirname'].'/'.$pathinfo['filename'].'.'.$pathinfo['extension'];
		$full_path = vtt_get_theme_file_path( $path );

		if( $path !== null ) 
			$url = vtt_get_theme_file_url( $path );
	}
	
	if( !$url ) return $image_info;
	
	$image_info['path'] = $full_path;
	$image_info['url'] = $url;

	$image_size = getimagesize( $image_info['path'] );
	$image_info['width'] = $image_size[0];
	$image_info['height'] = $image_size[1];
	
	return $image_info;
}
endif;



/**
 * 
 */
if( !function_exists('vtt_str_starts_with') ):
function vtt_str_starts_with($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}
endif;



/**
 * 
 */
if( !function_exists('vtt_str_ends_with') ):
function vtt_str_ends_with($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}
endif;



/**
 * 
 */
if( !function_exists('vtt_get_section') ):
function vtt_get_section( $wpquery = null )
{
	global $wp_query, $vtt_config;

	if( $wpquery === null ) $wpquery = $wp_query;
	if( $wpquery->get('section') ) return $wpquery->get('section');
	
	$qo = $wpquery->get_queried_object();
	
	if( $wpquery->is_archive() )
	{
		if( $wpquery->is_tax() || $wpquery->is_tag() || $wpquery->is_category() )
		{
			$section = $vtt_config->get_section( null, array( $qo->taxonomy => $qo->slug ), false );
			$wpquery->set( 'section', $section );
			return $section;
		}
		elseif( $wpquery->is_post_type_archive() )
		{
			$section = $vtt_config->get_section( $qo->name, null, false );
			$wpquery->set( 'section', $section );
			return $section;
		}

		return $vtt_config->get_default_section();
	}
	
	if( $wpquery->is_single() )
	{
		if( $qo === null )
		{
			$post_id = $wp_query->get( 'p' );

			if( !$post_id )
			{
				global $wpdb;
				
				$post_type = $wp_query->get( 'post_type', false );
				if( !$post_type ) $post_type = 'post';
				
				$post_slug = $wp_query->get( 'name', false );
				
				if( $post_slug !== false )
					$post_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_type = '$post_type' AND post_name = '$post_slug'" );
			}
		}
		else
		{
			$post_id = $qo->ID;
		}
		
		if( $post_id )
		{
			$post_type = get_post_type( $post_id );
			$taxonomies = vtt_get_taxonomies( $post_id );
			$section = $vtt_config->get_section( $post_type, $taxonomies, false, array('news') );
		}
		else
		{
			$section = $vtt_config->get_default_section();
		}
		
		$wpquery->set( 'section', $section );
		return $section;
	}
	
	return $vtt_config->get_default_section();
}
endif;


/**
 * Need to add support for displaying multiple authors...
 */
if( !function_exists( 'vtt_get_byline' ) ) :
function vtt_get_byline( $post )
{
	$date = date( 'F d, Y', strtotime($post->post_date) );
	
	$author = get_the_author_meta( 'display_name', $post->post_author );
	$url = get_author_posts_url($post->post_author);
	
//	return $date.' by '.$author;
	return $date.' by <a href="'.$url.'" title="Posts by '.$author.'">'.$author.'</a>';
}
endif;


if( !function_exists( 'vtt_get_breadcrumbs' ) ):
function vtt_get_breadcrumbs( $post )
{
	$breadcrumbs = array();
	$breadcrumbs[] = get_the_title( $post->ID );

	if( $post->post_parent )
	{
		$parent_id = $post->post_parent;
		while( $parent_id )
		{
			$page = get_page( $parent_id );
			$link = get_permalink($page->ID);
			$title = get_the_title($page->ID);
			$breadcrumbs[] = '<a href="'.$link.'" title="'.$title.'">'.$title.'</a>';
			$parent_id = $page->post_parent;
		}
	}

	if( count($breadcrumbs) > 1 )
		return implode( ' &raquo; ',  array_reverse($breadcrumbs) );
	return '';
}
endif;


/**
 * 
 */
if( !function_exists( 'vtt_get_taxonomy_breadcrumbs' ) ):
function vtt_get_taxonomy_breadcrumbs( $term_id, $taxonomy = 'category' )
{
	$term = get_term( $term_id, $taxonomy );
	if( $term === null || is_wp_error($term) ) return '';
	
	$breadcrumbs = array();
	while( $term->parent )
	{
		$term = get_term( $term->parent, $taxonomy );
		$link = get_term_link( $term, $taxonomy );
		$title = $term->name;
		$breadcrumbs[] = '<a href="'.$link.'" title="'.$title.'">'.$title.'</a>';
	}
	
	if( count($breadcrumbs) > 0 )
		return implode( ' &raquo; ',  $breadcrumbs ).' &raquo; ';
	return '';
}
endif;


/**
 * 
 */
if( !function_exists( 'vtt_get_taxonomy_list' ) ):
function vtt_get_taxonomy_list( $taxonomy_name, $post )
{
	$taxonomy = get_taxonomy( $taxonomy_name );
	if( !$taxonomy ) return '';

	$terms = wp_get_post_terms( $post->ID, $taxonomy_name );
	if( count($terms) == 0 ) return '';

	$html = '';
	$html .= '<div class="taxonomy-list '.$taxonomy->name.'-list">';	

	$taxonomy_label = $taxonomy->label;

	if( $taxonomy->label == "Categories" ) 
	{
		$taxonomy_label = get_option('category_base');
		if( !$taxonomy_label ) $taxonomy_label = $taxonomy->label;
		$taxonomy_label_style = "<span class='category-label'>";
	} 
	else if( $taxonomy->label == "Tags" ) 
	{
		$taxonomy_label = get_option('tag_base');
		if( !$taxonomy_label ) $taxonomy_label = $taxonomy->label;
		$taxonomy_label_style = "<span class='tag-label'>";
	}
	else
	{
		$taxonomy_label_style = "<span class='taxonomy-label'>";
	}
	
	$html .= $taxonomy_label_style.$taxonomy_label.': </span>';

	if( count($terms) > 0 )
	{
		$list = array();
		foreach( $terms as $t )
		{
			$list[] = vtt_get_anchor( get_term_link($t->term_id, $taxonomy_name), $t->name, $t->slug, $t->name );
		}
		$html .= implode( '', $list );
	}
	else
	{
		$html .= '-';
	}
	
	$html .= '</div>';
	
	return $html;
}
endif;


/**
 * 
 */
if( !function_exists('vtt_get_categories') ):
function vtt_get_categories( $categories = null )
{
	if( $categories == null )
		$categories = get_the_category();

	$category = array();
	if( $categories )
	{
		foreach( $categories as $c ) $category[] = $c->slug;
	}
	
	return $category;
}
endif;



/**
 * 
 */
if( !function_exists('vtt_get_tags') ):
function vtt_get_tags( $tags = null )
{
	if( $tags == null )
		$tags = get_the_tags();
	
	$tag = array();	
	if( $tags )
	{
		foreach( $tags as $t ) $tag[] = $t->slug;
	}
	
	return $tag;
}
endif;



/**
 * 
 */
if( !function_exists('vtt_get_taxonomies') ):
function vtt_get_taxonomies( $post_id = -1 )
{
	global $post;
	
	if( $post_id == -1 )
		$post_id = $post->ID;
		
	$all_taxonomies = get_taxonomies( '', 'names' );
	
	$taxonomies = array();
	foreach( $all_taxonomies as $taxname )
	{
		$terms = wp_get_post_terms( $post_id, $taxname, array('fields' => 'slugs') );
		if( count($terms) > 0 )
			$taxonomies[$taxname] = $terms;
	}
	
	return $taxonomies;
}
endif;



/**
 * 
 */
if( !function_exists('vtt_get_page_url') ):
function vtt_get_page_url()
{
	$page_url = 'http';
	if( isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on') ) $page_url .= 's';
	$page_url .= '://';
	$request_uri_parts = explode('?', $_SERVER['REQUEST_URI']);
	$request_uri = $request_uri_parts[0];
	if( $_SERVER['SERVER_PORT'] != '80' )
		$page_url .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$request_uri;
	else
		$page_url .= $_SERVER['SERVER_NAME'].$request_uri;
	return $page_url;
}
endif;



/**
 * 
 */
if( !function_exists('vtt_customize_register') ):
function vtt_customize_register( $wp_customize )
{
	global $vtt_config;
	
	//
	// Variation Section.
	//
	
	$wp_customize->add_section(
		VTT_VARIATION_OPTION.'-section',
		array(
			'title'      => 'Variation',
			'priority'   => 0,
		)
	);
	
	$wp_customize->add_setting(
		VTT_VARIATION_OPTION,
		array(
			'default'     => $vtt_config->get_variation_name(),
			'transport'   => 'refresh',
		)
	);
	
	$wp_customize->add_control( 
		new WP_Customize_Control( 
			$wp_customize, 
			VTT_VARIATION_OPTION.'-control', 
			array(
				'label'      => 'Name',
				'section'    => VTT_VARIATION_OPTION.'-section',
				'settings'   => VTT_VARIATION_OPTION,
				'type'       => 'select',
				'choices'    => $vtt_config->get_all_variation_names(),
			)
		)
	);
	
	
	//
	// Header Title section
	//
	
	$wp_customize->add_section(
		'vtt-header-title-section',
		array(
			'title'      => 'Header Title',
			'priority'   => 0,
		)
	);

	// Header Title hide
	
	$wp_customize->add_setting(
		'vtt-'.VTT_HEADER_TITLE_HIDE,
		array(
			'default'     => $vtt_config->value_to_string( 
				$vtt_config->get_value('header', 'title-hide')
			),
			'transport'   => 'refresh',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control( 
			$wp_customize, 
			'vtt-'.VTT_HEADER_TITLE_HIDE.'-control', 
			array(
				'label'      => 'Hide header title',
				'section'    => 'vtt-header-title-section',
				'settings'   => 'vtt-'.VTT_HEADER_TITLE_HIDE,
				'type'       => 'checkbox',
			)
		)
	);
	
	// Header Title position

	$wp_customize->add_setting(
		'vtt-'.VTT_HEADER_TITLE_POSITION,
		array(
			'default'     => $vtt_config->get_value( 'header', 'title-position' ),
			'transport'   => 'refresh',
		)
	);

	$wp_customize->add_control( 
		new VTT_Customize_Header_Position( 
			$wp_customize, 
			'vtt-'.VTT_HEADER_TITLE_POSITION.'-control', 
			array(
				'label'      => 'Position',
				'section'    => 'vtt-header-title-section',
				'settings'   => 'vtt-'.VTT_HEADER_TITLE_POSITION,
			)
		)
	);
	
	//
	// Featured Image section
	//
	
	$wp_customize->add_section(
		'vtt-featured-image-section',
		array(
			'title'      => 'Featured Image',
			'priority'   => 0,
		)
	);

	$wp_customize->add_setting(
		'vtt-'.VTT_FEATURED_IMAGE_POSITION,
		array(
			'default'     => $vtt_config->get_value( VTT_FEATURED_IMAGE_POSITION ),
			'transport'   => 'refresh',
		)
	);
	
	$wp_customize->add_control( 
		new WP_Customize_Control( 
			$wp_customize, 
			'vtt-'.VTT_FEATURED_IMAGE_POSITION.'-control', 
			array(
				'label'      => 'Position',
				'section'    => 'vtt-featured-image-section',
				'settings'   => 'vtt-'.VTT_FEATURED_IMAGE_POSITION,
				'type'       => 'select',
				'choices'    => array(
					'header'	=> 'Header Image',
					'left'		=> 'Left Image',
					'right'		=> 'Right Image',
					'center'	=> 'Across Top Centered',
				),
			)
		)
	);
	
	
}
endif;



/**
 * 
 */
if( !function_exists('vtt_customize_save') ):
function vtt_customize_save( $wp_customize )
{
	global $vtt_config;
	$vtt_config->set_variation( get_theme_mod(VTT_VARIATION_OPTION), true );
	$vtt_config->set_value( 'header', 'title-position', get_theme_mod('vtt-'.VTT_HEADER_TITLE_POSITION) );
	$vtt_config->set_value( 'header', 'title-hide', get_theme_mod('vtt-'.VTT_HEADER_TITLE_HIDE) );
	$vtt_config->set_value( VTT_FEATURED_IMAGE_POSITION, get_theme_mod('vtt-'.VTT_FEATURED_IMAGE_POSITION) );
}
endif;


/**
 * 
 */
if( !function_exists('vtt_customize_update_variation') ):
function vtt_customize_update_variation( $old_value, $new_value )
{
	global $wp_customize;
	if( isset($wp_customize) ) return;
	set_theme_mod( VTT_VARIATION_OPTION, $new_value );
}
endif;


/**
 * 
 */
if( !function_exists('vtt_customize_update_options') ):
function vtt_customize_update_options( $old_value, $new_value )
{
	global $wp_customize;
	if( isset($wp_customize) ) return;
	
	if( !empty($new_value['header']['title-position']) )
		set_theme_mod( 'vtt-'.VTT_HEADER_TITLE_POSITION, $new_value['header']['title-position'] );
	else
		remove_theme_mod( 'vtt-'.VTT_HEADER_TITLE_POSITION );
	
	if( !empty($new_value['header']['title-hide']) )
		set_theme_mod( 'vtt-'.VTT_HEADER_TITLE_HIDE, $new_value['header']['title-hide'] );
	else
		remove_theme_mod( 'vtt-'.VTT_HEADER_TITLE_HIDE );

	if( !empty($new_value[VTT_FEATURED_IMAGE_POSITION]) )
		set_theme_mod( 'vtt-'.VTT_FEATURED_IMAGE_POSITION, $new_value[VTT_FEATURED_IMAGE_POSITION] );
	else
		remove_theme_mod( 'vtt-'.VTT_FEATURED_IMAGE_POSITION );
}
endif;


/**
 * 
 */
if( !function_exists('vtt_find_comments_template_part') ):
function vtt_find_comments_template_part( $path )
{
	$filepath = vtt_get_theme_file_path( 'templates/other/comments.php' );
	if( $filepath ) $path = $filepath;

	return $path;
}
endif;



/**
 * 
 */
if( !function_exists('vtt_add_custom_mime_types') ):
function vtt_add_custom_mime_types( $mimes )
{
	// Mime types to remove:
	// .mp4, .mov, .wmv, .avi
	unset( $mimes['mp4'] );
	unset( $mimes['mov'] );
	unset( $mimes['wmv'] );
	unset( $mimes['avi'] );

	// Mime types to include:
	// .exe, .zip
	$mimes['exe'] = 'application/x-msdownload';
	$mimes['zip'] = 'application/zip';
	
	return $mimes;
}
endif;


/**
 * 
 */
if( !function_exists('vtt_read_more_link') ):
function vtt_read_more_link() {
	return '<a class="more-link" href="' . get_permalink() . '">Read more...</a>';
}
endif;


/**
 * Prints a backtrace for debugging.
 */
if( !function_exists('vtt_backtrace') ):
function vtt_backtrace( $fullpath = false )
{
	if(!function_exists('debug_backtrace')) 
	{
		echo '<pre style="display:block; clear:both;">'.
			"<b>Debug backtrace:</b> \r\n".
			"function debug_backtrace does not exists\r\n".
		'</pre>'; 
		return; 
	}
	
	$text = '';
	
	foreach(debug_backtrace() as $t) 
	{ 
		$text .= '@ '; 
		if( isset($t['file']) )
		{
			if( $fullpath )
				$text .= $t['file'] . ":\r\n\t" . $t['line']; 
			else
				$text .= basename($t['file']) . ':' . $t['line']; 
		}
		else 
		{ 
			// if file was not set, I assumed the functioncall 
			// was from PHP compiled source (ie XML-callbacks). 
			$text .= '<PHP inner-code>'; 
		} 

		$text .= ' -- '; 

		if(isset($t['class'])) $text .= $t['class'] . $t['type']; 

		$text .= $t['function']; 

		if(isset($t['args']) && sizeof($t['args']) > 0) $text .= '(...)'; 
		else $text .= '()'; 

		$text .= "\r\n"; 
	}

	echo '<pre style="display:block; clear:both;">'.
		"<b>Debug backtrace:</b> \r\n".
		$text."\r\n".
	'</pre>'; 
}
endif;


/**
 * 
 * 
 */
if( !function_exists('vtt_add_home_pages_menu_item') ):
function vtt_add_home_pages_menu_item( $args )
{
	$args['show_home'] = true;
	return $args;
}
endif;



