<?php global $vtt_config, $vtt_mobile_support; ?>

<!DOCTYPE html>

<?php
// Ensure lang attribute is always present, even if language_attributes() omits it.
$vtt_language_attributes = get_language_attributes();
if ( strpos( $vtt_language_attributes, 'lang=' ) === false ) {
	$vtt_lang = get_bloginfo( 'language' );
	if ( empty( $vtt_lang ) ) {
		$vtt_lang = 'en-US';
	}
	$vtt_language_attributes = 'lang="' . esc_attr( $vtt_lang ) . '" ' . $vtt_language_attributes;
}
?>
<html class="no-js" <?php echo $vtt_language_attributes; ?>>

<head>

	<meta charset="<?php bloginfo('charset'); ?>" />
	<title><?php echo bloginfo('name').' | '.wp_strip_all_tags(vtt_get_page_title()); ?></title>
	
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<link rel="shortcut icon" href="<?php echo vtt_get_theme_file_url('images/favicon.ico', 'all', false); ?>" />
	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="stylesheet" type="text/css" href="<?php echo vtt_get_theme_file_url('styles/normalize.css'); ?>">

	<?php wp_head(); ?>

	<script type="text/javascript">
		var htmlTag = document.getElementsByTagName('html');
		htmlTag[0].className = htmlTag[0].className.replace( 'no-js', '' );
		var is_mobile = <?php echo ($vtt_mobile_support->is_mobile) ? 'true' : 'false'; ?>;
		var use_mobile_site = <?php echo ($vtt_mobile_support->use_mobile_site) ? 'true' : 'false'; ?>;
	</script>

</head>

<?php
	$class = array();
	if( $vtt_mobile_support->use_mobile_site ) $class[] = 'mobile-site'; else $class[] = 'full-site';
?>
<body <?php body_class($class); ?> >

<a class="screen-reader-text" href="#main"><?php esc_html_e( 'Skip to main content', 'variations-template-theme' ); ?></a>

<div id="site-outside-wrapper" class="clearfix">
<div id="site-inside-wrapper" class="clearfix">

	<?php
	$vtt_main_template_parts = array( 'header-title', 'main', 'footer' );
	$vtt_main_template_parts = apply_filters( 'vtt-main-template-parts', $vtt_main_template_parts );

	foreach( $vtt_main_template_parts as $part )
	{
		vtt_get_template_part( $part, 'part', vtt_get_queried_object_type() );
	}
	?>

</div> <!-- #site-inside-wrapper -->
</div> <!-- #site-outside-wrapper -->

<?php wp_footer(); ?>

</body>

</html>
