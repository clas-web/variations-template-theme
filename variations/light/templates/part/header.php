<?php //vtt_print('part:header'); ?>
<?php
global $vtt_config, $post;


// Check if the header should be the current post's featured image.
$image = false;
if( 'header' === $vtt_config->get_value('featured-image-position') )
	$image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large' );


// If no featured image is found, then get the header image from the theme mods.
if( $image === false )
	list( $header_url, $header_width, $header_height ) = array_values( vtt_get_header_image() );
else
	list( $header_url, $header_width, $header_height ) = $image; 


// Get the header title position and contents.
$position = $vtt_config->get_value( 'header-title-position' );
$hide_title = $vtt_config->get_value( 'header-title-hide' );
$title = get_option( 'blogname' );
$title_link_default = $vtt_config->get_value( 'blogname_url_default' );
$description = get_option( 'blogdescription' );
$description_link_default = $vtt_config->get_value( 'blogdescription_url_default' );

if( $title_link_default )
	$title_link = get_site_url();
else
	$title_link = $vtt_config->get_value( 'blogname_url' );
if( $description_link_default )
	$description_link = get_site_url();
else
	$description_link = $vtt_config->get_value( 'blogdescription_url' );


// Calculate the text color and background colors.
$text_color = get_theme_mod(
	'header_textcolor', 
	get_theme_support( 'custom-header', 'default-text-color' )
);
if( $text_color ) $text_color = "color:#$text_color;";

$text_bgcolor = get_theme_mod(
	'header_textbgcolor', 
	get_theme_support( 'custom-header', 'default-text-bgcolor' )
);
if( $text_bgcolor )
{
	if( strpos($text_bgcolor, 'rgb') !== false || strpos($text_bgcolor, '#') !== false )
		$text_bgcolor = "background-color:$text_bgcolor;";
	else
		$text_bgcolor = "background-color:#$text_bgcolor;";
}

$text_style = implode( ';', array($text_color, $text_bgcolor) );


/**
 * Prints the header title box.
 * @param  mixed  $title_box_height  The header height in pixels or 'auto'.
 * @param  string  $position  The h and v position of the title box.
 * @param  string  $title  The title text.
 * @param  string  $title_link  The title link.
 * @param  string  $description  The description text.
 * @param  string  $description_link  The description link.
 * @param  string  $text_style  The css styles for the text (text color and bg color).
 */
if( !function_exists('vtt_title_box') ):
function vtt_title_box( $title_box_height, $position, $title, $title_link, $description, $description_link, $text_style )
{
	if( is_int($title_box_height) ) $title_box_height .= 'px';
	?>
		<div id="title-box-placeholder">
		<div id="title-box-wrapper">
		<div id="title-box" class="<?php echo $position; ?>">
		
		<?php
		if( !empty($title) ):
			$html = '<div class="name" style="'.$text_style.'">'.$title.'</div>';
			if( !empty($title_link) ):
				echo vtt_get_anchor( $title_link, null, null, $html );
			else:
				echo '<span>'.$html.'</span>';
			endif;
		endif;
		
		if( !empty($description) ):
			$html = '<div class="description" style="'.$text_style.'">'.$description.'</div>';
			if( !empty($description_link) ):
				echo vtt_get_anchor( $description_link, null, null, $html );
			else:
				echo '<span>'.$html.'</span>';
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
if( $header_url && strpos($position, 'vabove') === false )
	$responsive_overlap = 'responsive-overlap';
?>


<?php // Responsive (mobile) header ?>
<div id="responsive-title" class="clearfix" style="<?php echo $text_style; ?>">
<div class="relative-wrapper">

<div class="logo icon-button"></div>

<div class="title"><div><div>
<?php
if( !empty($title) ):
	$html = '<div class="name" style="'.$text_color.'">'.$title.'</div>';
	if( !empty($title_link) ):
		echo vtt_get_anchor( $title_link, null, null, $html );
	else:
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
		if( !$hide_title && strpos($position, 'vabove') !== false ):
			echo '<div id="full-title">';
			vtt_title_box( 'auto', $position, $title, $title_link, $description, $description_link, $text_style );
			echo '</div>';
		endif;
	?>

	<div id="header">
	
	<?php $header_type = get_theme_mod('header_type');
	if ($header_type == 'image' || $image != false || empty($header_type)) { 
		if (!get_theme_mod('header_home_only') || (get_theme_mod('header_home_only')&& is_front_page()) || empty($header_type)){	?>
		<div class="masthead" style="background-image:url('<?php echo $header_url; ?>'); width:<?php echo $header_width; ?>px; height:<?php echo $header_height; ?>px;"></div>
		<?php }} elseif ( $header_type == 'slider' )  { 
		if (!get_theme_mod('header_home_only') || (get_theme_mod('header_home_only')&& is_front_page())){		?>
		<div id="banner-wrapper">
		<div id="banner">
		<div class="placeholder"></div>
		<?php if (get_theme_mod( 'header_slider') != 0) soliloquy( get_theme_mod( 'header_slider') ); ?>
		</div><!-- #banner -->
		</div><!-- #banner-wrapper -->
		<?php }} ?>
	<?php
				if( !$hide_title && strpos($position, 'vabove') === false ):
					echo '<div id="full-title">';
					vtt_title_box( $header_height, $position, $title, $title_link, $description, $description_link, $text_style );
					echo '</div>';
				endif;
			?>
		
		<!--</div> .masthead -->
	
	</div><!-- #header -->
</div><!-- #header-wrapper -->



