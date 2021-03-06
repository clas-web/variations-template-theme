<?php
if( !class_exists('WP_Customize_Control') )
	require_once( ABSPATH . '/wp-includes/class-wp-customize-control.php' );


/**
 * Theme Customizer header position control for the default variations-template-theme.
 *
 * @package    variations-template-theme
 * @author     Crystal Barton <atrus1701@gmail.com>
 * @version    1.0
 */
if( !class_exists('VTT_Customize_Header_Position') ):
class VTT_Customize_Header_Position extends WP_Customize_Control
{
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
		
		$this->type = 'select';
		
		$v = array( 
			'vabove'	=> 'Above',
			'vtop'		=> 'Top', 
			'vcenter'	=> 'Middle', 
			'vbottom'	=> 'Bottom' );
		$h = array( 
			'hleft'		=> 'Left', 
			'hcenter'	=> 'Center', 
			'hright'	=> 'Right' );
		
		$this->choices = array();
		foreach( $v as $vvalue => $vname )
		{
			foreach( $h as $hvalue => $hname )
			{
				$this->choices["$hvalue $vvalue"] = "$hname $vname";
			}
		}
	}

	/**
	 * Enqueue control related scripts/styles.
	 */
	public function enqueue()
	{
		vtt_enqueue_files( 'script', 'customizer-header-position', 'classes/customizer/header-position/script.js', array( 'customize-controls' ) );
		vtt_enqueue_files( 'style', 'customizer-header-position', 'classes/customizer/header-position/style.css' );
	}

	/**
	 * Renders the control wrapper and calls $this->render_content() for the internals.
	 */
	protected function render() {
		$id    = 'customize-control-' . str_replace( '[', '-', str_replace( ']', '', $this->id ) );
		$class = 'customize-control customize-control-' . $this->type;

		?><li id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>">
			<?php $this->render_content(); ?>
		</li><?php
	}

	/**
	 * Render the control's content.
	 */
	protected function render_content()
	{
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo $this->label; ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>

			<select <?php $this->link(); ?>>
				<?php
				foreach ( $this->choices as $value => $label )
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . $label . '</option>';
				?>
			</select>
		</label>
		<?php
	}
}
endif;

