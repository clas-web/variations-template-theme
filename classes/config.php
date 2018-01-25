<?php
/**
 * Handles the config data and processing, including the current variation and variation directories.
 * 
 * @package    variations-template-theme
 * @author     Crystal Barton <atrus1701@gmail.com>
 */

if( !class_exists('VTT_Config') ):
class VTT_Config
{
	/**
	 * Current variation name.
	 * @var  string
	 */
	private $current_variation;

	
	/**
	 * A list of valid variations for the current theme.
	 * @var  Array
	 */
	private $valid_variations;
	
	
	/**
	 * A list of all variations keyed by the variation name.
	 * @var  Array
	 */
	private $all_variations;


	/**
	 * A list of variation data for all valid variations for the current theme.
	 * @var  Array
	 */
	private $filtered_variations;


	/**
	 * The config data for the theme which cannot be overwritten by the options or theme mods.
	 * @var  Array
	 */
	private $config;


	/**
	 * The options data with the default options combined with the current theme mods.
	 * @var  Array
	 */
	private  $options;


	/**
	 * The complete combined data of the options and config data.
	 * @var  Array
	 */
	private  $data;


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

		$this->check_db();

		// Initialize class variables.
		$this->all_variations = array();
		$this->filtered_variations = array();
		$this->current_variation = null;
		
		// Determine all directories that could have variations.
		$this->search_directories = array();
		do_action( 'vtt-search-folders' );

		if( !array_key_exists(5, $this->search_directories) )
			$this->search_directories[5] = array();
		array_unshift( $this->search_directories[5], get_template_directory() );
		
		if( is_child_theme() && !array_key_exists(10, $this->search_directories) ) 
		{
			$this->search_directories[10] = array();
			array_unshift( $this->search_directories[10], get_stylesheet_directory() );
		}
		
		ksort( $this->search_directories );

		// Get list of valid variations for the current theme.
		$this->valid_variations = apply_filters( 'vtt-valid-variations', array('default','dark') );

		// Get all variation directories.
		$this->get_all_variations();
		$this->get_filtered_variations();

		// Get the current variation with directory data.
		$variation = $this->get_current_variation();
		$this->current_variation = $this->all_variations[$variation];
		$variation_directories = $this->get_all_available_variations_directories();

		// Load the variation functions.php file before loading options.
		$this->load_variations_files( 'functions.php' );

		// Action to allow variations to update the db before getting the options.
		do_action( 'vtt-update-db' );

		// Get options data.
		$this->config = apply_filters( 'vtt-config', array() );
		$this->options = apply_filters( 'vtt-options', array() );
		$this->options = array_merge( $this->options, get_theme_mods() );

		if( isset($wp_customize) && !empty($_POST['customized']) )
		{
			$values = json_decode(wp_unslash($_POST['customized']), true);
			$this->options = array_merge( $this->options, $values );
		}

		$this->data = array_merge( $this->options, $this->config );
	}


	/**
	 * Get a value from the options using a list of keys as the parameters.
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
				$config = $config[$arg];
			else
				$config = null;

			if( $config === null ) break;
		}

		return $config;
	}
	
	
	/**
	 * Set a value in the options using a list of keys as parameters.
	 * The last parameters is the value to save.
	 * @param  string  {args}  A number of key values used to set data.
	 * @param  mixed  {last arg}  The value to save.
	 */
	public function set_value()
	{
		$args = func_get_args();
		if( count($args) == 1 && is_array($args[0]) ) $args = $args[0];
		
		$value = array_pop( $args );

		if( count($args) === 0 ) return;
		

		$options =& $this->options;
		
		foreach( $args as $arg )
		{
			if( !is_array($options) )
				$options = array();
			if( !array_key_exists($arg, $options) )
				$options[$arg] = array();

			$options =& $options[$arg];
		}

		$options = $value;

		$theme_slug = get_option( 'stylesheet' );
		update_option( "theme_mods_$theme_slug", $this->options );
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
			'selection-type' => 'relative',
			'attachment-id'  => -1,
			'path'           => '',
			'class'          => null,
			'title'          => null,
			'use-site-link'  => false,
			'link'           => '',
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
			'class'         => null,
			'title'         => null,
			'use-site-link'	=> false,
			'link' 			=> '',
		);
		
		$text_data = array_merge( $defaults, $text_data );
		return $text_data;
	}
	
	
	/**
	 * Gets the current variation's name.
	 * @return  string  The current variation's name.
	 */
	private function get_current_variation()
	{
		if( $this->current_variation !== null ) return $this->current_variation;
		
		$variation = get_theme_mod( 'vtt-variation' );
		if( !$variation ) return $this->get_default_variation();
		
		if( array_key_exists($variation, $this->filtered_variations) ) return $variation;
		
		$vnames = array_keys($this->filtered_variations);
		if( count($this->filtered_variations) > 0 )
			//return $this->filtered_variations[$vnames[0]]['name'];
			return get_theme_mod('vtt-variation');
		return 'light';
	}
	
	
	/**
	 * Sets the current variation in the database.
	 * @param  string  $name  The name of the current variation.
	 * @return  string  The current variation's name.
	 */
	public function set_variation( $name = '' )
	{
		if( $name === '' ) $name = $this->get_default_variation();
		$this->set_value( 'vtt-variation', $name );
		return $name;
	}


	/**
	 * Get the default variation.
	 * @return  string  The default variation.
	 */
	public function get_default_variation()
	{
		$name = 'light';

		$vnames = array_keys( $this->filtered_variations );
		if( count($this->filtered_variations) > 0 )
			$name = $this->filtered_variations[$vnames[0]]['name'];

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
		foreach( $this->all_variations as $variation_key => $variation )
		{
			if( in_array($variation_key, $this->valid_variations) )
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
					if( !array_key_exists('light', $directories) )
						$directories['light'] = array();
					$directories['light'][] = get_template_directory();
					break;
				case 10:
					if( is_child_theme() )
					{
						if( !array_key_exists('light', $directories) )
							$directories['light'] = array();
						$directories['light'][] = get_stylesheet_directory();
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
					if( !in_array($dir, $directory['all']) )
						$directory['all'][] = $dir;
					if( !in_array($dir.'/variations/'.$name, $directory['all']) )
						$directory['all'][] = $dir.'/variations/'.$name;
				}
			}
		}

		// Make sure all directories exists.
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
	
/*This function has been replaced by get_all_site_variation_names
Leaving to avoid potential conflicts or unforeseen consequences of removal.
*/	
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
	 * Get all the variations that are found and are allowed by the site.
	 * @return  Array  The array of variation names.
	 *                 The key of the array is the name and value is the title.
	 */
	public function get_all_site_variation_names()
	{
		$names = array();
		
		foreach( $this->all_variations as $name => $variation )
		{
			$names[$name] = $variation['title'];
		}
		
		return $names;
	}
	
	
	/**
	 * Check the database to check if an update is needed.
	 * If needed, then convert database options to the new version and update the database version.
	 */
	private function check_db()
	{
		$db_version = get_theme_mod( 'vtt-db-version' );
		if( ($db_version === false) || ($db_version === VTT_DB_VERSION) ) return;
		
		switch( $db_version )
		{
			default:
				break;
		}
		
		update_theme_mod( 'vtt-db-version', VTT_DB_VERSION );
	}
}
endif;

