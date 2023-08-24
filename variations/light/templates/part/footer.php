<?php //vtt_print('default:part:footer'); ?>


<div id="footer-wrapper">
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
		if( array_key_exists($widget_area, $widgets) && is_countable($widgets[$widget_area]) && count($widgets[$widget_area]) )
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


<div class="widget-area num-cols-<?php echo $footer_widgets_count ?> <?php echo $widget_area_class; ?>">
	<div class="widget-row">

	
	<?php
	if( $footer_widgets_count == 4 ):
		?>
		<div class="widget-row-grid">
		<?php
	endif;
	
	$i = 0;
	foreach( $footer_widgets as $widget_area => $show_area ):
		
		if( $show_area ):
			$widget_class = $widget_area;
			if( $fw == $i ) $widget_class .= ' first-widget';
			if( $lw == $i ) $widget_class .= ' last-widget';
			$widget_class = trim($widget_class);
			?>
			
			<div class="widget-column <?php echo $widget_class; ?>">
			<div class="widgets-wrapper">
			<?php dynamic_sidebar( $widget_area ); ?>
			</div>
			</div>

			<?php
		else:
			?>
			
			<div style="display:none;">
			<?php dynamic_sidebar( $widget_area ); ?>
			</div>

			<?php
		endif;
		
		if( $footer_widgets_count == 4 && $i == 1 ):
			?>

			</div><div class="widget-row-grid">

			<?php
		endif;
		
		$i++;
	
	endforeach;

	if( $footer_widgets_count == 4 ):
		?>
		</div>
		<?php
	endif;
	?>

	
	</div>
</div>

		
	</div><!-- #footer -->
</div><!-- #footer-wrapper -->

