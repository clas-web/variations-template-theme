<?php //vtt_print('default:part:main'); ?>


<div id="main-wrapper" class="clearfix">
	<div id="main" role="main">
	
	<?php
	echo '<div id="full-menu" class="hide">';
	vtt_get_template_part( 'header-menu', 'part', vtt_get_queried_object_type() );
	vtt_get_template_part( 'sidebar-left', 'part', vtt_get_queried_object_type() );
	vtt_get_template_part( 'sidebar-right', 'part', vtt_get_queried_object_type() );
	echo '</div>';

	vtt_get_template_part( 'content', 'part', vtt_get_queried_object_type() );
	?>
	
	</div><!-- #main -->
</div><!-- #main-wrapper -->

