

<?php //vtt_print('listing.php'); ?>
<?php global $vtt_config, $vtt_mobile_support, $vtt_template_vars, $wp_query; ?>

<div class="page-title">
<?php if( is_category() ): ?>
	<div class="breadcrumbs"><?php echo vtt_get_taxonomy_breadcrumbs(get_cat_ID($vtt_template_vars['page-title'])); ?></div>	
<?php endif; ?>

<?php if( isset($vtt_template_vars['listing-name']) ): ?>
	<div class="listing-name"><?php echo $vtt_template_vars['listing-name']; ?></div>
<?php endif; ?>

<?php if( !is_home() ): ?>
	<h1><?php echo $vtt_template_vars['page-title']; ?></h1>
<?php endif; ?>
</div>

<?php if( isset($vtt_template_vars['description']) ): ?>
	<div class="description"><?php echo $vtt_template_vars['description']; ?></div>
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
		vtt_get_template_part( 'listing', 'post', vtt_get_post_type() );
	
	endwhile; // while( have_posts() )
 
	//--------------------------------------------------------------------------------
	// Page Navigation.
	//--------------------------------------------------------------------------------
	vtt_get_template_part( 'pagination', 'other', vtt_get_queried_object_type() );

endif; // if( !have_posts() )
?>

