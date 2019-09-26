<?php

/**
 * The functions for the Light (default) variation for Variations Template Theme.
 *
 * @package    variations-template-theme
 * @author     Crystal Barton <atrus1701@gmail.com>
 */


if ( ! defined( 'VTT_DEFAULT_DB_VERSION' ) ) :

	/**
	 * The current version of the db settings for the default variation.
	 *
	 * @var  String
	 */
	define( 'VTT_DEFAULT_DB_VERSION', '1.1' );

endif;

// Setup theme customizer controls
if ( is_customize_preview() ) :
	require_once __DIR__ . '/classes/customizer/header-position/control.php';
endif;

// Config setup.
add_action( 'vtt-update-db', 'vtt_default_update_db' );
add_filter( 'vtt-options', 'vtt_default_default_options' );

// Theme setup.
add_action( 'after_setup_theme', 'vtt_default_theme_setup', 5 );
add_action( 'init', 'vtt_default_register_menus' );
add_action( 'init', 'vtt_default_setup_widget_areas' );

// Admin Bar
add_filter( 'show_admin_bar', 'vtt_default_show_admin_bar', 10 );
add_action( 'admin_bar_menu', 'vtt_default_add_responsive_close_button', 1 );
add_action( 'wp_before_admin_bar_render', 'vtt_shorten_admin_bar_title' );

// Theme customizer.
add_action( 'customize_register', 'vtt_default_customize_register', 11 );

// Post Content
add_filter( 'the_content_more_link', 'vtt_default_read_more_link' );

// Add Home to Pages menu.
add_filter( 'wp_page_menu_args', 'vtt_default_add_home_pages_menu_item' );


/**
 * Check if database needs to updated.
 */
if ( ! function_exists( 'vtt_default_update_db' ) ) :
	function vtt_default_update_db() {
		$current_db_version = get_theme_mod( 'vtt-default-db-version' );
		if ( ! $current_db_version ) { $current_db_version = '1.0';
		}
		if ( $current_db_version === VTT_DEFAULT_DB_VERSION ) { return;
		}

		switch ( $current_db_version ) {
			case '1.0':
				$blogname_url = get_theme_mod( 'blogname_url' );
				if ( $blogname_url === '/' ) {
					set_theme_mod( 'blogname_url_default', true );
					set_theme_mod( 'blogname_url', '' );
				}

				$blogdescription_url = get_theme_mod( 'blogdescription_url' );
				if ( $blogdescription_url === '/' ) {
					set_theme_mod( 'blogdescription_url_default', true );
					set_theme_mod( 'blogdescription_url', '' );
				}
				break;
		}

		set_theme_mod( 'vtt-default-db-version', VTT_DEFAULT_DB_VERSION );
	}
endif;


/**
 * Sets the default options for $vtt_config.
 *
 * @return  Array  The default options.
 */
if ( ! function_exists( 'vtt_default_default_options' ) ) :
	function vtt_default_default_options( $options ) {
		return array(
			'featured-image-position'     => 'left',
			'header-title-position'       => 'hleft vcenter',
			'header-title-hide'           => false,
			'blogname_url'                => '',
			'blogname_url_default'        => true,
			'blogdescription_url'         => '',
			'blogdescription_url_default' => false,
		);
	}
endif;


/**
 * Setup the theme support for featured images and custom background.
 */
if ( ! function_exists( 'vtt_default_theme_setup' ) ) :
	function vtt_default_theme_setup() {
		global $vtt_mobile_support, $vtt_config;

		// add theme support.
		vtt_default_add_featured_image_support();
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'custom-background' );
	}
endif;


/**
 * Always show admin bar.
 *
 * @param  bool  $show_admin_bar  Current state of whether the admin bar is shown.
 * @return  bool  True to show the admin bar.
 */
if ( ! function_exists( 'vtt_default_show_admin_bar' ) ) :
	function vtt_default_show_admin_bar( $show_admin_bar ) {
		return true;
	}
endif;


/**
 * Remove login header.
 */
add_theme_support( 'admin-bar', array( 'callback' => '__return_false' ) );

/**
 * Add the close responsive menu button to the admin bar.
 *
 * @param  WP_Admin_Bar  $wp_admin_bar
 */
if ( ! function_exists( 'vtt_default_add_responsive_close_button' ) ) :
	function vtt_default_add_responsive_close_button( $wp_admin_bar ) {
		$wp_admin_bar->add_menu(
			array(
				'id'     => 'close-menu-button',
				'href'   => '#',
				'meta'   => array(
					'class' => 'icon-button',
				),
				'parent' => 'top-secondary',
			)
		);
	}
endif;

/**
 * Shorten site titles longer than 20 characters in the admin bar.
 *
 * @param  WP_Admin_Bar $wp_admin_bar
 */
function vtt_shorten_admin_bar_title() {
	global $wp_admin_bar;
	$site_name_node = $wp_admin_bar->get_node( 'site-name' );
	if ( $site_name_node ) {
		$site_name_node->title = mb_strimwidth( $site_name_node->title, 0, 20, '...' );
		$wp_admin_bar->add_node( $site_name_node );
	}
}

/**
 * Adds support for featured images.
 * Additionally, default headers and added from all the variation directories.
 * As each directory is searched, each full-size header is located in {directory}/images/headers/full
 * and thumbnails are found in {directory}/images/headers/thubnail.
 */
if ( ! function_exists( 'vtt_default_add_featured_image_support' ) ) :
	function vtt_default_add_featured_image_support() {
		 global $vtt_config;

		add_theme_support(
			'custom-header',
			array(
				'width'                  => 1800,
				'height'                 => 300,
				'flex-width'             => true,
				'flex-height'            => true,
				'random-default'         => true,
				'admin-head-callback'    => 'vtt_default_admin_head_callback',
				'admin-preview-callback' => 'vtt_default_admin_preview_callback',
				'header-text'            => true,
				'default-text-color'     => '',
				'default-text-bgcolor'   => '',
			)
		);

		$all_directories = $vtt_config->get_all_directories( false );

		$images = array();
		foreach ( $all_directories as $directory ) {
			if ( ! is_dir( $directory . '/images/headers' ) ) { continue;
			}
			if ( ! is_dir( $directory . '/images/headers/full' ) ) { continue;
			}
			if ( ! is_dir( $directory . '/images/headers/thumbnail' ) ) { continue;
			}

			$url   = vtt_path_to_url( $directory );
			$files = scandir( $directory . '/images/headers/full' );
			foreach ( $files as $file ) {
				if ( $file[0] == '.' ) { continue;
				}
				if ( is_dir( $directory . '/images/headers/full/' . $file ) ) { continue;
				}
				if ( ! file_exists( $directory . '/images/headers/thumbnail/' . $file ) ) { continue;
				}
				if ( is_dir( $directory . '/images/headers/thumbnail/' . $file ) ) { continue;
				}

				$basename                             = basename( $file );
				$images[ $basename ]['url']           = $url . '/images/headers/full/' . $file;
				$images[ $basename ]['thumbnail_url'] = $url . '/images/headers/thumbnail/' . $file;
				$images[ $basename ]['description']   = $url;
			}
		}

		register_default_headers( $images );
	}
endif;


/**
 * Show the theme header with theme customizer options.
 */
if ( ! function_exists( 'vtt_default_admin_preview_callback' ) ) :
	function vtt_default_admin_preview_callback() {
		 vtt_get_template_part( 'header-title', 'part' );
	}
endif;


/**
 * Load the theme header styles for the theme customizer.
 */
if ( ! function_exists( 'vtt_default_admin_head_callback' ) ) :
	function vtt_default_admin_head_callback() {
		vtt_enqueue_files( 'style', 'header-style', 'styles/admin-header.css' );
	}
endif;


/**
 * Add the header menu.
 */
if ( ! function_exists( 'vtt_default_register_menus' ) ) :
	function vtt_default_register_menus() {
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
if ( ! function_exists( 'vtt_default_setup_widget_areas' ) ) :
	function vtt_default_setup_widget_areas() {
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

		$widget_area                  = array();
		$widget_area['before_widget'] = '<div id="%1$s" class="widget %2$s">';
		$widget_area['after_widget']  = '</div>';
		$widget_area['before_title']  = '<h2 class="widget-title">';
		$widget_area['after_title']   = '</h2>';

		foreach ( $widgets as $widget ) {
			$widget_area['name'] = $widget['name'];
			$widget_area['id']   = $widget['id'];
			register_sidebar( $widget_area );
		}
	}
endif;


/**
 * Setup the default-specific sections in the Theme Customizer.
 *
 * @param  WP_Customize_Manager  $wp_customize  Theme Customizer API controller.
 */
if ( ! function_exists( 'vtt_default_customize_register' ) ) :
	function vtt_default_customize_register( $wp_customize ) {
		global $vtt_config;

		// -------------------------------------------------------
		// HEADER: TITLE AND TAGLINE
		// -------------------------------------------------------
		$wp_customize->add_section(
			'vtt-header-title-tagline-section',
			array(
				'title'    => 'Header Title and Tagline',
				'priority' => 0,
			)
		);

		$wp_customize->add_setting(
			'blogname',
			array(
				'default' => get_option( 'blogname' ),
				'type'    => 'option',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'blogname-control',
				array(
					'label'    => 'Site Title',
					'section'  => 'vtt-header-title-tagline-section',
					'settings' => 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'blogname_url',
			array(
				'default' => $vtt_config->get_value( 'blogname_url' ),
				'type'    => 'theme_mod',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'blogname_url-control',
				array(
					'label'    => 'Site Title URL',
					'section'  => 'vtt-header-title-tagline-section',
					'settings' => 'blogname_url',
				)
			)
		);

		$wp_customize->add_setting(
			'blogname_url_default',
			array(
				'default' => $vtt_config->get_value( 'blogname_url_default' ),
				'type'    => 'theme_mod',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'blogname_url_default-control',
				array(
					'label'    => 'Use site URL for Site Title.',
					'section'  => 'vtt-header-title-tagline-section',
					'settings' => 'blogname_url_default',
					'type'     => 'checkbox',
				)
			)
		);

		$wp_customize->add_setting(
			'blogdescription',
			array(
				'default' => get_option( 'blogdescription' ),
				'type'    => 'option',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'blogdescription-control',
				array(
					'label'    => 'Site Tagline',
					'section'  => 'vtt-header-title-tagline-section',
					'settings' => 'blogdescription',
				)
			)
		);

		$wp_customize->add_setting(
			'blogdescription_url',
			array(
				'default' => $vtt_config->get_value( 'blogdescription_url' ),
				'type'    => 'theme_mod',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'blogdescription_url-control',
				array(
					'label'    => 'Site Description URL',
					'section'  => 'vtt-header-title-tagline-section',
					'settings' => 'blogdescription_url',
				)
			)
		);

		$wp_customize->add_setting(
			'blogdescription_url_default',
			array(
				'default' => $vtt_config->get_value( 'blogdescription_url_default' ),
				'type'    => 'theme_mod',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'blogdescription_url_default-control',
				array(
					'label'    => 'Use site URL for Site Description.',
					'section'  => 'vtt-header-title-tagline-section',
					'settings' => 'blogdescription_url_default',
					'type'     => 'checkbox',
				)
			)
		);

		// -------------------------------------------------------
		// HEADER: POSITION
		// -------------------------------------------------------
		$wp_customize->add_section(
			'vtt-header-position-section',
			array(
				'title'    => 'Header Title Position',
				'priority' => 0,
			)
		);

		// Hide header title.
		$wp_customize->add_setting(
			'header-title-hide',
			array(
				'default' => $vtt_config->get_value( 'header-title-hide' ),
				'type'    => 'theme_mod',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt-header-title-hide-control',
				array(
					'label'    => 'Hide header title',
					'section'  => 'vtt-header-position-section',
					'settings' => 'header-title-hide',
					'type'     => 'checkbox',
				)
			)
		);

		// Header position.
		$wp_customize->add_setting(
			'header-title-position',
			array(
				'default'   => $vtt_config->get_value( 'header-title-position' ),
				'type'      => 'theme_mod',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new VTT_Customize_Header_Position(
				$wp_customize,
				'vtt-header-title-position-control',
				array(
					'label'    => 'Position',
					'section'  => 'vtt-header-position-section',
					'settings' => 'header-title-position',
				)
			)
		);

		// -------------------------------------------------------
		// HEADER: COLORS
		// -------------------------------------------------------
		$wp_customize->add_section(
			'vtt-header-colors-section',
			array(
				'title'    => 'Header Title Colors',
				'priority' => 0,
			)
		);

		// Header Title Box Text Color
		$wp_customize->add_setting(
			'header_textcolor',
			array(
				'theme_supports'       => array( 'custom-header', 'header-text' ),
				'default'              => get_theme_support( 'custom-header', 'default-text-color' ),
				'sanitize_callback'    => array( $wp_customize, '_sanitize_header_textcolor' ),
				'sanitize_js_callback' => 'maybe_hash_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'header_textcolor',
				array(
					'label'   => 'Header Text Color',
					'section' => 'vtt-header-colors-section',
				)
			)
		);

		// Header Title Box Text Background Color
		$wp_customize->add_setting(
			'header_textbgcolor',
			array(
				'theme_supports'       => array( 'custom-header', 'header-text' ),
				'default'              => get_theme_support( 'custom-header', 'default-text-bgcolor' ),
				'sanitize_callback'    => 'vtt_default_sanitize_header_textbgcolor',
				'sanitize_js_callback' => 'maybe_hash_hex_color',
			)
		);

		$wp_customize->add_control(
			new Pluto_Customize_Alpha_Color_Control(
				$wp_customize,
				'header_textbgcolor',
				array(
					'label'   => 'Header Text Background Color',
					'palette' => true,
					'section' => 'vtt-header-colors-section',
				)
			)
		);

		// -------------------------------------------------------
		// HEADER IMAGE
		// -------------------------------------------------------
		$wp_customize->get_section( 'header_image' )->title    = 'Header Image';
		$wp_customize->get_section( 'header_image' )->priority = 10;

		// -------------------------------------------------------
		// BACKGROUND COLOR
		// -------------------------------------------------------
		$wp_customize->get_section( 'colors' )->title    = 'Background Color';
		$wp_customize->get_section( 'colors' )->priority = 40;

		// -------------------------------------------------------
		// FEATURED IMAGE
		// -------------------------------------------------------
		$wp_customize->add_section(
			'vtt-featured-image-section',
			array(
				'title'    => 'Featured Image',
				'priority' => 80,
			)
		);

		// Featured Image Position select
		$wp_customize->add_setting(
			'featured-image-position',
			array(
				'default'   => $vtt_config->get_value( 'featured-image-position' ),
				'type'      => 'theme_mod',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt-featured-image-position-control',
				array(
					'label'    => 'Position',
					'section'  => 'vtt-featured-image-section',
					'settings' => 'featured-image-position',
					'type'     => 'select',
					'choices'  => array(
						'header' => 'Header Image',
						'left'   => 'Left Image',
						'right'  => 'Right Image',
						'center' => 'Across Top Centered',
					),
				)
			)
		);

		// Remove Title & Tagline section.
		$wp_customize->remove_section( 'title_tagline' );
	}
endif;


/**
 * Sanitizes and verifies the text bg color from the theme customizer.
 *
 * @param  string  $color  The color value from the header text bg color theme customizer control.
 */
if ( ! function_exists( 'vtt_default_sanitize_header_textbgcolor' ) ) :
	function vtt_default_sanitize_header_textbgcolor( $color ) {
		if ( 'blank' === $color ) {
			return 'blank';
		}

		if ( strpos( $color, 'rgb' ) !== false ) {
			return $color;
		}

		$color = sanitize_hex_color_no_hash( $color );
		if ( empty( $color ) ) {
			$color = get_theme_support( 'custom-header', 'default-text-bgcolor' );
		}

		return $color;
	}
endif;


/**
 * Add a custom read more link.
 *
 * @return  string  The custom html for the read more link.
 */
if ( ! function_exists( 'vtt_default_read_more_link' ) ) :
	function vtt_default_read_more_link() {
		return '<a class="more-link" href="' . get_permalink() . '">Read more...</a>';
	}
endif;


/**
 * For pages menu, the home page should be shown by default.
 *
 * @param  Array  $args  The current args for pages.
 * @return  Array  The modified args.
 */
if ( ! function_exists( 'vtt_default_add_home_pages_menu_item' ) ) :
	function vtt_default_add_home_pages_menu_item( $args ) {
		// $args['show_home'] = true;
		return $args;
	}
endif;

/**
 * Determine if Featured Story option is selected.
 *
 * @return boolean True if post is a Featured Story, false if not.
 */
if ( ! function_exists( 'vtt_is_featured' ) ) :
	function vtt_is_featured() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		// Advanced Custom Fields must be active
		if ( is_plugin_active( 'advanced-custom-fields/acf.php' ) ) {
			$featured = get_field( 'featured_story' );

			// If Featured Story is checked on the single post or page, return true
			if ( $featured && is_singular() ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	endif;

/**
 * Add theme support for wide and full alignments on front end and back end.
 * Supports Cover Image, Image, Gallery, Pullquote, Video, Table, Columns, Categories, & Embed blocks
 */
add_action( 'after_setup_theme', 'vtt_add_wide_alignment' );
if ( ! function_exists( 'vtt_add_wide_alignment' ) ) :
	function vtt_add_wide_alignment() {
		add_theme_support( 'align-wide' );
	}
endif;

/**
 * Registers a custom field titled Featured Story
 * Requires the Advanced Custom Fields plugin to work
 * This field group will NOT appear in the ACF admin page
 */
add_action( 'acf/register_fields', 'vtt_add_featured_story_custom_field' );
if ( ! function_exists( 'vtt_add_featured_story_custom_field' ) ) :
	function vtt_add_featured_story_custom_field() {
		if ( function_exists( 'acf_add_local_field_group' ) ) :

			acf_add_local_field_group(
				array(
					'key'                   => 'group_5d5c5a9cd1505',
					'title'                 => 'Featured Story',
					'fields'                => array(
						array(
							'key'               => 'field_5d5c5aa6a7972',
							'label'             => '',
							'name'              => 'featured_story',
							'type'              => 'checkbox',
							'instructions'      => 'Check if this is a featured story',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'choices'           => array(
								'featured' => 'Featured Story',
							),
							'allow_custom'      => 0,
							'default_value'     => array(),
							'layout'            => 'vertical',
							'toggle'            => 0,
							'return_format'     => 'value',
							'save_custom'       => 0,
						),
					),
					'location'              => array(
						array(
							array(
								'param'    => 'post_type',
								'operator' => '==',
								'value'    => 'post',
							),
						),
						array(
							array(
								'param'    => 'post_type',
								'operator' => '==',
								'value'    => 'page',
							),
						),
					),
					'menu_order'            => 0,
					'position'              => 'normal',
					'style'                 => 'default',
					'label_placement'       => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen'        => '',
					'active'                => true,
					'description'           => '',
				)
			);
			endif;
	}
endif;


// add_action( 'acf/register_fields', 'vtt_add_featured_story_custom_field' );
// if ( ! function_exists( 'vtt_add_featured_story_custom_field' ) ) :
// 	function vtt_add_featured_story_custom_field() {
// 		if ( function_exists( 'register_field_group' ) ) {
// 			register_field_group(
// 				array(
// 					'id'         => 'acf_featured-story',
// 					'title'      => 'Featured Story',
// 					'fields'     => array(
// 						array(
// 							'key'           => 'field_5b63756d66f99',
// 							'label'         => 'Featured Story',
// 							'name'          => 'featured_story',
// 							'type'          => 'checkbox',
// 							'instructions'  => 'Check if this is a featured story',
// 							'choices'       => array(
// 								'featured' => 'Featured Story',
// 							),
// 							'default_value' => '',
// 							'layout'        => 'vertical',
// 						),
// 					),
// 					'location'   => array(
// 						array(
// 							array(
// 								'param'    => 'post_type',
// 								'operator' => '==',
// 								'value'    => 'post',
// 								'order_no' => 0,
// 								'group_no' => 0,
// 							),
// 						),
// 					),
// 					'options'    => array(
// 						'position'       => 'normal',
// 						'layout'         => 'default',
// 						'hide_on_screen' => array(),
// 					),
// 					'menu_order' => 0,
// 				)
// 			);
// 		}
// 	}
// endif;
