<?php



if( !function_exists('uncc_echo_name') ):
function uncc_input_name_e()
{
	echo 'uncc-options'.uncc_input_name( func_get_args() );
}
endif;

if( !function_exists('uncc_echo_name') ):
function uncc_input_name()
{
	$args = func_get_args();
	if( count($args) == 1 && is_array($args[0]) ) $args = $args[0];
	
	$name = '';
	
	foreach( $args as $arg )
	{
		if( is_array($arg) )
			$name .= uncc_input_name( $arg );
		else
			$name .= "[$arg]";
	}

	return $name;
}
endif;



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



