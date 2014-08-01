

<?php //uncc_print('PART: header'); ?>
<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars; ?>
<?php
$header_wrapper_bg = uncc_get_image_url( $uncc_config->get_value( 'header', 'header-wrapper-bg', 'path' ) );
$header_bg = uncc_get_image_url( $uncc_config->get_value( 'header', 'header-bg', 'path' ) );
list( $header_width, $header_height ) = getimagesize( $header_bg );

list( $banner_url, $banner_width, $banner_height ) = array_values( uncc_get_header_image() );

$position = $uncc_config->get_value( 'header', 'title-position' );
$title = $uncc_config->get_value( 'header', 'title' );
$description = $uncc_config->get_value( 'header', 'description' );

if( $title['use-blog-info'] )       $title['text'] = get_bloginfo('name');
if( $title['use-site-link'] )       $title['link'] = get_site_url();
if( $description['use-blog-info'] ) $description['text'] = get_bloginfo('description');
if( $description['use-site-link'] ) $description['link'] = get_site_url();
?>

<div id="header-wrapper" class="clearfix" style="background-image:url('<?php echo $header_wrapper_bg; ?>'); height:<?php echo $header_height; ?>px;">
	<div id="header" class="clearfix">


	<div class="masthead" style="background-image:url('<?php echo $header_bg; ?>'); height:<?php echo $header_height; ?>px; width:<?php echo $header_width; ?>px;">

		<?php uncc_image( $uncc_config->get_image_data( 'header', 'logo' ) ); ?>

		<div class="title-box-wrapper" style="height:100px">
		<div class="title-box <?php echo $position; ?>">
		
		<?php if( !empty($title['text']) ): ?>
			<?php echo uncc_get_anchor( $title['link'], null, null, '<div class="name">'.$title['text'].'</div>' ); ?>
		<?php endif; ?>
		<?php if( !empty($description['text']) ): ?>
			<?php if( !empty($title['text']) ): ?><br/><?php endif; ?>
			<?php echo uncc_get_anchor( $description['link'], null, null, '<div class="description">'.$description['text'].'</div>' ); ?>
		<?php endif; ?>

		</div>
		</div>
		

		<?php if( !$uncc_mobile_support->use_mobile_site ): ?>
			
			<div id="links">

				<?php
				$links = get_bookmarks( array('category' => 'header') );
				foreach( $links as $l ):
					printf( '<a href="%s" title="%s">%s</a>', $l->link_url, $l->link_name, $l->link_name );
				endforeach;
				?>

			</div>

			<div id="header-utility">
				<form id="site-searchform" class="searchform" method="get" action="<?php echo home_url( '/' ); ?>">
					<script>var header_search_used = false;</script>
					<input type="text" name="s" id="header-search" class="s" size="30" value="<?php if( is_search() ) { the_search_query(); } else { echo "Search ".get_bloginfo('name'); } ?>" onfocus="if (!header_search_used) { this.value = ''; header_search_used = true; }" /><input type="image" name="op" value="Search" id="edit-submit" alt="search" title="Search this site" src="<?php print get_stylesheet_directory_uri() ?>/images/search-button.png">
				</form>
			</div><!-- #header-utility -->
			
		<?php endif; ?>
	
	</div><!-- .masthead -->
	

	</div><!-- #header -->
</div><!-- #header-wrapper -->


<div id="banner-wrapper" class="clearfix">
	<div id="banner" class="clearfix" style="background-image:url('<?php echo $banner_url; ?>'); width:<?php echo $banner_width; ?>px; height:<?php echo $banner_height; ?>px;">
		<a href="<?php echo home_url( '/' ); ?>" title="<?php echo get_bloginfo('name'); ?>"></a>
	</div>
</div>

