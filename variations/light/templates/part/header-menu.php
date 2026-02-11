<?php //vtt_print('default:part:header-menu'); ?>


<?php
$header_menu = wp_nav_menu( 
	array(
		'container'            => 'nav',
		'container_class'      => 'header-navigation',
		'container_aria_label' => __( 'Main Navigation', 'variations-template-theme' ),
		'theme_location'       => 'header-navigation',
		'echo'                 => false,
		'fallback_cb'          => false,
	)
);
?>

<?php if( strpos( $header_menu, '</li>' ) !== false ): ?>

	<div id="header-menu-wrapper" class="clearfix">
		<div id="header-menu">
		<?php echo $header_menu; ?>
		</div><!-- #header-menu -->
	</div><!-- #header-menu-wrapper -->

<?php endif; ?>
