

<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars, $post, $wp_query; ?>


<h1><?php echo $post->post_title; ?></h1>

<div class="post clearfix">

	<div class="details">

	<div class="contents">
	<?php echo get_the_content(); ?>
	</div><!-- .contents -->

	</div><!-- .details -->

</div><!-- .post -->

