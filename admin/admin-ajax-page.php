<?php


abstract class NH_AdminAjaxPage
{

	protected $_status;
	protected $_message;
	protected $_output;

//	abstract public static function get_instance( $page );
	
	public function show()
	{
		$this->process_post();
		$this->output();
	}
	
	abstract protected function process_post();
	
	protected function output()
	{
		echo json_encode(
			array(
				'status' => $this->_status,
				'message' => $this->_message,
				'output' => $this->_output,
			)
		);
	}

}

