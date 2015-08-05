<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/core/site_admin_controller'.EXT);
require_once(APPPATH . '/third_party/Services/Twilio' . EXT);

class Send_sms extends Site_admin_controller
{
     private $_row;
    public function __construct() {
        parent::__construct();
        $this->load->model(array('staff_model','twilio_model','site_model','staff_assignment_model'));
       
    }
    public function index(){
        
        
         
        $this->load->library(array('form_validation'));
        $this->form_validation->set_rules('site_id', 'Site', 'required');
        $this->form_validation->set_rules('staff_id', 'Staff', 'required');
        $this->form_validation->set_rules('text_msg', 'Message', 'required');
        //$this->_filter = filter_load('filter', array('name_match'=>'','is_published'=>'True'));
        //$sites = $this->site_model->get_rows($this->_filter);

         toolbar_process_task($this);
         
         $data = array('row' => $this->_row,'site_list'=>$this->site_model->get_dropdown_list('Select Site'),'staff_list'=>$this->staff_model->get_dropdown_list('Select Staff'));
         $this->load->view('admin/broadcast_sms',$data);
    }
     function get_staff(){
        $site_id = $this->input->post('site_id');
        $staff = $this->staff_assignment_model->get_rows(array('site_id'=>$site_id));
        $html = '';
        foreach($staff as $s){
            $sdet=$this->staff_model->get_rows(array('id'=>$s->staff_id));
            //var_dump($sdet); exit;
            $html .= '<option value="'.$sdet[0]->id.'">'.$sdet[0]->first_name.' '.$sdet[0]->last_name.'</option>';
        }
        echo $html;
    }
     protected function _save() {
        if ($this->form_validation->run()) {
            $this->load->library('staff_phone_manager');
            $staff_id = $_POST['staff_id'];
            $message = $_POST['text_msg'];
            foreach($staff_id as $id){
               
             $staff_number = $this->staff_model->getNumber($id);
              
             
             $this->staff_phone_manager->send_sms_message($staff_number,$message);
            }
            set_message_note($this->lang->line('success_save'), MESSAGE_NOTE_SUCCESS);
            return true;
        } else {
            set_message_note($this->form_validation->error_string(), MESSAGE_NOTE_WARNING);
        }
        return false;
    }
    
     public function toolbar_save() {
        if ($this->_save()) {
            redirect('admin/send_sms');
        }
    }
     protected function _call() {
        if ($this->form_validation->run()) {
            
            $staff_id = $_POST['staff_id'];
            $message = $_POST['text_msg'];
            $twilio_det = $this->twilio_model->get_row();
            $twilio_number = $twilio_det->twilio_number;
            $twilio_sid = $twilio_det->twilio_sid;
            $twilio_token = $twilio_det->twilio_token;
            $client = new Services_Twilio($twilio_sid, $twilio_token);
            foreach($staff_id as $id){
               $str = '<Response>
<Say voice="alice">Hello, This is an announcement call.</Say><Pause length="2"/><Say>' . $message . '</Say><Pause length="2"/><Say>Thank you.</Say>
</Response>';
               //echo 'http://twimlets.com/echo?Twiml=' . urlencode($str); exit;
             $staff_number = $this->staff_model->getNumber($id);
              try {


                            $call = $client->account->calls->create(
                                    $twilio_number, // Caller ID
                                    '+'  . $staff_number, // Your friend's number
                                    'http://twimlets.com/echo?Twiml=' . urlencode($str), // Location of your TwiML
                                    array('Timeout' => '120','Record' => 'false')
                            );

                           
                        } catch (Exception $e) {

                            echo 'Error starting phone call: ' . $e->getMessage() . "\n"; exit;
                        }
             
             //$this->staff_phone_manager->send_sms_message($staff_number,$message);
            }
            set_message_note('Announcement call for staff is initiated.', MESSAGE_NOTE_SUCCESS);
            return true;
        } else {
            set_message_note($this->form_validation->error_string(), MESSAGE_NOTE_WARNING);
        }
        return false;
    }
    public function toolbar_apply() {
        if ($this->_call()) {
            redirect('admin/send_sms');
        }
    }

    public function toolbar_cancel() {
        redirect('admin/send_sms');
    }
}
?>
