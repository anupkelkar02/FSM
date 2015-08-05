<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH . '/core/site_admin_controller' . EXT);

class Home extends Site_admin_controller {

    public function index() {
        $this->load->helper('user_helper');
		$this->load->helper('jquery_tab');
        $data = array(
            'user_row' => $this->user->get_row()
        );


        $this->load->view('admin/home', $data);
    }

    public function logout() {
        $this->user->logout();
        redirect('');
    }

    public function blank() {
        $this->load->view('admin/home_blank');
    }

    
function qc(){
    $this->load->view('admin/qc');
}
function camp(){
    $this->load->view('admin/camp');
}
function crm(){
    $this->load->view('admin/crm');
}
function bday_template(){
    $this->load->model('sysconf_model');
      $data['bday_temp']=$this->sysconf_model->get_bday();
      $this->load->view('admin/bday_template',$data);
}
function bday_save(){
    $this->load->model('sysconf_model');
    $mytextarea = $_POST['mytextarea'];
    $data = array('bday_template'=>$mytextarea);
    $bday_temp=$this->sysconf_model->get_bday();
    if($bday_temp!=false){
        $this->sysconf_model->update_bday($data);
    }else{
        $this->sysconf_model->insert_bday($data);
    }
     set_message_note('Birthday greeting set successfully', MESSAGE_NOTE_SUCCESS);
    
     redirect('admin/home/bday_template');
}
}

?>
