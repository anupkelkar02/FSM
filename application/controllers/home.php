<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


 
require_once(APPPATH.'/core/site_controller'.EXT);

class Home extends Site_Controller 
{


	public function index()
	{
//		echo openssl_digest('test', 'md5');
		$this->load->view('home');
	}
}

