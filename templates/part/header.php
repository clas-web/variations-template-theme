

<?php //uncc_print('PART: header'); ?>
<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars; ?>
<?php
list( $header_url, $header_width, $header_height ) = array_values( uncc_get_header_image() );

$image_link = $uncc_config->get_value( 'header', 'image-link' );
$position = $uncc_config->get_value( 'header', 'title-position' );
$title = $uncc_config->get_value( 'header', 'title' );
$description = $uncc_config->get_value( 'header', 'description' );

if( $title['use-blog-info'] )       $title['text'] = get_bloginfo('name');
if( $title['use-site-link'] )       $title['link'] = get_site_url();
if( $description['use-blog-info'] ) $description['text'] = get_bloginfo('description');
if( $description['use-site-link'] ) $description['link'] = get_site_url();
?>


<div id="header-wrapper" class="clearfix">
	<div id="header" class="clearfix">

	<div class="masthead" style="background-image:url('<?php echo $header_url; ?>'); width:<?php echo $header_width; ?>px; height:<?php echo $header_height; ?>px;">
	
		<div class="title-box-wrapper" style="height:<?php echo $header_height; ?>px;">
		<div class="title-box <?php echo $position; ?>">
		
		<?php if( !empty($title['text']) ): ?>
			<?php echo uncc_get_anchor( $title['link'], null, null, '<div class="name">'.$title['text'].'</div>' ); ?>
		<?php endif; ?>
		<?php if( !empty($description['text']) ): ?>
			<?php if( !empty($title['text']) ): ?><br/><?php endif; ?>
			<?php echo uncc_get_anchor( $description['link'], null, null, '<div class="description">'.$description['text'].'</div>' ); ?>
		<?php endif; ?>
		
		</div><!-- .title-box -->
		</div><!-- .title-box-wrapper -->
		
	</div><!-- .masthead -->
	

	<?php if( $image_link ): ?>
		<a href="<?php echo get_site_url(); ?>" title="<?php echo get_bloginfo('name'); ?>" class="click-box"></a>
	<?php endif; ?>


	<?php if( count(wp_get_nav_menu_items('header-navigation')) > 0 ): ?>

	<div id="header-menu-wrapper" class="clearfix">
		<div id="header-menu" class="clearfix">
		<?php if( $uncc_mobile_support->use_mobile_site ): ?><h2>Menu</h2><?php endif; ?>
		<?php wp_nav_menu( array( 'menu_class' => 'navmenu', 'theme_location' => 'header-navigation' ) ); ?>
		</div><!-- #header-menu -->
	</div><!-- #header-menu-wrapper -->
	
	<?php endif; ?>
	
	
	<div id="header-menu-button" class="button" controls="header-menu"></div>


	</div><!-- #header -->
</div><!-- #header-wrapper -->

