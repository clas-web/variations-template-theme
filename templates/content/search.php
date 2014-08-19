

<?php //uncc_print('search.php'); ?>
<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars, $wp_query; ?>

<?php
//------------------------------------------------------------------------------------
// Print of the stories for this archive listing.
//------------------------------------------------------------------------------------
if( have_posts() ):

	uncc_get_template_part( 'listing', 'content', 'none' );
	
else:

	if( isset($uncc_template_vars['listing-name']) ):
		?><div class="listing-name"><?php echo $uncc_template_vars['listing-name']; ?></div><?php
	endif;

	?><h1><?php echo $uncc_template_vars['page-title']; ?></h1><?php

	if( isset($uncc_template_vars['description']) ):
		?><div class="description"><?php echo $uncc_template_vars['description']; ?></div><?php
	endif;

	?>
	<p>Sorry, but nothing matched your search criteria. Please try again with some different keywords.</p>
	<form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>" >
		<label class="screen-reader-text" for="s">Search for:</label>
		<div class="textbox_wrapper"><input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" /></div>
		<input type="submit" id="searchsubmit" value="Search" />
	</form>
	<?php
	
endif;

