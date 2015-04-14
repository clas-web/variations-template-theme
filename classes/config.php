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
	const DB_VERSION = '1.0';
	
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
			
			if( (strlen($value) > 2) && ($value[1] === ':') )
			{
				switch( $value[0] )
				{
					case 'b':
						$value = ( substr($value, 2) === 'true' ? true : false );
						break;
						
					case 'i':
						$value = intval( substr($value, 2) );
						break;
					
					case 'd':
						$value = doubleval( substr($value, 2) );
						break;
					
					case 'a':
						// TODO: implement arrays in ini file.
						$value = substr($value, 2);
						break;
				}
			}
		}
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
	
	
	/**
	 * Gets a theme mod options.
	 * @param   string      $key      The key/name of the theme mod option.
	 * @param   bool|mixed  $default  The default value of the option if the not found.
	 * @return  mixed       The value of the theme mod option.
	 */
	public function get_theme_mod( $key, $default = false )
	{
		if( isset($_POST['customized']) )
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
			return $this->set_variation( $this->all_variations[$vnames[0]]['name'] );
		return $this->set_variation( 'default' );
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
		$folders = array();
		$theme_variation_folders = array();
		$theme_variation_folders = array( get_template_directory().'/variations' );
		if( is_child_theme() ) 
			$theme_variation_folders[] = get_stylesheet_directory().'/variations';
		$folders = apply_filters( 'vtt-variations-folders', $folders );
		$folders = array_merge( $theme_variation_folders, $folders );
		
		$variation_directories = $this->get_all_variation_directories( $folders );
		
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
	private function get_all_variation_directories( $folders )
	{
		$directories = array();
		
		$directories['default'] = array( get_template_directory() );
		if( is_child_theme() ) $directories['default'][] = get_stylesheet_directory();
		
		foreach( $folders as $folder )
		{
			if( !file_exists($folder) ) continue;
			
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
	public function get_variation_other_directories( $reverse = true )
	{
		return $this->get_variation_directories( 'variation_other', $reverse );
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
	private function set_variation_directories()
	{
		if( isset($this->current_variation['directory']) ) return;
		
		$vname = array();
		if( !empty($this->current_variation['parent']) )
			$vname[] = $this->current_variation['parent'];
		$vname[] = $this->current_variation['name'];
		
		$directory = array();
		
		$other_variations_folders = array();
		$other_variations_folders = apply_filters( 
			'vtt-variations-folders', 
			$other_variations_folders
		);
		
		// get Variation Other directories.
		$directory['variation_other'] = array();
		foreach( $other_variations_folders as $d )
		{
			foreach( $vname as $name )
				$directory['variation_other'][] = $d.'/'.$name;
		}
		
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
		$directory['variation_all'] = array_merge(
			$directory['variation_theme'],
			$directory['variation_other']
		);		
		
		// get Theme directories.
		$directory['theme'] = array();
		$directory['theme']['p'] = get_template_directory();
		if( is_child_theme() )
		{
			$directory['theme']['c'] = get_stylesheet_directory();
		}
		
		// get All directories.
		$directory['all'] = array();
		$directory['all'][] = get_template_directory();
		foreach( $vname as $name )
			$directory['all'][] = get_template_directory().'/variations/'.$name;
		if( is_child_theme() )
		{
			$directory['all'][] = get_stylesheet_directory();
			foreach( $vname as $name )
				$directory['all'][] = get_stylesheet_directory().'/variations/'.$name;
		}
		$directory['all'] = array_merge(
			$directory['all'],
			$directory['variation_other']
		);
		
		$this->current_variation['directory'] = $directory;
		
		// make sure all directories exists.
		foreach( $this->current_variation['directory'] as $key => $directories )
		{
			foreach( $directories as $k => $dir )
			{
				if( !file_exists($dir) )
					unset( $this->current_variation['directory'][$key][$k] );
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
				// $this->convert_db_from_10_to_11();
				// new version function here...
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
	}
}

