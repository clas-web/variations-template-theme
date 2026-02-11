<?php
/**
 * The main functions for the Variations Template Theme.
 * 
 * @package    variations-template-theme
 * @author     Crystal Barton <atrus1701@gmail.com>
 */


if( !defined('VTT') ):

/**
 * The full title of the Variations Template Theme.
 * @var  string
 */
define( 'VTT', 'Variations Template Theme' );

/**
 * True if debug is active, otherwise False.
 * @var  string
 */
define( 'VTT_DEBUG', false );

/**
 * The path to the VTT theme.
 * @var  string
 */
define( 'VTT_PATH', __DIR__ );

/**
 * The url to the VTT theme.
 * @var  string
 */
define( 'VTT_URL', get_template_directory_uri() );

/**
 * The version of the VTT theme.
 * @var  string
 */
define( 'VTT_VERSION', '1.0.0' );

/**
 * The database version of the VTT theme.
 * @var  string
 */
define( 'VTT_DB_VERSION', '1.1' );

/**
 * The log file used for debugging.
 * @var  string
 */
define( 'VTT_LOG_PATH', VTT_PATH.'/log.txt' );

endif;


// Set the global variables.
global $vtt_mobile_support, $vtt_config;

// Setup mobile support.
require_once( get_template_directory().'/classes/mobile-support.php' );
$vtt_mobile_support = new VTT_Mobile_Support;

// Setup the config data.
require_once( get_template_directory().'/classes/config.php' );
$vtt_config = new VTT_Config;

// Setup theme customizer controls
if( is_customize_preview() ):
	require_once( VTT_PATH.'/classes/customizer/variation/control.php' );
	require_once( VTT_PATH.'/classes/customizer/color-picker-alpha/control.php' );
endif;
if( defined('DOING_AJAX') && DOING_AJAX ):
	require_once( VTT_PATH.'/classes/customizer/variation/functions.php' );
endif;

// Admin styles
add_action( 'admin_init', 'vtt_add_editor_styles' );

// Admin bar
add_action( 'admin_bar_menu', 'vtt_add_login', 10 );

// Theme setup
add_action( 'wp_enqueue_scripts', 'vtt_enqueue_scripts', 0 );
add_action( 'admin_enqueue_scripts', 'vtt_admin_enqueue_scripts', 0 );

// Embeded content
add_filter( 'embed_oembed_html', 'vtt_video_embed_html', 10, 3 );
add_filter( 'video_embed_html', 'vtt_video_embed_html' );

// Theme Customizer
add_action( 'customize_register', 'vtt_customize_register', 11 );

// Comments
add_filter( 'comments_template', 'vtt_find_comments_template_part', 999 );

// Categories Widget
add_action( 'init', 'vtt_setup_categories_walker' );

// Enable Links subpanel
add_filter( 'pre_option_link_manager_enabled', '__return_true' );



/**
 * Clear the log file or creates the file, if it does not exist.
 */
if( !function_exists('vtt_clear_log') ):
function vtt_clear_log()
{
	file_put_contents( VTT_LOG_PATH, '' );
}
endif;


/**
 * Writes an object to the log file.
 * @param  mixed  $var  An object to print to file.
 * @param  string  $label  The label for the object, if any.
 */
if( !function_exists('vtt_write_log') ):
function vtt_write_log( $var, $label = '' )
{
	if( $label !== '' ) 
		file_put_contents( VTT_LOG_PATH, "-----\r\n$label: \r\n", FILE_APPEND );
	file_put_contents( VTT_LOG_PATH, print_r($var, TRUE)."\r\n", FILE_APPEND );
	if( $label !== '' )
		file_put_contents( VTT_LOG_PATH, "-----\r\n", FILE_APPEND );

}
endif;


/**
 * Store the content type for the page.
 * @param  string  $value  The content type.
 */
if( !function_exists('vtt_set_page_content_type') ):
function vtt_set_page_content_type( $value )
{
	global $vtt_template_vars;
	$vtt_template_vars['content_type'] = $value;
}
endif;


/**
 * Get the content type for the page.
 * @return  string  The content type.
 */
if( !function_exists('vtt_get_page_content_type') ):
function vtt_get_page_content_type()
{
	global $vtt_template_vars;
	return ( isset($vtt_template_vars['content_type']) ? $vtt_template_vars['content_type'] : '' );
}
endif;


/**
 * Store the title for the page.
 * @param  string  $value  The title.
 */
if( !function_exists('vtt_set_page_title') ):
function vtt_set_page_title( $value )
{
	global $vtt_template_vars;
	$vtt_template_vars['page_title'] = $value;
}
endif; 


/**
 * Get the title for the page.
 * @return  string  The title.
 */
if( !function_exists('vtt_get_page_title') ):
function vtt_get_page_title()
{
	global $vtt_template_vars;
	if ( is_post_type_archive() )
	{
		$vtt_template_vars['page_title'] = post_type_archive_title('', false);
	}
	elseif( !isset($vtt_template_vars['page_title']) )
	{
		$vtt_template_vars['page_title'] = '';
	} 		
	return $vtt_template_vars['page_title'];
}
endif;


/**
 * Store the listing name for the the page.
 * @param  string  $value  The listing name.
 */
if( !function_exists('vtt_set_page_listing_name') ):
function vtt_set_page_listing_name( $value )
{
	global $vtt_template_vars;
	$vtt_template_vars['listing_name'] = $value;
}
endif; 


/**
 * Determines iif the current page has a listing name.
 * @return  bool  True if the current page has a listing name, otherwise False.
 */
if( !function_exists('vtt_has_page_listing_name') ):
function vtt_has_page_listing_name()
{
	global $vtt_template_vars;
	return isset($vtt_template_vars['listing_name']);
}
endif; 


/**
 * Get the listing name for the the page.
 * @return  string  The listing name.
 */
if( !function_exists('vtt_get_page_listing_name') ):
function vtt_get_page_listing_name()
{
	global $vtt_template_vars;
	return ( isset($vtt_template_vars['listing_name']) ? $vtt_template_vars['listing_name'] : '' );
}
endif; 


/**
 * Store the description for the page.
 * @param  string  $value  The description.
 */
if( !function_exists('vtt_set_page_description') ):
function vtt_set_page_description( $value )
{
	global $vtt_template_vars;
	$vtt_template_vars['description'] = $value;
}
endif; 


/**
 * Determines if the current page has a description.
 * @return  bool  True if the current page has a description, otherwise False.
 */
if( !function_exists('vtt_has_page_description') ):
function vtt_has_page_description()
{
	global $vtt_template_vars;
	return isset($vtt_template_vars['description']);
}
endif; 


/**
 * Get the description for the the page.
 * @return  string  The description.
 */
if( !function_exists('vtt_get_page_description') ):
function vtt_get_page_description()
{
	global $vtt_template_vars;
	return ( isset($vtt_template_vars['description']) ? $vtt_template_vars['description'] : '' );
}
endif; 


/**
 * Show the current page.
 */
if( !function_exists('vtt_render_page') ):
function vtt_render_page()
{
	$page_path = vtt_get_theme_file_path( 'templates/page.php' );
	if( $page_path )
	{
		require_once( $page_path );
		return;
	}

	die( 'The page.php in the templates folder could not be found.' );
}
endif;


/**
 * Get today's date for the WordPress site's chosen timezone.
 * @return  DateTime  The current date/time.
 */
if( !function_exists('vtt_get_current_datetime') ):
function vtt_get_current_datetime()
{
	if( get_option('timezone_string') )
		date_default_timezone_set( get_option('timezone_string') );
	return new Datetime;
}
endif;


/**
 * Returns an empty string.
 * @return  Empty string.
 */
if( !function_exists('vtt_return_nothing') ):
function vtt_return_nothing()
{
	return '';
}
endif; 


/**
 * Setup a custom category walker to display the Categories widget.
 */
if( !function_exists('vtt_setup_categories_walker') ):
function vtt_setup_categories_walker()
{
	$filepath = vtt_get_theme_file_path( 'classes/categories-walker.php' );
	if( $filepath ) require_once( $filepath );

	add_filter( 'widget_categories_args', function($args) {
		$args['walker'] = new VTT_Categories_Walker;
		return $args;
	});
}
endif;


/**
 * Wrap video embed in container.
 * @param  string  $html  The video embed html.
 * @return  The full html with container.
 */
if( !function_exists('vtt_video_embed_html') ):
function vtt_video_embed_html( $html )
{
	return '<div class="video-container">' . $html . '</div>';
}
endif; 


/**
 * Add the login link if user is not logged in.
 * @param  WP_Admin_Bar  $wp_admin_bar  
 */
if( !function_exists('vtt_add_login')):
function vtt_add_login( $wp_admin_bar )
{
	if( !is_user_logged_in() ):
		$wp_admin_bar->add_menu(
			array(
				'id'	=> 'login-link',
				'title'	=> __( 'Log In' ),
				'href'	=> wp_login_url(),
				'meta'	=> array(
					'title' => 'Log In',
				),
				'parent' => 'top-secondary',
			)
		);
	endif;
}
endif;


/**
 * Add the editor style(s) for the admin backend.
 */
if( !function_exists('vtt_add_editor_styles') ):
function vtt_add_editor_styles()
{
	global $vtt_config;

	$directories = $vtt_config->get_all_directories( false );

	$urls = array();
	foreach( $directories as $key => $directory )
	{
		if( file_exists($directory.'/editor-style.css') )
		{
			$urls[] = vtt_path_to_url( $directory.'/editor-style.css' );
		}
	}

	if( count($urls) > 0 ) add_editor_style( $urls );
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
	vtt_enqueue_files( 'style', 'main', 'style.css', array(), '1.0.0' );
	
	vtt_enqueue_file( 'script', 'vtt_responsive_ready', 'scripts/jquery.responsive-ready.js' );
}
endif;


/**
 * Enqueue any needed css or javascript files for the admin backend.
 */
if( !function_exists('vtt_admin_enqueue_scripts') ):
function vtt_admin_enqueue_scripts()
{
	vtt_enqueue_files( 'style', 'admin-main', 'admin.css', array(), '1.0.0' );
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
 * Get the header image url, width, and height.
 * @return  Array  List of header image properties.
 */
if( !function_exists('vtt_get_header_image') ):
function vtt_get_header_image()
{
	$header_url = get_header_image();

	//Set height and width to null in case there is no header image.
	$header_width = "";
    $header_height = "";
	
	//get_custom_header will return a height and width even if there is no header image.
	$data = get_custom_header();

	if ($data && has_header_image())
	{
		$header_width = $data->width;
		$header_height = $data->height;
	}
	
	//Debuging Helpers
	//vtt_print($data); exit;
	//vtt_print($header_url); exit;

	return array(
		'url' 		=> $header_url,
		'width' 	=> $header_width,
		'height' 	=> $header_height,
	);
}
endif;


/**
 * Converts a URL to absolute path.
 * @param  string  $url  The url to convert.
 * @return  string  The absolute path, or empty string if it cannot be converted.
 */
if( !function_exists('vtt_url_to_path') ):
function vtt_url_to_path( $url )
{
	// Remove url protocol.
	$url = vtt_convert_url( $url );
	
	// Check if url leads to upload directory.
	$uploads_info = wp_upload_dir();
	$uploads_info['baseurl'] = vtt_convert_url($uploads_info['baseurl']);
	
	if( strpos($url, $uploads_info['baseurl']) !== false )
	{
		return str_replace( $uploads_info['baseurl'], $uploads_info['basedir'], $url );
	}

	// Check if url leads to the home directory.
	$home_url = vtt_convert_url(home_url());
	if( strpos($url, $home_url) !== false )
	{
		return str_replace( $home_url.'/', ABSPATH, $url );
	}
	
	// Check if url leads to the template or stylesheet directory.
	$url_parts = parse_url($url);
	$url_path = $url_parts['host'].$url_parts['path'];
	
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

	// No matches found.
	return '';
}
endif;


/**
 * Converts an absolute path to the URL.
 * @param  string  $path  The absolute path.
 * @return  string  The url to the path.
 */
if( !function_exists('vtt_path_to_url') ):
function vtt_path_to_url( $path )
{
	// Clean the file path to convert Windows style \ to Unix style /.
	$path = vtt_clean_path( $path );

	// Check if path leads to base upload directory.
	$uploads_info = wp_upload_dir();
	$uploads_info['basedir'] = vtt_clean_path( $uploads_info['basedir'] );
	if( strpos($path, $uploads_info['basedir']) !== false )
	{
		return str_replace( $uploads_info['basedir'], vtt_convert_url($uploads_info['baseurl']).'/', $path );
	}
	
	// Check if path leads to the absolute path of the WordPress install.
	if( strpos($path, vtt_clean_path(ABSPATH)) !== false )
	{
		return str_replace( vtt_clean_path(ABSPATH), vtt_convert_url(home_url()).'/', $path );
	}
	
	// Remove url protocol.
	return vtt_convert_url(plugins_url( $path ));
}
endif;


/**
 * Converts any Windows directory seperators (\) with Unix directory seperators (/).
 * @param   string  $path  The file path.
 * @return  string  The modified file path.
 */
function vtt_clean_path( $path )
{
	return str_replace( '\\', '/', $path );
}


/**
 * Converts a url to a scheme-less url without http or https.
 * @param   string  $url  The url.
 * @return  string  The modified url.
 */
if( !function_exists('vtt_convert_url') ):
function vtt_convert_url( $url )
{
	return preg_replace( "/https?:\/\//i", '//', $url );
}
endif;


/**
 * Writes an object to the page with <pre> tags.
 * @param  mixed  $var  An object to var_dump.
 * @param  string  $label  The label for the object, if any.
 */
if( !function_exists('vtt_print') ):
function vtt_print( $var, $label = '' )
{
	echo '<pre style="display:block; clear:both;">';
	if( $label !== '' ) echo "<b>$label:</b> \r\n";
	var_dump($var);
	echo '</pre>';
}
endif;


/**
 * Retreives the absolute path to a file within the theme.
 * @param  string  $filepath  The relative path within the theme to the file.
 * @param  string  $search_type  The type of directories to search for file path.
 * @param  bool  $return_null  True if null should be returned if not found.
 * @return  string|null  The absolute path to the file in the theme.
 */
if( !function_exists('vtt_get_theme_file_path') ):
function vtt_get_theme_file_path( $filepath, $search_type = 'all', $return_null = true )
{
	global $vtt_config;
	
	$filepath = trim($filepath, '/');
	
	$directories = array();
	switch( $search_type )
	{
		case 'variation':
			$directories = $vtt_config->get_all_variations_directories( true );
			break;
		case 'theme':
			$directories = $vtt_config->get_theme_directories( true );
			break;
		case 'search':
			$directories = $vtt_config->get_search_directories( true );
			break;
		case 'all':
		case 'both':
			$directories = $vtt_config->get_all_directories( true );
			break;
		default:
			$directories = array();
			break;
	}

	foreach( $directories as $directory )
	{
		if( file_exists($directory.'/'.$filepath) )
			return $directory.'/'.$filepath;
	}
		
	if( $return_null ) return null;
	return '';
}
endif;


/**
 * Retreives the absolute url to a file within the theme. 
 * @param  string  $filepath  The relative path within the theme to the file.
 * @return  string|null  The absolute path to the file in the theme.
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
 * Find, then includes the template part.
 * @param  string  $name  The name of the template part.
 * @param  string  $folder  The folder within templates to search for the part.
 * @param  string  $key  The key value to search for.
 * @return  bool  True if the template was found, otherwise False.
 */
if( !function_exists('vtt_get_template_part') ):
function vtt_get_template_part( $name, $folder = '', $key = '' )
{
	global $vtt_config;
	if( $folder ) $folder = 'templates/'.$folder.'/'; else $folder = 'templates/';
	
	$filepath = null;
	if( $key )
	{
		$filepath = vtt_get_theme_file_path( $folder.$name.'-'.$key.'.php' );
	}
	
	if( $filepath === null )
	{
		$filepath = vtt_get_theme_file_path( $folder.$name.'.php' );
	}

	if( $filepath !== null )
	{
		include( $filepath );
		return true;
	}
	
	return false;
}
endif;


/**
 * Get the type of object that was queried for the current page. 
 * Types include taxonomy name and post type.
 * @param  bool  $apply_filter  True to filter the results through the vtt-queried-object-type filter.
 * @return  string  The queried object type.
 */
if( !function_exists('vtt_get_queried_object_type') ):
function vtt_get_queried_object_type( $apply_filter = true )
{
	global $wp_query;
	$object_type = '';
	
	$qo = $wp_query->get_queried_object();
	
	if( $wp_query->is_archive() )
	{
		if( $wp_query->is_tax() || $wp_query->is_tag() || $wp_query->is_category() )
		{
			$object_type = $qo->taxonomy;
		}
		elseif( $wp_query->is_post_type_archive() )
		{
			$object_type = $qo->name;
		}
		else
		{
			$object_type = 'post';
		}
	}
	elseif( $wp_query->is_single() || $wp_query->is_singular() )
	{
		if( $qo === null )
		{
			$post_id = $wp_query->get( 'p' );

			if( !$post_id )
			{
				$post_type = $wp_query->get( 'post_type', false );
				if( !$post_type ) $post_type = 'post';
				$object_type = $post_type;
			}
		}
		else
		{
			$post_id = $qo->ID;
		}
		
		if( $post_id )
		{
			$object_type = get_post_type( $post_id );
		}
		else
		{
			$object_type ='post';
		}
	}
	
	if( $apply_filter )
		$object_type = apply_filters( 'vtt-queried-object-type', $object_type );
	
	return $object_type;
}
endif;


/**
 * Gets a post's post type.
 * @param  WP_Post|null  $p  The WP_Post object or null if the global $post should be used.
 * @return  string  The post type.
 */
if( !function_exists('vtt_get_post_type') ):
function vtt_get_post_type( $p = null )
{
	global $post;
	$post_type = 'post';
	
	if( !$p ) $p = $post;
	if( is_a($p, 'WP_Post') ) $post_type = $p->post_type;

	return apply_filters( 'vtt-post-type', $post_type, $p );
}
endif;


 /**
 * Creates the HTML for the an anchor.  If contents are provided, then the anchor will
 * wrap the contents, else only the beginning anchor tag will be returned.
 * @param  string  $url  The url of the anchor.
 * @param  string  $title  The title for the anchor.
 * @param  string|null  $class  The class for the anchor, if any.
 * @param  string|null  $contents  The contents wrapped by the anchor.
 * @return  string  The created anchor tag.
 */
if( !function_exists('vtt_get_anchor') ):
function vtt_get_anchor( $url, $title, $class = null, $contents = null )
{
	if( empty($url) ) return $contents;
	
	$anchor = '<a href="'.$url.'"';
	if( !empty($title) ) {
		$anchor .= ' title="'.htmlentities($title, ENT_QUOTES, 'UTF-8').'"';
	}
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
 * Determines the image's url, then creates an img tag and wrapping anchor tag, if needed.
 * @param  Array  $image_info  The list of image info.
 * @param  bool  $echo  True to output the image html, or False to return the html.
 * @return  string  The generated html.
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
 * @param  string  $path  The absolute or relative path to the image.
 * @return  string|null  The absolute url to the image.
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
 * Checks if the haystack string starts with the needle string.
 * @param  string  $haystack  The string to search.
 * @param  string  $needle  The string to search for.
 * @return  bool  True if the haystack starts with the needle.
 */
if( !function_exists('vtt_str_starts_with') ):
function vtt_str_starts_with( $haystack, $needle )
{
    return $needle === '' || strpos($haystack, $needle) === 0;
}
endif;



/**
 * Checks if the haystack string ends with the needle string.
 * @param  string  $haystack  The string to search.
 * @param  string  $needle  The string to search for.
 * @return  bool  True if the haystack ends with the needle.
 */
if( !function_exists('vtt_str_ends_with') ):
function vtt_str_ends_with( $haystack, $needle )
{
    return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
}
endif;


/**
 * Get the by line html for a post.
 * @param  WP_Post  $p  The WP_Post object or null if global $post object should be used.
 * @return  string  The generated html.
 */
if( !function_exists( 'vtt_get_byline' ) ) :
function vtt_get_byline( $p = null )
{
	global $post;
	if( !$p ) $p = $post;
	
	$date = date( 'F d, Y', strtotime($p->post_date) );
	
	$author = get_the_author_meta( 'display_name', $p->post_author );
	$url = get_author_posts_url($p->post_author);
	
	return '<span class="entry-date">'.$date.'</span><span class="entry-author"> by <a href="'.$url.'" title="Posts by '.$author.'">'.$author.'</a></span>';
}
endif;


/**
 * Get the breadcrumb html for a post.
 * @param  WP_Post  $p  The WP_Post object or null if global $post object should be used.
 * @return  string  The generated html.
 */
if( !function_exists( 'vtt_get_breadcrumbs' ) ):
function vtt_get_breadcrumbs( $p )
{
	global $post;
	if( !$p ) $p = $post;

	$breadcrumbs = array();
	$breadcrumbs[] = get_the_title( $p->ID );

	if( $p->post_parent )
	{
		$parent_id = $p->post_parent;
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
 * Get the breadcrumb html for a taxonomy term.
 * @param  int  $term_id  The id of the term.
 * @param  string  $taxonomy  The taxonomy of the term.
 * @param  bool  $include_home  Include home in the breadcrumb.
 * @return  string  The generated html, or an empty string if no parent is found.
 */
if( !function_exists( 'vtt_get_taxonomy_breadcrumbs' ) ):
function vtt_get_taxonomy_breadcrumbs( $term_id, $taxonomy = 'category', $include_home = FALSE )
{
	$term = get_term( $term_id, $taxonomy );
	if( $term === null || is_wp_error($term) ) return '';
	
	$breadcrumbs = array();
	
	if( $include_home ) {
		$breadcrumbs[] = '<a href="'.site_url().'" title="Home">Home</a>';
	}
	
	while( $term->parent )
	{
		$term = get_term( $term->parent, $taxonomy );
		$link = get_term_link( $term, $taxonomy );
		$title = $term->name;
		$breadcrumbs[] = '<a href="'.$link.'" title="'.$title.'">'.$title.'</a>';
	}
	
	if( count($breadcrumbs) > 0 ) {
		return implode( ' &raquo; ',  $breadcrumbs ).' &raquo; ';
	}
	return '';
}
endif;


/**
 * Get the html for a taxonomy list.
 * @param  string  $taxonomy_name  The name of the taxonomy.
 * @param  WP_Post  $p  The WP_Post object or null if global $post object should be used.
 * @return  string  The generated html.
 */
if( !function_exists( 'vtt_get_taxonomy_list' ) ):
function vtt_get_taxonomy_list( $taxonomy_name, $p )
{
	global $post;
	if( !$p ) $p = $post;

	$taxonomy = get_taxonomy( $taxonomy_name );
	if( !$taxonomy ) return '';

	$terms = wp_get_post_terms( $p->ID, $taxonomy_name );
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
 * Get the current page's url without the query string.
 * @return  The page url.
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
 * Setup the variations selection in the Theme Customizer.
 * @param  WP_Customize_Manager  $wp_customize  Theme Customizer API controller.
 */
if( !function_exists('vtt_customize_register') ):
function vtt_customize_register( $wp_customize )
{
	global $vtt_config;

	$wp_customize->add_section(
		'vtt-variation-section',
		array(
			'title'    => 'Variation',
			'priority' => 0,
		)
	);
	
	$wp_customize->add_setting(
		'vtt-variation',
		array(
			'default' => $vtt_config->get_variation_name(),
		)
	);
	
	$wp_customize->add_control( 
		new VTT_Customize_Variation( 
			$wp_customize, 
			'vtt-variation-control', 
			array(
				'label'   => 'Name',
				'section' => 'vtt-variation-section',
			)
		)
	);
	
	
	$wp_customize->add_setting(
		'vtt-variation-choices',
		array(
			'default' => '',
			'capability' => 'update_themes',
			'type' => 'theme_mod',
			'transport' => 'postMessage',
			'sanitize_callback' => 'sanitize_variation_choices'
		)
	);
	
	$wp_customize->add_control( 
		new VTT_Customize_Variation_Checkboxes(
			$wp_customize,
			'vtt-variation-choices-control', 
			array(
				'label'   => 'Variations Available on this Site',
				'description' => 'Only visible to SuperAdmins',
				'section' => 'vtt-variation-section',
				'settings' => 'vtt-variation-choices',
				'choices' => $vtt_config->get_all_site_variation_names()
			)
		)
	);
	
	$wp_customize->add_setting(
        'header_type',
        array(
            'default'    => get_theme_mod('header_type'),
            'capability' => 'edit_theme_options',
            'type'       => 'theme_mod'
        )
    );

    $wp_customize->add_control(
        'custom_header_type',
        array(
            'settings' => 'header_type',
            'label'    => __( 'Type of Header:', 'textdomain' ),
            'section'  => 'header_image',
            'type'     => 'select',
            'choices'  => array(
				'none'   => 'No Header',
                'slider' => 'Slider',
                'image'  => 'Image'
            ),
			'priority'	=> 1
        )
    );
	
	$wp_customize->add_setting(
        'header_slider',
        array(
			'default'	 => get_theme_mod('header_slider'),
            'capability' => 'edit_theme_options',
            'type'       => 'theme_mod'
        )
    );

    $wp_customize->add_control(
        'header_slider',
        array(
            'settings' => 'header_slider',
            'label'    => __( 'Pick your slider:', 'textdomain' ),
            'section'  => 'header_image',
            'type'     => 'select',
			'priority'	=> 2,
            'choices'  => get_slider_posts(),
			'active_callback' => 'header_controls',
        )
    );
	
	$wp_customize->add_setting(
        'header_constrain_width',
        array(
			'default'	 => get_theme_mod('header_constrain_width'),
            'capability' => 'edit_theme_options',
            'type'       => 'theme_mod'
        )
    );

    $wp_customize->add_control(
        'header_constrain_width',
        array(
            'settings' => 'header_constrain_width',
            'label'    => __( 'Constrain header width', 'textdomain' ),
			'description' => 'Limits the width of the header image/slider to the width of the main content',
            'section'  => 'header_image',
            'type'     => 'checkbox',
			'priority'	=> 2,
			'std'        => '1',
			'active_callback' => 'header_controls'
        )
    );
	
	$wp_customize->add_setting(
        'header_home_only',
        array(
			'default'	 => get_theme_mod('header_home_only'),
            'capability' => 'edit_theme_options',
            'type'       => 'theme_mod'
        )
    );

    $wp_customize->add_control(
        'header_home_only',
        array(
            'settings' => 'header_home_only',
            'label'    => __( 'Display only on the home page', 'textdomain' ),
            'section'  => 'header_image',
            'type'     => 'checkbox',
			'priority'	=> 3,
			'std'        => '1',
			'active_callback' => 'header_controls'
        )
    );
	
	$wp_customize->get_control( 'header_image' )->active_callback = 'header_controls';
}
endif;

function header_controls($control) {
	$control_id = $control->id;
	$header_type = get_theme_mod('header_type');
	
	if ( $control_id == 'header_slider' && $header_type == 'slider' ) return true;
	if ( $control_id == 'header_image' && $header_type == 'image' ) return true;
	if ( $control_id == 'header_home_only' && ($header_type == 'image' || $header_type == 'slider')) return true;
	if ( $control_id == 'header_constrain_width' && ($header_type == 'image' || $header_type == 'slider')) return true;
		return false;
}

function get_slider_posts() {
	$soliloquy_header = array();
	$args = array('post_type' => 'soliloquy');
    $soliloquy_posts = get_posts($args);
    foreach($soliloquy_posts as $soliloquy_post) {
            $soliloquy_header[$soliloquy_post->ID] = $soliloquy_post->post_title;
    }
	//if (empty($soliloquy_header)) $soliloquy_header[0] = 'Create a new slider.';
	return $soliloquy_header;
}

function sanitize_variation_choices( $values ) {
    $multi_values = !is_array( $values ) ? explode( ',', $values ) : $values;
    return !empty( $multi_values ) ? array_map( 'sanitize_text_field', $multi_values ) : array();
}

/**
 * Get the full path to the the comments template.
 * @param  string  $path  The default path.
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


// Load config and variation's functions.php files.
$vtt_config->load_config();

