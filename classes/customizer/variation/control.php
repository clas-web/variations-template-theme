<?php

if( !class_exists('WP_Customize_Control') )
	require_once( ABSPATH . '/wp-includes/class-wp-customize-control.php' );

require_once( dirname(__FILE__).'/functions.php' );

if( !class_exists('VTT_Customize_Variation') ):
class VTT_Customize_Variation extends WP_Customize_Control
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
		
		global $vtt_config;
		
		$this->description = 'Changing this option will cause the page to refresh.';
		$this->type = 'select';
		
		$this->choices = $vtt_config->get_all_variation_names();

		$settings = array();
		$this->setting = $this->manager->get_setting( 'vtt-variation' );
		$settings['default'] = $this->setting;
		$this->settings = $settings;
	}

	/**
	 * Enqueue control related scripts/styles.
	 */
	public function enqueue()
	{
		vtt_enqueue_files( 'script', 'customizer-variation', 'classes/customizer/variation/script.js', array( 'customize-controls' ) );
//		vtt_enqueue_files( 'style', 'customizer-variation', 'classes/customizer/variation/style.css' );
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
	 *
	 * Allows the content to be overriden without having to rewrite the wrapper in $this->render().
	 *
	 * Control content can alternately be rendered in JS. See {@see WP_Customize_Control::print_template()}.
	 */
	protected function render_content()
	{
		?>
		<label>
			<input type="hidden" name="vtt-variation-nonce" value="<?php echo wp_create_nonce('vtt-variation') ?>" />
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>

			<select>
				<?php
				foreach ( $this->choices as $value => $label )
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . $label . '</option>';
				?>
			</select>
		</label>
		
		<label style="display:block;margin-top:0.5em;">
			<button>Reset</button>&nbsp;&nbsp;<span>Return to defaults</span>
		</label>
		<?php
	}
}
endif;

