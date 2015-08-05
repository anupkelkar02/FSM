<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Site_controller extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->config('site_controller');
		
		$this->set_template($this->config->item('template_name'));
		
		if ( $this->config->item('css_file') ) {
			$this->add_css_file($this->config->item('css_file'));
		}
		if ( $this->config->item('js_file') ) {
			$this->add_javascript_file($this->config->item('js_file'));
		}
		
		$this->set_title($this->config->item('title'));		
		$this->set_fav_icon('images/favicon.ico');

		$this->load->language('site');

		if ( is_string($this->config->item('autoload_language_folder')) ) {
			$this->set_autoload_language_folder( $this->config->item('autoload_language_folder'));
		}		
		$this->load->library('menu', array('config'=>'menu', 'name'=>'items'));
		$this->set_output_data('menu_bar', $this->menu->show());
		$this->set_output_data('title_long', $this->config->item('title_long'));
		
	}

}

?>
