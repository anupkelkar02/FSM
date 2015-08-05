<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/core/site_admin_controller'.EXT);

class Reply_status extends Site_admin_controller
{	
	
	private $_sort_order;
	private $_row;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('reply_status_model',
								)
						);
		$this->load->helper('site_helper');
						
	}

	public function index()
	{	
		$this->_sort_order = $this->reply_status_model->get_sort_order();
		
		$this->_filter = array();
		toolbar_process_task($this);
		
		


		$rows = $this->reply_status_model->get_rows($this->_filter, $this->_sort_order);

				
		$data = array(
						'rows'=>$rows,
						'filter'=>$this->_filter,
						'sort_order'=>$this->_sort_order,

					);
		$this->load->view('admin/reply_status_list', $data);		
	}

	public function toolbar_reload()
	{
		
	}
	
	public function toolbar_add()
	{

		
	}
	
	public function toolbar_delete()
	{

	}
	
	public function toolbar_toggle_published()
	{

	}
	
	public function toolbar_sort_order_changed()
	{
		$this->_sort_order = form_sort_order_apply($this->_sort_order);		
	}
	
	public function toolbar_edit()
	{
		$checkid = $this->get_checkid();
		if ( $checkid ) {
			redirect('admin/work_satatus/edit/'.$checkid);		
		}
		else {
			set_message_note($this->lang->line('error_no_check_id','edit'), MESSAGE_NOTE_WARNING);
		}
	}


	public function edit($id)
	{
		if ( ! $this->reply_status_model->load_by_id(intval($id)) ) {
			set_message_note($this->lang->line('error_no_record'), MESSAGE_NOTE_FAILURE);
			redirect('admin/reply_status');
		}
		$this->load->library(array('form_validation'));
		$this->load->helper('jscolor_helper');
		
		$this->form_validation->set_rules('number', 'Number', '');
		$this->form_validation->set_rules('title', 'Title', 'required');
		$this->form_validation->set_rules('code', 'Code', 'required');
		$this->form_validation->set_rules('background_color', 'Background Color', '');
	
		
		
		$this->_row = $this->reply_status_model->get_row();
		$this->_row = $this->form_validation->get_input_row($this->_row);
		
		toolbar_process_task($this);
		
		
		$data = array('row'=>$this->_row,
					);
		$this->load->view('admin/reply_status_edit', $data);		
	}
	
	protected function _save()
	{
		if ( $this->form_validation->run() ) {
			$this->reply_status_model->update_row($this->_row, $this->staff_model->id);
			set_message_note($this->lang->line('success_save'), MESSAGE_NOTE_SUCCESS);
			return true;
		}
		else {
			set_message_note($this->form_validation->error_string(), MESSAGE_NOTE_WARNING);
		}
		return false;
	}
	
	public function toolbar_apply()
	{
		$this->_save();
		redirect(uri_string());
	}
	
	public function toolbar_save()
	{
		if ( $this->_save() ) {
			redirect('admin/reply_status');
		}
	}
	
	public function toolbar_cancel()
	{
		redirect('admin/reply_status');
	}

}


?>
