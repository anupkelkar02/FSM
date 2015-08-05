<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/core/site_admin_controller'.EXT);

class Timesheets extends Site_admin_controller
{	
	
	private $_sort_order;
	private $_row;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('timesheet_model'
								)
						);
		$this->load->helper('site_helper','url','form');
		$this->load->library(array('session','pagination'));
		$this->load->library('table');
	}

	public function index($row_pos = 0)
	{	
		$data["page_title"]    = "Timesheet";
		if($this->input->get('start',TRUE) == 1){
		   $this->session->unset_userdata('searchData');
		}
		if($this->input->post('frmSearch')) {
			$data["siteName"] = $this->input->post('siteNames'); 
			$data["staffName"]= $this->input->post('staffNames'); 
			$data["monthName"]= $this->input->post('getMonths'); 
			$this->session->set_userdata(array('searchData'=>$data));
			$noOfRecords  = $this->timesheet_model->getStaffAssigmentDataCount($data);		
		} else {
			$noOfRecords  = $this->timesheet_model->getStaffAssigmentDataCount();	
		}

		/* Set the config parameters */
		$config['base_url']   = base_url()."admin/timesheet/index";
		$config['total_rows'] = $noOfRecords;
		$config['per_page']   = $this->config->item('limit'); 
		$config['cur_page']   = $this->uri->segment(4);
		$config['suffix']     = '?rid=37';
		
		$this->pagination->initialize($config);
		$data['pagination']   = $this->pagination->create_links();		
		
		if($this->input->post('frmSearch')) {
			$data["staffAssignementData"] = $this->timesheet_model->getStaffAssigmentData($config,$data);	
			$data["searchResult"] = 1;	
		} 
		if($this->session->userdata('searchData')) {
			$data["staffAssignementData"] = $this->timesheet_model->getStaffAssigmentData($config,$data);
			$data["searchResult"] = 1;			
		} else {
			$data["staffAssignementData"] = "";
			$data["searchResult"] = 0;		
		}		
		
		$data["sitesData"]=$this->timesheet_model->get_dropdown_list();					
		$data["staffsData"]=$this->timesheet_model->get_staffs_dropdown_list();
	
		$this->load->view('admin/timesheets', $data);		
	}

	public function getSiteStaffs($siteID="") {
		if($siteID!=0) {
			$siteStaffsData=$this->timesheet_model->get_site_staffs_dropdown_list($siteID);
			$getSiteStaffNames="";
			if(!empty($siteStaffsData)) {
				$getSiteStaffNames ='<select name="staffNames">';
				$getSiteStaffNames .='<option value="">--Select--</option>';
				foreach($siteStaffsData as $gsIndex=>$gSiteStaffData) {
					$getSiteStaffNames .='<option value="'.$gSiteStaffData->staff_id.'">'.$gSiteStaffData->staff_name.'</option>';
				}
				$getSiteStaffNames .='</select>';
			}
			echo $getSiteStaffNames;die;
		} else {
			$getStaffsData=$this->timesheet_model->get_staffs_dropdown_list();
			$getStaffNames="";
			if(!empty($getStaffsData)) {
				$getStaffNames ='<select name="staffNames">';
				$getStaffNames .='<option value="">--Select--</option>';
				foreach($getStaffsData as $ssIndex=>$sStaffData) {
					$getStaffNames .='<option value="'.$ssIndex.'">'.$sStaffData.'</option>';
				}
				$getStaffNames .='</select>';
			}
			echo $getStaffNames;die;
		}
	}

	public function toolbar_reload()
	{
		
	}
	
	public function toolbar_add()
	{
		$data = array(
						'name'=>'New Site',
						'is_published'=>'True', 
						'update_time'=>date('Y-m-d H:i:s'),
					);
		$id = $this->site_model->add_row($data);
		redirect('admin/sites/edit/'.$id);
		
	}
	
	public function toolbar_delete()
	{
		$count = 0;
		$checkids = $this->get_checkids();
		foreach ( $checkids as $id ) {
			if ( $this->site_model->delete_id($id) ) {
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
			$this->site_model->toggle_is_published($checkid);
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
			redirect('admin/sites/edit/'.$checkid);		
		}
		else {
			set_message_note($this->lang->line('error_no_check_id','edit'), MESSAGE_NOTE_WARNING);
		}
	}


	public function edit($id)
	{
		if ( ! $this->site_model->load_by_id(intval($id)) ) {
			set_message_note($this->lang->line('error_no_record'), MESSAGE_NOTE_FAILURE);
			redirect('admin/sites');
		}
		$this->load->model(array('staff_model', 'site_shift_model', 'postal_district_model', 'staff_assignment_model'));
		$this->load->helper('jquery_tab_helper');
		$this->load->library(array('form_validation'));
		
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('street_number', 'Street Number', '');
		$this->form_validation->set_rules('street_name', 'Street Name', '');
		$this->form_validation->set_rules('unit_number', 'Unit Number', '');
		$this->form_validation->set_rules('city', 'City', '');
		$this->form_validation->set_rules('postcode', 'Post Code', '');
		$this->form_validation->set_rules('country', 'Country', '');
		$this->form_validation->set_rules('is_published', 'Published', '');
	
		
		
		$this->_row = $this->site_model->get_row();
		$this->_row = $this->form_validation->get_input_row($this->_row);
		
		toolbar_process_task($this);
		
		
		$data = array('row'=>$this->_row,
					'shift_rows'=>$this->site_shift_model->get_rows(array('site_id'=>$this->site_model->id)),
					'postal_disctrict_row'=>reset($this->postal_district_model->get_rows(array('postcode'=>$this->_row->postcode))),
					'assignment_rows'=>$this->staff_assignment_model->get_rows(array('site_id'=>$this->site_model->id)),
					'shift_type_list'=>$this->staff_assignment_model->get_shift_type_dropdown_list('Shift Type'),
					'assign_type_list'=>$this->staff_assignment_model->get_assign_type_dropdown_list('Assign Type'),
					'staff_list'=>$this->staff_model->get_dropdown_list('Select Staff')
					
					);
		$this->load->view('admin/site_edit', $data);		
	}
	
	protected function _save()
	{
		if ( $this->form_validation->run() ) {
			$this->site_model->update_row($this->_row, $this->site_model->id);
			$this->_save_assignment_rows();
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
			redirect('admin/sites');
		}
	}
	
	public function toolbar_cancel()
	{
		redirect('admin/sites');
	}

	public function toolbar_add_staff()
	{
		$data = array('site_id'=>$this->site_model->id);
		$this->staff_assignment_model->add_row($data);
	}
	
	protected function _save_assignment_rows()
	{
		$staff_ids = $this->input->post('assignment_staff_id');
		$shift_types = $this->input->post('assignment_shift_type');
		$assign_types = $this->input->post('assignment_assign_type');
		
		foreach ( $staff_ids as $assignemnt_id=>$staff_id ) {
			$data = array('staff_id'=>$staff_id,
						'shift_type'=>$shift_types[$assignemnt_id],
						'assign_type'=>$assign_types[$assignemnt_id],
					);
			$this->staff_assignment_model->update_row($data, $assignemnt_id);
		}
		jquery_tab_set_tab_index(2);
	}
	public function toolbar_remove_staff()
	{
		$ids = form_checkids_ids('assignment_check_id');
		if ( count($ids) == 0 ) {
			set_message_note($this->lang->line('error_no_staff_check_id', 'delete'), MESSAGE_NOTE_WARNING);
		}
		foreach ( $ids as $id ) {
			$this->staff_assignment_model->delete_id($id);
		}
		jquery_tab_set_tab_index(2);
	}
}




?>
