<?php
/**
 * Original code found here:
 * http://pluto.kiwi.nz/2014/07/how-to-add-a-color-control-with-alphaopacity-to-the-wordpress-theme-customizer/
 *
 * Cleaned up by Crystal Barton.
 */


if( !class_exists('WP_Customize_Control') )
	require_once( ABSPATH . '/wp-includes/class-wp-customize-control.php' );

if( !class_exists('Pluto_Customize_Alpha_Color_Control') ):
class Pluto_Customize_Alpha_Color_Control extends WP_Customize_Control
{
	public $type = 'alphacolor';
	public $palette = true;
	
	
	public function __construct( $manager, $id, $args = array() ) 
	{
		parent::__construct( $manager, $id, $args );
	}	

	public function enqueue()
	{
		vtt_enqueue_files( 'script', 'customizer-color-picker-alpha', 'classes/customizer/color-picker-alpha/script.js', array( 'customize-controls' ) );
		vtt_enqueue_files( 'style', 'customizer-color-picker-alpha', 'classes/customizer/color-picker-alpha/style.css' );
	}
	
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
	
	public function render_content()
	{
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<input type="text" data-palette="<?php echo $this->palette; ?>" data-default-color="<?php echo $this->default; ?>" value="<?php echo intval( $this->value() ); ?>" class="pluto-color-control" <?php $this->link(); ?>  />
		</label>
		<?php
	}
}
endif;

