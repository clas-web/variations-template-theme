

<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars, $post; ?>


<div class="post clearfix">

<div class="details clearfix">

	<a href="<?php echo get_permalink($post->ID); ?>"><h3><?php echo $post->post_title; ?></h3></a>
	
	<div class="excerpt">
	<?php echo get_the_excerpt(); ?>
	</div><!-- .excerpt -->

</div><!-- .description -->

</div><!-- .post -->

