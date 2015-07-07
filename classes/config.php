<?php

/**
 * VTT_Config
 * 
 * This class includes all the config information for the Variations Template Theme,
 * including the current variation data.
 * 
 * @package    variations-template-theme
 * @subpackage classes
 * @author     Crystal Barton <cbarto11@uncc.edu>
 */

class VTT_Config
{

//========================================================================================
//======================================================================= Properties =====
	
	
	// current database version
	const DB_VERSION = '1.1';
	
	// relative paths to the config and otions files.
	const CONFIG_DEFAULT_INI_FILENAME = 'config/config-default.ini';
	const CONFIG_INI_FILENAME = 'config/config.ini';
	const OPTIONS_DEFAULT_INI_FILENAME = 'config/options-default.ini';
	const OPTIONS_INI_FILENAME = 'config/options.ini';

	// complete set of data with config and options
	private $data;
	
	// config from config.ini
	private $config;
	
	// options from options.ini and database options
	private $options;
	
	// current variation that is loaded
	private $current_variation;
	private $all_variations;
	

//========================================================================================
//====================================================================== Constructor =====


	/**
	 * Creates an VTT_Config object.
	 */
	public function __construct() { }
	

//========================================================================================
//================================================================ Load Configuration ====

		
	/**
	 * Loads the config and options data, as well as current variation information.
	 */
	public function load_config()
	{
		global $wp_customize;
		if( !isset($wp_customize) )
		{
			$this->check_db();
		}
		
		//
		// initialize
		//
		$this->data = array();
		$this->config = array();
		$this->options = array();
		
		$this->all_variations = array();
		$this->current_variation = null;
		
		$config_ini = array();
		$options_ini = array();
		$db_options = array();
		
		//
		// load theme config.ini data
		//
		if( $this->load_from_ini( $this->config, get_stylesheet_directory().'/'.self::CONFIG_INI_FILENAME ) );
		elseif( $this->load_from_ini( $this->config, get_template_directory().'/'.self::CONFIG_INI_FILENAME ) );
		elseif( $this->load_from_ini( $this->config, get_template_directory().'/'.self::CONFIG_DEFAULT_INI_FILENAME ) );
		else exit( 'Unable to locate theme '.self::CONFIG_INI_FILENAME.' file.' );
		
		//
		// get / set variation
		//
		$this->search_directories = array();
		do_action( 'vtt-search-folders' );

		if( !array_key_exists(5, $this->search_directories) )
			$this->search_directories[5] = array();
		array_unshift( $this->search_directories[5], get_template_directory() );
		
		if( is_child_theme() && !array_key_exists(10, $this->search_directories) ) 
			$this->search_directories[10] = array();
		array_unshift( $this->search_directories[10], get_stylesheet_directory() );
		
		ksort( $this->search_directories );

		$this->all_variations = $this->get_variations();
		$variation = $this->get_current_variation();
		$this->current_variation = $this->all_variations[$variation];
		$variation_directories = $this->get_variation_all_directories();
		
		// 
		// load variation config.ini data.
		// 
		foreach( $variation_directories as $directory )
		{
			if( $this->load_from_ini( $this->config, $directory.'/'.self::CONFIG_INI_FILENAME ) )
				break;
		}
		
		// 
		// load theme and variation options.ini data.
		// 
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
		
		// 
		// load database options.
		// 
		if( !isset($wp_customize) )
		{
			$db_options = get_option( 'vtt-options', array() );
		}
		else
		{
			$db_options = $this->get_upgraded_db_options();

			// theme customizer options
			$db_options = apply_filters( 'vtt-theme-customizer-options', $db_options );
		}
		if( empty($db_options) || !is_array($db_options) ) $db_options = array();
		
		$replace = array();
		
		// 
		// merge options.ini and database options.
		// 
		$this->options = array_replace_recursive( $options_ini, $db_options );
		foreach( $replace as $key )
		{
			if( isset($db_options[$key]) ) { $this->options[$key] = $db_options[$key]; continue; }
			if( isset($options_ini[$key]) ) { $this->options[$key] = $options_ini[$key]; continue; }
			$this->options[$key] = array();
		}
		
		// 
		// merge config.ini with complete options.
		// 
		$this->data = array_replace_recursive( $this->options, $this->config );
		foreach( $replace as $key )
		{
			if( isset($this->options[$key]) ) { $this->data[$key] = $this->options[$key]; continue; }
			$this->data[$key] = array();
		}
		
		$this->data = apply_filters( 'vtt-data', $this->data );

		//
		// convert values.
		//
		$this->convert_values( $this->data );
	}
	
	
	/**
	 * Loads an ini file's data into the $config parameter.
	 * @param   array   $config           The variable to use to store the ini data.
	 * @param   string  $config_filename  The filename of the config file.
	 * @return  bool    True if the ini was loaded into $config successfully.
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
	 * Converts values in an array from strings into primitive data type.
	 * @param   array  $array  The array to modify.
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
	
//========================================================================================
//=========================================================================== Options ====


	/**
	 * Get a value from the data using a list of keys as the parameters.
	 * @param   mixed  ...  A number of key values used to find data.
	 * @return  mixed  The value of the 
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
	
	
	public function set_value()
	{
		$args = func_get_args();
		if( count($args) == 1 && is_array($args[0]) ) $args = $args[0];
		$value = array_pop( $args );
		
		$db_options = get_option( 'vtt-options', array() );
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
		
		update_option( 'vtt-options', $db_options );
	}
	
	
	/**
	 * Gets a theme mod options.
	 * @param   string      $key      The key/name of the theme mod option.
	 * @param   bool|mixed  $default  The default value of the option if the not found.
	 * @return  mixed       The value of the theme mod option.
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
	 * @param   mixed  ...  A number of key values used to find image data.
	 * @return  array  An array of image information.
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
	 * @param   mixed  ...  A number of key values used to find text data.
	 * @return  array  An array of text information.
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
	 * @return  array  The config options.
	 */
	public function get_options()
	{
		return $this->options;
	}


	/**
	 * Clear / reset database options.
	 */
	public function reset_options()
	{
		update_option( 'vtt-options', array() );
	}
	
	
	/**
	 * Get today's date for the timezone from the config data.
	 * @return  DateTime  The current date/time.
	 */
	public function get_todays_datetime()
	{
		if( isset($this->data['timezone']) )
			date_default_timezone_set( $this->data['timezone'] );
		$todays_datetime = new DateTime;
		return $todays_datetime;
	}
	
	
//========================================================================================
//======================================================================== Variations ====


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
		
		if( array_key_exists($variation, $this->all_variations) ) return $variation;
		
		$vnames = array_keys($this->all_variations);
		if( count($this->all_variations) > 0 )
			return $this->all_variations[$vnames[0]]['name'];
		return 'default';
	}
	
	
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
	 * @param   string  $name                     The name of the current variation.
	 * @param   bool    $saving_theme_customizer  True if theme customizer is changing
	 *                                            the current variation, otherwise False.
	 * @return  string  The current variation's name.
	 */
	public function set_variation( $name = '', $saving_theme_customizer = false )
	{
		if( $name === '' )
		{
			$vnames = array_keys($this->all_variations);
			if( count($this->all_variations) > 0 )
				$name = $this->all_variations[$vnames[0]]['name'];
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
	 * Gets a list of all variations.
	 * @param   bool   $filter_variations  True if variations should be limited to those
	 *                                     allowed by the current config.
	 * @return  array  An array of variations with name, title, and directory information.
	 */
	public function get_variations( $filter_variations = true )
	{
		$variation_directories = $this->get_all_variation_directories();
		
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
		
		if( $filter_variations && array_key_exists('variations', $this->config) )
		{
			$allowed_variations = $this->config['variations'];
			foreach( array_keys($variations) as $variation_key )
			{
				if( !in_array($variation_key, $allowed_variations) )
					unset( $variations[$variation_key] );
			}
		}
		
		return $variations;
	}
	
	
//========================================================================================
//================================================================= Custom Post Types ====
	
	
	/**
	 * Gets a list of custom posts types included in the theme.
	 * @return  array  An array of custom post types included in the theme.
	 */
	public function get_custom_post_types()
	{
		$folders = array( get_template_directory().'/custom-post-types' );
		if( is_child_theme() )
			array_push( $folders, get_stylesheet_directory().'/custom-post-types' );

		return array_keys( $this->get_directories( $folders ) );
	}
	
	
//========================================================================================
//=============================================================== Directories & Files ====
	
	
	/**
	 * 
	 */
	private function get_files( $folders, $filename = '' )
	{
		$files = array();
		if( $filename !== '' ) $filename = DIRECTORY_SEPARATOR.$filename;
		
		if( file_exists(get_template_directory().$filename) )
			$files['default'] = get_template_directory().$filename;
		if( is_child_theme() && file_exists(get_stylesheet_directory().$filename) )
			$files['default'] = get_stylesheet_directory().$filename;
		
		foreach( $folders as $folder )
		{
			if( !file_exists($folder) ) continue;
			
			$f = scandir( $folder );
			foreach( $f as $name )
			{
				if( (!in_array($name, array('.','..'))) && 
				    (is_dir($folder.DIRECTORY_SEPARATOR.$name)) )
				{
					if( file_exists($folder.DIRECTORY_SEPARATOR.$name.$filename) )
						$files[$name] = $folder.DIRECTORY_SEPARATOR.$name.$filename;
				}
			}
		}
		
		return $files;
	}
	
	
	/**
	 * 
	 */
	private function get_directories( $folders )
	{
		return $this->get_files( $folders );
	}
	
	
	/**
	 * 
	 */
	private function get_all_variation_directories()
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
	 * 
	 */
	private function get_stylesheet_paths( $folders )
	{
		return $this->get_files( 'style.css' );
	}


	/**
	 * 
	 */
	public function load_variations_files( $filename )
	{
		$variation_directories = $this->get_variation_all_directories( true );
		
		foreach( $variation_directories as $directory )
		{
			if( file_exists($directory.'/'.$filename) )
				require_once( $directory.'/'.$filename );
		}
	}
	
	
	/**
	 * 
	 */
	public function get_variation_filepath( $filepath )
	{
		$variation_directories = $this->get_variation_all_directories( true );
		
		foreach( $variation_directories as $directory )
		{
			if( file_exists($directory.'/'.$filepath) )
				return $directory.'/'.$filepath;
		}
		
		return null;
	}
	
	
	/**
	 * 
	 */
	public function get_variation_name()
	{
		return $this->current_variation['name'];
	}

	
	/**
	 * 
	 */
	public function get_variation_title()
	{
		return $this->current_variation['title'];
	}
	
	
	/**
	 * 
	 */
	public function get_variation_theme_directories( $reverse = true )
	{
		return $this->get_variation_directories( 'variation_theme', $reverse );
	}
	
	
	/**
	 * 
	 */
	public function get_variation_all_directories( $reverse = true )
	{
		return $this->get_variation_directories( 'variation_all', $reverse );
	}

	
	/**
	 * 
	 */
	public function get_theme_directories( $reverse = true )
	{
		return $this->get_variation_directories( 'theme', $reverse );
	}
	
	
	/**
	 * 
	 */
	public function get_all_directories( $reverse = true )
	{
		return $this->get_variation_directories( 'all', $reverse );
	}
	
	
	/**
	 * 
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
	 * 
	 */
	public function add_search_folder( $path, $priority = 10 )
	{
		if( !array_key_exists($priority, $this->search_directories) )
			$this->search_directories[$priority] = array();
		$this->search_directories[$priority][] = $path;
	}
	
	
	/**
	 * 
	 */
	private function set_variation_directories()
	{
		if( isset($this->current_variation['directory']) ) return;
		
		$vname = array();
		if( !empty($this->current_variation['parent']) )
			$vname[] = $this->current_variation['parent'];
		$vname[] = $this->current_variation['name'];
		
		$directory = array();
		
		// get Variation Theme directories.
		$directory['variation_theme'] = array();
		foreach( $vname as $name )
			$directory['variation_theme'][] = get_template_directory().'/variations/'.$name;
		
		if( is_child_theme() )
		{
			foreach( $vname as $name )
				$directory['variation_theme'][] = get_stylesheet_directory().'/variations/'.$name;
		}
		
		// get Variation All directories.
		$directory['variation_all'] = array();
		foreach( $this->search_directories as $dirs )
		{
			foreach( $dirs as $dir )
			{
				foreach( $vname as $name )
				{
					$directory['variation_all'][] = $dir.'/variations/'.$name;
				}
			}
		}
		
		// get Theme directories.
		$directory['theme'] = array();
		$directory['theme']['p'] = get_template_directory();
		if( is_child_theme() )
		{
			$directory['theme']['c'] = get_stylesheet_directory();
		}
		
		// get All directories.
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
		
		$this->current_variation['directory'] = $directory;
		
		// make sure all directories exists.
		foreach( $this->current_variation['directory'] as $key => &$directories )
		{
			foreach( $directories as $k => &$dir )
			{
				if( !is_dir($dir) )
				{
					unset( $this->current_variation['directory'][$key][$k] );
					continue;
				}
				$dir = vtt_clean_path( $dir );
			}
		}
	}
	
	
	/**
	 * 
	 */
	public function get_all_variation_names()
	{
		$names = array();
		
		foreach( $this->all_variations as $name => $variation )
		{
			$names[$name] = $variation['title'];
		}
		
		return $names;
	}
	

//========================================================================================
//========================================================================== Database ====
	
	
	/**
	 * 
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
		
		return get_option( 'vtt-options', array() );
	}
	
	
	/**
	 * 
	 */
	private function check_db()
	{
		$db_version = get_option( 'vtt-db-version', false );
		if( ($db_version === false) || ($db_version === self::DB_VERSION) ) return;
		
		switch( $db_version )
		{
			case '1.0':
				$this->convert_db_from_10_to_11();
// 			case '1.1':
// 				$this->convert_db_from_11_to_12();
// 			case '1.2':
// 				$this->convert_db_from_12_to_13();
			default:
				break;
		}
		
		update_option( 'vtt-db-version', self::DB_VERSION );
	}
	
	
	/**
	 * 
	 */
	private function convert_db_from_10_to_11()
	{
		// get options from database.
		$db_options = get_option( 'vtt-options', array() );
		if( !is_array($db_options) ) return;
		
		// add theme-mods.
		if( !array_key_exists('theme-mods', $db_options) )
			$db_options['theme-mods'] = array();
		
		if( isset($db_options['header']) )
		{
			// remove image-link.
			if( isset($db_options['header']['image-link']) )
			{
				unset( $db_options['header']['image-link'] );
			}
			
			// move title-position to theme-mods.
			if( isset($db_options['header']['title-position']) )
			{
				$db_options['theme-mods']['header-title-position'] = $db_options['header']['title-position'];
				unset( $db_options['header']['title-position'] );
			}

			// move title-hide to theme-mods.
			if( isset($db_options['header']['title-hide']) )
			{
				$db_options['theme-mods']['header-title-hide'] = $db_options['header']['title-hide'];
				unset( $db_options['header']['title-hide'] );
			}

			// move blog-title data to theme-mods.
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

			// move blog-description data to theme-mods.
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
		
		// update options.
		update_option( 'vtt-options', $db_options );
		
		// move header-title-position in theme-mods.
		$header_title_position = get_theme_mod( 'vtt-header-title-position', null );
		if( $header_title_position !== null )
		{
			set_theme_mod( 'header-title-position' );
			remove_theme_mod( 'vtt-header-title-position' );
		}

		// move header-title-hide in theme-mods.
		$header_title_hide = get_theme_mod( 'vtt-header-title-hide', null );
		if( $header_title_hide !== null )
		{
			set_theme_mod( 'header-title-hide' );
			remove_theme_mod( 'vtt-header-title-hide' );
		}
	}
}

