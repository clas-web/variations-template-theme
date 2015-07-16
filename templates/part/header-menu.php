
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

<?php if( strpos( $header_menu, '</li>' ) !== false ): ?>
<div id="header-menu-wrapper" class="clearfix">
	<div id="header-menu">
	<?php echo $header_menu; ?>
	</div><!-- #header-menu -->
</div><!-- #header-menu-wrapper -->
<?php endif; ?>


