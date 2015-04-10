

<?php //uncc_print('part:author'); ?>
<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars, $wp_query; ?>


<div class="author-info clearfix">

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

	uncc_get_template_part( 'listing', 'content', 'none' );
	
else:

	?>
	<p>No stories found.</p>
	<?php
	
endif;

