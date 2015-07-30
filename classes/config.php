<?php

/**
 * Handles the config data and processing, including the current variation and variation directories.
 * 
 * @package    variations-template-theme
 * @author     Crystal Barton <atrus1701@gmail.com>
 * @version    1.0
 */
if( !class_exists('VTT_Config') ):
class VTT_Config
{
	/**
	 * The current version of how information is stored in the database.
	 * @var  string
	 */
	const DB_VERSION = VTT_DB_VERSION;
	
	/**
	 * The relative path to the config-default.ini file.
	 * @var  string
	 */
	const CONFIG_DEFAULT_INI_FILENAME = 'config/config-default.ini';

	/**
	 * The relative path to the config.ini file.
	 * @var  string
	 */
	const CONFIG_INI_FILENAME = 'config/config.ini';

	/**
	 * The relative path to the options-default.ini file.
	 * @var  string
	 */
	const OPTIONS_DEFAULT_INI_FILENAME = 'config/options-default.ini';

	/**
	 * The relative path to the options.ini file.
	 * @var  string
	 */
	const OPTIONS_INI_FILENAME = 'config/options.ini';

	/**
	 * Complete set of data with config and options.
	 * @var  Array
	 */
	private $data;
	
	/**
	 * Only config data for the config.ini.
	 * @var  Array
	 */
	private $config;
	
	/**
	 * Only config data for the options.ini and database.
	 * @var  Array
	 */
	private $options;
	
	/**
	 * Current variation name.
	 * @var  string
	 */
	private $current_variation;

	/**
	 * A list of all variations keyed by the variation name.
	 * @var  Array
	 */
	private $all_variations;

	/**
	 * A list of all allowed variations for the current theme.
	 * @var  Array
	 */
	private $filtered_variations;
	

	/**
	 * Constructor.
	 * Creates an VTT_Config object.
	 */
	public function __construct() { }
	

	/**
	 * Loads the config and options data, as well as current variation information.
	 */
	public function load_config()
	{
		global $wp_customize;

		// Update the database, if needed.
		if( !isset($wp_customize) )
		{
			$this->check_db();
		}
		
		// Initialize class variables.
		$this->data = array();
		$this->config = array();
		$this->options = array();
		
		$this->all_variations = array();
		$this->filtered_variations = array();
		$this->current_variation = null;
		
		$config_ini = array();
		$options_ini = array();
		$db_options = array();
		
		// Load theme config.ini data.
		if( $this->load_from_ini( $this->config, get_stylesheet_directory().'/'.self::CONFIG_INI_FILENAME ) );
		elseif( $this->load_from_ini( $this->config, get_template_directory().'/'.self::CONFIG_INI_FILENAME ) );
		elseif( $this->load_from_ini( $this->config, get_template_directory().'/'.self::CONFIG_DEFAULT_INI_FILENAME ) );
		else exit( 'Unable to locate theme '.self::CONFIG_INI_FILENAME.' file.' );
		
		// Determine all directories that could have variations.
		$this->search_directories = array();
		do_action( 'vtt-search-folders' );

		if( !array_key_exists(5, $this->search_directories) )
			$this->search_directories[5] = array();
		array_unshift( $this->search_directories[5], get_template_directory() );
		
		if( is_child_theme() && !array_key_exists(10, $this->search_directories) ) 
			$this->search_directories[10] = array();
		array_unshift( $this->search_directories[10], get_stylesheet_directory() );
		
		ksort( $this->search_directories );

		// Get all variation directories.
		$this->get_all_variations();
		$this->get_filtered_variations();

		// Get the current variation with directory data.
		$variation = $this->get_current_variation();
		$this->current_variation = $this->all_variations[$variation];
		$variation_directories = $this->get_all_available_variations_directories();
		
		// Load variation config.ini data.
		foreach( $variation_directories as $directory )
		{
			if( $this->load_from_ini( $this->config, $directory.'/'.self::CONFIG_INI_FILENAME ) )
				break;
		}
		
		// Load theme and variation options.ini data.
		foreach( $variation_directories as $directory )
		{
			if( $this->load_from_ini( $options_ini, $directory.'/'.self::OPTIONS_INI_FILENAME ) )
				break;
		}
		
		if( empty($options_ini) )
		{
			if( $this->load_from_ini( $options_ini, get_stylesheet_directory().'/'.self::OPTIONS_INI_FILENAME ) );
			elseif( $this->load_from_ini( $options_ini, get_template_directory().'/'.self::OPTIONS_INI_FILENAME ) );
			elseif( $this->load_from_ini( $options_ini, get_template_directory().'/'.self::OPTIONS_DEFAULT_INI_FILENAME ) );
			else exit( 'Unable to locate variation '.self::OPTIONS_INI_FILENAME.' file.' );
		}
		
		// Load database options.
		if( !isset($wp_customize) )
		{
			$db_options = get_option( VTT_OPTIONS, array() );
		}
		else
		{
			$db_options = $this->get_upgraded_db_options();
			$db_options = apply_filters( 'vtt-theme-customizer-options', $db_options );
		}
		if( empty($db_options) || !is_array($db_options) ) $db_options = array();
		
		// Merge options.ini and database options.
		$this->options = array_replace_recursive( $options_ini, $db_options );
		
		// Merge config.ini with complete options.
		$this->data = array_replace_recursive( $this->options, $this->config );

		// Filter data, if needed.		
		$this->data = apply_filters( 'vtt-data', $this->data );

		// Convert string values to intended value type.
		$this->convert_values( $this->data );
	}
	
	
	/**
	 * Loads an ini file's data into the $config parameter.
	 * @param  Array  $config  Reference variable to use to store the ini data.
	 * @param  string  $config_filename  The filename of the config file.
	 * @return  bool  True if the ini was loaded into $config successfully, otherwise False.
	 */
	private function load_from_ini( &$config, $config_filename )
	{
		if( !file_exists($config_filename) ) return false;
		
		$ini_config = parse_ini_file( $config_filename, true);		
		if( $ini_config === false ) return false;
		
		$this->convert_values( $ini_config );
		
		if( !empty($config) ) $config = array_replace_recursive( $config, $ini_config );
		else $config = $ini_config;
		
		return true;
    }
    

	/**
	 * Converts values in an array from string into primitive data type.
	 * @param  Array  $array  Reference to array to modify.
	 */
    private function convert_values( &$array )
    {
		foreach( $array as $key => &$value )
		{
			if( is_array($value) )
			{
				$this->convert_values( $value );
				continue;
			}
			
			if( (is_string($value)) && (strlen($value) > 2) && ($value[1] === ':') )
			{
				$value = $this->string_to_value( $value );
			}
		}
	}   
	
	
	/**
	 * Converts string into primitive data type.
	 * @param  string  $string  String value.
	 * @return  mixed  The converted string to primitive data type.
	 */
	public function string_to_value( $string )
	{
		$value = null;
		
		switch( $string[0] )
		{
			case 'b':
				$value = ( substr($string, 2) === 'true' ? true : false );
				break;
				
			case 'i':
				$value = intval( substr($string, 2) );
				break;
			
			case 'd':
				$value = doubleval( substr($string, 2) );
				break;
		}
		
		return $value;
	}
	

	/**
	 * Converts data type into a string.
	 * @param  mixed  The value.
	 * @return  string  The converted data type to string.
	 */
	public function value_to_string( $value )
	{
		$string = '';
		
		if( is_bool($value) )
		{
			if( $value === true )
				$string = 'b:true';
			else
				$string = 'b:false';
		}
		elseif( is_int($value) )
		{
			$string = 'i:'.$value;
		}
		elseif( is_double($value) )
		{
			$string ='d:'.$value;
		}
		else
		{
			$string .= print_r($value, true);
		}
		
		return $string;
	}
	

	/**
	 * Get a value from the data using a list of keys as the parameters.
	 * @param   string  {args}  A number of key values used to find data.
	 * @return  mixed  The requested value, if exists, otherwise Null.
	 */
	public function get_value()
	{
		$args = func_get_args();
		if( count($args) == 1 && is_array($args[0]) ) $args = $args[0];
		
		$config = $this->data;
		foreach( $args as $arg )
		{
			if( array_key_exists($arg, $config) )
			{
				$config = $config[$arg];
			}
			else
			{
				$config = null;
				break;
			}
		}

		return $config;
	}
	
	
	/**
	 * Set a value in the database using a list of keys as parameters.
	 * The last parameters is the value to save.
	 * @param  string  {args}  A number of key values used to set data.
	 * @param  mixed  {last arg}  The value to save.
	 */
	public function set_value()
	{
		$args = func_get_args();
		if( count($args) == 1 && is_array($args[0]) ) $args = $args[0];
		$value = array_pop( $args );
		
		$db_options = get_option( VTT_OPTIONS, array() );
		if( !is_array($db_options) ) $db_options = array();
		$options =& $db_options;
		
		foreach( $args as $arg )
		{
			if( !is_array($options) )
				$options = array();
			if( !array_key_exists($arg, $options) )
				$options[$arg] = array();

			$options =& $options[$arg];
		}
		
		$options = $value;
		
		update_option( VTT_OPTIONS, $db_options );
	}
	
	
	/**
	 * Gets a theme mod options.
	 * @param  string  $key  The key/name of the theme mod option.
	 * @param  bool|mixed  $default  The default value of the option if the not found.
	 * @return  mixed  The value of the theme mod option.
	 */
	public function get_theme_mod( $key, $default = false )
	{
		if( !empty($_POST['customized']) )
		{
			$values = json_decode(wp_unslash($_POST['customized']), true);
			if( array_key_exists($key, $values) ) return $values[$key];
		}

		return get_theme_mod( $key, $default );
	}
	
	
	/**
	 * Gets the image data from the config data.
	 * @param  mixed  {args}  A number of key values used to find image data.
	 * @return  array  An array of image information, or Null on failure.
	 */
	public function get_image_data()
	{
		$args = func_get_args();
		if( count($args) == 1 && is_array($args[0]) ) $args = $args[0];
		
		$image_data = $this->get_value( $args );
		if( $image_data === null ) return null;
		
		$defaults = array(
			'selection-type' 	=> 'relative',
			'attachment-id' 	=> -1,
			'path' 				=> '',
			'use-site-link' 	=> false,
			'link' 				=> '',
		);
		
		$image_data = array_merge( $defaults, $image_data );
		return $image_data;
	}
	
	
	/**
	 * Gets the text data from the config data.
	 * @param  mixed  {args}  A number of key values used to find text data.
	 * @return  Array  An array of text information.
	 */
	public function get_text_data()
	{
		$args = func_get_args();
		if( count($args) == 1 && is_array($args[0]) ) $args = $args[0];
		
		$text_data = $this->get_value( $args );
		if( $text_data === null ) return null;
		
		$defaults = array(
			'text' 			=> null,
			'use-site-link'	=> false,
			'link' 			=> '',
		);
		
		$text_data = array_merge( $defaults, $text_data );
		return $text_data;
	}
	

	/**
	 * Gets the config options.
	 * @return  Array  The config options.
	 */
	public function get_options()
	{
		return $this->options;
	}


	/**
	 * Clear ("reset") database options.
	 */
	public function reset_options()
	{
		update_option( VTT_OPTIONS, array() );
	}
	
	
	/**
	 * Gets the current variation's name.
	 * @return  string  The current variation's name.
	 */
	private function get_current_variation()
	{
		if( $this->current_variation !== null ) return $this->current_variation;
		
		global $wp_customize;
		if( isset($wp_customize) )
		{
			$variation = $this->get_theme_mod( 'vtt-variation', false );
			if( $variation !== false ) return $variation;
		}
		
		$variation = get_option( 'vtt-variation', false );
		if( $variation === false ) return $this->set_variation();
		
		if( array_key_exists($variation, $this->filtered_variations) ) return $variation;
		
		$vnames = array_keys($this->filtered_variations);
		if( count($this->filtered_variations) > 0 )
			return $this->filtered_variations[$vnames[0]]['name'];
		return 'default';
	}
	
	
	/**
	 * Get a theme mod option from the config options.
	 * @param  string  $options_key  The option name.
	 * @param  mixed  $default  The default value to return if option not found.
	 * @return  mixed  The theme mod option value.
	 */
	public function get_theme_value( $options_key, $default = false )
	{
		$return = $this->get_theme_mod(
			$options_key,
			$this->get_value( array('theme-mods', $options_key) )
		);
		
		if( $return === null ) return $default;
		return $return;
	}
	
	
	/**
	 * Sets the current variation in the database.
	 * @param  string  $name  The name of the current variation.
	 * @param  bool  $saving_theme_customizer  True if theme customizer is changing the current 
	 *                                         variation, otherwise False.
	 * @return  string  The current variation's name.
	 */
	public function set_variation( $name = '', $saving_theme_customizer = false )
	{
		if( $name === '' )
		{
			$vnames = array_keys( $this->filtered_variations );
			if( count($this->filtered_variations) > 0 )
				$name = $this->filtered_variations[$vnames[0]]['name'];
			else
				$name = 'default';
		}
		
		global $wp_customize;
		if( (!isset($wp_customize)) || ($saving_theme_customizer) )
		{
			update_option( 'vtt-variation', $name );
		}
		
		return $name;
	}


	/**
	 * Get the data for a variation.
	 * @param  string  $variation_name  The name of the variation.
	 * @return  Array  The variation data, or Null if it does not exist.
	 */
	public function get_variation_info( $variation_name )
	{
		if( array_key_exists($variation_name, $this->all_variations) )
			return $this->all_variations[$variation_name];
		return null;
	}
	
	
	/**
	 * Gets a list of all variations.
	 * @return  Array  An array of variations with name, title, and directory information.
	 */
	public function get_all_variations()
	{
		if( !empty($this->all_variations) ) return $this->all_variations;

		$variation_directories = $this->get_all_available_variations_directories();
		
		$variations = array();
		foreach( $variation_directories as $name => $directories )
		{
			if( !array_key_exists($name, $variations) )
			{
				$variations[$name] = array(
					'name'		=> $name,
					'title'		=> $name,
					'parent'	=> null,
				);
			}
			
			foreach( $directories as $directory )
			{
				$variation_name = '';
				if( file_exists($directory.'/style.css') )
				{
					$data = get_file_data( 
						$directory.'/style.css', 
						array(
							'variation'		=> 'Variation Name',
							'parent'		=> 'Parent Variation'
						)
					);
					if( !empty($data['variation']) ) $variations[$name]['title'] = $data['variation'];
					if( !empty($data['parent']) ) $variations[$name]['parent'] = $data['parent'];
				}
			}
		}

		$this->all_variations = $variations;
		return $variations;
	}

	
	/**
	 * Gets a list all variations allowed by the current theme.
	 * @return  Array  An array of variations with name, title, and directory information.
	 */
	public function get_filtered_variations()
	{
		if( !empty($this->filtered_variations) ) return $this->filtered_variations;

		if( empty($this->all_variations) ) $this->get_all_variations();

		$variations = array();
		$allowed_variations = $this->config['variations'];
		foreach( $this->all_variations as $variation_key => $variation )
		{
			if( in_array($variation_key, $allowed_variations) )
				$variations[$variation_key] = $variation;
		}

		$this->filtered_variations = $variations;
		return $variations;
	}
	
	
	/**
	 * Get all available variations folders.
	 * @return  Array  An array of variations folders with the variation name is the key.
	 */
	private function get_all_available_variations_directories()
	{
		$directories = array();
		
		foreach( $this->search_directories as $priority => $dirs )
		{
			switch( $priority )
			{
				case 5:
					if( !array_key_exists('default', $directories) )
						$directories['default'] = array();
					$directories['default'][] = get_template_directory();
					break;
				case 10:
					if( is_child_theme() )
					{
						if( !array_key_exists('default', $directories) )
							$directories['default'] = array();
						$directories['default'][] = get_stylesheet_directory();
					}
					break;
			}
			
			foreach( $dirs as $dir )
			{
				$folder = $dir.DIRECTORY_SEPARATOR.'variations';
				if( !is_dir($folder) ) continue;

				$files = scandir( $folder );
				foreach( $files as $name )
				{
					if( (!in_array($name, array('.','..'))) && 
						(is_dir($folder.DIRECTORY_SEPARATOR.$name)) )
					{
						if( !array_key_exists($name, $directories) )
							$directories[$name] = array();
						$directories[$name][] = $folder.DIRECTORY_SEPARATOR.$name;
					}
				}
			}
		}
		
		foreach( $directories as &$directory )
		{
			$directory = vtt_clean_path( $directory );
		}

		return $directories;
	}


	/**
	 * Load a file in all variations.
	 * @param  string  $filepath  The relative file path to load.
	 */
	public function load_variations_files( $filepath )
	{
		$variation_directories = $this->get_all_variations_directories( true );
		
		foreach( $variation_directories as $directory )
		{
			if( file_exists($directory.'/'.$filepath) )
				require_once( $directory.'/'.$filepath );
		}
	}
	
	
	/**
	 * Get the first filepath to the a file within a variation.
	 * @param  string  $filepath  The relative file path to search for.
	 * @return  The absolute file path, or Null if none are found.
	 */
	public function get_variation_filepath( $filepath )
	{
		$variation_directories = $this->get_all_variations_directories( true );
		
		foreach( $variation_directories as $directory )
		{
			if( file_exists($directory.'/'.$filepath) )
				return $directory.'/'.$filepath;
		}
		
		return null;
	}
	
	
	/**
	 * Get the current variation name.
	 * @return  string  The variation name.
	 */
	public function get_variation_name()
	{
		return $this->current_variation['name'];
	}

	
	/**
	 * Get the current variations title.
	 * @return  string  The variation title.
	 */
	public function get_variation_title()
	{
		return $this->current_variation['title'];
	}
	
	
	/**
	 * Get list of the variations directories within each theme (parent and child).
	 * @param  bool  $reverse  Return list in directories in reverse order (child then parent).
	 * @return  Array  The directories.
	 */
	public function get_variation_theme_directories( $reverse = true )
	{
		return $this->get_variation_directories( 'theme_variations', $reverse );
	}
	
	
	/**
	 * Get list of all variation directories associated with the current variation.
	 * @param  bool  $reverse  Return list in directories in reverse order.
	 * @return  Array  The directories.
	 */
	public function get_all_variations_directories( $reverse = true )
	{
		return $this->get_variation_directories( 'all_variations', $reverse );
	}

	
	/**
	 * Get list of theme directories.
	 * @param  bool  $reverse  Return list in directories in reverse order (child then parent).
	 * @return  Array  The directories.
	 */
	public function get_theme_directories( $reverse = true )
	{
		return $this->get_variation_directories( 'theme', $reverse );
	}


	/**
	 * Get list of search directories.
	 * @param  bool  $reverse  Return list in directories in reverse order (child then parent).
	 * @return  Array  The directories.
	 */
	public function get_search_directories( $reverse = true )
	{
		if( $reverse )
			return array_reverse( $this->search_directories );
		return $this->search_directories;
	}
	
	
	/**
	 * Get list of search directories and all variation directories associated with the current variation.
	 * @param  bool  $reverse  Return list in directories in reverse order.
	 * @return  Array  The directories.
	 */
	public function get_all_directories( $reverse = true )
	{
		return $this->get_variation_directories( 'all', $reverse );
	}
	
	
	/**
	 * Get a list of directories from the current variation object.
	 * @param  string  $name  The key for the directories list.
	 * @param  bool  $reverse  Return list in directories in reverse order.
	 * @return  Array  The directories.
	 */
	private function get_variation_directories( $name, $reverse = true )
	{
		$this->set_variation_directories();
		
		if( array_key_exists($name, $this->current_variation['directory']) )
		{
			if( $reverse )
				return array_reverse( $this->current_variation['directory'][$name] );
			return $this->current_variation['directory'][$name];
		}
		
		return array();
	}
	
	
	/**
	 * Adds a path to the list of search folders.
	 * The priority of the parent theme folder is 5 and child theme is 10.
	 * @param  string  $path  The absolute path to the search folder.
	 * @param  int  $priority  The priority of the search folder in the list of folders.
	 */
	public function add_search_folder( $path, $priority = 10 )
	{
		if( !array_key_exists($priority, $this->search_directories) )
			$this->search_directories[$priority] = array();
		$this->search_directories[$priority][] = $path;
	}
	
	
	/**
	 * Sets the directories for the current variation which are used in searching.
	 */
	private function set_variation_directories()
	{
		// If the current variation directories are set, then no need to continue.
		if( isset($this->current_variation['directory']) ) return;
		$directory = array();
		

		// Get the list of variations starting with the current and going up the chain of parent variations.
		$vname = array();
		$vname[] = $this->current_variation['name'];

		$parent = $this->current_variation['parent'];
		while( !empty($parent) )
		{
			$parent_variation = $this->get_variation_info( $parent );
			if( !$parent_variation )
			{
				$parent = null;
				break;
			}

			$vname[] = $parent;
			$parent = $parent_variation['parent'];
		}

		$vname = array_reverse($vname);
		

		// Get the variation path within the theme folders.
		$directory['theme_variations'] = array();
		foreach( $vname as $name )
			$directory['theme_variations'][] = get_template_directory().'/variations/'.$name;
		
		if( is_child_theme() )
		{
			foreach( $vname as $name )
				$directory['theme_variations'][] = get_stylesheet_directory().'/variations/'.$name;
		}
		

		// Get all the variation directories within the search folders.
		$directory['all_variations'] = array();
		foreach( $this->search_directories as $dirs )
		{
			foreach( $dirs as $dir )
			{
				foreach( $vname as $name )
				{
					$directory['all_variations'][] = $dir.'/variations/'.$name;
				}
			}
		}
		
		
		// Get the theme directories.
		$directory['theme'] = array();
		$directory['theme']['p'] = get_template_directory();
		if( is_child_theme() )
		{
			$directory['theme']['c'] = get_stylesheet_directory();
		}
		
		
		// Get all variation directories.
		// Get all the search path and variation path within all the search folders.
		$directory['all'] = array();
		foreach( $this->search_directories as $dirs )
		{
			foreach( $dirs as $dir )
			{
				foreach( $vname as $name )
				{
					$directory['all'][] = $dir;
					$directory['all'][] = $dir.'/variations/'.$name;
				}
			}
		}


		// make sure all directories exists.
		foreach( $directory as $key => &$dir )
		{
			foreach( $dir as &$d )
			{
				if( !is_dir($d) )
				{
					unset( $d );
					continue;
				}
				$d = vtt_clean_path( $d );
			}
		}
		

		// Set the current variations
		$this->current_variation['directory'] = $directory;
	}
	
	
	/**
	 * Get all the variations that are found and are allowed by the theme.
	 * @return  Array  The array of variation names.
	 *                 The key of the array is the name and value is the title.
	 */
	public function get_all_variation_names()
	{
		$names = array();
		
		foreach( $this->filtered_variations as $name => $variation )
		{
			$names[$name] = $variation['title'];
		}
		
		return $names;
	}
	

	/**
	 * Get the database options after upgrading the database, if needed.
	 * @return  Array  The database options.
	 */
	private function get_upgraded_db_options()
	{
		$db_version = get_option( 'vtt-db-version', false );
		
		switch( $db_version )
		{
			case '1.0':
				break;
			
			default:
				break;
		}
		
		return get_option( VTT_OPTIONS, array() );
	}
	
	
	/**
	 * Check the database to check if an update is needed.
	 * If needed, then convert database options to the new version and update the database version.
	 */
	private function check_db()
	{
		$db_version = get_option( 'vtt-db-version', false );
		if( ($db_version === false) || ($db_version === self::DB_VERSION) ) return;
		
		switch( $db_version )
		{
			case '1.0':
				$this->convert_db_from_10_to_11();
			default:
				break;
		}
		
		update_option( 'vtt-db-version', self::DB_VERSION );
	}
	
	
	/**
	 * Convert the database from version 1.0 to 1.1.
	 */
	private function convert_db_from_10_to_11()
	{
		// Get options from database.
		$db_options = get_option( VTT_OPTIONS, array() );
		if( !is_array($db_options) ) return;
		
		// Add theme-mods.
		if( !array_key_exists('theme-mods', $db_options) )
			$db_options['theme-mods'] = array();
		
		if( isset($db_options['header']) )
		{
			// Remove image-link.
			if( isset($db_options['header']['image-link']) )
			{
				unset( $db_options['header']['image-link'] );
			}
			
			// Move title-position to theme-mods.
			if( isset($db_options['header']['title-position']) )
			{
				$db_options['theme-mods']['header-title-position'] = $db_options['header']['title-position'];
				unset( $db_options['header']['title-position'] );
			}

			// Move title-hide to theme-mods.
			if( isset($db_options['header']['title-hide']) )
			{
				$db_options['theme-mods']['header-title-hide'] = $db_options['header']['title-hide'];
				unset( $db_options['header']['title-hide'] );
			}

			// Move blog-title data to theme-mods.
			if( isset($db_options['header']['title']) )
			{
				if( isset($db_options['header']['title']['use-blog-info']) && $db_options['header']['title']['use-blog-info'] )
				{
					$db_options['theme-mods']['blogname'] = '/';
				}
				elseif( isset($db_options['header']['title']['text']) )
				{
					$db_options['theme-mods']['blogname'] = $db_options['header']['title']['text'];
				}

				if( isset($db_options['header']['title']['use-site-link']) && $db_options['header']['title']['use-site-link'] )
				{
					$db_options['theme-mods']['blogname_url'] = '/';
				}
				elseif( isset($db_options['header']['title']['link']) )
				{
					$db_options['theme-mods']['blogname_url'] = $db_options['header']['title']['link'];
				}
				
				unset( $db_options['header']['title'] );
			}

			// Move blog-description data to theme-mods.
			if( isset($db_options['header']['description']) )
			{
				if( isset($db_options['header']['description']['use-blog-info']) && $db_options['header']['description']['use-blog-info'] )
				{
					$db_options['theme-mods']['blogdescription'] = '/';
				}
				elseif( isset($db_options['header']['description']['text']) )
				{
					$db_options['theme-mods']['blogdescription'] = $db_options['header']['description']['text'];
				}

				if( isset($db_options['header']['description']['use-site-link']) && $db_options['header']['description']['use-site-link'] )
				{
					$db_options['theme-mods']['blogdescription_url'] = '/';
				}
				elseif( isset($db_options['header']['description']['link']) )
				{
					$db_options['theme-mods']['blogdescription_url'] = $db_options['header']['description']['link'];
				}
				
				unset( $db_options['header']['description'] );
			}
		}
		
		// Update options.
		update_option( VTT_OPTIONS, $db_options );
		
		// Move header-title-position in theme-mods.
		$header_title_position = get_theme_mod( 'vtt-header-title-position', null );
		if( $header_title_position !== null )
		{
			set_theme_mod( 'header-title-position' );
			remove_theme_mod( 'vtt-header-title-position' );
		}

		// Move header-title-hide in theme-mods.
		$header_title_hide = get_theme_mod( 'vtt-header-title-hide', null );
		if( $header_title_hide !== null )
		{
			set_theme_mod( 'header-title-hide' );
			remove_theme_mod( 'vtt-header-title-hide' );
		}
	}
}
endif;

