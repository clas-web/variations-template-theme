<?php
/**
 * UNCC_ThemeOptionsVariationsTabAdminPage
 * 
 * This class controls the admin page "Theme Options > Variations".
 * 
 * @package    uncc
 * @subpackage admin-pages/pages
 * @author     Crystal Barton <cbarto11@uncc.edu>
 */

if( !class_exists('UNCC_ThemeOptionsVariationsTabAdminPage') ):
class UNCC_ThemeOptionsVariationsTabAdminPage extends APL_TabAdminPage
{
	
	private $model = null;	
	private $list_table = null;
	
	private $filter_types;
	private $filter;
	private $search;
	private $orderby;
	
	
	/**
	 * Creates an UNCC_ThemeOptionsVariationsTabAdminPage object.
	 */
	public function __construct( 
		$parent,
		$name = 'variations', 
		$tab_title = 'Variations', 
		$page_title = 'Variations' )
	{
		parent::__construct( $parent, $name, $tab_title, $page_title );
	}


	/**
	 * Register each individual settings for the Settings API.
	 */
	public function register_settings()
	{
		$this->register_setting( UNC_CHARLOTTE_THEME_OPTIONS );
	}
	

	/**
	 * Add the sections used for the Settings API. 
	 */
	public function add_settings_sections()
	{
		$this->add_section(
			'uncc-theme-variations',
			'Variations',
			'print_section_variations'
		);
	}
	
	
	/**
	 * Add the settings used for the Settings API. 
	 */
	public function add_settings_fields()
	{
		$this->add_field(
			'uncc-theme-variations',
			'current-variation',
			'Current Variation',
			'print_field_current_variation'
		);
	}
	
	
	public function print_section_variations()
	{
		apl_print( 'print_variations_section' );
	}
	
	public function print_field_current_variation( $args )
	{
		global $uncc_config;
		
		$current_variation = $uncc_config->get_current_variation();
		$variations = $uncc_config->get_variations();
		?>
		
		<select name="<?php uncc_name_e( 'variations', 'variation' ); ?>">
		
		<?php foreach( $variations as $key => $name ): ?>
			<option value="<?php echo $key; ?>" 
			        <?php selected( $key, $current_variation ); ?>>
				<?php echo $name; ?>
			</option>
		<?php endforeach; ?>
		
		</select>
		
		<div>
		<input type="checkbox" 
		       name="<?php uncc_name_e( 'variations', 'reset-options' ); ?>" 
		       value="reset-options" />
		Reset options?
		</div>

		<?php
	}
	
	
	/**
	 * Processes the current admin page.
	 */
	public function process()
	{
		if( empty($_REQUEST['action']) ) return;
		
		switch( $_REQUEST['action'] )
		{
// 			case 'refresh':
// 				break;
		}
	}
	
	
	/**
	 * Processes the current admin page's Settings API input.
	 * @param   array   $settings  The inputted settings from the Settings API.
	 * @param   string  $option    The option key of the settings input array.
	 * @return  array   The resulted array to store in the db.
	 */
	public function process_settings( $settings, $option )
	{
		if( $option !== UNC_CHARLOTTE_THEME_OPTIONS ) return $settings;

		global $uncc_config;
		if( isset($settings['variations']) ):
			
			$tab_input = $settings['variations'];
			
			if( isset($tab_input['variation']) ):
				
				$variations = $uncc_config->get_variations();
				$chosen_variation = $tab_input['variation'];
				
				if( (!array_key_exists($chosen_variation, $variations)) && ($chosen_variation !== 'default') )
				{
					add_settings_error( '', '', 'Invalid variation: '.$chosen_variation );
					return parent::process_settings( $settings, $option );
				}
				
				$uncc_config->set_variation( $chosen_variation );
				
			endif;

			if( isset($settings['reset-options']) ):
			
				add_settings_error( '', '', 'Settings saved.', 'updated' );
				$new_options = array();
				add_settings_error( '', '', 'Reset options for variation: '.$chosen_variation, 'updated' );
				return $new_options;
			
			endif;
			
		endif;
		
		return parent::process_settings( $settings, $option );
	}
	
	
	/**
	 * Displays the current admin page.
	 */
	public function display()
	{
		$this->print_settings();
	}
	
} // class UNCC_ThemeOptionsVariationsTabAdminPage extends APL_TabAdminPage
endif; // if( !class_exists('UNCC_ThemeOptionsVariationsTabAdminPage') )

