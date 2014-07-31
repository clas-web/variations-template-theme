
<?php //uncc_print('listing.php'); ?>
<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars, $wp_query; ?>

<?php if( isset($uncc_template_vars['listing-name']) ): ?>
	<div class="listing-name"><?php echo $uncc_template_vars['listing-name']; ?></div>
<?php endif; ?>

<h1><?php echo $uncc_template_vars['page-title']; ?></h1>

<?php if( isset($uncc_template_vars['description']) ): ?>
	<div class="description"><?php echo $uncc_template_vars['description']; ?></div>
<?php endif; ?>

<?php
//------------------------------------------------------------------------------------
// Print of the stories for this archive listing.
//------------------------------------------------------------------------------------
if( !have_posts() ):

	?>
	<p>No stories found.</p>
	<?php

else:

	while( have_posts() ):

		the_post();
		uncc_get_template_part( 'listing', 'story' );
	
	endwhile; // while( have_posts() )
 
	//--------------------------------------------------------------------------------
	// Page Navigation.
	//--------------------------------------------------------------------------------
	uncc_get_template_part( 'pagination', 'other', $key );

endif; // if( !have_posts() )
?>

