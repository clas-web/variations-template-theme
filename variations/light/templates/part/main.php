<?php // vtt_print('default:part:main'); ?>

<?php
// From News Hub variation, determine if Featured Story option is selected
global $nhs_section;
$featured = 'not featured';

// Feature Story only works for single posts
if ( is_single() ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( 'advanced-custom-fields/acf.php' ) ) {
		$featured = get_field( 'featured_story' );
		if ( $featured ) {
			$featured = $featured[0];
		}
	}
}

	// if ( ! function_exists( 'nhs_get_wpquery_section' ) ) {
		// print_r( $post );
// } else {
	// $nhs_section = nhs_get_wpquery_section();
	// 	$post        = $nhs_section->get_single_story( $post );W
	// 	extract( $post->nhs_data );
	// 	$wide_header = false;

	// 	switch ( $nhs_section->thumbnail_image ) :
	// 		case 'landscape':
	// 		case 'normal':
	// 		case 'embed':
	// 			if ( $image ) :
	// 				$wide_header = true;
	// 			endif;
	// 			break;
	// 		default:
	// 			break;
	// endswitch;
	// }
// }


?>

<div id="main-wrapper" class="clearfix">

	<?php
	// Using Featured Story from News Hub variation
	if ( 'featured' === $featured ) :
		?>
		<div class="feature-wrapper break-out">
			<div class="wide-header" title="Featured Image" style="background-image:url(<?php echo $image; ?>)"></div>
			<div class="featured-meta">
				<div id="feature-title">
					<h1><?php echo vtt_get_page_title(); ?></h1>
				</div>
			</div>
		</div>
		<div id="main" class="feature" role="main">
			<!-- <div id="full-menu" class="hide">			 -->

		<?php
		// Not using Featured Story
		else :
			?>
			<div id="main" role="main">
			<?php
		endif;

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

		?>
		</div><!-- #full-menu -->

		<?php
		// Get content.php
		vtt_get_template_part( 'content', 'part', vtt_get_queried_object_type() );
		?>
	</div><!-- #main -->
</div><!-- #main-wrapper -->
