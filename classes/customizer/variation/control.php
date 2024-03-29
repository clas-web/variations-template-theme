<?php
if( !class_exists('WP_Customize_Control') )
	require_once( ABSPATH . '/wp-includes/class-wp-customize-control.php' );
require_once( __DIR__.'/functions.php' );

/**
 * Theme Customizer variation selector.
 *
 * @package    variations-template-theme
 * @author     Crystal Barton <atrus1701@gmail.com>
 * @version    1.0
 */
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
		
		$this->choices = $vtt_config->get_all_site_variation_names();

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
			<input type="hidden" name="vtt-variation-nonce" value="<?php echo wp_create_nonce('vtt-variation') ?>" />
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>

			<select>
				<?php

				$theme_mod = get_theme_mod('vtt-variation-choices');
				foreach ( $this->choices as $value => $label )
				
// 				if(in_array($value, get_theme_mod('vtt-variation-choices'))){
// 					echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . $label . '</option>';
// 				}
				

				if (is_array($theme_mod) && in_array($value, $theme_mod)) {
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . $label . '</option>';
				}
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

class VTT_Customize_Variation_Checkboxes extends WP_Customize_Control {

    /**
     * The type of customize control being rendered.
     *
     * @since  1.0.0
     * @access public
     * @var    string
     */
    public $type = 'checkbox-multiple';

    
    /**
     * Displays the control content.
     *
     * @since  1.0.0
     * @access public
     * @return void
     */
    public function render_content() {

        if ( empty( $this->choices ) )
            return; ?>

        <?php if ( !empty( $this->label ) ) : ?>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
        <?php endif; ?>

        <?php if ( !empty( $this->description ) ) : ?>
            <span class="description customize-control-description"><?php echo $this->description; ?></span>
        <?php endif; ?>

        <?php $multi_values = !is_array( $this->value() ) ? explode( ',', $this->value() ) : $this->value(); ?>

        <ul>
            <?php foreach ( $this->choices as $value => $label ) : ?>

                <li>
                    <label>
                        <input type="checkbox" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $multi_values ) ); ?> /> 
                        <?php echo esc_html( $label ); ?>
                    </label>
                </li>

            <?php endforeach; ?>
        </ul>

        <input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( implode( ',', $multi_values ) ); ?>" />
    <?php }
}

