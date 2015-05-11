

<?php //vtt_print('PART: header'); ?>
<?php global $vtt_config, $vtt_mobile_support, $vtt_template_vars, $post; ?>
<?php
$featured_image_position = $vtt_config->get_theme_value( 'featured-image-position' );

$image = false;
if( $featured_image_position === 'header' )
{
	$image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large' );
}

if( $image === false )
	list( $header_url, $header_width, $header_height ) = array_values( vtt_get_header_image() );
else
	list( $header_url, $header_width, $header_height ) = $image; 


$position = $vtt_config->get_theme_value( 'header-title-position' );
$hide_title = $vtt_config->get_theme_value( 'header-title-hide' );
$title = $vtt_config->get_theme_value( 'blogname' );
$title_link = $vtt_config->get_theme_value( 'blogname_url' );
$description = $vtt_config->get_theme_value( 'blogdescription' );
$description_link = $vtt_config->get_theme_value( 'blogdescription_url' );

if( $title == '/' )				$title = get_bloginfo('name');
if( $title_link == '/' )		$title_link = get_site_url();
if( $description == '/' )		$description = get_bloginfo('description');
if( $description_link == '/' )	$description_link = get_site_url();


if( !function_exists('vtt_title_box') ):
function vtt_title_box( $title_box_height, $position, $title, $title_link, $description, $description_link )
{
	$style = '';
	
	$text_color = get_theme_mod(
		'header_textcolor', 
		get_theme_support( 'custom-header', 'default-text-color' )
	);
	if( $text_color ) $style .= "color:#$text_color;";

	$text_bgcolor = get_theme_mod(
		'header_textbgcolor', 
		get_theme_support( 'custom-header', 'default-text-bgcolor' )
	);
	if( $text_bgcolor ) $style .= "background-color:#$text_bgcolor;";

	if( is_int($title_box_height) ) $title_box_height .= 'px';
	?>
		<div id="title-box-placeholder">
		<div id="title-box-wrapper" style="height:<?php echo $title_box_height; ?>;">
		<div id="title-box" class="<?php echo $position; ?>">
		
		<?php
		if( !empty($title) ):
			$html = '<div class="name" style="'.$style.'">'.$title.'</div>';
			if( !empty($title_link) ):
				echo vtt_get_anchor( $title_link, null, null, $html );
			else:
				echo $html;
			endif;
		endif;
		
		if( !empty($description) ):
			$html = '<div class="description" style="'.$style.'">'.$description.'</div>';
			if( !empty($description_link) ):
				echo vtt_get_anchor( $description_link, null, null, $html );
			else:
				echo $html;
			endif;
		endif;
		?>
		
		</div><!-- #title-box -->
		</div><!-- #title-box-wrapper -->
		</div><!-- #title-box-placeholder -->
	<?php
}
endif;
?>


<div id="header-wrapper" class="clearfix">

	<?php
		if( !$hide_title && strpos($position, 'vabove') !== false ):
			vtt_title_box( 'auto', $position, $title, $title_link, $description, $description_link );
		endif;
	?>

	<div id="header" class="clearfix">

	<div class="masthead" style="background-image:url('<?php echo $header_url; ?>'); width:<?php echo $header_width; ?>px; height:<?php echo $header_height; ?>px;">
	
		<?php
			if( !$hide_title && strpos($position, 'vabove') === false ):
				vtt_title_box( $header_height, $position, $title, $title_link, $description, $description_link );
			endif;
		?>
		
	</div><!-- .masthead -->
	

	<a href="<?php echo get_site_url(); ?>" title="<?php echo get_bloginfo('name'); ?>" class="click-box"></a>

	</div><!-- #header -->
</div><!-- #header-wrapper -->

