<?php //vtt_print('default:content:single'); ?>
<?php
global $vtt_config, $post;
$featured_image_position = $vtt_config->get_value( 'featured-image-position' );
?>


<div class="page-title">
	<div class="breadcrumbs"><?php echo vtt_get_breadcrumbs( $post ); ?></div>
	<?php $page_title = '<h1>'.vtt_get_page_title().'</h1>'; ?>
	<?php echo apply_filters('collections_page_title', $page_title, $post); ?>
</div>


<div <?php post_class(); ?>>

	<div class="details">
	
	<?php if( $post->post_type === 'post' ): ?>
		<div class="entry-meta"><?php echo vtt_get_byline($post); ?></div>
	<?php endif; ?>

	<?php if( $post->post_type === 'post' ): ?>
		<?php echo vtt_get_taxonomy_list('category', $post); ?>
	<?php endif; ?>

	<div class="entry-content">
	
		<?php if( $featured_image_position !== 'header' && has_post_thumbnail($post->ID) && !get_field( 'featured_story' ) ): ?>
			<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large' ); ?>
			<div class="featured-image <?php echo $featured_image_position; ?>">
				<img src="<?php echo $image[0]; ?>" title="Featured Image" />
			</div>
		<?php endif; ?>
		
		<?php echo apply_filters('collections_before_the_content', '', $post);?>

		<?php echo apply_filters('the_content', $post->post_content); ?>
		
		<?php wp_link_pages('before=<div id="page-links">&after=</div>'); ?>
		
	</div><!-- .entry-content -->

	<?php if( $post->post_type === 'post' ): ?>
		<?php echo vtt_get_taxonomy_list('post_tag', $post); ?>
	<?php endif; ?>
	
	<?php echo apply_filters('collections_after_the_content', '', $post);?>

	</div><!-- .details -->

	<?php comments_template() ?>
	
</div><!-- .post -->
