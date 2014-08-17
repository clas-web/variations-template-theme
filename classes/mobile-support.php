<?php


require_once( dirname(__FILE__).'/../libraries/mobile-detect/Mobile-Detect-2.7.1/Mobile_Detect.php' );


/**
 *
 */
class Mobile_Support
{
	public $is_mobile;
	public $use_mobile_site;
	public $device_type;
	
	public function __construct()
	{
		$detect = new Mobile_Detect;
		
		$this->device_type = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
		$this->is_mobile = ($detect->isMobile() && !$detect->isTablet());

		if(!session_id()) session_start();
		
		//for testing purposes...
		//$this->is_mobile = true;
		//$this->set_use_mobile_site(true);
		//return;
		
		if( isset($_GET['mobile']) )
		{
			$this->set_use_mobile_site(true);
			/*
			switch( $_GET['mobile'] )
			{
				case '0':
					$this->set_use_mobile_site(false);
					return; break;
					
				case '1':
					$this->set_use_mobile_site(true);
					return; break;
			}
			*/
		}

		if( isset($_GET['full']) )
		{
			$this->set_use_mobile_site(false);
		}
		
		if( isset($_SESSION['use_mobile_site']) )
		{
			switch( $_SESSION['use_mobile_site'] )
			{
				case '0':
					$this->set_use_mobile_site(false);
					return; break;
					
				case '1':
					$this->set_use_mobile_site(true);
					return; break;
			}
		}	

		$this->set_use_mobile_site($this->is_mobile);
	}
	
	private function set_use_mobile_site( $use_mobile_site )
	{
		$this->use_mobile_site = $use_mobile_site;

		$session_value = '0';
		if( $use_mobile_site ) $session_value = '1';
		
		$_SESSION['use_mobile_site'] = $session_value;

		//$expire_time = time() + (60 * 60 * 24 * 30);
		//setcookie( 'use_mobile_site', $cookie_value, $expire_time );
	}
	
}

