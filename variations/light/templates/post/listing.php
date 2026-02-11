<?php //vtt_print('default:post:listing'); ?>
<?php
global $vtt_config, $post;
$featured_image_position = $vtt_config->get_value( 'featured-image-position' );
?>


<div <?php post_class(); ?>>

	<h2 class="entry-title"><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a></h2>

	<div class="description">
		
		<?php if( $post->post_type === 'post' ): ?>
			<div class="entry-meta"><?php echo vtt_get_byline($post); ?></div>
			<?php echo vtt_get_taxonomy_list('category', $post); ?>
		<?php endif; ?>
		
		<div class="entry-content">

			<?php if( $featured_image_position !== 'header' && has_post_thumbnail($post->ID) ): ?>
				<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail' ); ?>
				<?php $image_alt = get_post_meta( get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true ); ?>
				<div class="featured-image <?php echo $featured_image_position; ?>">
					<img src="<?php echo $image[0]; ?>" alt="<?php echo esc_attr( $image_alt ); ?>" />
				</div>
			<?php endif; ?>
			
			<?php if( $post->post_excerpt || is_search()): ?>
				<?php the_excerpt(); ?>
			<?php else:?>
				<?php the_content(); ?>
			<?php endif; ?>

			<?php wp_link_pages('before=<div id="page-links">&after=</div>'); ?>
			
		</div><!-- .entry-content -->
		
		<?php if( $post->post_type === 'post' ): ?>
			<?php echo vtt_get_taxonomy_list('post_tag', $post); ?>
		<?php endif; ?>

	</div><!-- .description -->

</div><!-- .post -->
