<?php // vtt_print('default:part:main'); ?>

<?php
// From News Hub variation, determine if Featured Story option is selected
global $nhs_section;

if ( ! function_exists( 'nhs_get_wpquery_section' ) ) {

	// Get Feature Image
	$image = wp_get_attachment_image_src(
		get_post_thumbnail_id( $post_id ),
		'full'
	)[0];

} else {

	// For News Hub sections
	$nhs_section = nhs_get_wpquery_section();
	$post        = $nhs_section->get_single_story( $post );
	extract( $post->nhs_data );
	$wide_header = false;
	switch ( $nhs_section->thumbnail_image ) :
		case 'landscape':
		case 'normal':
		case 'embed':
			if ( $image ) :
				$wide_header = true;
				endif;
			break;
		default:
			break;
	endswitch;
}

?>

<div id="main-wrapper" class="clearfix">

<?php // Using Featured Story from News Hub variation ?>
	<?php if ( vtt_is_featured() ) : ?>

	<div class="feature-wrapper break-out">
		<?php if ( $image ) : ?>		
			<div class="wide-header" title="Featured Image" style="background-image:url(<?php echo $image; ?>)"></div>
			<div class="featured-meta">
		<?php else : ?>
			<div class="featured-meta-no-image">
		<?php endif; ?>
				<div id="feature-title">
					<h1><?php echo vtt_get_page_title(); ?></h1>
				</div><!-- #feature-title -->
			</div><!-- #featured-meta -->
		</div><!-- #feature-wrapper break-out -->
		<div id="main" class="feature" role="main">

	<?php else : ?>

		<!-- Not using Featured Story -->
		<div id="main" role="main">

	<?php endif; ?>

	<?php
		echo '<div id="full-menu" class="hide">';

		// Add header menu if needed
	if ( ! has_nav_menu( 'header-navigation' ) ) {
		vtt_get_template_part( 'header-menu', 'part', vtt_get_queried_object_type() );
	}

		// Add sidebars if needed
	if ( vtt_get_theme_file_path( 'templates/part/sidebar.php' ) ) {
		vtt_get_template_part( 'sidebar', 'part', vtt_get_queried_object_type() );
	} else {
		if ( vtt_get_theme_file_path( 'templates/part/sidebar-left.php' ) ) {
			vtt_get_template_part( 'sidebar-left', 'part', vtt_get_queried_object_type() );
		}
		if ( vtt_get_theme_file_path( 'templates/part/sidebar-right.php' ) ) {
			vtt_get_template_part( 'sidebar-right', 'part', vtt_get_queried_object_type() );
		}
	}

		echo '</div><!-- #full-menu -->';
	?>

	<?php vtt_get_template_part( 'content', 'part', vtt_get_queried_object_type() ); ?>

		</div><!-- #main -->
	</div><!-- #main-wrapper -->
