<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'core/site_controller'.EXT);


class Login extends Site_Controller 
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->library(array('form_validation', 'facebook_manager'));
		$this->load->helper('facebook_helper');	
	}
	
	public function participant()
	{		
				
		$participant_user_rows = $this->user_model->get_rows(array('owner_type'=>'Participant', 'is_published'=>'True'));
		$participant_user_row = FALSE;
		if ( count($participant_user_rows) > 0) {
			$participant_user_row = $participant_user_rows[rand(0, count($participant_user_rows) - 1)];
		}	
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'Email Address', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('keep_logged_in', 'Keep me logged in', '');
			
		
		$row = $this->form_validation->get_input_row();
		
		if ( $this->input->post('test_login') and $participant_user_row ) {
			if ( $this->login_session->login($participant_user_row->username, 'Test', 'User') ) {
				redirect('participant');
			}				
		}
		if ( $this->input->post('login') ) {
			if ( $this->form_validation->run() ) {
				$user_row = $this->user_model->get_row_by_email($row->email);
				if ( $user_row and $user_row->owner_type == 'Participant') {
					if ( $this->login_session->login($user_row->username, $row->password, 'User', $row->keep_logged_in == 'True') ) {
						redirect('participant');
					}
				}			
			}
		}
		else {
			set_message_note($this->form_validation->error_string(), MESSAGE_NOTE_WARNING);
		}
		
		$data = array('row'=>$row,
						'participant_user_rows_count'=>count($participant_user_rows),
						'fb_login_url'=>$this->facebook_manager->get_login_url('login/facebook'),
						);
		$this->load->view('login_participant', $data);
		
	}
	
	public function company()
	{
		$company_user_rows = $this->user_model->get_rows(array('owner_type'=>'Company', 'is_published'=>'True'));
		$company_user_row = FALSE;
		if ( count($company_user_rows) > 0) {
			$company_user_row = $company_user_rows[rand(0, count($company_user_rows) - 1)];
		}

		$worksite_user_rows = $this->user_model->get_rows(array('owner_type'=>'Worksite', 'is_published'=>'True'));
		$worksite_user_row = FALSE;
		if ( count($worksite_user_rows) > 0) {
			$worksite_user_row = $worksite_user_rows[rand(0, count($worksite_user_rows) - 1)];
		}	
		
		$this->form_validation->set_rules('email', 'Email Address', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');		
		$this->form_validation->set_rules('keep_logged_in', 'Keep me logged in', '');
		
		$row = $this->form_validation->get_input_row();
		
		if ( $this->input->post('test_login_worksite') and $worksite_user_row ) {
			if ( $this->login_session->login($worksite_user_row->username, 'Test', 'User') ) {
				redirect('worksite');
			}				
		}

		if ( $this->input->post('test_login_company') and $company_user_row ) {
			if ( $this->login_session->login($company_user_row->username, 'Test', 'User') ) {
				redirect('company');
			}				
		}
		
		if ( $this->input->post('login') ) {
			if ( $this->form_validation->run() ) {
				$user_row = $this->user_model->get_row_by_email($row->email, 'True');
				if ( $user_row and ($user_row->owner_type == 'Company' or $user_row->owner_type == 'Worksite') ) {
					if ( $this->login_session->login( $user_row->username, $row->password, 'User', $row->keep_logged_in == 'True') ) {
						if ( $user_row->owner_type == 'Company') {
							redirect('company');
						}
						else {
							redirect('worksite');
						}
					}
				}
//				set_message_note($this->lang->line('success_login'), MESSAGE_NOTE_SUCCESS);
			}
			else {
				set_message_note($this->form_validation->error_string(), MESSAGE_NOTE_WARNING);
			}
		}
		
		$data = array('row'=>$row,
						'company_user_rows_count' => count($company_user_rows),
						'worksite_user_rows_count' => count($worksite_user_rows),
						'fb_login_url'=>$this->facebook_manager->get_login_url('login/facebook'),
					);
		$this->load->view('login_company', $data);
		
	}
	
	
	public function facebook()
	{
		
		if ( $this->_auto_login_facebook_user() ) {
			redirect('/');
		}
		
		$row = new StdClass();
		$row->email = '';
		$row->password = '';
		if ( $this->facebook_manager->get_user_id()) {
			$profile = $this->facebook_manager->get_user_profile();
			$row->email = $profile['email'];
		}
		
		$this->form_validation->set_rules('email', 'Email Address', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');		
		
		$row = $this->form_validation->get_input_row($row);
		
		if ( $this->input->post('login') ) {
			if ( $this->form_validation->run() ) {
				$user_row = $this->user_model->get_row_by_email($row->email);
				if ( $user_row ) {
					if ( $this->login_session->login( $user_row->username, $row->password, 'User') ) {
						$this->user->set_facebook_user_info($this->facebook_manager->get_user_id(),
											$this->facebook_manager->get_access_token());
						redirect('/');
					}
				}
			}
			else {
				set_message_note($this->form_validation->error_string(), MESSAGE_NOTE_WARNING);
			}
		}
		
		$data = array('row'=>$row,
					);
		$this->load->view('login_facebook', $data);
		
	}
	
	public function channel()
	{
		$this->set_output_mode(MY_Controller::OUTPUT_NORMAL);
		echo '<script src="//connect.facebook.net/en_US/all.js"></script>';
		return;
	}
	
	
/*-----------------------------------------------------------------------------
 * 
 * 		Request a login reset page
 * 
 *-----------------------------------------------------------------------------
*/
	
	public function reset_password()
	{
		$this->form_validation->set_rules('email_to', 'email address', 'required|valid_email');
		$data = array('email_to'=>'',
						'is_activated' => false
					);
		
		if ( $this->form_validation->run() == true ) {
			$row = $this->form_validation->get_input_row();
			$data['email_to'] = $row->email_to;			
			if ( $row ) {
				if ( $this->_reset_account($row->email_to)) {
					$data['is_activated'] = true;
					set_message_note($this->lang->line('success_reset', $row->email_to), MESSAGE_NOTE_SUCCESS);
				}
				else {
					set_message_note($this->lang->line('error_reset', $row->email_to), MESSAGE_NOTE_WARNING);
				}
			}	
		}
		else {
			set_message_note($this->form_validation->error_string(), MESSAGE_NOTE_WARNING);			
		}
		$this->load->view('login_reset_password', $data);
		
	}
	
	public function activate($ref)
	{
		$this->load->model('participant_model', 'company_model');
		$this->form_validation->set_rules('new_password', 'new password', 'required|min_length[6]');
		$this->form_validation->set_rules('new_password_again', 'new password again', 'required|min_length[6]');
		
		
		if ( $this->form_validation->run() == true ) {
			$row = $this->form_validation->get_input_row();
			if ( $row->new_password == $row->new_password_again ) {
				if ( $user_row = $this->_activate_account($ref, $row->new_password) ) {
					if ( $user_row->owner_type == 'Participant') {
						$participant_row = $this->participant_model->get_row_by_id($user_row->owner_id);
						if ( $participant_row ) {
							if ( $participant_row->status == 'Register' ) {
								$this->participant_model->set_status('Active', $participant_row->id);
							}
						}
					}
					set_message_note($this->lang->line('success_activation'), MESSAGE_NOTE_SUCCESS);
					if ( $this->login_session->login($user_row->username, $row->new_password, 'User') ) {
						if ( $user_row->owner_type == 'Participant') {
							redirect('participant');
						}
						redirect('company');
					}
					redirect('');
				}
				else {
					set_message_note($this->lang->line('error_activation'), MESSAGE_NOTE_WARNING);
				}
			}
			else {
				set_message_note($this->lang->line('error_activation_password_mismatch'), MESSAGE_NOTE_WARNING);
			}
		}	
		else {
			set_message_note($this->form_validation->error_string(), MESSAGE_NOTE_WARNING);			
		}
		$data = array();
		$this->load->view('login_activation', $data);
	}
	
	protected function _reset_account($to_email)
	{
		// load in the message template
		$this->load->model('message_template_model');
		$this->load->helper('string');
		$this->load->library('message_manager');
		
		$row = $this->user_model->get_row_by_email($to_email);
		if ( $row ) {
			do {
				$activate_ref = random_string('alnum', 32);
				$found_row = $this->user_model->get_row_by_activate_ref($activate_ref);
			} while ( $found_row);
			$this->user_model->reactivate($activate_ref, $row->id);
			$ref = $activate_ref.md5($activate_ref.$row->email.$row->id);
			$link = base_url('login/activate/'.$ref);
			$row->url_link = $link;
			return $this->message_manager->send_system_message('LoginReset', $row);
		}
		return FALSE;
	}
	protected function _get_owner_row($user_row)
	{
		$row = FALSE;
		switch ( $uer_row->owner_type) {
			case 'Participant':
				$row = $this->participant_model->get_row_by_id($row->owner_id);
			break;
			case 'Company':
				$row = $this->company_model->get_row_by_id($row->owner_id);
			break;
			case 'Worksite':
				$row = $this->worksite_model->get_row_by_id($row->owner_id);
			break;
		}
		return $row;
	}
	
	protected function _activate_account($ref, $new_password)
	{
		$this->load->library('message_manager');
		
		$activate_ref = substr($ref, 0, 32);
		$check_sum = substr($ref, 32, 32);
		$row = $this->user_model->get_row_by_activate_ref($activate_ref);
		if ( $row ) {
			if ( $check_sum == md5($activate_ref.$row->email.$row->id) ) {
				$password = $this->login_session->encrypt_password($row->username, $new_password);			
				$this->user_model->activate($password, $row->id);
				$this->message_manager->send_system_message('LoginActivateUser', $row);
				$this->message_manager->send_system_message('LoginActivateAdmin', $row);
			}
		}
		return $row;
	}
	
	protected function _auto_login_facebook_user()
	{
		if ( $this->login_session->is_active ) {
			return FALSE;
		}
		$fb_user_id = $this->facebook_manager->get_user_id();
		$fb_access_token = $this->facebook_manager->get_access_token();
		if ( $fb_user_id and $fb_access_token ) {
			$user_row = $this->user_model->get_row_by_facebook_access($fb_user_id);
			if ( $user_row ) {
				$this->login_session->login_user_id($user_row->id);
				return TRUE;
			}
		}
		return FALSE;
	}
	
	
}

?>
