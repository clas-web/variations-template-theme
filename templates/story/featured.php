

<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars, $post; ?>


<div class="post clearfix">

<div class="details">

	<a href="<?php echo get_permalink($post->ID); ?>"><h3><?php echo $post->post_title; ?></h3></a>

	<?php if( $post->post_type == 'post' ): echo uncc_get_taxonomy_list('category', $post); endif; ?>

	<div class="excerpt">
	<?php echo get_the_excerpt(); ?>
	</div><!-- .excerpt -->

	<?php if( $post->post_type == 'post' ): echo uncc_get_taxonomy_list('post_tag', $post); endif; ?>

</div><!-- .description -->

</div><!-- .post -->

