<?php


/**
 * Processes, generates, and displays the plugin's admin page.
 */
abstract class NH_AdminPage
{
	public $slug;
	
//	abstract public static function get_instance( $page );
	
	abstract public function enqueue_scripts();
	abstract public function add_head_script();
	abstract public function register_settings();
	abstract public function add_settings_sections();
	abstract public function add_settings_fields();
	abstract public function process_input( $options, $page, $tab, $option, $input );
	abstract public function show();
}

