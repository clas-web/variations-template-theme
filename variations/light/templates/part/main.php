<?php // vtt_print('default:part:main');

/* From News Hub variation */
global $nhs_section;
$featured = 'not featured';

if ( is_single() ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( 'advanced-custom-fields/acf.php' ) ) {
		$featured = get_field( 'featured_story' );
		if ( $featured ) { $featured = $featured[0];
		}
	}
	$nhs_section = nhs_get_wpquery_section();
	$post        = $nhs_section->get_single_story( $post );
	extract( $post->nhs_data );
	$wide_header = false;
	// echo $nhs_section->thumbnail_image;
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
/* From News Hub variation */
?>

<div id="main-wrapper" class="clearfix">

	<?php
// Using Featured Story from News Hub variation
if ( $wide_header && 'featured' === $featured ) :
	?>
	<div class="feature-wrapper break-out">
		<div class="wide-header" title="Featured Image" style="background-image:url(<?php echo $image; ?>)"></div>
		<div class="featured-meta">
			<div id="feature-title">
				<h1><?php echo vtt_get_page_title(); ?></h1>
			</div>
		</div>
	</div>
	<div id="main" class="feature">
		<div id="full-menu" class="hide">
			<?php vtt_get_template_part( 'sidebar', 'part', vtt_get_queried_object_type() ); ?>

			<?php
	// vtt_get_template_part( 'sidebar', 'part', vtt_get_queried_object_type() );
	// vtt_get_theme_file_path( 'variations/light/templates/part/page.php' );	
	/* From News Hub variation */
	?>



			<?php
	else :
		// Not using Featured Story
		echo "HELLO WORLD";
		?>
		

			<div id="main" role="main">
				<?php
		echo '<div id="full-menu" class="hide">';
		vtt_get_template_part( 'header-menu', 'part', vtt_get_queried_object_type() );
		vtt_get_template_part( 'sidebar-left', 'part', vtt_get_queried_object_type() );
		vtt_get_template_part( 'sidebar-right', 'part', vtt_get_queried_object_type() );
		// echo '</div>';
		endif;
	?>

			</div><!-- #full-menu -->

			<?php
// Get content.php
vtt_get_template_part( 'content', 'part', vtt_get_queried_object_type() );
?>
		</div><!-- #main -->
	</div><!-- #main-wrapper -->