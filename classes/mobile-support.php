<?php

require_once( __DIR__.'/../libraries/mobile-detect/Mobile-Detect-2.7.1/Mobile_Detect.php' );

/**
 * Handles checking and storing which version of site to display (full or mobile).
 *
 * @package    variations-template-theme
 * @author     Crystal Barton <atrus1701@gmail.com>
 */
if( !class_exists('VTT_Mobile_Support') ):
class VTT_Mobile_Support
{
	/**
	 * True if the on a mobile phone device, otherwise False.
	 * @var  bool
	 */
	public $is_mobile;

	/**
	 * True if the mobile site should be shown, otherwise False.
	 * @var  bool
	 */
	public $use_mobile_site;

	/**
	 * The type of device the site is being viewed on: 'phone', 'tablet', or 'computer'.
	 * @var  string
	 */
	public $device_type;
	

	/**
	 * Constructor.
	 * Determines the type of device and which site to display and save results in the session.
	 */
	public function __construct()
	{
		$detect = new Mobile_Detect;
		
		$this->device_type = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
		$this->is_mobile = ($detect->isMobile() && !$detect->isTablet());

		if(!session_id()) session_start();
		
		// FOR TESTING
		//$this->is_mobile = true;
		//$this->set_use_mobile_site(true);
		//return;
		
		if( isset($_GET['mobile']) )
		{
			$this->set_use_mobile_site(true);
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
	

	/**
	 * Set session value indicating if the mobile site should be used.
	 * @param  bool  $use_mobile_site  True if the mobile site should be used, otherwise False.
	 */
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
endif;

