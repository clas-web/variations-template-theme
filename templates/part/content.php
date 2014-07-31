

<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars; ?>

<?php
$widgets = wp_get_sidebars_widgets();
$class = '';
$sidebar_count = 0;
$use_left_sidebar = false;
$use_right_sidebar = false;

if( array_key_exists('uncc-left-sidebar', $widgets) && count($widgets['uncc-left-sidebar']) ):
	$use_left_sidebar = true;
	$sidebar_count++;
	$class .= ' left-sidebar';
endif;

if( array_key_exists('uncc-right-sidebar', $widgets) && count($widgets['uncc-right-sidebar']) ):
	$use_right_sidebar = true;
	$sidebar_count++;
	$class .= ' right-sidebar';
endif;

switch( $sidebar_count )
{
	case 0:
		$class = 'full-width' . $class;
		break;
	case 1:
		$class = 'one-sidebar-width' . $class;
		break;
	case 2:
		$class = 'two-sidebars-width' . $class;
		break;
}
?>


<div id="content-wrapper" class="<?php echo $class; ?> wrapper clearfix">
	<div id="content" class="<?php echo $class; ?> clearfix">

	<?php if( $sidebar_count > 0 ): ?>
	
	<div class="sidebar-controls">
		
		<?php if( $use_left_sidebar ): ?>
			<div id="left-sidebar-button" class="left-sidebar button" controls="left-sidebar"></div>
		<?php endif; ?>

		<?php if( $use_right_sidebar ): ?>
			<div id="right-sidebar-button" class="right-sidebar button" controls="right-sidebar"></div>
		<?php endif; ?>
		
	</div>
	
	<?php endif; ?>

	<?php
	uncc_get_template_part( $uncc_template_vars['content-type'], 'content' );
	?>

	
	</div><!-- #content -->
</div><!-- #content-wrapper -->

