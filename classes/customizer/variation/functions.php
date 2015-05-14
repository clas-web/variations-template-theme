<?php


add_action( 'wp_ajax_vtt-variation-customizer-control', 'vtt_variation_customizer_control_action' );




if( !function_exists('vtt_variation_customizer_control_action') ):
function vtt_variation_customizer_control_action()
{
// 	echo 'vtt_variation_customizer_control_action';
	
	$nonce = $_POST['nonce'];
	$action = $_POST['vtt-action'];
	$value = $_POST['value'];
	
	
	$output = array(
		'status'	=> true,
		'message'	=> ''
	);
	
	if( check_ajax_referer('vtt-variation', 'nonce', false) == false )
	{
		$output['status'] = false;
		$output['message'] = 'The submitted data cannot be verified.';
		echo json_encode($output);
		exit();
	}
	
	switch( $action )
	{
		case 'change-variation':
			global $vtt_config;
			$vtt_config->set_variation( $value, true );
			break;
		
		case 'reset':
			delete_option( 'vtt-options' );
			remove_theme_mods();
			break;
		
		default:
			$output['status'] = false;
			$output['message'] = 'Invalid action: '.$action;
			break;
	}
	
	echo json_encode($output);
	exit();
}
endif;
