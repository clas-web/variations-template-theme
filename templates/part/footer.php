
<?php //vtt_print('PART: footer'); ?>
<?php global $vtt_config, $vtt_mobile_support, $vtt_template_vars; ?>


<div id="footer-wrapper" class="clearfix">
	<div id="footer" class="clearfix">

<?php
$widgets = wp_get_sidebars_widgets();
$footer_widgets = array();
$footer_widgets_count = 0;
$widget_area_class = '';
$fw = -1;
$lw = -1;
for( $i = 0; $i < 4; $i++ )
{
	$widget_area = 'vtt-footer-'.($i+1);
	$footer_widgets[$widget_area] = false;
	if( array_key_exists($widget_area, $widgets) && count($widgets[$widget_area]) )
	{
		$footer_widgets[$widget_area] = true;
		$footer_widgets_count++;
		$widget_area_class .= ' widget-'.($i+1);
		if( $fw < 0 ) $fw = $i;
		if( $lw < $i ) $lw = $i;
	}
}
$widget_area_class = trim($widget_area_class);
?>

<div class="widget-area num-cols-<?php echo $footer_widgets_count ?> <?php echo $widget_area_class; ?> clearfix">
	<?php
	$i = 0;
	foreach( $footer_widgets as $widget_area => $show_area ):
		if( $show_area ):
			$widget_class = $widget_area;
			if( $fw == $i ) $widget_class .= ' first-widget';
			if( $lw == $i ) $widget_class .= ' last-widget';
			$widget_class = trim($widget_class);
			?><div class="widget-column <?php echo $widget_class; ?>"><?php
			dynamic_sidebar( $widget_area );
			?></div><?php
		else:
			?><div style="display:none;"><?php
			dynamic_sidebar( $widget_area );
			?></div><?php
		endif;
		$i++;
	endforeach; ?>
</div>

<?php if( $vtt_mobile_support->is_mobile || $vtt_mobile_support->use_mobile_site ): ?>

	<div class="mobile-links">

	<?php if( $vtt_mobile_support->use_mobile_site ): ?>
		<a href="<?php echo vtt_get_page_url(); ?>?full">Full Site</a> | Mobile Site
	<?php else: ?>
		Full Site | <a href="<?php echo vtt_get_page_url(); ?>?mobile">Mobile Site</a>
	<?php endif; ?>
	
	</div> <!-- .mobile-links -->

<?php endif; ?>
		
	</div><!-- #footer -->
</div><!-- #footer-wrapper -->

