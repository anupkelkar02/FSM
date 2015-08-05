<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Site_admin_controller extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->config('site_admin_controller');

		$this->set_template('admin');

		if ( $this->config->item('css_file') ) {
			$this->add_css_file($this->config->item('css_file'));
		}
		if ( $this->config->item('js_file') ) {
			$this->add_javascript_file($this->config->item('js_file'));
		}
		
		$this->set_fav_icon('images/favicon.ico');

	
		if ( !$this->login_session->load_active_user('Admin') ) {
			redirect('admin/login');
		}
	
		if ( !$this->user->is_group('admin') ) {
			$this->set_error_message('Invalid access');
			redirect('admin/login');
		}

		if ( $this->config->item('autoload_language_folder') ) {
			$this->set_autoload_language_folder( $this->config->item('autoload_language_folder'));
		}
		
		$this->load->library('menu', array('config'=>'admin/menu'));
		$this->set_output_data('menubar', $this->menu->show());
		$this->set_output_data('user', $this->user);
	}
	
	

}

?>
