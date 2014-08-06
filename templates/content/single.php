

<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars, $post, $wp_query; ?>


<h1><?php echo apply_filters( 'the_title', $post->post_title ); ?></h1>

<div class="post clearfix">

	<div class="details">
	<div class="entry-meta">
	<?php // uncc_posted_on(); //should only be added to posts not pages ?>
	</div><!-- .entry-meta -->
	<div class="entry-content">
	<?php echo apply_filters( 'the_content', $post->post_content ); ?>
	</div><!-- .contents -->
	
	</div><!-- .details -->

</div><!-- .post -->

