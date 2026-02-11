<?php // vtt_print('default:other:pagination'); ?>


<?php
global $wp_query;

if( $wp_query->max_num_pages > 1 ):

	?>
	<nav id="page-navigation" aria-label="<?php esc_attr_e( 'Posts navigation', 'variations-template-theme' ); ?>">
		<div class="nav-next">
			<?php next_posts_link( '&laquo; Older Posts' ); ?>
		</div>
		<div class="nav-prev">
			<?php previous_posts_link( 'Newer Posts &raquo;' ); ?>
		</div>
	</nav>
	<?php

endif;
?>
