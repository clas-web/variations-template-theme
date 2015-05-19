

<?php global $vtt_config, $vtt_mobile_support, $vtt_template_vars; ?>


<div id="main-wrapper" class="clearfix">
	<div id="main" class="clearfix">
	
	
	<?php
	vtt_get_template_part( 'sidebar-left', 'part', vtt_get_queried_object_type() );
	vtt_get_template_part( 'content', 'part', vtt_get_queried_object_type() );
	vtt_get_template_part( 'sidebar-right', 'part', vtt_get_queried_object_type() );
	?>
	
	
	</div><!-- #main -->
</div><!-- #main-wrapper -->

