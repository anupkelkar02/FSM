<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/core/site_admin_controller'.EXT);
require_once(APPPATH.'/controllers/admin/staff_call'.EXT);

class Schedule extends Site_admin_controller
{	
	
	private $_sort_order;
	private $_row;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('schedule_model',
								'site_model',
								'staff_model',
								'work_status_model',
								'reply_status_model',
								)
						);
		$this->load->helper(array('site_helper',
									 'staff_helper',
									 'work_status_helper',
									 'reply_status_helper'
								)
							);
	}

	public function index($row_pos = 0)
	{	
		$this->_sort_order = $this->schedule_model->get_sort_order();
		
		$this->_filter = filter_load('filter', array('start_date'=>datetime_start_month(),
													'site_id_match'=>'')
									);
		toolbar_process_task($this);
		
		
		$this->load->library('pagination');
		$row_pos = intval($row_pos);
		$row_count = $this->schedule_model->get_row_count($this->_filter);
		$config = array('total_rows'=>$row_count, 
						'base_url'=>site_url('admin/schedule/index'),
						'cur_page'=>$row_pos,
						'per_page'=>100
					);
		$this->pagination->initialize($config);


		$rows = $this->schedule_model->get_rows($this->_filter, $this->_sort_order, $row_pos, $this->pagination->per_page);

		
		$data = array(
						'rows'=>$rows,
						'site_list'=>$this->site_model->get_dropdown_list('Select Site'),
						'filter'=>$this->_filter,
						'sort_order'=>$this->_sort_order,
						'pagination_links'=>$this->pagination->create_links(),

					);
		$this->load->view('admin/schedule_list', $data);		
	}

	public function toolbar_reload()
	{
		
	}
	
	public function toolbar_add()
	{
		$data = array(
						'start_date'=>datetime_today(),
					);
		$id = $this->schedule_model->add_row($data);
		redirect('admin/schedules/edit/'.$id);
		
	}
	
	public function toolbar_delete()
	{
		$count = 0;
		$checkids = $this->get_checkids();
		foreach ( $checkids as $id ) {
			if ( $this->schedule_model->delete_id($id) ) {
				$count ++;
			}
		} 
		if ( $count > 0 ) {
			set_message_note($this->lang->line('info_delete_items', $count), MESSAGE_NOTE_INFORMATION);
		}
		else {
			set_message_note($this->lang->line('error_no_check_id', 'delete'), MESSAGE_NOTE_WARNING);
		}

		redirect(uri_string());
	}
	
	public function toolbar_toggle_published()
	{
		$checkid = form_checkids_id_value();
		if ( $checkid ) {
			$this->schedule_model->toggle_is_published($checkid);
			redirect(uri_string());		
		}
		else {
			set_message_note($this->lang->line('error_no_check_id', 'publish/unpublish'), MESSAGE_NOTE_WARNING);
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
			redirect('admin/schedule/edit/'.$checkid);		
		}
		else {
			set_message_note($this->lang->line('error_no_check_id','edit'), MESSAGE_NOTE_WARNING);
		}
	}


	public function edit($id)
	{
		if ( ! $this->schedule_model->load_by_id(intval($id)) ) {
			set_message_note($this->lang->line('error_no_record'), MESSAGE_NOTE_FAILURE);
			redirect('admin/schedule');
		}
		$this->load->helper('jquery_tab_helper');
		$this->load->library(array('form_validation'));
		
		$this->form_validation->set_rules('start_date', 'Start Date', 'required');
		$this->form_validation->set_rules('site_id', 'Site', '');
		$this->form_validation->set_rules('staff_id', 'Staff', '');
		$this->form_validation->set_rules('work_status_id', 'Work Status', '');
		$this->form_validation->set_rules('reply_status_id', 'Reply Status', '');
	
		
		
		$this->_row = $this->schedule_model->get_row();
		$this->_row = $this->form_validation->get_input_row($this->_row);
		
		toolbar_process_task($this);
		
		
		$data = array('row'=>$this->_row,
					'site_list'=>$this->site_model->get_dropdown_list('Select Site'),
					'staff_list'=>$this->staff_model->get_dropdown_list('Select Staff'),
					'shift_type_list'=>$this->schedule_model->get_shift_type_dropdown_list('Select Shift Type'),
					'work_status_list'=>$this->work_status_model->get_dropdown_list('Select Work Status'),
					'reply_status_list'=>$this->reply_status_model->get_dropdown_list('Select Reply Status')
					);
		$this->load->view('admin/schedule_edit', $data);		
	}
	
	protected function _save()
	{
		if ( $this->form_validation->run() ) {
			$this->schedule_model->update_row($this->_row, $this->schedule_model->id);
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
			redirect('admin/schedule');
		}
	}
	
	public function toolbar_cancel()
	{
		redirect('admin/schedule');
	}


	public function assignment($start_day = TRUE,$site_id='')
	{
		$this->load->model(array('staff_assignment_model'));
		$this->add_javascript_file('raphael-2.1.2-min.js');
		$this->add_javascript_file('staff-assignment-0.1.0.js');
	
		if($this->input->post('site_id')==''){
			$site_id =$site_id ;
		}else{
			$site_id = intval($this->input->post('site_id'));
		}
		$start_date = $this->input->post('start_date');
if($start_date==''){
                    $start_date = date('Y-m-01');
                }
		if ( $start_day == true ) {
			$tim = strtotime($start_date);
			$start_date = date('Y-m-d', mktime(0, 0, 0, date('m', $tim), true,date('Y', $tim)));
		}
		else {
			$start_date = date('Y-m-01');
		}
		$staff_rows = array();
		$schedule_rows = array();
		$schedule_month_year_rows = $this->schedule_model->get_distinct_month_year(array());
		$work_status_rows = array();
		$reply_status_rows = array();
		if ( $site_id ) {
			$this->site_model->load_by_id($site_id);
			$staff_rows = $this->staff_assignment_model->get_staff_rows(array('site_id'=>$this->site_model->id));
			$schedule_rows = $this->schedule_model->get_rows(array('site_id'=>$this->site_model->id, 'start_date >='=>$start_date));
			$work_status_rows = $this->work_status_model->get_rows(array());
			$reply_status_rows = $this->reply_status_model->get_rows(array());
		}
		
		$data = array('site_list'=>$this->site_model->get_dropdown_list('Select Site'),
					'site_id'=>$site_id,
					'staff_rows'=>$staff_rows,
					'schedule_rows'=>$schedule_rows,
					'work_status_rows'=>$work_status_rows,
					'reply_status_rows'=>$reply_status_rows,
					'month_year_list'=>$this->_get_dropdown_month_year_list($schedule_month_year_rows),
					'start_date'=>$start_date
					);
		$this->load->view('admin/schedule_assignment', $data);
	}
	
	public function update_row()
	{
		$row_id = $this->input->post('schedule_id');
		$work_status_id = $this->input->post('work_status_id');
		if ( $row_id and $work_status_id ) {
			$data = array('work_status_id'=>$work_status_id);
			$this->schedule_model->update_row($data, $row_id);
		}
	}

	public function add_row()
	{
		$this->set_output_mode(MY_Controller::OUTPUT_NORMAL);
		$staff_id = $this->input->post('staff_id');
		$start_date = $this->input->post('start_date');
		$work_status_id = $this->input->post('work_status_id');
		$site_id = $this->input->post('site_id');
		if ( $staff_id and $start_date and $work_status_id ) {
			$data = array('staff_id'=>$staff_id,
						'start_date'=>$start_date,
						'work_status_id'=>$work_status_id,
						'site_id'=>$site_id);
			$id = $this->schedule_model->add_row($data);
			echo $id;
		}
	}

	public function delete_row()
	{
		$row_id = $this->input->post('schedule_id');
		if ( $row_id ) {
			$this->schedule_model->delete_id($row_id);
		}
	}
	
	public function request_attendance()
	{
		$this->load->model('site_shift_model');
		$schedule_id = intval($this->input->post('schedule_id'));
                $send_pref = $this->input->post('send_pref');
		$row = reset($this->schedule_model->get_rows(array('id'=>$schedule_id)));
		$this->staff_model->load_by_id($row->staff_id);
		
		$start_time = $row->start_date;
		$shift_row = reset($this->site_shift_model->get_rows(array('site_id'=>$row->site_id, 'shift_type'=>$row->shift_type)));
		if ( $shift_row ) {
			$start_time .= ' '.$shift_row->start_time;
		}

		if ( $this->staff_model->phone_number == '91234567' ) {
			return  'Invalid Number';
		}
               
		$this->load->library('staff_phone_manager');
                if($send_pref=='sms'){
                        $this->staff_phone_manager->request_attendance($row->staff_id, $row->site_id, $row->id, $row->shift_type, $start_time,$send_pref);
	return 'SMS is sent';                
}else{
                    $staff_ins = new Staff_call();
                    
                   $res= $staff_ins->Random_Call($row->staff_id,$row->id);
                   return 'Call is initiated';
                }
	}
	
	protected function _get_dropdown_month_year_list($rows)
	{
		$result = array();
		foreach ( $rows as $row ) {
			$date = sprintf("%04d", $row->year).'-'.sprintf("%02d", $row->month).'-01';
			$result[$date] = date('M Y', strtotime($date));
		}
		return $result;
		
	}
	
}


?>
