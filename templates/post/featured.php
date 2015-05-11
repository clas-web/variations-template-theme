

<?php global $vtt_config, $vtt_mobile_support, $vtt_template_vars, $post; ?>
<?php
$featured_image_position = $vtt_config->get_theme_value( array('featured-image-position'), 'vtt-featured-image-position' );
?>


<div class="post clearfix">

<div class="details clearfix">

	<a href="<?php echo get_permalink($post->ID); ?>"><h3><?php echo $post->post_title; ?></h3></a>

	<?php if( $post->post_type === 'post' ): ?>
		<div class="entry-meta"><?php echo vtt_get_byline($post); ?></div><!-- .entry-meta -->
	<?php endif; ?>

	<?php if( $post->post_type === 'post' ): ?>
		<?php echo vtt_get_taxonomy_list('category', $post); ?>
	<?php endif; ?>
	
	<?php if( $post->post_type === 'post' ): ?>
		<?php echo vtt_get_taxonomy_list('post_tag', $post); ?>
	<?php endif; ?>

	<div class="excerpt">

		<?php if( $featured_image_position !== 'header' && has_post_thumbnail($post->ID) ): ?>
			<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail' ); ?>
			<div class="featured-image <?php echo $featured_image_position; ?>">
				<img src="<?php echo $image[0]; ?>" title="Featured Image" />
			</div>
		<?php endif; ?>
		
		<?php echo get_the_excerpt(); ?>
		
	</div><!-- .excerpt -->

</div><!-- .description -->

</div><!-- .post -->

