

<?php //vtt_print('part:author'); ?>
<?php global $vtt_config, $vtt_mobile_support, $vtt_template_vars, $wp_query; ?>


<div class="author-info">

	<?php echo get_avatar( get_the_author_meta('ID') ); ?>

	<div class="stats">
	
		<?php 
		$user_url = get_the_author_meta( 'user_url' );
		$user_description = get_the_author_meta( 'description' );
		if( !empty($user_description) )
		{
			echo '<div class="description">'.$user_description.'</div>';
		}
		if( !empty($user_url) )
		{
			echo '<a href="'.$user_url.'" title="Homepage">'.$user_url.'</a>';
		}

		?>
	
	</div>

</div>

<?php
//------------------------------------------------------------------------------------
// Print of the stories for this archive listing.
//------------------------------------------------------------------------------------
if( have_posts() ):

	vtt_get_template_part( 'listing', 'content', vtt_get_queried_object_type() );
	
else:

	?>
	<p>No stories found.</p>
	<?php
	
endif;

