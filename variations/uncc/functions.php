<?php


add_action( 'after_setup_theme', 'uncc_modify_custom_header', 99 );


//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_show_admin_bar') ):
function uncc_show_admin_bar( $show_admin_bar )
{
	return $show_admin_bar;
}
endif;


//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_modify_custom_header') ):
function uncc_modify_custom_header()
{
	remove_theme_support( 'custom-header' );
	add_theme_support( 'custom-header', array( 'width' => 950, 'random-default' => false ) );
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
			'header-links' => __( 'Header Links' ),
			'header-navigation' => __( 'Header Menu' ),
		)
	);
}
endif;

