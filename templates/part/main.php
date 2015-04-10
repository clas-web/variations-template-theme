

<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars; ?>


<div id="main-wrapper" class="clearfix">
	<div id="main" class="clearfix">
	
	
	<?php
	uncc_get_template_part( 'sidebar', 'part', 'left' );
	uncc_get_template_part( 'content', 'part' );
	uncc_get_template_part( 'sidebar', 'part', 'right' );
	?>
	
	
	</div><!-- #main -->
</div><!-- #main-wrapper -->

