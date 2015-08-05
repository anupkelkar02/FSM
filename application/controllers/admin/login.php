<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Login extends MY_Controller
{
	public function index()
	{
        $this->set_output_mode(self::OUTPUT_NORMAL);

		$this->load->helper(array('form'));
		$this->load->library(array('form_validation'));
		$this->load->language('admin/login');
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$data = array('error_message'=> '');
		if ( $this->form_validation->run() == true ) {
			$this->load->model('user_model');
			$username = $this->form_validation->set_value('username');
			$password = $this->form_validation->set_value('password');
			if ( $this->login_session->login($username, $password, 'Admin') ) {
				redirect('admin/home');
			}
			else {
				$data = array();
				set_message_note($this->lang->line('error_invalid_access'), MESSAGE_NOTE_FAILURE);
			}
		}
		$this->load->view('admin/login', $data);
	}
	public function reset()
	{
		$this->load->library('login_session');
		$this->load->model('user_model');
		$row = $this->user_model->get_row_by_username('Admin');
		if ( $row ) {
			$this->user_model->update_row(array('password'=>$this->login_session->encrypt_password($row->username, 'adm1nr0st3r')), $row->id);
			echo "Reset Password";
		}
	}


}

?>
