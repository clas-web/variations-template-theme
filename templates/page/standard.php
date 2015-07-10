<?php //vtt_print('PART: standard'); ?>
<?php global $vtt_config, $vtt_mobile_support, $vtt_template_vars; ?>


<!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

<head>

	<meta charset="<?php bloginfo('charset'); ?>" />
	<title><?php echo bloginfo('name').' | '.$vtt_template_vars['page-title']; ?></title>
	
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<link rel="shortcut icon" href="<?php echo vtt_get_theme_file_url('images/favicon.ico', 'all', false); ?>" />
	
	<meta name="viewport" content="user-scalable=no, initial-scale=1, minimum-scale=1, maximum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi">
	
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

<div id="responsive-menu" class="hide"></div><!-- #responsive-menu -->

<div id="site-wrapper" class="clearfix">
<div id="site" class="clearfix">

	<?php
	vtt_get_template_part( 'header', 'part', vtt_get_queried_object_type() );
	vtt_get_template_part( 'main', 'part', vtt_get_queried_object_type() );
	vtt_get_template_part( 'footer', 'part', vtt_get_queried_object_type() );
	?>

</div> <!-- #site -->
</div> <!-- #site-wrapper -->

<?php wp_footer(); ?>

</body>

</html>

