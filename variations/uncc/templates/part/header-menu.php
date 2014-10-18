
<?php //uncc_print('PART: header-menu'); ?>
<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars; ?>
<?php
	$header_menu = wp_nav_menu( 
		array(
			'menu_class' => 'header-navigation',
			'theme_location' => 'header-navigation',
			'echo' => false,
			'fallback_cb' => 'uncc_return_nothing',
		)
	);
?>

<div id="header-menu-placeholder" class="clearfix">

<div id="header-menu-wrapper" class="clearfix">
	<div id="header-menu" class="clearfix">

	<?php if( $uncc_mobile_support->use_mobile_site ): ?>
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
		<?php if( $uncc_mobile_support->use_mobile_site ): ?><h2 class="menu">Menu</h2><?php endif; ?>
		<?php echo $header_menu; ?>
	<?php endif; ?>

	</div><!-- #header-menu -->
</div><!-- #header-menu-wrapper -->


<div id="header-menu-icon-wrapper">
	<div id="header-menu-icon">
	
	<?php if( $uncc_mobile_support->use_mobile_site ): ?>
		<div id="header-menu-button"></div>
		<div id="header-menu-button-search-menu">
			SEARCH<?php if( strpos( $header_menu, '</li>' ) !== false ): ?> / MENU<?php endif; ?>
		</div>
	<?php endif; ?>

	</div><!-- #header-menu-icon -->
</div><!-- #header-menu-icon-wrapper -->

</div><!-- #header-menu-placeholder -->

