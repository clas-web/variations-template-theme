
<?php //vtt_print('PART: standard'); ?>
<?php global $vtt_config, $vtt_mobile_support, $vtt_template_vars; ?>


<!DOCTYPE html>

<!--[if lt IE 7 ]> <html class="ie6 old-ie no-js" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7 ]>    <html class="ie7 old-ie no-js" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]>    <html class="ie8 old-ie no-js" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="new-browser no-js" <?php language_attributes(); ?>>   <!--<![endif]-->

<head>

	<meta charset="<?php bloginfo('charset'); ?>" />
	<title><?php echo bloginfo('name').' | '.$vtt_template_vars['page-title']; ?></title>
	
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<link rel="shortcut icon" href="<?php echo vtt_get_theme_file_url('images/favicon.ico', 'both', false); ?>" />
	
	<?php if( $vtt_mobile_support->is_mobile ): ?>
		<meta name="viewport" content="user-scalable=no, initial-scale=1, minimum-scale=1, maximum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi">
	<?php endif; ?>
	
	<?php wp_head(); ?>

	<script type="text/javascript">
		jQuery('html').removeClass('no-js');
		var is_mobile = <?php echo ($vtt_mobile_support->is_mobile) ? 'true' : 'false'; ?>;
		var use_mobile_site = <?php echo ($vtt_mobile_support->use_mobile_site) ? 'true' : 'false'; ?>;
	</script>

</head>

<?php
	$class = array();
	if( $vtt_mobile_support->use_mobile_site ) $class[] = 'mobile-site'; else $class[] = 'full-site';
?>
<body <?php body_class($class); ?> >

<div id="overlay"></div>

<div id="site-outside-wrapper" class="clearfix">
<div id="site-inside-wrapper" class="clearfix">

	<?php
	vtt_get_template_part( 'header', 'part', vtt_get_queried_object_type() );
	vtt_get_template_part( 'header-menu', 'part', vtt_get_queried_object_type() );
	vtt_get_template_part( 'main', 'part', vtt_get_queried_object_type() );
	vtt_get_template_part( 'footer', 'part', vtt_get_queried_object_type() );
	?>

</div> <!-- #site-inside-wrapper -->
</div> <!-- #site-outside-wrapper -->

<?php wp_footer(); ?>

</body>

</html>

