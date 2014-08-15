<?php
//========================================================================================
// 
//
// @package WordPress
// @subpackage unc-charlotte-theme
//----------------------------------------------------------------------------------------
// Main setup at bottom of file.
//========================================================================================



// 
// Setup the config information.
//----------------------------------------------------------------------------------------
require_once( get_template_directory().'/classes/config.php' );
$uncc_config = new uncc_config;
$uncc_config->load_config();

// 
// Include variation's functions.php
//----------------------------------------------------------------------------------------
if( file_exists(get_stylesheet_directory().'/variations/'.$uncc_config->get_current_variation().'/functions.php') )
require_once( get_stylesheet_directory().'/variations/'.$uncc_config->get_current_variation().'/functions.php' );

if( file_exists(get_template_directory().'/variations/'.$uncc_config->get_current_variation().'/functions.php') )
require_once( get_template_directory().'/variations/'.$uncc_config->get_current_variation().'/functions.php' );


//========================================================================================
//====================================================== Default filters and actions =====

add_filter( 'show_admin_bar', 'uncc_show_admin_bar', 10 );
add_action( 'admin_bar_menu', 'uncc_setup_admin_bar' );

add_action( 'init', 'uncc_setup_widget_areas' );
add_action( 'init', 'uncc_register_menus' );
add_action( 'after_setup_theme', 'uncc_add_featured_image_support' );
add_action( 'wp_enqueue_scripts', 'uncc_enqueue_scripts', 0 );

add_filter( 'embed_oembed_html', 'uncc_embed_html', 10, 3 );
add_filter( 'video_embed_html', 'uncc_embed_html' );


//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_embed_html') ):
function uncc_embed_html( $html )
{
	return '<div class="video-container">' . $html . '</div>';
}
endif; 

//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_show_admin_bar') ):
function uncc_show_admin_bar( $show_admin_bar )
{
	return true;
}
endif;


//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_register_menus') ):
function uncc_register_menus()
{
	register_nav_menus(
		array(
			'header-navigation' => __( 'Header Menu' ),
		)
	);
}
endif;


//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_setup_admin_bar') ):
function uncc_setup_admin_bar( $wp_admin_bar )
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


//----------------------------------------------------------------------------------------
// Sets up the widget areas.
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_setup_widget_areas') ):
function uncc_setup_widget_areas()
{
	global $uncc_config;
	
	$widgets = array(
		array(
			'id'   => 'uncc-left-sidebar',
			'name' => 'Left Sidebar',
		),
		array(
			'id'   => 'uncc-right-sidebar',
			'name' => 'Right Sidebar',
		),
	);
	
	$widget_area = array();
	$widget_area['before_widget'] = '<div id="%1$s" class="widget %2$s">';
	$widget_area['after_widget'] = '</div>';
	$widget_area['before_title'] = '<h2 class="widget-title">';
	$widget_area['after_title'] = '</h2>';

	//uncc_print($widgets);

	foreach( $widgets as $widget )
	{
		$widget_area['name'] = $widget['name'];
		$widget_area['id'] = $widget['id'];
		register_sidebar( $widget_area );
	}
}
endif;



//----------------------------------------------------------------------------------------
// Enqueue any needed css or javascript files.
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_enqueue_scripts') ):
function uncc_enqueue_scripts()
{
	global $uncc_mobile_support, $uncc_config;
	$name = $uncc_config->get_current_variation();
	$folder = 'variations/'.$name;
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui', '//code.jquery.com/ui/1.11.0/jquery-ui.js' );
	uncc_enqueue_files( 'style', 'main-style', 'style.css' );
	uncc_enqueue_files( 'style', 'main-style-'.$name, $folder.'/style.css' );
	
	if( $uncc_mobile_support->use_mobile_site )
	{
		uncc_enqueue_files( 'style', 'mobile-site', 'styles/mobile-site.css');
		uncc_enqueue_files( 'style', 'mobile-site-'.$name, $folder.'/styles/mobile-site.css');
	}
	else
	{
		uncc_enqueue_files( 'style', 'full-site', 'styles/full-site.css');
		uncc_enqueue_files( 'style', 'full-site-'.$name, $folder.'/styles/full-site.css');
	}
	
	uncc_enqueue_file( 'script', 'uncc_toggle_sidebar', 'scripts/jquery.toggle-sidebars.js' );
}
endif;



//----------------------------------------------------------------------------------------
// Enqueues the theme version of the the file specified.
// 
// @param	$type		string		The type of file to enqueue (script or style).
// @param	$name		string		The name to give te file.
// @param	$filepath	string		The relative path to filename.
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_enqueue_files') ):
function uncc_enqueue_files( $type, $name, $filepath )
{
	if( $type !== 'script' && $type !== 'style' ) return;

	$paths = array();
	
	if( file_exists(get_template_directory().'/'.$filepath) )
		$paths['p'] = get_template_directory_uri().'/'.$filepath;

	if( (is_child_theme()) && (file_exists(get_stylesheet_directory().'/'.$filepath)) )
		$paths['c'] = get_stylesheet_directory_uri().'/'.$filepath;
	
	foreach( $paths as $key => $theme_filepath )
	{	
		if( $theme_filepath !== null )
		{
			call_user_func( 'wp_register_'.$type, $name.'-'.$key, $theme_filepath );
			call_user_func( 'wp_enqueue_'.$type, $name.'-'.$key );
		}
	}
}
endif;



//----------------------------------------------------------------------------------------
// Enqueues the theme version of the the file specified.
// 
// @param	$type		string		The type of file to enqueue (script or style).
// @param	$name		string		The name to give te file.
// @param	$filepath	string		The relative path to filename.
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_enqueue_file') ):
function uncc_enqueue_file( $type, $name, $filepath )
{
	if( $type !== 'script' && $type !== 'style' ) return;
	
	$theme_filepath = uncc_get_theme_file_url($filepath);
	
	if( $theme_filepath !== null )
	{
		call_user_func( 'wp_register_'.$type, $name, $theme_filepath );
		call_user_func( 'wp_enqueue_'.$type, $name );
	}
}
endif;



//----------------------------------------------------------------------------------------
// Adds support for featured images.
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_add_featured_image_support') ):
function uncc_add_featured_image_support()
{
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-header', array( 'width' => 950, 'random-default' => true ) );
	
	if( (is_child_theme()) && (file_exists(get_stylesheet_directory().'/images/headers/full')) )
	{
		$headers_fullpath = get_stylesheet_directory().'/images/headers/full';
		$headers_thumbpath = get_stylesheet_directory().'/images/headers/thumbnail';
	}
	elseif( (file_exists(get_template_directory().'/images/headers/full')) )
	{
		$headers_fullpath = get_template_directory().'/images/headers/full';
		$headers_thumbpath = get_template_directory().'/images/headers/thumbnail';
	}
	else
	{
		return;
	}
	
	$images = array();
	$files = scandir( $headers_fullpath );
	foreach( $files as $file )
	{
		if( $file[0] == '.' ) continue;
		if( is_dir($headers_fullpath.'/'.$file) ) continue;
		if( !file_exists($headers_thumbpath.'/'.$file) ) continue;
		
		list( $dirname, $filename, $extension, $basename ) = array_values( pathinfo($headers_fullpath.'/'.$file) );
		$images[$basename]['url'] = '%s/images/headers/full/'.$filename;
		$images[$basename]['thumbnail_url'] = '%s/images/headers/thumbnail/'.$filename;
		$images[$basename]['description'] = $filename;
	}
	
	register_default_headers( $images );	
}
endif;



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_get_header_image') ):
function uncc_get_header_image()
{
	$header_url = get_custom_header()->url;
	if( !$header_url ) $header_url = get_random_header_image();

	if( !$header_url )
	{
		$header_url = '';
		$header_width = 0;
		$header_height = 0;
	}
	else
	{
		$header_path = uncc_url_to_path($header_url);
		$header_url = uncc_path_to_url($header_path);
		list( $header_width, $header_height ) = getimagesize( $header_path );
	}
	
	return array(
		'url' => $header_url,
		'width' => $header_width,
		'height' => $header_height,
	);
}
endif;


//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_url_to_path') ):
function uncc_url_to_path( $url )
{
	if( is_child_theme() )
	{
		if( strpos($url, get_stylesheet_directory_uri()) !== false )
		{
			$path = str_replace( get_stylesheet_directory_uri(), get_stylesheet_directory(), $url );
			if( file_exists($path) ) return $path;

			$path = str_replace( get_stylesheet_directory_uri(), get_template_directory(), $url );
			if( file_exists($path) ) return $path;
			
			return '';
		}

		if( strpos($url, get_template_directory_uri()) !== false )
		{
			$path = str_replace( get_template_directory_uri(), get_stylesheet_directory(), $url );
			if( file_exists($path) ) return $path;

			$path = str_replace( get_template_directory_uri(), get_template_directory(), $url );
			if( file_exists($path) ) return $path;
			
			return '';
		}
	}
	else
	{
		if( strpos($url, get_template_directory_uri()) !== false )
		{
			$path = str_replace( get_template_directory_uri(), get_template_directory(), $url );
			if( file_exists($path) ) return $path;
			
			return '';
		}
	}

	$upload = wp_upload_dir();
	if( strpos($url, $upload['baseurl']) !== false )
	{
		$path = str_replace( $upload['baseurl'], $upload['basedir'], $url );
		if( file_exists($path) ) return $path;
		
		return '';
	}
	
	return '';
}
endif;


//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_path_to_url') ):
function uncc_path_to_url( $path )
{
	if( !file_exists($path) ) return '';

	if( strpos($path, get_stylesheet_directory()) !== false )
	{
		return str_replace( get_stylesheet_directory(), get_stylesheet_directory_uri(), $path );
	}

	if( strpos($path, get_template_directory()) !== false )
	{
		return str_replace( get_template_directory(), get_template_directory_uri(), $path );
	}

	$upload = wp_upload_dir();
	if( strpos($path, $upload['basedir']) !== false )
	{
		return str_replace( $upload['basedir'], $upload['baseurl'], $path );
	}
	
	return '';
}
endif;


//----------------------------------------------------------------------------------------
// Clears the log file.
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_clear_log') ):
function uncc_clear_log()
{
	global $uncc_logfile;
	file_put_contents( $uncc_logfile, '' );
}
endif;



//----------------------------------------------------------------------------------------
// Writes a line into the log file.
// 
// @param	$line		string		A line of text to write into a file.
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_write_to_log') ):
function uncc_write_to_log( $line )
{
	global $uncc_logfile;
	file_put_contents( $uncc_logfile, print_r($line, true)."\n", FILE_APPEND );
}
endif;



//----------------------------------------------------------------------------------------
// Writes an object to the page with <pre> tags.
// 
// @param	$var		mixed		An object to var_dump.
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_print') ):
function uncc_print( $var, $label = '' )
{
	echo '<pre style="display:block; clear:both;">';
	if( $label !== '' ) echo $label.": \n";
	var_dump($var);
	echo '</pre>';
}
endif;



//----------------------------------------------------------------------------------------
// Retreives the absolute path to a file within the theme.
// 
// @param	$filepath	string		The relative path within the theme to the file.
// @return				string|null	The absolute path to the file in the theme.
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_get_theme_file_path') ):
function uncc_get_theme_file_path( $filepath, $search_type = 'both', $return_null = true )
{
	global $uncc_config;
	
	if( (strlen($filepath) > 0) && ($filepath[0] === '/') ) $filepath = substr( $filepath, 1 );
	
	if( $search_type === 'both' || $search_type === 'variation' ):
	
	if( file_exists(get_stylesheet_directory().'/variations/'.$uncc_config->get_current_variation().'/'.$filepath) )
		return get_stylesheet_directory().'/variations/'.$uncc_config->get_current_variation().'/'.$filepath;
	
	if( file_exists(get_template_directory().'/variations/'.$uncc_config->get_current_variation().'/'.$filepath) )
		return get_template_directory().'/variations/'.$uncc_config->get_current_variation().'/'.$filepath;
	
	endif;
	
	if( $search_type === 'both' || $search_type === 'theme' ):
	
	if( file_exists(get_stylesheet_directory().'/'.$filepath) )
		return get_stylesheet_directory().'/'.$filepath;
	
	if( file_exists(get_template_directory().'/'.$filepath) )
		return get_template_directory().'/'.$filepath;

	endif;
		
	if( $return_null ) return null;
	return '';
}
endif;



//----------------------------------------------------------------------------------------
// Retreives the absolute url to a file within the theme.
// 
// @param	$filepath	string		The relative path within the theme to the file.
// @return				string|null	The absolute path to the file in the theme.
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_get_theme_file_url') ):
function uncc_get_theme_file_url( $filepath, $search_type = 'both', $return_null = true )
{
	global $uncc_config;
	
	if( (strlen($filepath) > 0) && ($filepath[0] === '/') ) $filepath = substr( $filepath, 1 );
	
	if( $search_type === 'both' || $search_type === 'variation' ):
	
	if( file_exists(get_stylesheet_directory().'/'.$uncc_config->get_current_variation().'/'.$filepath) )
		return get_stylesheet_directory_uri().'/'.$uncc_config->get_current_variation().'/'.$filepath;
	
	if( file_exists(get_template_directory().'/'.$uncc_config->get_current_variation().'/'.$filepath) )
		return get_template_directory_uri().'/'.$uncc_config->get_current_variation().'/'.$filepath;

	endif;
	
	if( $search_type === 'both' || $search_type === 'theme' ):
	
	if( file_exists(get_stylesheet_directory().'/'.$filepath) )
		return get_stylesheet_directory_uri().'/'.$filepath;
	
	if( file_exists(get_template_directory().'/'.$filepath) )
		return get_template_directory_uri().'/'.$filepath;

	endif;
		
	if( $return_null ) return null;
	return '';
}
endif;



//----------------------------------------------------------------------------------------
// 
// 
// @param	$filepath	string		The relative path within the theme to the file.
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_include_files') ):
function uncc_include_files( $filepath )
{
	if( is_child_theme() && file_exists(get_stylesheet_directory().'/'.$filepath) )
		include_once( get_stylesheet_directory().'/'.$filepath );
	
	if( file_exists(get_template_directory().'/'.$filepath) )
		include_once( get_template_directory().'/'.$filepath );
}
endif;



//----------------------------------------------------------------------------------------
// Find, then includes the template part.
// TODO: alter this!!
// 
// @param	$name		string		The name of the template part.
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_get_template_part') ):
function uncc_get_template_part( $name, $folder = '', $key = '' )
{
	global $uncc_config;
	if( $folder ) $folder = 'templates/'.$folder.'/'; else $folder = 'templates/';
	
	$filepath = null;
	if( $key )
		$filepath = uncc_get_theme_file_path( $folder.$name.'-'.$key.'.php' );
	
	if( $filepath === null )
		$filepath = uncc_get_theme_file_path( $folder.$name.'.php' );
	
	if( $filepath !== null )
	{
		include( $filepath );
		return true;
	}
	
	return false;
}
endif;



//----------------------------------------------------------------------------------------
// Retreives a tag object based on the slug.
//
// @param	$slug		string		The slug/name of the tag.
// @return				mixed		Term Row (array) or false if not found.
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_get_tag_by_slug') ):
function uncc_get_tag_by_slug( $slug )
{
	return get_term_by( 'slug', $slug, 'post_tag' );
}
endif;



//----------------------------------------------------------------------------------------
// Creates the HTML for the an anchor.  If contents are provided, then the anchor will
// wrap the contents, else only the beginning anchor tag will be returned.
// 
// @param	$url		string		The url of the anchor.
// @param	$title		string		The title for the anchor.
// @param	$class		string|null	The class for the anchor, if any.
// @param	$contents	string|null	The contents wrapped by the anchor.
// @return				string		The created anchor tag.
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_get_anchor') ):
function uncc_get_anchor( $url, $title, $class = null, $contents = null )
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



//----------------------------------------------------------------------------------------
// Gets the current datetime for the current timezone.
//
// @return				DateTime	The current datetime.
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_get_current_datetime') ):
function uncc_get_current_datetime()
{
	global $uncc_config;
	$timezone = $uncc_config->get_timezone();
	date_default_timezone_set($timezone);
	return ( new Datetime() );
}
endif;



//----------------------------------------------------------------------------------------
// TODO...
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_use_widget') ):
function uncc_use_widget( $part, $placement )
{
	global $uncc_config;
	
	if( !function_exists('dynamic_sidebar') ) return;
	
	if( $uncc_config->use_widget($part, $placement) ) dynamic_sidebar( $part.'-'.$placement );
}
endif;



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_image') ):
function uncc_image( $image_info, $echo = true )
{
	global $uncc_mobile_support;

	if( empty($image_info) ) return;
	
	if( !isset($image_info['selection-type']) ) $image_info['selection-type'] = 'relative';
	if( !isset($image_info['use-site-link']) ) $image_info['use-site-link'] = false;
	
	switch( $image_info['selection-type'] )
	{
		case 'relative':
			$image_info['path'] = uncc_get_theme_file_url( $image_info['path'] );
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
		$html = uncc_get_anchor( $image_info['link'], $image_info['title'], $image_info['class'], $html );

	if( $echo ) echo $html;
	else return $html;
}
endif;



//----------------------------------------------------------------------------------------
// Retreives an image's url.
// 
// @param	$path		string		The absolute or relative path to the image.
// @return				string|null	The absolute url to the image.
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_get_image_url') ):
function uncc_get_image_url( $path )
{
	global $uncc_mobile_support;
	
	if( is_array($path) ) $path = $path['url'];
	
	$url = '';
	if( $uncc_mobile_support->use_mobile_site )
	{
		$pathinfo = pathinfo( $path );
		$url = uncc_get_theme_file_url( $pathinfo['dirname'].'/'.$pathinfo['filename'].'-mobile.'.$pathinfo['extension'] );
	}
	
	if( $url ) return $url;
	return uncc_get_theme_file_url($path);
}
endif;



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_get_image_info') ):
function uncc_get_image_info( $image_info )
{
	global $uncc_mobile_support;
	
	if( !$image_info ) return $image_info;
	
	$image_info['height'] = 'auto';
	$image_info['width'] = 'auto';

	$pathinfo = pathinfo( $image_info['path'] );
	if( !$pathinfo ) return $image_info;	
	
	uncc_print( $pathinfo, 'pathinfo' );
	
	$full_path = ''; $path = ''; $url = '';
	if( $uncc_mobile_support->use_mobile_site )
	{
		$path = $pathinfo['dirname'].'/'.$pathinfo['filename'].'-mobile.'.$pathinfo['extension'];
		$full_path = uncc_get_theme_file_path( $path );
		
		if( $path !== null ) 
			$url = uncc_get_theme_file_url( $path );
	}

	if( !$url )
	{
		$path = $pathinfo['dirname'].'/'.$pathinfo['filename'].'.'.$pathinfo['extension'];
		$full_path = uncc_get_theme_file_path( $path );

		if( $path !== null ) 
			$url = uncc_get_theme_file_url( $path );
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



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_str_starts_with') ):
function uncc_str_starts_with($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}
endif;



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_str_ends_with') ):
function uncc_str_ends_with($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}
endif;



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_get_section') ):
function uncc_get_section( $wpquery = null )
{
	global $wp_query, $uncc_config;

	if( $wpquery === null ) $wpquery = $wp_query;
	if( $wpquery->get('section') ) return $wpquery->get('section');
	
	$qo = $wpquery->get_queried_object();
	
	if( $wpquery->is_archive() )
	{
		if( $wpquery->is_tax() || $wpquery->is_tag() || $wpquery->is_category() )
		{
			$section = $uncc_config->get_section( null, array( $qo->taxonomy => $qo->slug ), false );
			$wpquery->set( 'section', $section );
			return $section;
		}
		elseif( $wpquery->is_post_type_archive() )
		{
			$section = $uncc_config->get_section( $qo->name, null, false );
			$wpquery->set( 'section', $section );
			return $section;
		}

		return $uncc_config->get_default_section();
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
			$taxonomies = uncc_get_taxonomies( $post_id );
			$section = $uncc_config->get_section( $post_type, $taxonomies, false, array('news') );
		}
		else
		{
			$section = $uncc_config->get_default_section();
		}
		
		$wpquery->set( 'section', $section );
		return $section;
	}
	
	return $uncc_config->get_default_section();
}
endif;

//----------------------------------------------------------------------------------------
// Need to add support for displaying multiple authors...
//----------------------------------------------------------------------------------------
if( !function_exists( 'uncc_get_byline' ) ) :
function uncc_get_byline( $post )
{
	$date = date( 'F d, Y', strtotime($post->post_modified) );
	
	$author = get_the_author_meta( 'display_name', $post->post_author );
	$url = get_author_posts_url($post->post_author);
	
//	return $date.' by '.$author;
	return $date.' by <a href="'.$url.'" title="Posts by '.$author.'">'.$author.'</a>';
}
endif;


if( !function_exists( 'uncc_get_breadcrumbs' ) ):
function uncc_get_breadcrumbs( $post )
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


//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists( 'uncc_get_taxonomy_list' ) ):
function uncc_get_taxonomy_list( $taxonomy_name, $post )
{
	$taxonomy = get_taxonomy( $taxonomy_name );
	if( !$taxonomy ) return '';

	$terms = wp_get_post_terms( $post->ID, $taxonomy_name );

	$html = '';
	$html .= '<div class="taxonomy-list '.$taxonomy->name.'-list">';	

	$html .= $taxonomy->label.': ';

	if( count($terms) > 0 )
	{
		$list = array();
		foreach( $terms as $t )
		{
			$list[] = uncc_get_anchor( get_term_link($t->term_id, $taxonomy_name), $t->name, $t->slug, $t->name );
		}
		$html .= implode( ', ', $list );
	}
	else
	{
		$html .= '-';
	}
	
	$html .= '</div>';
	
// 	echo '<pre>'; var_dump($html); echo '</pre>';
	return $html;
}
endif;


//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_get_categories') ):
function uncc_get_categories( $categories = null )
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



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_get_tags') ):
function uncc_get_tags( $tags = null )
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



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_get_taxonomies') ):
function uncc_get_taxonomies( $post_id = -1 )
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



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_get_page_url') ):
function uncc_get_page_url()
{
	$page_url = 'http';
	if( isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on') ) $page_url .= 's';
	$page_url .= '://';
	if( $_SERVER['SERVER_PORT'] != '80' )
		$page_url .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
	else
		$page_url .= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	return $page_url;
}
endif;


//========================================================================================
//======================================================================= Main Setup =====

// 
// Set the log's file path.
//----------------------------------------------------------------------------------------
$uncc_logfile = dirname(__FILE__).'/uncc.log';

// 
// Set blog name.
//----------------------------------------------------------------------------------------
define( 'UNCC_BLOG_NAME', trim( preg_replace("/[^A-Za-z0-9 ]/", '-', get_blog_details()->path), '-' ) );

// 
// Add the image sizes for thumbnails.
//----------------------------------------------------------------------------------------
add_image_size( 'thumbnail_portrait', 120 );
add_image_size( 'thumbnail_landscape', 324 );

// 
// Setup mobile support.
//----------------------------------------------------------------------------------------
require_once( get_template_directory().'/classes/mobile-support.php' );
$uncc_mobile_support = new Mobile_Support;

//
// Include the admin backend. 
//----------------------------------------------------------------------------------------
if( is_admin() ):

require_once( get_template_directory().'/admin/main.php' );
if( (is_child_theme()) && (file_exists(get_stylesheet_directory().'/admin/main.php')) ) 
	require_once( get_stylesheet_directory().'/admin/main.php' );

$filepath = uncc_get_theme_file_path( '/admin/main.php', 'variation' );
if( $filepath ) require_once( $filepath );

endif;

