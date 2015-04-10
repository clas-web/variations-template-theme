<?php


/**
 * 
 */
if( !function_exists('uncc_name_e') ):
function uncc_name_e()
{
	$args = func_get_args();
	$fargs = array();
    array_walk_recursive(
    	$args,
    	function( $item, $key ) use (&$fargs)
    	{
    		$fargs[] = $item;
    	}
    );
	$fargs = array_merge( array(UNC_CHARLOTTE_THEME_OPTIONS), $fargs );
	
	apl_name_e( $fargs );
}
endif;


/**
 * 
 */
if( !function_exists('uncc_string_to_value') ):
function uncc_string_to_value( $value )
{
	if( is_array($value) ) $value = array_map( 'uncc_string_to_value', $value );
	if( !is_string($value) ) return $value;
	
	switch( substr( $value, 0, 2 ) )
	{
		case 'b:':
			$value = ( ($value === 'b:true') ? true : false );
			break;
			
		case 'i:':
			$value = intval( substr($value, 2) );
			break;
	}
	
	return $value;
}
endif;

