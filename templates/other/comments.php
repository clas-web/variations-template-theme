
<?php global $vtt_config, $vtt_mobile_support, $vtt_template_vars, $post, $wp_query; ?>

<?php if( post_password_required() ) return; ?>



<div id="comments" class="comments-area">

	<?php if( have_comments() ) : ?>
	
	<h2 class="comments-title">
		<?php
			printf( _n( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number() ),
				number_format_i18n( get_comments_number() ), get_the_title() );
		?>
	</h2>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
		<h1 class="screen-reader-text">Comment navigation</h1>
		<div class="nav-previous"><?php previous_comments_link( '&larr; Older Comments' ); ?></div>
		<div class="nav-next"><?php next_comments_link( 'Newer Comments &rarr;' ); ?></div>
	</nav><!-- #comment-nav-above -->
	<?php endif; // Check for comment navigation. ?>

	<ol class="comment-list">
		<?php
			wp_list_comments( array(
				'style'      => 'ol',
				'short_ping' => true,
				'avatar_size'=> 34,
			) );
		?>
	</ol><!-- .comment-list -->

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
		<h1 class="screen-reader-text">Comment navigation</h1>
		<div class="nav-previous"><?php previous_comments_link( '&larr; Older Comments' ); ?></div>
		<div class="nav-next"><?php next_comments_link( 'Newer Comments &rarr;' ); ?></div>
	</nav><!-- #comment-nav-below -->
	<?php endif; // Check for comment navigation. ?>

	<?php endif; // have_comments() ?>

	<?php comment_form(); ?>

</div><!-- #comments -->

