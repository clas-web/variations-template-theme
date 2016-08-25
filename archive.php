<?php
/**
 * Displays the Event custom post type archive page.
 *
 * @package variations-template-theme
 */


// vtt_print( 'PAGE:archive.php' );

$page_title = 'Archives';
if( is_day() ):
	$page_title = sprintf( 'Daily Archives: %s', '<span>'.get_the_date().'</span>' );
elseif( is_month() ):
	$page_title = sprintf( 'Monthly Archives: %s', '<span>'.get_the_date('F Y').'</span>' );
elseif( is_year() ):
	$page_title = sprintf( 'Yearly Archives: %s', '<span>'.get_the_date('Y').'</span>' );
elseif( is_tax() ):
	$qo = get_queried_object();
	if( $qo && is_a( $qo, 'WP_Term' ) ) {
		$page_title = $qo->name;
		vtt_set_page_description( term_description( $qo->term_id, $qo->taxonomy ) );
	}
endif;

vtt_set_page_content_type( 'listing' );
vtt_set_page_title( $page_title );
vtt_render_page();
