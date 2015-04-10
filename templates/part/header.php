

<?php //vtt_print('PART: header'); ?>
<?php global $vtt_config, $vtt_mobile_support, $vtt_template_vars; ?>
<?php
list( $header_url, $header_width, $header_height ) = array_values( vtt_get_header_image() );

$image_link = $vtt_config->get_value( 'header', 'image-link' );
$position = $vtt_config->get_value( 'header', 'title-position' );
$title = $vtt_config->get_value( 'header', 'title' );
$description = $vtt_config->get_value( 'header', 'description' );

if( $title['use-blog-info'] )       $title['text'] = get_bloginfo('name');
if( $title['use-site-link'] )       $title['link'] = get_site_url();
if( $description['use-blog-info'] ) $description['text'] = get_bloginfo('description');
if( $description['use-site-link'] ) $description['link'] = get_site_url();
?>


<div id="header-wrapper" class="clearfix">
	<div id="header" class="clearfix">

	<div class="masthead" style="background-image:url('<?php echo $header_url; ?>'); width:<?php echo $header_width; ?>px; height:<?php echo $header_height; ?>px;">
	
		<div id="title-box-placeholder">
		<div id="title-box-wrapper" style="height:<?php echo $header_height; ?>px;">
		<div id="title-box" class="<?php echo $position; ?>">
		
		<?php if( !empty($title['text']) ): ?>
			<?php echo vtt_get_anchor( $title['link'], null, null, '<div class="name">'.$title['text'].'</div>' ); ?>
		<?php endif; ?>
		<?php if( !empty($description['text']) ): ?>
			<?php echo vtt_get_anchor( $description['link'], null, null, '<div class="description">'.$description['text'].'</div>' ); ?>
		<?php endif; ?>
		
		</div><!-- #title-box -->
		</div><!-- #title-box-wrapper -->
		</div><!-- #title-box-placeholder -->
		
	</div><!-- .masthead -->
	

	<?php if( $image_link ): ?>
		<a href="<?php echo get_site_url(); ?>" title="<?php echo get_bloginfo('name'); ?>" class="click-box"></a>
	<?php endif; ?>

	</div><!-- #header -->
</div><!-- #header-wrapper -->

