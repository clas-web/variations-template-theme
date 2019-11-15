<?php // vtt_print('part:header'); ?>
<?php
global $vtt_config, $post;

$featured_image          = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
$featured_image_position = $vtt_config->get_value( 'featured-image-position' );

$header_type                                       = get_theme_mod( 'header_type' );
list( $header_url, $header_width, $header_height ) = array_values( vtt_get_header_image() );

if ( $featured_image ) { list( $featured_url, $featured_width, $featured_height ) = $featured_image;
}

$constrain_header = '';
if ( get_theme_mod( 'header_constrain_width' ) == 1 ) { $constrain_header = 'constrain-header';
}

// Get the header title position and contents.
$position   = $vtt_config->get_value( 'header-title-position' );
$hide_title = $vtt_config->get_value( 'header-title-hide' );

if ( has_filter( 'collections_header_title' ) ) {
	$title = apply_filters( 'collections_header_title', $title );
} else {
	$title = get_option( 'blogname' );
}

$title_link_default       = $vtt_config->get_value( 'blogname_url_default' );
$description              = get_option( 'blogdescription' );
$description_link_default = $vtt_config->get_value( 'blogdescription_url_default' );
$longtitleclass           = '';
if ( strlen( $title ) > 30 ) { $longtitleclass = 'long-title';
}

if ( $title_link_default ) {
	$title_link = get_site_url();
} else {    $title_link = $vtt_config->get_value( 'blogname_url' );
}
if ( $description_link_default ) {
	$description_link = get_site_url();
} else {    $description_link = $vtt_config->get_value( 'blogdescription_url' );
}


// Calculate the text color and background colors.
$text_color = get_theme_mod(
	'header_textcolor',
	get_theme_support( 'custom-header', 'default-text-color' )
);
if ( $text_color ) { $text_color = "color:#$text_color;";
}

$text_bgcolor = get_theme_mod(
	'header_textbgcolor',
	get_theme_support( 'custom-header', 'default-text-bgcolor' )
);
if ( $text_bgcolor ) {
	if ( strpos( $text_bgcolor, 'rgb' ) !== false || strpos( $text_bgcolor, '#' ) !== false ) {
		$text_bgcolor = "background-color:$text_bgcolor;";
	} else {        $text_bgcolor = "background-color:#$text_bgcolor;";
	}
}

$text_style = implode( ';', array( $text_color, $text_bgcolor ) );


/**
 * Prints the header title box.
 *
 * @param  mixed  $title_box_height  The header height in pixels or 'auto'.
 * @param  string  $position  The h and v position of the title box.
 * @param  string  $title  The title text.
 * @param  string  $title_link  The title link.
 * @param  string  $description  The description text.
 * @param  string  $description_link  The description link.
 * @param  string  $text_style  The css styles for the text (text color and bg color).
 */
if ( ! function_exists( 'vtt_title_box' ) ) :
	function vtt_title_box( $title_box_height, $position, $title, $title_link, $longtitleclass, $description, $description_link, $text_style ) {
		if ( is_int( $title_box_height ) ) { $title_box_height .= 'px';
		}
		?>
		<div id="title-box-placeholder">
		<div id="title-box-wrapper">
		<div id="title-box" class="<?php echo $position; ?>">
		
		<?php
		if ( ! empty( $title ) ) :
			$html = '<div class="name ' . $longtitleclass . '" style="' . $text_style . '">' . $title . '</div>';
			if ( ! empty( $title_link ) ) :
				echo vtt_get_anchor( $title_link, null, null, $html );
			else :
				echo $html;
			endif;
			endif;

		if ( ! empty( $description ) ) :
			$html = '<div class="description" style="' . $text_style . '">' . $description . '</div>';
			if ( ! empty( $description_link ) ) :
				echo vtt_get_anchor( $description_link, null, null, $html );
			else :
				// Need to remove the $html from within span for validation
				echo '<span>' . $html . '</span>';
			endif;
			endif;
		?>
		
		</div><!-- #title-box -->
		</div><!-- #title-box-wrapper -->
		</div><!-- #title-box-placeholder -->
		<?php
	}
endif;


// Determine if the responsive (mobile) header overlaps the header image.
$responsive_overlap = '';
if ( $header_url && strpos( $position, 'vabove' ) === false ) {
	$responsive_overlap = 'responsive-overlap';
}
?>


<?php // Responsive (mobile) header ?>
<div id="responsive-title" class="clearfix" style="<?php echo $text_style; ?>">
<div class="relative-wrapper">

<div class="logo icon-button"></div>

<div class="title"><div><div>
<?php
if ( ! empty( $title ) ) :
	$html = '<div class="name ' . $longtitleclass . '">' . $title . '</div>';
	if ( ! empty( $title_link ) ) :
		echo vtt_get_anchor( $title_link, null, null, $html );
	else :
		echo $html;
	endif;
endif;
?>
</div></div></div><!-- .title -->

</div><!-- .relative-wrapper -->
</div><!-- #responsive-title -->


<?php // Tablet and Desktop header ?>
	<div id="header-wrapper" class="<?php echo $responsive_overlap; ?> clearfix">

		<?php
		if ( ! $hide_title && strpos( $position, 'vabove' ) !== false ) :
			echo '<div id="full-title">';
			vtt_title_box( 'auto', $position, $title, $title_link, $longtitleclass, $description, $description_link, $text_style );
			echo '</div>';
			endif;
		?>


		<div id="header" class="mini-header <?php echo $constrain_header; ?>" >

		<?php
		// IF on the front page
		// OR the featured image is not set to display in the header and the header image is shown on all pages
		// OR there is no featured image and the header image is shown on all pages
		if ( is_front_page() || ( $featured_image_position != 'header' && ! get_theme_mod( 'header_home_only' ) ) || ( ! $featured_url && ! get_theme_mod( 'header_home_only' ) ) ) {
			if ( $header_type == 'image' || empty( $header_type ) ) {
				?>
				<div class="header-title" style="background-image:url('<?php echo $header_url; ?>'); width:<?php echo $header_width; ?>px; height:<?php echo $header_height; ?>px;"></div>
				<?php
			} elseif ( $header_type == 'slider' ) {
				?>
				<div id="header-image">
				<?php if ( get_theme_mod( 'header_slider' ) != 0 ) { soliloquy( get_theme_mod( 'header_slider' ) );} ?>
				</div><!-- #header-image -->
				<?php
			}
		} else {
			if ( $featured_image_position == 'header' ) {
				?>
				<div class="header-title" style="background-image:url('<?php echo $featured_url; ?>'); width:<?php echo $featured_width; ?>px; height:<?php echo $featured_height; ?>px;"></div>
				<?php
			}
		}

		?>
		<?php
		if ( ! $hide_title && strpos( $position, 'vabove' ) === false ) :
			echo '<div id="full-title">';
			vtt_title_box( $header_height, $position, $title, $title_link, $longtitleclass, $description, $description_link, $text_style );
			echo '</div>';
					endif;
		?>

		</div><!-- #header -->
	</div><!-- #header-wrapper -->
