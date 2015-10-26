<?php //vtt_print('default:content:author'); ?>


<div class="author-info">

	<?php echo get_avatar( get_the_author_meta('ID') ); ?>

	<div class="stats">
	
		<?php 
		$user_url = get_the_author_meta( 'user_url' );
		$user_description = get_the_author_meta( 'description' );
		if( !empty($user_description) )
			echo '<div class="description">'.$user_description.'</div>';
		if( !empty($user_url) )
			echo '<a href="'.$user_url.'" title="Homepage">'.$user_url.'</a>';
		?>
	
	</div>

</div>


<?php if( have_posts() ): ?>
	<?php vtt_get_template_part( 'listing', 'content', vtt_get_queried_object_type() ); ?>
<?php else: ?>
	<p>No posts.</p>
<?php endif; ?>
