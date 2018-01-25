<?php //vtt_print('default:post:rss'); ?>
<?php global $post; ?>


<div class="post">

<div class="details">

	<a href="<?php echo get_permalink($post->ID); ?>"><h3><?php echo $post->post_title; ?></h3></a>
	
	<div class="excerpt">
	<?php echo get_the_excerpt(); ?>
	</div><!-- .excerpt -->

</div><!-- .description -->

</div><!-- .post -->
