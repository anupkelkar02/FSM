<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Manager
{
	protected $_config;
	protected $CI;
	
	public function __construct($params = '', $default_config = '')
	{
		$this->_config = array('config_name'=>strtolower(get_class($this)));

		$this->initialize($params, $default_config);
	}
	
	public function initialize($params = '', $default_config = '')
	{
		$this->CI =& get_instance();
		
		if ( $default_config ) {
			$this->_config = array_merge($this->_config, $default_config);
		}
		if ( $params ) {
			$this->_config = array_merge($this->_config, $params);
		}
		$config_name = $this->_config['config_name'];
		$this->CI->load->config($config_name, TRUE, TRUE);
		if ( $this->CI->config->item($config_name) ) {
			$this->_config = array_merge($this->_config, $this->CI->config->item($config_name));
		}
	}
		
}

?>
