<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/core/site_controller'.EXT);

class Parks extends Site_Controller 
{

	function __construct()
	{
		parent::__construct();

		$this->load->model('parks/park_model');
		
		$this->load->library('form_validation');
	}


	public function index()
	{
		$sort_order = $this->park_model->get_sort_order();
		$rows = $this->park_model->get_rows(array(), $sort_order);		
		
		toolbar_process_task($this);
		$data = array('rows'=>$rows,
					'park_list'=>$this->park_model->get_title_dropdown_list($rows),
					'sort_order'=>$sort_order
				);
		$this->load->view('park_list', $data);
		
	}

	public function toolbar_add()
	{
		$data = array('title'=>'New Park',
					'pos_x'=>38420,
					'pos_y'=>36090
				);
		$id = $this->park_model->add_row($data);
		if ( $id ) {
			redirect('parks/edit/'.$id);
		}
	}
	
	public function toolbar_edit()
	{
		$id = form_checkids_id_value();
		if ( $id ) {
			redirect('parks/edit/'.$id);
		}
	}
	
	public function edit($id)
	{
		$this->load->helper('arcgis_helper');
		$this->park_model->load_by_id($id);

				
		$this->load->model('parks/park_point_model');
		
		$this->form_validation->set_rules('title', 'Title', 'required');
		$this->form_validation->set_rules('location', 'Location', 'required');
		$this->form_validation->set_rules('facilities', 'Facilities', '');
		$this->form_validation->set_rules('size', 'Size', '');
		$this->form_validation->set_rules('vistor_info', 'Vistor Info', '');
		$this->form_validation->set_rules('map_link', 'Map Link', '');
		$this->form_validation->set_rules('web_link', 'Web Link', '');
		$this->form_validation->set_rules('brochure_link', 'Brochure Link', '');
		$this->form_validation->set_rules('pos_x', 'Pos Y', '');
		$this->form_validation->set_rules('pos_y', 'Pos Y', '');
		
		$this->_row = $this->park_model->get_row();
		$this->_row = $this->form_validation->get_input_row($this->_row);
		
		if ( toolbar_process_task($this) ){
			redirect(uri_string());
		}
		
		$data = array('row'=> $this->_row, 
					'point_rows'=>$this->park_point_model->get_rows(array('park_id'=>$this->park_model->id)),
					'point_type_list'=>$this->park_point_model->get_type_dropdown_list('Select Type'),
					);
		$this->load->view('park_edit', $data);
		
	}
	
	public function toolbar_save()
	{
		if ( $this->form_validation->run() ) {
			if ( $this->_row->id == 0 ) {
				$id = $this->park_model->add_row($this->_row);
				$this->park_model->load_by_id($id);
				set_message_note($this->lang->line('success_add'), MESSAGE_NOTE_SUCCESS);
			}
			else {
				$this->park_model->update_row($this->_row);
				$point_ids = $this->input->post('point_id');
				foreach ($point_ids as $point_id) {
					$this->park_point_model->load_by_id($point_id);
					$row = array(
									'type'=>$this->input->post('point_type')[$point_id],
									'title'=>$this->input->post('point_title')[$point_id],
									'pos_x'=>$this->input->post('point_pos_x')[$point_id],
									'pos_y'=>$this->input->post('point_pos_y')[$point_id],
								);
					if ( $row['type'] == '0' ) {
						$row['type'] = '';
					}
					$this->park_point_model->update_row($row, $point_id);
				}
				set_message_note($this->lang->line('success_save'), MESSAGE_NOTE_SUCCESS);
			}
			return TRUE;
		}
		else {
			set_message_note($this->form_validation->error_string(), MESSAGE_NOTE_WARNING);
		}
		return FALSE;
	}
	
	public function toolbar_back()
	{
		redirect('parks');
	}

	public function toolbar_add_point()
	{
		$row = new StdClass();
		
		$rows = $this->park_point_model->get_rows(array('park_id'=>$this->park_model->id), 'id');
		$last_row = end($rows);
		if ( $last_row ) {
			$row->pos_x = $last_row->pos_x + 4;
			$row->pos_y = $last_row->pos_y + 4;
			$row->type = $last_row->type;
		}
		else {
			$row->pos_x = $this->park_model->pos_x + 0.5;
			$row->pos_y = $this->park_model->pos_y + 0.5;
		}
		$row->park_id = $this->park_model->id;
		$row->title = 'New Point';
		$this->park_point_model->add_row($row);
		return TRUE;
	}
	public function toolbar_delete_point()
	{
		$ids = form_checkids_ids();
		foreach ( $ids as $id ) {
			$this->park_point_model->delete_id($id);
		}
		return TRUE;
	}
}

