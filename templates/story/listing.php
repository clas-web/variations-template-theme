

<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars, $post; ?>


<div class="post clearfix">

<div class="details">

	<h2 class="entry-title"><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a></h2>
	<div class="entry-meta">
	<?php uncc_posted_on(); ?>
	</div><!-- .entry-meta -->
	<div class="excerpt">
	<?php echo get_the_excerpt(); ?>
	</div><!-- .excerpt -->

</div><!-- .description -->

</div><!-- .story -->

