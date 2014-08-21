<?php //uncc_print( 'RIGHT-SIDEBAR-PAGE' ); ?>
<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars; ?>

<?php
$widgets = wp_get_sidebars_widgets();
if( array_key_exists('uncc-right-sidebar', $widgets) && count($widgets['uncc-right-sidebar']) ):
?>

	<div id="right-sidebar-wrapper" class="sidebar-wrapper clearfix">
		<div id="right-sidebar" class="sidebar clearfix">
	
			<?php if( $uncc_mobile_support->use_mobile_site ): ?>
				<h2>Right Sidebar</h2>
			<?php endif; ?>
			<?php dynamic_sidebar( 'uncc-right-sidebar' ); ?>
	
		</div><!-- #right-sidebar -->
	</div><!-- #right-sidebar-wrapper -->

<?php else: ?>

	<?php 
	global $wp_customize;
	if( isset($wp_customize) ):

		?>
		<div style="display:none;">
			<?php dynamic_sidebar( 'uncc-right-sidebar' ); ?>
		</div>
		<?php
	
	endif;
	?>

<?php endif; ?>

