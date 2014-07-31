<?php

/**
 *
 */
class NH_AdminAjaxPage_Banner extends NH_AdminAjaxPage
{

	private static $_instance = null;
	

	/* Default private constructor. */
	private function __construct()
	{
		$this->_status = true;
		$this->_message = '';
		$this->_output = null;
	}
	
	
	/**
	 *
	 */	
	public static function get_instance()
	{
		if( self::$_instance === null )
		{
			self::$_instance = new NH_AdminAjaxPage_Banner();
		}
		
		return self::$_instance;
	}	
	
	
	/**
	 *
	 */
	protected function process_post()
	{
// 		if( !isset($_POST['nonce']) || 
// 			!wp_verify_nonce($_POST['nonce'], "nh-banner-options-nonce") )
// 		{
// 			$this->_status = false;
// 			$this->_message = 'Invalid nonce code ('.$_POST['nonce'].').';
// 			return;
//    		}
   		
		if( !isset($_POST) )
		{
			$this->_status = false;
			$this->_message = 'No post data.';
			return;
		}
		
		if( !isset($_POST['ajax-action']) )
		{
			$this->_status = false;
			$this->_message = 'No action post data.';
			return;
		}

		switch( $_POST['ajax-action'] )
		{
			case 'delete-banner':
				$this->delete_banner();
				break;
			
			default:
				$this->_status = false;
				$this->_message = 'Invalid ajax-action type ('.$_POST['ajax-action'].').';
				break;
		}	
	}


	/**
	 *
	 */
	private function delete_banner()
	{
		if( !isset($_POST['banner-id']) )
		{
			$this->_status = false;
			$this->_message = 'No banner id data.';
			return;
		}
		
		$banner_id = $_POST['banner-id'];
		
		if( wp_delete_attachment( $banner_id, true ) === false )
		{
			$this->_status = false;
			$this->_message = 'Deleting the banner attachment failed.';
			return;
		}
		
		$this->_message = 'The banner attachment was deleted.';
	}

}

