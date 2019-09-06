<?php // vtt_print('default:part:content'); ?>

<?php

if ( function_exists( 'collections_sf_redirect' ) ) {
	$search_term = collections_sf_redirect( $searchandfilter, $post );
}


$widgets            = wp_get_sidebars_widgets();
$class              = '';
$sidebar_count      = 0;
$use_left_sidebar   = false;
$use_right_sidebar  = false;
$left_sidebar       = ' left-sidebar';
$right_sidebar      = ' right-sidebar';
$full_width         = 'full-width';
$one_sidebar_width  = 'one-sidebar-width';
$two_sidebars_width = 'two-sidebars-width';

if ( array_key_exists( 'vtt-left-sidebar', $widgets ) && count( $widgets['vtt-left-sidebar'] ) ) :
	$use_left_sidebar = true;
	$sidebar_count++;
	$class .= $left_sidebar;
endif;

if ( array_key_exists( 'vtt-right-sidebar', $widgets ) && count( $widgets['vtt-right-sidebar'] ) ) :
	$use_right_sidebar = true;
	$sidebar_count++;
	$class .= $right_sidebar;
endif;

switch ( $sidebar_count ) {
	case 0:
		$class = $full_width . $class;
		break;
	case 1:
		$class = $one_sidebar_width . $class;
		break;
	case 2:
		$class = $two_sidebars_width . $class;
		break;
}
?>

<?php
// Add proper classes if Featured Story is checked
if ( vtt_is_featured() ) {
	$class = $two_sidebars_width . $left_sidebar . $right_sidebar;
}

	echo '<div id="content-wrapper" class =  "' . $class . ' wrapper">';

?>

	<div id="content" class="<?php echo $class; ?>">

	<?php if ( function_exists( 'collections_get_searchform' ) ) { print collections_get_searchform( $search_term );} ?>
	<?php vtt_get_template_part( vtt_get_page_content_type(), 'content', vtt_get_queried_object_type() ); ?>

	</div><!-- #content -->
</div><!-- #content-wrapper -->
