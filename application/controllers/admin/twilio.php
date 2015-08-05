<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/core/site_admin_controller'.EXT);

class Twilio extends Site_admin_controller
{
     private $_row;
    public function __construct() {
        parent::__construct();
       
       
    }
    public function index(){
        
         $this->load->model(array('twilio_model'));
         
        if (!$this->twilio_model->load_by_id(1)) {
            set_message_note($this->lang->line('error_no_record'), MESSAGE_NOTE_FAILURE);
            redirect('admin/sites');
        }
        $this->load->library(array('form_validation'));
        $this->form_validation->set_rules('twilio_sid', 'SID', 'required');
        $this->form_validation->set_rules('twilio_token', 'Token', 'required');
        $this->form_validation->set_rules('twilio_number', 'From Number', 'required');
        $this->_row = $this->twilio_model->get_row();
        $this->_row = $this->form_validation->get_input_row($this->_row);

         toolbar_process_task($this);
         
         $data = array('row' => $this->_row);
         $this->load->view('admin/twilio_config',$data);
    }
     protected function _save() {
        if ($this->form_validation->run()) {
            $this->twilio_model->update_row($this->_row, $this->twilio_model->id);
           // $this->_save_assignment_rows();
            set_message_note($this->lang->line('success_save'), MESSAGE_NOTE_SUCCESS);
            return true;
        } else {
            set_message_note($this->form_validation->error_string(), MESSAGE_NOTE_WARNING);
        }
        return false;
    }
    
     public function toolbar_save() {
        if ($this->_save()) {
            redirect('admin/sites');
        }
    }

    public function toolbar_cancel() {
        redirect('admin/sites');
    }
}
?>
