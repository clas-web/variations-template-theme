<?php global $vtt_config, $vtt_mobile_support; ?>

<!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

<head>

	<meta charset="<?php bloginfo('charset'); ?>" />
	<title><?php echo bloginfo('name').' | '.wp_strip_all_tags(vtt_get_page_title()); ?></title>
	
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<link rel="shortcut icon" href="<?php echo vtt_get_theme_file_url('images/favicon.ico', 'all', false); ?>" />
	
	<meta name="viewport" content="user-scalable=no, initial-scale=1, minimum-scale=1, maximum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi">
	
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

<div id="site-outside-wrapper" class="clearfix">
<div id="site-inside-wrapper" class="clearfix">

	<?php
	$vtt_main_template_parts = array( 'header', 'main', 'footer' );
	$vtt_main_template_parts = apply_filters( 'vtt_main_template_parts', $vtt_main_template_parts );

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
