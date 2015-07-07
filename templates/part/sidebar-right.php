<?php //vtt_print( 'RIGHT-SIDEBAR-PAGE' ); ?>
<?php global $vtt_config, $vtt_mobile_support, $vtt_template_vars; ?>

<?php
$widgets = wp_get_sidebars_widgets();
if( array_key_exists('vtt-right-sidebar', $widgets) && count($widgets['vtt-right-sidebar']) ):
?>

	<div id="right-sidebar-wrapper" class="sidebar-wrapper">
		<div id="right-sidebar" class="sidebar">
	
			<?php dynamic_sidebar( 'vtt-right-sidebar' ); ?>
	
		</div><!-- #right-sidebar -->
	</div><!-- #right-sidebar-wrapper -->

<?php else: ?>

	<?php 
	global $wp_customize;
	if( isset($wp_customize) ):

		?>
		<div style="display:none;">
			<?php dynamic_sidebar( 'vtt-right-sidebar' ); ?>
		</div>
		<?php
	
	endif;
	?>

<?php endif; ?>

