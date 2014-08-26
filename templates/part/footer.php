
<?php //uncc_print('PART: footer'); ?>
<?php global $uncc_config, $uncc_mobile_support, $uncc_template_vars; ?>


<div id="footer-wrapper" class="clearfix">
	<div id="footer" class="clearfix">


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

