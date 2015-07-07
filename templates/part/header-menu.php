
<?php //vtt_print('PART: header-menu'); ?>
<?php global $vtt_config, $vtt_mobile_support, $vtt_template_vars; ?>
<?php
$header_menu = wp_nav_menu( 
	array(
		'menu_class' => 'header-navigation',
		'theme_location' => 'header-navigation',
		'echo' => false,
	)
);
?>

<div id="header-menu-wrapper" class="clearfix">
	<div id="header-menu">

	<?php if( $vtt_mobile_support->use_mobile_site ): ?>
		<h2 class="search">Search</h2>
		<form id="site-searchform" role="search" method="get" class="searchform" action="<?php echo home_url( '/' ); ?>">
			<script>var main_search_used = false;</script>
			<div class="textbox_wrapper">
				<input type="text" name="s" id="header-search" class="s" size="30" value="<?php if( is_search() ) { the_search_query(); } else { echo "Search this site"; } ?>" onfocus="if (!main_search_used) { this.value = ''; main_search_used = true; }" />
				<input type="submit" id="searchsubmit" value="Search">
			</div>
		</form><!-- #site-searchform -->
	<?php endif; ?>

	<?php if( strpos( $header_menu, '</li>' ) !== false ): ?>
		<?php if( $vtt_mobile_support->use_mobile_site ): ?><h2 class="menu">Menu</h2><?php endif; ?>
		<?php echo $header_menu; ?>
	<?php endif; ?>

	</div><!-- #header-menu -->
</div><!-- #header-menu-wrapper -->


