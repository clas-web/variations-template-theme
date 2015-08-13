<?php //vtt_print('default:content:single'); ?>
<?php
global $post;
$featured_image_position = $vtt_config->get_value( 'featured-image-position' );
?>


<div class="page-title">
	<div class="breadcrumbs"><?php echo vtt_get_breadcrumbs( $post ); ?></div>
	<?php echo '<h1>'.vtt_get_page_title().'</h1>'; ?>
</div>


<div class="post">

	<div class="details">

	<?php if( $post->post_type === 'post' ): ?>
		<div class="entry-meta"><?php echo vtt_get_byline($post); ?></div>
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
