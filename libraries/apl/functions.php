<?php


/**
 * Print the content of a variable with a label as a "title".  The entire contents is 
 * enclosed in a <pre> block.
 * @param  mixed		$var	The variable to "dumped"/printed to screen.
 * @param  string|null  $label  The label/title of the variable information.
 */
if( !function_exists('apl_print') ):
function apl_print( $var, $label = null )
{
	echo '<pre>';
	
	if( $label !== null )
	{
		$label = print_r( $label, true );
		echo "<strong>$label:</strong><br/>";
	}
	
	var_dump($var);
	
	echo '</pre>';
}
endif;


/**
 * Prints the name of an input field.
 * @param  {args}  The keys of the input name.  For example:
 *                   apl_name_e( 'a', 'b', 'c' ) will echo "a[b][c]"
 *                   apl_name_e( array( 'a', 'b', 'c' ) ) will echo "a[b][c]"
 */
if( !function_exists('apl_name_e') ):
function apl_name_e()
{
	$args = func_get_args();
	if( count($args) > 0 )
	{
		if( is_array($args[0]) )
			echo call_user_func_array( 'apl_name', $args[0] );
		else
			echo call_user_func_array( 'apl_name', $args );
	}
}
endif;


/**
 * Constructs the name of an input field.
 * @param   array|{args}  The keys of the input name.  For example:
 *                          apl_name( 'a', 'b', 'c' ) will return "a[b][c]"
 *                          apl_name( array( 'a', 'b', 'c' ) ) will return "a[b][c]"
 * @return  string		The constructed input name. 
 */
if( !function_exists('apl_name') ):
function apl_name()
{
	$name = '';
	$args = func_get_args();
	
	if( count($args) === 1 && is_array($args[0]) )
	{
		$args = $args[0];
	}
	
	if( count($args) > 0 )
	{
		$name .= $args[0];
	}
	
	for( $i = 1; $i < count($args); $i++ )
	{
		$arg = $args[$i];
		
		if( is_array($arg) )
			$name .= apl_name( $arg );
		else
			$name .= "[$arg]";
	}

	return $name;
}
endif;


/**
 * Constructs the name of an input field.
 * @param   array|{args}  The keys of the input name.  For example:
 *                          apl_name( 'a', 'b', 'c' ) will return "a[b][c]"
 *                          apl_name( array( 'a', 'b', 'c' ) ) will return "a[b][c]"
 * @return  string		The constructed input name. 
 */
if( !function_exists('apl_setting_e') ):
function apl_setting_e()
{
	$args = func_get_args();
	if( count($args) > 0 )
	{
		if( is_array($args[0]) )
			echo call_user_func_array( 'apl_setting', $args[0] );
		else
			echo call_user_func_array( 'apl_setting', $args );
	}
}
endif;


/**
 * Constructs the name of an input field.
 * @param   array|{args}  The keys of the input name.  For example:
 *                          apl_name( 'a', 'b', 'c' ) will return "a[b][c]"
 *                          apl_name( array( 'a', 'b', 'c' ) ) will return "a[b][c]"
 * @return  string		The constructed input name. 
 */
if( !function_exists('apl_setting') ):
function apl_setting()
{
	$value = '';
	
	$name = '';
	$args = func_get_args();
	
	if( count($args) === 1 && is_array($args[0]) )
	{
		$args = $args[0];
	}
	
	if( count($args) > 0 )
	{
		$option = $args[0];
	}

	if( is_network_admin() )
	{
		$settings = get_site_option( $option, array() );
	}
	else
	{
		$settings = get_option( $option, array() );
	}
	
	for( $i = 1; $i < count($args); $i++ )
	{
		if( !array_key_exists($args[$i], $settings) ) break;
		
		$settings = $settings[$args[$i]];
		
		if( count($args) == $i + 1 )
		{
			$value = $settings;
			break;
		}
		
		if( !is_array($settings) ) break;
	}
	
	return $value;
}
endif;


/**
 * Constructs the current page's complete url.
 * @param   bool    True if the full url with domain, path, and arguments should be 
 *                  returned, otherwise just the domain and path url.
 * @return  string  The constructed page URL.
 */
if( !function_exists('apl_get_page_url') ):
function apl_get_page_url( $full_url = true )
{
	$page_url = 'http';
	if( isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on') ) $page_url .= 's';
	$page_url .= '://';
	
	if( $full_url )
	{
		if( $_SERVER['SERVER_PORT'] != '80' )
			$page_url .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
		else
			$page_url .= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	}
	else
	{
		if( $_SERVER['SERVER_PORT'] != '80' )
			$page_url .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].strtok( $_SERVER['REQUEST_URI'], '?' );
		else
			$page_url .= $_SERVER['SERVER_NAME'].strtok( $_SERVER['REQUEST_URI'], '?' );
	}
	
	return $page_url;
}
endif;


/**
 * Starts a session if not already started.
 */
if( !function_exists('apl_start_session') ):
function apl_start_session()
{
	if( !session_id() ) @session_start();
}
endif;


/**
 * Prints a backtrace for debugging.
 */
if( !function_exists('apl_backtrace') ):
function apl_backtrace()
{
	if(!function_exists('debug_backtrace')) 
	{
		apl_print( 'function debug_backtrace does not exists' ); 
		return; 
	}
	
	$title = 'Debug backtrace';
	$text = "\r\n";
	
	foreach(debug_backtrace() as $t) 
	{ 
		$text .= "\t" . '@ '; 
		if(isset($t['file'])) $text .= basename($t['file']) . ':' . $t['line']; 
		else 
		{ 
			// if file was not set, I assumed the functioncall 
			// was from PHP compiled source (ie XML-callbacks). 
			$text .= '<PHP inner-code>'; 
		} 

		$text .= ' -- '; 

		if(isset($t['class'])) $text .= $t['class'] . $t['type']; 

		$text .= $t['function']; 

		if(isset($t['args']) && sizeof($t['args']) > 0) $text .= '(...)'; 
		else $text .= '()'; 

		$text .= "\r\n"; 
	}
	
	apl_print( $text, $title );
}
endif;

