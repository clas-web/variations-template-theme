<?php


add_action( 'after_setup_theme', 'uncc_modify_custom_header', 99 );
add_filter( 'uncc-widget-areas', 'uncc_modify_widgets' );

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
	global $_wp_theme_features;
	
	if( array_key_exists('custom-header', $_wp_theme_features) && count($_wp_theme_features['custom-header'] > 0) )
	{
		$_wp_theme_features['custom-header'][0]['random-default'] = false;
	}
	else
	{
		add_theme_support( 'custom-header',
			array( 
				'width' => 950, 
				'random-default' => false,
			)
		);
	}
}
endif;


//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('uncc_modify_widgets') ):
function uncc_modify_widgets( $widgets )
{
	$widgets = array(
		array(
			'id'   => 'uncc-left-sidebar',
			'name' => 'Left Sidebar',
		),
		array(
			'id'   => 'uncc-right-sidebar',
			'name' => 'Right Sidebar',
		)
	);
	
	return $widgets;
}
endif;


