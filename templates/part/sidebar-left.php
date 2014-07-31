<?php //uncc_print( 'LEFT-SIDEBAR-PAGE' ); ?>
<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars; ?>

<?php
$widgets = wp_get_sidebars_widgets();
if( array_key_exists('uncc-left-sidebar', $widgets) && count($widgets['uncc-left-sidebar']) ):
?>

<div id="left-sidebar-wrapper" class="left sidebar wrapper clearfix">
	<div id="left-sidebar" class="left sidebar clearfix">
	
		<?php if( $uncc_mobile_support->use_mobile_site ): ?><h2>Left Sidebar</h2><?php endif; ?>
		<?php dynamic_sidebar( 'uncc-left-sidebar' ); ?>
	
	</div><!-- #left-sidebar -->
</div><!-- #left-sidebar-wrapper -->

<?php endif; ?>

