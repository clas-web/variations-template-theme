<?php //vtt_print('default:content:search'); ?>


<?php
if( have_posts() ):

	vtt_get_template_part( 'listing', 'content', vtt_get_post_type() );
	
else:

	?>
	<div class="page-title">

	<?php
	if( vtt_has_page_listing_name() )
		echo '<div class="listing-name">'.vtt_get_page_listing_name().'</div>';

	echo '<h1>'.vtt_get_page_title().'</h1>';
	?>
	
	</div>
	

	<?php
	if( vtt_has_page_description() )
		echo '<div class="description">'.vtt_get_page_description().'</div>';
	?>
	

	<p>Sorry, but nothing matched your search criteria. Please try again with some different keywords.</p>
	<form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>" >
		<label class="screen-reader-text" for="s">Search for:</label>
		<div class="textbox_wrapper"><input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" /></div>
		<input type="submit" id="searchsubmit" value="Search" />
	</form>
	
	<?php
	
endif;
?>
