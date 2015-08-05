<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


// menu plugin



class menu_plugin
{

	protected $_menu;
	protected $_config;
	public $CI;
	
	function __construct($menu, $config)
	{
		$this->CI =& get_instance();
		$this->_menu = $menu;
		$this->_config = array_merge($this->get_default_config(), $config);
	}
	
	
	public function setup()
	{


	}
	
	public function show($items)
	{
	}
	
	public function get_default_config()
	{
		return array();
	}	
	

}




?>
