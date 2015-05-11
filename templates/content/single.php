

<?php //vtt_print('single.php'); ?>
<?php global $vtt_config, $vtt_mobile_support, $vtt_template_vars, $post, $wp_query; ?>
<?php
$featured_image_position = $vtt_config->get_theme_value( array('featured-image-position'), 'vtt-featured-image-position' );
?>


<div class="breadcrumbs"><?php echo vtt_get_breadcrumbs( $post ); ?></div>

<h1><?php echo apply_filters( 'the_title', $post->post_title ); ?></h1>

<div class="post clearfix">

	<div class="details clearfix">

	<?php if( $post->post_type === 'post' ): ?>
		<div class="entry-meta"><?php echo vtt_get_byline($post); ?></div><!-- .entry-meta -->
	<?php endif; ?>

	<?php if( $post->post_type === 'post' ): ?>
		<?php echo vtt_get_taxonomy_list('category', $post); ?>
	<?php endif; ?>
	
	<div class="entry-content">
	
		<?php if( $featured_image_position !== 'header' && has_post_thumbnail($post->ID) ): ?>
			<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large' ); ?>
			<div class="featured-image <?php echo $featured_image_position; ?>">
				<img src="<?php echo $image[0]; ?>" title="Featured Image" />
			</div>
		<?php endif; ?>

		<?php echo apply_filters( 'the_content', $post->post_content ); ?>
		
	</div><!-- .entry-content -->
	
	<?php if( $post->post_type === 'post' ): ?>
		<?php echo vtt_get_taxonomy_list('post_tag', $post); ?>
	<?php endif; ?>
	
	</div><!-- .details -->

	<?php comments_template() ?>
	
</div><!-- .post -->

