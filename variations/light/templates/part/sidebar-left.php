<?php //vtt_print('default:part:sidebar-left'); ?>


<?php
$widgets = wp_get_sidebars_widgets();
if( array_key_exists('vtt-left-sidebar', $widgets) && count($widgets['vtt-left-sidebar']) ):

	?>
	<aside id="left-sidebar-wrapper" class="sidebar-wrapper" aria-label="<?php esc_attr_e( 'Left Sidebar', 'variations-template-theme' ); ?>">
		<div id="left-sidebar" class="sidebar">
	
			<?php dynamic_sidebar( 'vtt-left-sidebar' ); ?>
	
		</div><!-- #left-sidebar -->
	</aside><!-- #left-sidebar-wrapper -->
	<?php

else:

	global $wp_customize;
	if( isset($wp_customize) ):

		?>
		<div style="display:none;">
			<?php dynamic_sidebar( 'vtt-left-sidebar' ); ?>
		</div>
		<?php
	
	endif;

endif;
?>
