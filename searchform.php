
<?php

$search_term = get_search_query();
$search_class = '';

if( !$search_term )
{
	$search_term = 'Search site...';
	$search_class = 'unused';
}

?>

<form role="search" method="get" class="searchform" action="<?php echo esc_url( home_url('/') ) ?>">
	<div>
		<label class="screen-reader-text" for="s">Search for:</label>
		<div class="textbox_wrapper">
			<input id="s" name="s" type="text" value="<?php echo $search_term; ?>" class="<?php echo $search_class; ?>" onfocus="if(this.getAttribute('class') == 'unused') { this.value = ''; this.setAttribute('class', ''); }" />
		</div>
		<input type="submit" id="searchsubmit" value="Search">
	</div>
</form>
