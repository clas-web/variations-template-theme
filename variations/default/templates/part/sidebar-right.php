<?php //vtt_print('default:part:sidebar-right'); ?>


<?php
$widgets = wp_get_sidebars_widgets();
if( array_key_exists('vtt-right-sidebar', $widgets) && count($widgets['vtt-right-sidebar']) ):

	?>
	<div id="right-sidebar-wrapper" class="sidebar-wrapper">
		<div id="right-sidebar" class="sidebar">
	
			<?php dynamic_sidebar( 'vtt-right-sidebar' ); ?>
	
		</div><!-- #right-sidebar -->
	</div><!-- #right-sidebar-wrapper -->
	<?php

else:

	global $wp_customize;
	if( isset($wp_customize) ):

		?>
		<div style="display:none;">
			<?php dynamic_sidebar( 'vtt-right-sidebar' ); ?>
		</div>
		<?php
	
	endif;

endif;
?>
