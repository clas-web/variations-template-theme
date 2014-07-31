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
		$this->check_db();

		$variation = $this->get_current_variation();
		
		$config_ini = array();
		$options_ini = array();
		$db_options = array();
		
		// 
		// load config.ini data.
		// 
		if( $this->load_from_ini( $config_ini, get_stylesheet_directory().'/variations/'.$variation.'/'.self::CONFIG_INI_FILENAME ) );
		elseif( $this->load_from_ini( $config_ini, get_template_directory().'/variations/'.$variation.'/'.self::CONFIG_INI_FILENAME ) );
		elseif( $this->load_from_ini( $config_ini, get_stylesheet_directory().'/'.self::CONFIG_INI_FILENAME ) );
		elseif( $this->load_from_ini( $config_ini, get_template_directory().'/'.self::CONFIG_INI_FILENAME ) );
		elseif( $this->load_from_ini( $config_ini, get_template_directory().'/'.self::CONFIG_DEFAULT_INI_FILENAME ) );
		else exit( 'Unable to locate theme '.self::CONFIG_INI_FILENAME.' file.' );
		
		// 
		// load options.ini data.
		// 
		if( $this->load_from_ini( $options_ini, get_stylesheet_directory().'/variations/'.$variation.'/'.self::OPTIONS_INI_FILENAME ) );
		elseif( $this->load_from_ini( $options_ini, get_template_directory().'/variations/'.$variation.'/'.self::OPTIONS_INI_FILENAME ) );
		elseif( $this->load_from_ini( $options_ini, get_stylesheet_directory().'/'.self::OPTIONS_INI_FILENAME ) );
		elseif( $this->load_from_ini( $options_ini, get_template_directory().'/'.self::OPTIONS_INI_FILENAME ) );
		elseif( $this->load_from_ini( $options_ini, get_template_directory().'/'.self::OPTIONS_DEFAULT_INI_FILENAME ) );
		else exit( 'Unable to locate theme '.self::OPTIONS_INI_FILENAME.' file.' );
		
		// 
		// load database options.
		// 
		$db_options = get_option( 'uncc-options', array() );
		if( empty($db_options) || !is_array($db_options) ) $db_options = array();

		$replace = array();
		
// 		if( !isset($_POST) || empty($_POST) )
// 		{
// 		nh_print( $options_ini, 'options ini' );
// 		nh_print( $db_options, 'db options' );
// 		}

		//
		// set config data.
		//
		$this->config = $config_ini;
				
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
		$this->data = array_replace_recursive( $this->options, $config_ini );
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
		
		//
		// Update the database version.
		//
		update_option( 'uncc-db-version', self::DB_VERSION );
		
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
		$variation = get_option( 'uncc-variation', false );
		
		if( $variation === false ) return $this->set_variation();
		
		$variations = $this->get_variations();
		foreach( $variations as $var )
		{
			if( $variation === $var ) return $variation;
		}
		
		return $this->set_variation();
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function set_variation( $name = 'default' )
	{
		update_option( 'uncc-variation', $name );
		return $name;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_variations()
	{
		$folders = array( get_template_directory().'/variations' );
		if( is_child_theme() )
			array_push( $folders, get_stylesheet_directory().'/variations' );

		return $this->get_directories( $folders );
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

		return $this->get_directories( $folders );
	}
	
	
//========================================================================================
//======================================================================= Directories ====

	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function get_directories( $folders )
	{
		$directories = array();		
		foreach( $folders as $folder )
		{
			$files = scandir( $folder );
			foreach( $files as $file )
			{
				if( (!in_array($file, array('.','..'))) && 
				    (is_dir($folder.DIRECTORY_SEPARATOR.$file)) )
				{
					array_push( $directories, $file );
				}
			}
		}
		
		return array_unique( $directories );
	}


//========================================================================================
//========================================================================== Database ====
	
	
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
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function convert_db_from_10_to_11()
	{
	}
	
	
}

