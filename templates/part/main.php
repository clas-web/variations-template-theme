

<?php global $vtt_config, $vtt_mobile_support, $vtt_template_vars; ?>


<div id="main-wrapper" clas="clearfix">
	<div id="main">
	
	
	<?php
	
	echo '<div id="full-menu">';
	vtt_get_template_part( 'header-menu', 'part', vtt_get_queried_object_type() );
	vtt_get_template_part( 'sidebar-left', 'part', vtt_get_queried_object_type() );
	vtt_get_template_part( 'sidebar-right', 'part', vtt_get_queried_object_type() );
	echo '</div>';

	vtt_get_template_part( 'content', 'part', vtt_get_queried_object_type() );
	?>
	
	
	</div><!-- #main -->
</div><!-- #main-wrapper -->

