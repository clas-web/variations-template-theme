

<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars, $post, $wp_query; ?>


<div class="breadcrumbs"><?php echo uncc_get_breadcrumbs( $post ); ?></div>

<h1><?php echo apply_filters( 'the_title', $post->post_title ); ?></h1>

<div class="post clearfix">

	<div class="details">

	<div class="entry-meta"><?php echo uncc_get_byline($post); ?></div><!-- .entry-meta -->

	<div class="entry-content">
	<?php echo apply_filters( 'the_content', $post->post_content ); ?>
	</div><!-- .contents -->
	
	</div><!-- .details -->

</div><!-- .post -->

