<?php
if( !class_exists('WP_Customize_Control') )
	require_once( ABSPATH . '/wp-includes/class-wp-customize-control.php' );


/**
 * Theme Customizer color with alpha control.
 *
 * @package    variations-template-theme
 * @author     Pluto <steven@plutomedia.co.nz>
 * @author     Crystal Barton <atrus1701@gmail.com>
 * @link       http://pluto.kiwi.nz/2014/07/how-to-add-a-color-control-with-alphaopacity-to-the-wordpress-theme-customizer/
 * @version    1.1
 */
if( !class_exists('Pluto_Customize_Alpha_Color_Control') ):
class Pluto_Customize_Alpha_Color_Control extends WP_Customize_Control
{
	/**
	 * The type of the theme customizer control.
	 * @var  string
	 */
	public $type = 'alphacolor';

	/**
	 * 
	 * @var  bool
	 */
	public $palette = true;
	
	
	/**
	 * Constructor.
	 *
	 * Supplied $args override class property defaults.
	 *
	 * If $args['settings'] is not defined, use the $id as the setting ID.
	 * 
	 * @param WP_Customize_Manager $manager
	 * @param string $id
	 * @param array $args
	 */
	public function __construct( $manager, $id, $args = array() ) 
	{
		parent::__construct( $manager, $id, $args );
	}	


	/**
	 * Enqueue the control's script and styles.
	 */
	public function enqueue()
	{
		vtt_enqueue_files( 'script', 'customizer-color-picker-alpha', 'classes/customizer/color-picker-alpha/script.js', array( 'customize-controls' ) );
		vtt_enqueue_files( 'style', 'customizer-color-picker-alpha', 'classes/customizer/color-picker-alpha/style.css' );
	}
	

	/**
	 * Renders the control wrapper and calls $this->render_content() for the internals.
	 */
    protected function render()
    {
		$id = 'customize-control-' . str_replace( '[', '-', str_replace( ']', '', $this->id ) );
		$class = 'customize-control customize-control-' . $this->type;
		
		?>
		<li id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>">
			<?php $this->render_content(); ?>
		</li>
    	<?php
    }	
	

	/**
	 * Render the control's content.
	 */
	public function render_content()
	{
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<label>
			<span class="screen-reader-text"><?php echo esc_html( $this->label ); ?></span>
			<input type="text" data-palette="<?php echo $this->palette; ?>" data-default-color="<?php echo $this->default; ?>" value="<?php echo intval( $this->value() ); ?>" class="pluto-color-control" <?php $this->link(); ?>  />
		</label>
		<?php
	}
}
endif;

