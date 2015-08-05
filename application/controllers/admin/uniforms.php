<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


 
require_once(APPPATH.'/core/site_admin_controller'.EXT);

class Uniforms extends Site_admin_controller
{
	
	private $_sort_order;
	private $_row;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('staff_model','uniforms_model')
        );
		$this->load->helper('site_helper');
	}


	public function listings()
	{
		$groupdata= $this->session->userdata('logged_in');
		
		$rows = $this->uniforms_model->get_uniforms($groupdata['group_id']);
		$sizes = $this->uniforms_model->get_sizes();
		$data = array(
						'rows'=>$rows,'sizes'=>$sizes

					);
					
				
				

		$this->load->view('admin/uniformslist', $data);
	}
	
	
	public function add()
	{
		$this->load->view('admin/add_type', $data);
	}
	
	
	
	public function add_sizes()
	{
		$this->load->view('admin/add_sizes', $data);
	}
	
	public function edit_sizes($id)
	{
		$title = $this->uniforms_model->get_uniforms_size($id);
		$data = array(
						'id'=>$id,'size'=>$title[0]->size
					);
	
		$this->load->view('admin/edit_sizes', $data);
	}
	
	
	public function edit_type($id)
	{
		$title = $this->uniforms_model->get_uniforms_title($id);
		$data = array(
						'id'=>$id,'title'=>$title[0]->title
					);
	
		$this->load->view('admin/edit_type', $data);
	}
	
	public function save_type()
	{
		$groupdata= $this->session->userdata('logged_in');
		$title=$this->input->post('typetitle');
   		$data = array(
						'group_id'=>$groupdata['group_id'],
						'title'=>$title
					);
		$id = $this->uniforms_model->add_record('grs_uniforms',$data);
		redirect('admin/uniforms/typeslist');
		
	}
	
	
	public function update_type()
	{
		$groupdata= $this->session->userdata('logged_in');
		$title=$this->input->post('typetitle');
		$u_id=array('u_id'=>$this->input->post('u_id'));
   		$data = array(
						'title'=>$title
					);
		$id = $this->uniforms_model->update_record('grs_uniforms',$data,$u_id);
		
		redirect('admin/uniforms/typeslist');
		
	}
	
	
	public function save_sizes()
	{
		$groupdata= $this->session->userdata('logged_in');
		$title=$this->input->post('typetitle');
   		$data = array(
						'size'=>$title
					);
		$id = $this->uniforms_model->add_record('grs_sizes',$data);
		redirect('admin/uniforms/sizeslist');
		
	}
	
	public function update_sizes()
	{
		$title=$this->input->post('typetitle');
		$s_id=array('s_id'=>$this->input->post('s_id'));
   		$data = array(
						'size'=>$title
					);
		$id = $this->uniforms_model->update_record('grs_sizes',$data,$s_id);
		
		redirect('admin/uniforms/sizeslist');
		
	}
	
	public function delete_type()
	{
		
		$count = 0;
        $checkids = $this->get_checkids();
        foreach ($checkids as $id) {
			
			$this->db->query("delete from grs_uniforms where u_id='".$id."'");
        }
		redirect('admin/uniforms/typeslist');
	}
	
	
	public function delete_size()
	{
		
		$count = 0;
        $checkids = $this->get_checkids();
        foreach ($checkids as $id) {
			
			$this->db->query("delete from grs_sizes where s_id='".$id."'");
        }
		redirect('admin/uniforms/sizeslist');
	}
	
	
	public function additem($id)
	{
		
		$title = $this->uniforms_model->get_uniforms_title($id);
		$sizes = $this->uniforms_model->get_sizes();


		$data = array(
						'id'=>$id,'title'=>$title[0]->title,'sizes'=>$sizes
					);
			$this->load->view('admin/add_uniforms',$data);
	}
	
	
	public function save_items()
	{
		$un_id=$this->input->post('type_id');
		$s_id=$this->input->post('size_id');
		$qty=$this->input->post('qty');
   		$data = array(
						'un_id'=>$un_id,
						's_id'=>$s_id,
						'qty'=>$qty,
						'type'=>'Add'
						);
						
		$id = $this->uniforms_model->add_record('grs_add_issue_return',$data);
		redirect('admin/uniforms/listings');
		
	}
	
	
	public function inventory($id)
	{
		$groupdata= $this->session->userdata('logged_in');
		
		$title = $this->uniforms_model->get_uniforms_title($id);
		$sizes = $this->uniforms_model->get_sizes();
		$staff = $this->uniforms_model->get_staff($groupdata['group_id']);


		$data = array(
						'id'=>$id,'title'=>$title[0]->title,'sizes'=>$sizes,'staff'=>$staff
					);
			$this->load->view('admin/issue_return_uniforms',$data);
	}
	
	
	
	public function save_inventory()
	{
		$un_id=$this->input->post('type_id');
		$s_id=$this->input->post('size_id');
		$qty=$this->input->post('qty');
		$name=$this->input->post('name');
		$type=$this->input->post('type');
   		$data = array(
						'un_id'=>$un_id,
						's_id'=>$s_id,
						'qty'=>$qty,
						'name'=>$name,
						'type'=>$type
						);
						
		$id = $this->uniforms_model->add_record('grs_add_issue_return',$data);
		redirect('admin/uniforms/listings');
		
	}
	
	
	public function history($row_pos = 0)
	{
	
		$groupdata= $this->session->userdata('logged_in');
		$this->load->library('pagination');
		
		
		$params = str_replace(base_url(),"",current_url());
		$params = explode("/",$params);
		
		//echo $category = $params[5];
		
		
		
        $row_pos = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $row_count = $this->uniforms_model->get_row_count($groupdata['group_id']);
		
        $config = array('total_rows' => $row_count,
            'base_url' => site_url('admin/uniforms/history'),
            'cur_page' => $row_pos,
            'per_page' => 15
        );
        $this->pagination->initialize($config);


		
		$rows = $this->uniforms_model->get_uniforms_history($groupdata['group_id'], $row_pos, $this->pagination->per_page);
		$data = array(
						'rows'=>$rows,'pagination_links' => $this->pagination->create_links()

					);

		$this->load->view('admin/inventory_history', $data);
	}
	
	
	public function checkstock()
	{
		$un_id=$this->input->post('type_id');
		$s_id=$this->input->post('size_id');
		
		$getaddstock=$this->uniforms_model->get_uniforms_stock($un_id,$s_id);
		$getissuestock=$this->uniforms_model->get_uniforms_issue($un_id,$s_id);
		$getreturnstock=$this->uniforms_model->get_uniforms_return($un_id,$s_id); 
     	
		echo ($getaddstock[0]->totalstock-$getissuestock[0]->totalissued)+$getreturnstock[0]->totalreturn;
		exit();	
	}
	
	
	
	public function typeslist()
	{
		$groupdata= $this->session->userdata('logged_in');
		
		$rows = $this->uniforms_model->get_uniforms($groupdata['group_id']);
		$data = array(
						'rows'=>$rows

					);
					
			$this->load->view('admin/typeslist', $data);
	}
	
	public function sizeslist()
	{
		$sizes = $this->uniforms_model->get_sizes();
		$data = array(
						'sizes'=>$sizes

					);
					
		$this->load->view('admin/sizeslist', $data);
	}
	
	
	
}

