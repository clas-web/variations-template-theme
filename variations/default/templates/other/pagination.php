<?php //vtt_print('default:other:pagination'); ?>


<?php
if( $wp_query->max_num_pages > 1 ):

	?>
	<div id="page-navigation" role="navigation">
		<div class="nav-next">
			<?php next_posts_link( '&laquo; Older Posts' ); ?>
		</div>
		<div class="nav-prev">
			<?php previous_posts_link( 'Newer Posts &raquo;' ); ?>
		</div>
	</div>
	<?php

endif;
?>
