<?php //vtt_print( 'LEFT-SIDEBAR-PAGE' ); ?>
<?php global $vtt_config, $vtt_mobile_support, $vtt_template_vars; ?>

<?php
$widgets = wp_get_sidebars_widgets();
if( array_key_exists('vtt-left-sidebar', $widgets) && count($widgets['vtt-left-sidebar']) ):
?>

	<div id="left-sidebar-wrapper" class="sidebar-wrapper clearfix">
		<div id="left-sidebar" class="sidebar clearfix">
	
			<?php if( $vtt_mobile_support->use_mobile_site ): ?>
				<h2>Left Sidebar</h2>
			<?php endif; ?>
			<?php dynamic_sidebar( 'vtt-left-sidebar' ); ?>
	
		</div><!-- #left-sidebar -->
	</div><!-- #left-sidebar-wrapper -->

<?php else: ?>

	<?php 
	global $wp_customize;
	if( isset($wp_customize) ):

		?>
		<div style="display:none;">
			<?php dynamic_sidebar( 'vtt-left-sidebar' ); ?>
		</div>
		<?php
	
	endif;
	?>

<?php endif; ?>

