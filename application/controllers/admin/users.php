<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/core/site_admin_controller'.EXT);

class Users extends Site_admin_controller
{	
	private $_sort_order;
	private $_filter;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('user_group_model', 'user_model'));
	}

	public function index()
	{
		$this->_sort_order = $this->user_model->get_sort_order();
		
		$this->load->helper('filter_helper');
		$this->_filter = filter_load('filter', array('name_match'=>'', 
												'username_match'=>'',
												'group_id'=>0)
							);
		
		if ( toolbar_process_task($this) ) {
			return;
		}
		
		$data = array(
						'rows'=>$this->user_model->get_rows($this->_filter, $this->_sort_order),
						'sort_order'=>$this->_sort_order,
						'filter'=>$this->_filter,
						'group_list'=>$this->user_model->get_group_dropdown_list('Select Group')
					);
		$this->load->view('admin/user_list', $data);		
	}
	
	public function toolbar_reload()
	{
	}
	
	public function toolbar_add()
	{
		$data = array('username'=>'user', 
						'name'=>'New User', 
						'password'=>''
					);
		$id = $this->user_model->add_row($data);
		redirect('admin/users/edit/'.$id);
		
	}
	public function toolbar_delete()
	{
		$count = 0;
		$checkids = $this->get_checkids();
		foreach ( $checkids as $id ) {
			if ( $this->user_model->delete_id($id) ) {
				$count ++;
			}
		}
		set_message_note($this->lang->line('info_delete_items', $count), MESSAGE_NOTE_INFORMATION);

		redirect(uri_string());
	}
	public function toolbar_toggle_published()
	{
		$checkid = form_checkids_id_value();
		if ( $checkid ) {
			$this->user_model->toggle_is_published($checkid);
			redirect(uri_string());		
		}
		else {
			set_message_note($this->lang->line('error_no_check_id','toggle published'), MESSAGE_NOTE_WARNING);
		}
	}
	
	public function toolbar_sort_order_changed()
	{
		$this->_sort_order = form_sort_order_apply($this->_sort_order);		
	}
	
	public function toolbar_edit()
	{
		$checkid = $this->get_checkid();
		if ( $checkid ) {
			redirect('admin/users/edit/'.$checkid);		
		}
		else {
			set_message_note($this->lang->line('error_no_check_id','editing'), MESSAGE_NOTE_WARNING);
		}
	}
		
	public function edit($id)
	{
		if ( ! $this->user_model->load_by_id(intval($id)) ) {
			set_message_note($this->lang->line('error_no_user'), MESSAGE_NOTE_FAILURE);
			redirect('admin/users');
		}
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', '');
		$this->form_validation->set_rules('email', 'Email', 'required');
		$this->form_validation->set_rules('is_published', 'Published', '');
		$this->form_validation->set_rules('group_id', 'User Group', '');
	
		
		$this->_row = $this->user_model->get_row();	
		$this->_row->password = '';	
		$this->_row = $this->form_validation->get_input_row($this->_row);
		
		toolbar_process_task($this);
		$view_name = 'admin/user_edit';
		$user_edit_link = '';
		$data = array('row'=>$this->_row,
					'group_name'=>$this->user_model->get_group_name(),
					'groups'=>$this->user_group_model->get_dropdown_list(),
					);
		$this->load->view($view_name, $data);		
	}
	
	protected function _save()
	{
		if ( $this->form_validation->run() ) {
			if ( $this->_row->password and $this->_row->password != 'password') {					
				$this->_row->password = $this->login_session->encrypt_password($this->_row->username, $this->_row->password);
			}
			else {
				unset($this->_row->password);
			}
			$this->user_model->update_row($this->_row);
			set_message_note($this->lang->line('success_save'), MESSAGE_NOTE_SUCCESS);

		}
		else {
			set_message_note($this->form_validation->error_string(), MESSAGE_NOTE_WARNING);
		}
	}
	public function toolbar_apply()
	{
		$this->_save();
		redirect(uri_string());		
	}
	
	public function toolbar_save()
	{
		$this->_save();
		redirect('admin/users');
	}
	
	public function toolbar_cancel()
	{
		redirect('admin/users');
	}
	
	public function toolbar_logs()
	{
		redirect('admin/user_logs/index/0/'.$this->user_model->id);
	}
	
}

?>
