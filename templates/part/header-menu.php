
<?php //vtt_print('PART: header-menu'); ?>
<?php global $vtt_config, $vtt_mobile_support, $vtt_template_vars; ?>
<?php
$header_menu = wp_nav_menu( 
	array(
		'container'       => 'div',
		'container_class' => 'header-navigation',
		'theme_location'  => 'header-navigation',
		'echo'            => false,
	)
);
?>

<div id="header-menu-wrapper" class="clearfix">
	<div id="header-menu">

	<?php if( strpos( $header_menu, '</li>' ) !== false ): ?>
		<?php echo $header_menu; ?>
	<?php endif; ?>

	</div><!-- #header-menu -->
</div><!-- #header-menu-wrapper -->


