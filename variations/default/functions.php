<?php

/**
 * The functions for the Light (default) variation for Variations Template Theme.
 * 
 * @package    variations-template-theme
 * @author     Crystal Barton <cbarto11@uncc.edu>
 */


// Setup theme customizer controls
if( is_customize_preview() ):
	require_once( __DIR__.'/classes/customizer/header-position/control.php' );
endif;

// Config setup.
add_filter( 'vtt-options', 'vtt_default_default_options' );

// Theme setup.
add_action( 'after_setup_theme', 'vtt_default_theme_setup', 5 );
add_action( 'init', 'vtt_default_register_menus' );
add_action( 'init', 'vtt_default_setup_widget_areas' );

// Always show the admin bar.
add_filter( 'show_admin_bar', 'vtt_default_show_admin_bar', 10 );

// Theme customizer.
add_action( 'customize_register', 'vtt_default_customize_register', 11 );
add_filter( 'theme_mod_blogname', 'vtt_default_customize_theme_mod_blogname', 99 );
add_filter( 'theme_mod_blogname_url', 'vtt_default_customize_theme_mod_blogname_url', 99 );
add_filter( 'theme_mod_blogdescription', 'vtt_default_customize_theme_mod_blogdescription', 99 );
add_filter( 'theme_mod_blogdescription_url', 'vtt_default_customize_theme_mod_blogdescription_url', 99 );
add_filter( 'pre_set_theme_mod_blogname', 'vtt_default_customize_set_theme_mod_blogname', 99, 2 );
add_filter( 'pre_set_theme_mod_blogname_url', 'vtt_default_customize_set_theme_mod_blogname_url', 99, 2 );
add_filter( 'pre_set_theme_mod_blogdescription', 'vtt_default_customize_set_theme_mod_blogdescription', 99, 2 );
add_filter( 'pre_set_theme_mod_blogdescription_url', 'vtt_default_customize_set_theme_mod_blogdescription_url', 99, 2 );

// Post Content
add_filter( 'the_content_more_link', 'vtt_default_read_more_link' );

// Add Home to Pages menu.
add_filter( 'wp_page_menu_args', 'vtt_default_add_home_pages_menu_item' );


/**
 * Sets the default options for $vtt_config.
 * @return  Array  The default options.
 */
if( !function_exists('vtt_default_default_options') ):
function vtt_default_default_options( $options )
{
	return array(
		'featured-image-position' => 'left',
		'header-title-position'   => 'hleft vcenter',
		'header-title-hide'       => false,
		'blogname'                => '/',
		'blogname_url'            => '/',
		'blogdescription'         => '/',
		'blogdescription_url'     => '/',
	);
}
endif;


/**
 * Setup the theme support for featured images and custom background.
 */
if( !function_exists('vtt_default_theme_setup') ):
function vtt_default_theme_setup()
{
	global $vtt_mobile_support, $vtt_config;
	
	// add theme support.
	vtt_default_add_featured_image_support();
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-background' );
	
	// add editor styles.
	add_editor_style( 'editor-style.css' );
}
endif; 


/**
 * Always show admin bar.
 * @param  bool  True to show admin bar.
 */
if( !function_exists('vtt_default_show_admin_bar') ):
function vtt_default_show_admin_bar( $show_admin_bar )
{
	return true;
}
endif;


/**
 * Adds support for featured images.
 * Additionally, default headers and added from all the variation directories.
 * As each directory is searched, each full-size header is located in {directory}/images/headers/full 
 * and thumbnails are found in {directory}/images/headers/thubnail.
 */
if( !function_exists('vtt_default_add_featured_image_support') ):
function vtt_default_add_featured_image_support()
{
	global $vtt_config;
	
	add_theme_support( 'custom-header',
		array( 
			'width' 					=> 950, 
			'height'					=> 200,
			'flex-width'				=> false,
			'flex-height'				=> true,
			'random-default' 			=> true,
			'admin-head-callback' 		=> 'vtt_default_admin_head_callback',
			'admin-preview-callback' 	=> 'vtt_default_admin_preview_callback',
			'header-text'				=> true,
			'default-text-color'		=> '',
			'default-text-bgcolor'		=> '',
		)
	);
	
	$all_directories = $vtt_config->get_all_directories( false );
	
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
 * Show the theme header with theme customizer options.
 */
if( !function_exists('vtt_default_admin_preview_callback') ):
function vtt_default_admin_preview_callback()
{
	vtt_get_template_part( 'header', 'part' );
}
endif;


/**
 * Load the theme header styles for the theme customizer.
 */
if( !function_exists('vtt_default_admin_head_callback') ):
function vtt_default_admin_head_callback()
{
	vtt_enqueue_files( 'style', 'header-style', 'styles/admin-header.css' );
}
endif;


/**
 * Add the header menu.
 */
if( !function_exists('vtt_default_register_menus') ):
function vtt_default_register_menus()
{
	register_nav_menus(
		array(
			'header-navigation' => __( 'Header Menu' ),
		)
	);
}
endif;


/**
 * Sets up the widget areas.
 */
if( !function_exists('vtt_default_setup_widget_areas') ):
function vtt_default_setup_widget_areas()
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
 * 
 * @param  WP_Customize_Manager  $wp_customize  Theme Customizer API controller.
 */
if( !function_exists('vtt_default_customize_register') ):
function vtt_default_customize_register( $wp_customize )
{
	global $vtt_config;

	// Header Title section
	$wp_customize->add_section(
		'vtt-header-title-section',
		array(
			'title'      => 'Header Title',
			'priority'   => 0,
		)
	);

	$wp_customize->add_setting(
		'header-title-hide',
		array(
			'default'     => $vtt_config->get_value( 'header-title-hide' ),
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control( 
			$wp_customize, 
			'vtt-header-title-hide-control', 
			array(
				'label'      => 'Hide header title',
				'section'    => 'vtt-header-title-section',
				'settings'   => 'header-title-hide',
				'type'       => 'checkbox',
			)
		)
	);
	
	// Header Title Position
	$wp_customize->add_setting(
		'header-title-position',
		array(
			'default'     => $vtt_config->get_value( 'header-title-position' ),
			'transport'   => 'refresh',
		)
	);

	$wp_customize->add_control( 
		new VTT_Customize_Header_Position( 
			$wp_customize, 
			'vtt-header-title-position-control', 
			array(
				'label'      => 'Position',
				'section'    => 'vtt-header-title-section',
				'settings'   => 'header-title-position',
			)
		)
	);
	
	// Featured Image section
	$wp_customize->add_section(
		'vtt-featured-image-section',
		array(
			'title'      => 'Featured Image',
			'priority'   => 0,
		)
	);
	
	// Featured Image Position select
	$wp_customize->add_setting(
		'featured-image-position',
		array(
			'default'     => $vtt_config->get_value( 'featured-image-position' ),
			'transport'   => 'refresh',
		)
	);
	
	$wp_customize->add_control( 
		new WP_Customize_Control( 
			$wp_customize, 
			'vtt-featured-image-position-control', 
			array(
				'label'      => 'Position',
				'section'    => 'vtt-featured-image-section',
				'settings'   => 'featured-image-position',
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
	
	// Site Title & Tagline
	$wp_customize->remove_setting( 'blogname' );
	$wp_customize->remove_control( 'blogname' );
	$wp_customize->remove_setting( 'blogdescription' );
	$wp_customize->remove_control( 'blogdescription' );
	
	$name = $vtt_config->get_value( 'blogname' );
	$url = $vtt_config->get_value( 'blogname_url' );
	if( $name == '/' ) $name = get_bloginfo('name');
	if( $url == '/' ) $url = get_home_url();
	
	$wp_customize->add_setting(
		'blogname',
		array(
			'default'		=> $name,
		)
	);

	$wp_customize->add_control( 
		new WP_Customize_Control( 
			$wp_customize, 
			'blogname-control',
			array(
				'label'			=> 'Site Title',
				'section'		=> 'title_tagline',
				'settings'		=> 'blogname',
			)
		)
	);
	
	$wp_customize->add_setting(
		'blogname_url',
		array(
			'default'		=> $url,
		)
	);

	$wp_customize->add_control( 
		new WP_Customize_Control( 
			$wp_customize, 
			'blogname_url-control',
			array(
				'label'			=> 'Site Title URL',
				'section'		=> 'title_tagline',
				'settings'		=> 'blogname_url',
			)
		)
	);

	$name = $vtt_config->get_value( 'blogdescription' );
	$url = $vtt_config->get_value( 'blogdescription_url' );
	if( $name == '/' ) $name = get_bloginfo('description');
	if( $url == '/' ) $url = get_home_url();
	
	$wp_customize->add_setting(
		'blogdescription',
		array(
			'default'		=> $name,
		)
	);

	$wp_customize->add_control( 
		new WP_Customize_Control( 
			$wp_customize, 
			'blogdescription-control',
			array(
				'label'			=> 'Site Description',
				'section'		=> 'title_tagline',
				'settings'		=> 'blogdescription',
			)
		)
	);
	
	$wp_customize->add_setting(
		'blogdescription_url',
		array(
			'default'		=> $url,
		)
	);

	$wp_customize->add_control( 
		new WP_Customize_Control( 
			$wp_customize, 
			'blogdescription_url-control',
			array(
				'label'			=> 'Site Description URL',
				'section'		=> 'title_tagline',
				'settings'		=> 'blogdescription_url',
			)
		)
	);


	// Header Title Box Text Color
	$wp_customize->add_setting(
		'header_textcolor',
		array(
			'theme_supports'		=> array( 'custom-header', 'header-text' ),
			'default'				=> get_theme_support( 'custom-header', 'default-text-color' ),
			'sanitize_callback'		=> array( $wp_customize, '_sanitize_header_textcolor' ),
			'sanitize_js_callback'	=> 'maybe_hash_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control( 
			$wp_customize, 
			'header_textcolor', 
			array(
				'label'				=> 'Header Text Color',
				'section'			=> 'colors',
			)
		)
	);

	// Header Title Box Text Background Color
	$wp_customize->add_setting(
		'header_textbgcolor', 
		array(
			'theme_supports'		=> array( 'custom-header', 'header-text' ),
			'default'				=> get_theme_support( 'custom-header', 'default-text-bgcolor' ),
			'sanitize_callback'		=> 'vtt_default_sanitize_header_textbgcolor',
			'sanitize_js_callback'	=> 'maybe_hash_hex_color',
		)
	);

	$wp_customize->add_control(
		new Pluto_Customize_Alpha_Color_Control(
			$wp_customize,
			'header_textbgcolor',
			array(
				'label'		=> 'Header Text Background Color',
				'palette'	=> true,
				'section'	=> 'colors'
			)
		)
	);

	// Remote Display Header Text checkbox
	$wp_customize->remove_control( 'display_header_text' );
}
endif;


/**
 * Sanitizes and verifies the text bg color from the theme customizer.
 * @param  string  $color  The color value from the header text bg color theme customizer control.
 */
if( !function_exists('vtt_default_sanitize_header_textbgcolor') ):
function vtt_default_sanitize_header_textbgcolor( $color )
{
	if ( 'blank' === $color )
		return 'blank';
	
	if( strpos($color, 'rgb') !== false )
		return $color;
	
	$color = sanitize_hex_color_no_hash( $color );
	if ( empty( $color ) )
		$color = get_theme_support( 'custom-header', 'default-text-bgcolor' );
	
	return $color;
}
endif;


/**
 * Sanitizes and verifies the blog name from the theme customizer.
 * @param  string  $value  The value from the blog name theme customizer control.
 */
if( !function_exists('vtt_default_customize_theme_mod_blogname') ):
function vtt_default_customize_theme_mod_blogname( $value )
{
	if( $value == '/' ) return get_bloginfo('name');
	return $value;
}
endif;


/**
 * Sanitizes and verifies the blog name url from the theme customizer.
 * @param  string  $value  The value from the blog name url theme customizer control.
 */
if( !function_exists('vtt_default_customize_theme_mod_blogname_url') ):
function vtt_default_customize_theme_mod_blogname_url( $value )
{
	if( $value == '/' ) return get_site_url();
	return $value;
}
endif;


/**
 * Sanitizes and verifies the blog description from the theme customizer.
 * @param  string  $value  The value from the blog description theme customizer control.
 */
if( !function_exists('vtt_default_customize_theme_mod_blogdescription') ):
function vtt_default_customize_theme_mod_blogdescription( $value )
{
	if( $value == '/' ) return get_bloginfo('description');
	return $value;
}
endif;


/**
 * Sanitizes and verifies the blog description url from the theme customizer.
 * @param  string  $value  The value from the blog description url theme customizer control.
 */
if( !function_exists('vtt_default_customize_theme_mod_blogdescription_url') ):
function vtt_default_customize_theme_mod_blogdescription_url( $value )
{
	if( $value == '/' ) return get_site_url();
	return $value;
}
endif;


/**
 * Sanitizes and verifies the blog name for saving.
 * @param  string  $new_value  The new value of the blog name.
 * @param  string  $old_value  The old value of the blog name.
 */
if( !function_exists('vtt_default_customize_set_theme_mod_blogname') ):
function vtt_default_customize_set_theme_mod_blogname( $new_value, $old_value )
{
	if( $new_value == get_bloginfo('name') ) $new_value = '/';
	return $new_value;
}
endif;


/**
 * Sanitizes and verifies the blog name url for saving.
 * @param  string  $new_value  The new value of the blog name url.
 * @param  string  $old_value  The old value of the blog name url.
 */
if( !function_exists('vtt_default_customize_set_theme_mod_blogname_url') ):
function vtt_default_customize_set_theme_mod_blogname_url( $new_value, $old_value )
{
	if( $new_value == get_site_url() ) $new_value = '/';
	return $new_value;
}
endif;


/**
 * Sanitizes and verifies the blog description for saving.
 * @param  string  $new_value  The new value of the blog description.
 * @param  string  $old_value  The old value of the blog description.
 */
if( !function_exists('vtt_default_customize_set_theme_mod_blogdescription') ):
function vtt_default_customize_set_theme_mod_blogdescription( $new_value, $old_value )
{
	if( $new_value == get_bloginfo('description') ) $new_value = '/';
	return $new_value;
}
endif;


/**
 * Sanitizes and verifies the blog description url for saving.
 * @param  string  $new_value  The new value of the blog description url.
 * @param  string  $old_value  The old value of the blog description url.
 */
if( !function_exists('vtt_default_customize_set_theme_mod_blogdescription_url') ):
function vtt_default_customize_set_theme_mod_blogdescription_url( $new_value, $old_value )
{
	if( $new_value == get_site_url() ) $new_value = '/';
	return $new_value;
}
endif;


/**
 * Sanitize and verifies the theme mod options.
 * @param  string  $new_value  The new value of the theme mods.
 * @param  string  $old_value  The old value of the theme mods.
 */
if( !function_exists('vtt_customize_pre_update_options') ):
function vtt_customize_pre_update_options( $new_value, $old_value )
{
	global $wp_customize;
	if( isset($wp_customize) ) return;

	if( !array_key_exists('theme-mods', $new_value) ) return;

	if( (array_key_exists('blogname', $new_value['theme-mods'])) &&
	    ($new_value['theme-mods']['blogname'] == get_bloginfo('name')) )
	{
		$new_value['theme-mods']['blogname'] = '/';
	}

	if( (array_key_exists('blogname_url', $new_value['theme-mods'])) &&
	    ($new_value['theme-mods']['blogname_url'] == get_site_url()) )
	{
		$new_value['theme-mods']['blogname_url'] = '/';
	}

	if( (array_key_exists('blogdescription', $new_value['theme-mods'])) &&
	    ($new_value['theme-mods']['blogdescription'] == get_bloginfo('description')) )
	{
		$new_value['theme-mods']['blogdescription'] = '/';
	}

	if( (array_key_exists('blogdescription_url', $new_value['theme-mods'])) &&
	    ($new_value['theme-mods']['blogdescription_url'] == get_site_url()) )
	{
		$new_value['theme-mods']['blogdescription_url'] = '/';
	}
	
	return $new_value;
}
endif;


/**
 * Add a custom read more link.
 * @return  string  The custom html for the read more link.
 */
if( !function_exists('vtt_default_read_more_link') ):
function vtt_default_read_more_link() {
	return '<a class="more-link" href="' . get_permalink() . '">Read more...</a>';
}
endif;


/**
 * For pages menu, the home page should be shown by default.
 * @param  Array  $args  The current args for pages.
 * @return  Array  The modified args.
 */
if( !function_exists('vtt_default_add_home_pages_menu_item') ):
function vtt_default_add_home_pages_menu_item( $args )
{
	$args['show_home'] = true;
	return $args;
}
endif;

