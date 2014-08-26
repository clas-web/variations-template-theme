<?php

//========================================================================================
// 
// 
// 
//========================================================================================
class UNCC_Config
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
	

//========================================================================================
//====================================================================== Constructor =====


	//------------------------------------------------------------------------------------
	// Default Constructor.
	//------------------------------------------------------------------------------------
	public function __construct() { }
	

//========================================================================================
//================================================================ Load Configuration ====

	
	//------------------------------------------------------------------------------------
	// Loads the database options.
	//------------------------------------------------------------------------------------
	public function load_config()
	{
		global $wp_customize;
		if( !isset($wp_customize) )
		{
			$this->check_db();
		}

		$this->data = array();
		$this->config = array();
		$this->options = array();
		
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
		$variation = $this->get_current_variation();
		
		// 
		// load variation config.ini data.
		// 
		if( $this->load_from_ini( $this->config, get_stylesheet_directory().'/variations/'.$variation.'/'.self::CONFIG_INI_FILENAME ) );
		elseif( $this->load_from_ini( $this->config, get_template_directory().'/variations/'.$variation.'/'.self::CONFIG_INI_FILENAME ) );
		
		// 
		// load theme and variation options.ini data.
		// 
		if( $this->load_from_ini( $options_ini, get_stylesheet_directory().'/variations/'.$variation.'/'.self::OPTIONS_INI_FILENAME ) );
		elseif( $this->load_from_ini( $options_ini, get_template_directory().'/variations/'.$variation.'/'.self::OPTIONS_INI_FILENAME ) );
		elseif( $this->load_from_ini( $options_ini, get_stylesheet_directory().'/'.self::OPTIONS_INI_FILENAME ) );
		elseif( $this->load_from_ini( $options_ini, get_template_directory().'/'.self::OPTIONS_INI_FILENAME ) );
		elseif( $this->load_from_ini( $options_ini, get_template_directory().'/'.self::OPTIONS_DEFAULT_INI_FILENAME ) );
		else exit( 'Unable to locate variation '.self::OPTIONS_INI_FILENAME.' file.' );
		
		// 
		// load database options.
		// 
		if( !isset($wp_customize) )
		{
			$db_options = get_option( 'uncc-options', array() );
		}
		else
		{
			$db_options = $this->get_upgraded_db_options();

			// theme customizer options
			
			$db_options = apply_filters( 'uncc-theme-customizer-options', $db_options );
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

		$this->data = apply_filters( 'uncc-config-merge-data', $this->data );

		//
		// convert values.
		//
		$this->convert_values( $this->data );
		
// 		nh_print( $config_ini, 'config-ini' );
// 		nh_print( $options_ini, 'options-ini' );
// 		nh_print( $db_options, 'db-options' );
// 		nh_print( $this->data, 'data' );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	// 
	// @param	$config_filname	string		The path to the config INI file.
	//------------------------------------------------------------------------------------
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
    

	//------------------------------------------------------------------------------------
	// 
	// 
	// @param	
	//------------------------------------------------------------------------------------
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
						$value = substr($value, 2);
						// TODO...
						break;
				}
			}
		}
	}    
	
//========================================================================================
//=========================================================================== Options ====


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
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
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_theme_mod( $key, $default = false )
	{
		if( isset($_POST['customized']) )
		{
			$values = json_decode(wp_unslash($_POST['customized']), true);
			if( array_key_exists($key, $values) ) return $values[$key];
		}

		return get_theme_mod( $key, $default );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_image_data()
	{
		$args = func_get_args();
		if( count($args) == 1 && is_array($args[0]) ) $args = $args[0];
		
		$image_data = $this->get_value( $args );
		if( $image_data === null ) return null;
		
		$defaults = array(
			'selection-type' => 'relative',
			'attachment-id' => -1,
			'path' => '',
			'use-site-link' => false,
			'link' => '',
		);
		
		$image_data = array_merge( $defaults, $image_data );
		return $image_data;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_text_data()
	{
		$args = func_get_args();
		if( count($args) == 1 && is_array($args[0]) ) $args = $args[0];
		
		$text_data = $this->get_value( $args );
		if( $text_data === null ) return null;
		
		$defaults = array(
			'text' => null,
			'use-site-link' => false,
			'link' => '',
		);
		
		$text_data = array_merge( $defaults, $text_data );
		return $text_data;
	}
	

	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_options()
	{
		return $this->options;
	}


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function reset_options()
	{
		update_option( 'uncc-options', array() );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_todays_datetime()
	{
		if( isset($this->data['timezone']) )
			date_default_timezone_set( $this->data['timezone'] );
		$todays_datetime = new DateTime;
		return $todays_datetime;
	}
	
	
//========================================================================================
//======================================================================== Variations ====


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_current_variation()
	{
		global $wp_customize;
		if( isset($wp_customize) )
		{
	        $variation = $this->get_theme_mod( 'uncc-variation', false );
			if( $variation !== false ) return $variation;
		}
		
		$variation = get_option( 'uncc-variation', false );
		if( $variation === false ) return $this->set_variation();
		
		if( array_key_exists($variation, $this->get_variations()) ) return $variation;
		return $this->set_variation( $variation );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function set_variation( $name = '', $saving_theme_customizer = false )
	{
		if( $name === '' )
		{
			if( (array_key_exists('variations', $this->config)) && 
			    (is_array($this->config['variations'])) &&
			    (count($this->config['variations']) > 0) )
			{
				$name = $this->config['variations'][0];
			}
			else
			{
				$name = 'default';
			}
		}
		
		global $wp_customize;
		if( (!isset($wp_customize)) || ($saving_theme_customizer) )
		{
			update_option( 'uncc-variation', $name );
		}
		
		return $name;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_variations( $filter_variations = true )
	{
		$folders = array( get_template_directory().'/variations' );
		if( is_child_theme() )
			array_push( $folders, get_stylesheet_directory().'/variations' );

		$directories = $this->get_directories( $folders );

		$variations = array();
		foreach( $directories as $dir )
		{
			$variation_name = '';
			
			if( file_exists($dir.'/style.css') )
			{
				$data = get_file_data( $dir.'/style.css', array('variation'=>'Variation Name') );
				if( array_key_exists('variation', $data) ) $variation_name = $data['variation'];
			}
			
			if( $variation_name === '' ) $variation_name = basename($dir);
			$variations[basename($dir)] = $variation_name;
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
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_custom_post_types()
	{
		$folders = array( get_template_directory().'/custom-post-types' );
		if( is_child_theme() )
			array_push( $folders, get_stylesheet_directory().'/custom-post-types' );

		return array_keys( $this->get_directories( $folders ) );
	}
	
	
//========================================================================================
//======================================================================= Directories ====

	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function get_directories( $folders, $find_style_css = false )
	{
		$filename = '';
		if( $find_style_css ) $filename = DIRECTORY_SEPARATOR.'style.css';
		
		
		$directories['default'] = get_template_directory().$filename;
		foreach( $folders as $folder )
		{
			if(is_dir($folder.DIRECTORY_SEPARATOR.'default'))
			{
				$directories['default'] = $folder.DIRECTORY_SEPARATOR.'default'.$filename;
			}
		}
		
		foreach( $folders as $folder )
		{
			if( !file_exists($folder) ) continue;
			
			$files = scandir( $folder );
			foreach( $files as $name )
			{
				if( (!in_array($name, array('.','..'))) && 
				    (is_dir($folder.DIRECTORY_SEPARATOR.$name)) )
				{
					$directories[$name] = $folder.DIRECTORY_SEPARATOR.$name.$filename;
				}
			}
		}
		
		return $directories;
	}


//========================================================================================
//========================================================================== Database ====
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function get_upgraded_db_options()
	{
		$db_version = get_option( 'uncc-db-version', false );
		
		switch( $db_version )
		{
			case '1.0':
				break;
			
			default:
				break;
		}
		
		return get_option( 'uncc-options', array() );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function check_db()
	{
		$db_version = get_option( 'uncc-db-version', false );
		if( ($db_version === false) || ($db_version === self::DB_VERSION) ) return;
		
		switch( $db_version )
		{
			case '1.0':
				// $this->convert_db_from_10_to_11();
				// new version function here...
			default:
				break;
		}
		
		update_option( 'uncc-db-version', self::DB_VERSION );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function convert_db_from_10_to_11()
	{
	}
	
	
}

