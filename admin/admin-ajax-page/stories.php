<?php

/**
 *
 */
class NH_AdminAjaxPage_Stories extends NH_AdminAjaxPage
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
			self::$_instance = new NH_AdminAjaxPage_Stories();
		}
		
		return self::$_instance;
	}	
	
	
	/**
	 *
	 */
	protected function process_post()
	{
// 		if( !isset($_POST['nonce']) || 
// 			!wp_verify_nonce($_POST['nonce'], "nh-stories-options-nonce") )
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
			case 'get-search-results':
				$this->get_search_results();
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
	private function get_search_results()
	{
		global $uncc_config;
		
		if( !isset($_POST['section']) )
		{
			$this->_status = false;
			$this->_message = 'No section data.';
			return;
		}
		
		if( !isset($_POST['search_text']) )
		{
			$this->_status = false;
			$this->_message = 'No search text.';
			return;
		}
		
		$section = $uncc_config->get_section_by_key( $_POST['section'] );
		
		if( $section === null )
		{
			$this->_status = false;
			$this->_message = 'Invalid section: "'.$_POST['section'].'"';
			return;
		}
		
		$search_results = $section->get_search_results( $_POST['search_text'] );
		if( $search_results === false )
		{
			$this->_status = false;
			$this->_message = 'Unable to retrieve search results.';
			return;
		}
		
		$this->_output = $search_results;
		$this->_message = 'The search results were successfully retrieved.';
	}

}

