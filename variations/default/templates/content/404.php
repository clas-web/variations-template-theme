<?php //vtt_print('default:content:404'); ?>


<div class="page-title">
	<h1>Entry not found.</h1>
</div>


<div class="not-found">

	<p>The article you were looking for could not be found.  Try searching for it.</p>
	<form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>" >
		<label class="screen-reader-text" for="s">Search for:</label>
		<div class="textbox_wrapper"><input type="text" value="" name="s" id="s" /></div>
		<input type="submit" id="searchsubmit" value="Search" />
	</form>

</div>
