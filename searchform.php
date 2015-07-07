
<form role="search" method="get" class="searchform" action="<?php echo esc_url( home_url('/') ) ?>">
	<div>
		<label class="screen-reader-text" for="s">Search for:</label>
		<div class="textbox_wrapper">
			<input id="s" name="s" type="text" value="<?php echo get_search_query(); ?>" placeholder="Search site..." />
		</div>
		<button id="searchsubmit" value="Search">Search</button>
	</div>
</form>
