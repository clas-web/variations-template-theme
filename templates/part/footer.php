
<?php //uncc_print('PART: footer'); ?>
<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars; ?>


<div id="footer-wrapper" class="clearfix">
	<div id="footer" class="clearfix">

<?php
$widgets = wp_get_sidebars_widgets();
$footer_widgets = array();
$footer_widgets_count = 0;
for( $i = 0; $i < 4; $i++ )
{
	$widget_area = 'uncc-footer-'.($i+1);
	$footer_widgets[$widget_area] = false;
	if( array_key_exists($widget_area, $widgets) && count($widgets[$widget_area]) )
	{
		$footer_widgets[$widget_area] = true;
		$footer_widgets_count++;
	}
}
?>

<div class="widget-area num-cols-<?php echo $footer_widgets_count ?> clearfix">
	<?php
	foreach( $footer_widgets as $widget_area => $show_area ):
		if( $show_area ):
			?><div class="widget-column <?php echo $widget_area; ?>"><?php
			dynamic_sidebar( $widget_area );
			?></div><?php
		else:
			?><div style="display:none;"><?php
			dynamic_sidebar( $widget_area );
			?></div><?php
		endif;
	endforeach; ?>
</div>

<?php if( $uncc_mobile_support->is_mobile || $uncc_mobile_support->use_mobile_site ): ?>

	<div class="mobile-links">

	<?php if( $uncc_mobile_support->use_mobile_site ): ?>
		<a href="<?php echo uncc_get_page_url(); ?>?full">Full Site</a> | Mobile Site
	<?php else: ?>
		Full Site | <a href="<?php echo uncc_get_page_url(); ?>?mobile">Mobile Site</a>
	<?php endif; ?>
	
	</div> <!-- .mobile-links -->

<?php endif; ?>
		
	</div><!-- #footer -->
</div><!-- #footer-wrapper -->

