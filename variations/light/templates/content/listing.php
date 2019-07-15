<?php //vtt_print('default:content:listing'); ?>
<?php global $wp_query, $wp, $post, $searchandfilter; ?>


<div class="page-title">

	<?php if( is_category() ): ?>
	<div class="breadcrumbs">
		<?php echo vtt_get_taxonomy_breadcrumbs( get_queried_object_id() ); ?>
	</div>	
	<?php endif; ?>
	
	<?php
	if( vtt_has_page_listing_name() )
		echo '<div class="listing-name">'.vtt_get_page_listing_name().'</div>';
	?>

	<?php
	if( !is_home() )
		echo '<h1>'.vtt_get_page_title().'</h1>';
	?>

</div>


<?php
if( vtt_has_page_description() ) 
	echo '<div class="description">' . vtt_get_page_description() . '</div>';
?>


<?php
if( !have_posts() ) {

	echo '<p>No posts.</p>';

} else {
		
	echo apply_filters('collections_before_post_listing','');
	
	$number = 1;
	while( have_posts() ):
		the_post();
		
		if (has_filter('collections_post_listing')) {
			$post_listing = "<h2 class='entry-title'><a href=".get_permalink($post->ID).">".$post->post_title."</a></h2>";
			echo apply_filters('collections_post_listing', $post_listing, $post, $number);
		} else {
			vtt_get_template_part( 'listing', 'post', vtt_get_post_type() );
		}
		$number ++;
	endwhile;
	
	echo apply_filters('collections_after_post_listing','');

	vtt_get_template_part( 'pagination', 'other', vtt_get_queried_object_type() );

};
?>
