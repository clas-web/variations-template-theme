<?php
/**
 * Functions for the Theme Customizer variation selector.
 *
 * @package    variations-template-theme
 * @author     Crystal Barton <atrus1701@gmail.com>
 * @version    1.0
 */

add_action( 'wp_ajax_vtt-variation-customizer-control', 'vtt_variation_customizer_control_action' );


/**
 * Setup the AJAX referer that will process requests to change the variation.
 */
if( !function_exists('vtt_variation_customizer_control_action') ):
function vtt_variation_customizer_control_action()
{
	$nonce = $_POST['nonce'];
	$action = $_POST['vtt-action'];
	$value = $_POST['value'];
	
	// Default ouput array.
	$output = array(
		'status'	=> true,
		'message'	=> ''
	);
	
	// Check that the nonce is valid for the request.
	if( check_ajax_referer('vtt-variation', 'nonce', false) == false )
	{
		$output['status'] = false;
		$output['message'] = 'The submitted data cannot be verified.';
		echo json_encode($output);
		exit();
	}
	
	// Change the variation.
	switch( $action )
	{
		case 'change-variation':
			global $vtt_config;
			$vtt_config->set_variation( $value, true );
			break;
		
		case 'reset':
			delete_option( VTT_OPTIONS );
			remove_theme_mods();
			break;
		
		default:
			$output['status'] = false;
			$output['message'] = 'Invalid action: '.$action;
			break;
	}
	
	// Serialize the results of the change.
	echo json_encode($output);
	exit();
}
endif;

