<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'core/site_controller'.EXT);


class Register extends Site_Controller 
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->library(array('form_validation'));
		$this->load->model('register_model');
	}
	
	public function index()
	{
		$this->register_model->clear_as_new();
		$row = $this->register_model->get_row();
		
		$this->form_validation->set_rules('email', 'Email Address', 'required|valid_email');
		$this->form_validation->set_rules('username', 'Username', 'required|alpha_dash');
		$this->form_validation->set_rules('first_name', 'First Name', 'required|alpha');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|alpha');
			
		
		$row = $this->form_validation->get_input_row($row);
		
		if ( $this->input->post('register') == 'Register') {
			
			$this->_save_user($row);
		}

		$data = array('row'=>$row,
					);
		$this->load->view('register', $data);
	}
	
	protected function _save_user($input_row)
	{
		if ( $this->form_validation->run() ) {
			$rows = $this->register_model->get_rows(array('username'=>$this->db->escape($input_row->username)));
			if ( count($rows) == 0 ) {
			}
			print_r($rows);
		}
		else {
			set_message_note($this->form_validation->error_string(), MESSAGE_NOTE_WARNING);
		}
	}
}

?>
