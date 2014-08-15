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

		//for testing purposes...
		//$this->is_mobile = true;
		//$this->set_use_mobile_site(true);
		//return;
		
		if( isset($_GET['mobile']) )
		{
			$this->set_use_mobile_site(true);
			return;
		}

		if( isset($_GET['full']) )
		{
			$this->set_use_mobile_site(false);
			return;
		}
		
		switch( get_transient('use_site_type') )
		{
			case 'full':
				$this->set_use_mobile_site(false);
				return;
				
			case 'mobile':
				$this->set_use_mobile_site(true);
				return;
		}

		$this->set_use_mobile_site($this->is_mobile);
	}
	
	private function set_use_mobile_site( $use_mobile_site )
	{
		$this->use_mobile_site = $use_mobile_site;

		if( $use_mobile_site ) $value = 'mobile';
		else $value = 'full';
		
		set_transient( 'use_site_type', $value );

		//$expire_time = time() + (60 * 60 * 24 * 30);
		//setcookie( 'use_mobile_site', $cookie_value, $expire_time );
	}
	
}





