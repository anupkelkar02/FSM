<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/core/site_admin_controller'.EXT);

class Logout extends Site_admin_controller
{	
	public function index()
	{
		$this->login_session->logout();
		redirect(base_url());
	}

}

?>
