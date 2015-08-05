<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/core/site_admin_controller'.EXT);

class Systemconf extends Site_admin_controller
{
     private $_row;
    public function __construct() {
        parent::__construct();
       
       
    }
    public function index(){
        
         $this->load->model(array('sysconf_model'));
         
        if (!$this->sysconf_model->load_by_id(1)) {
            set_message_note($this->lang->line('error_no_record'), MESSAGE_NOTE_FAILURE);
            redirect('admin/sites');
        }
        $this->load->library(array('form_validation'));
        $this->form_validation->set_rules('autocall_confirm', 'Automatic Call Confirmation', '');
        $this->form_validation->set_rules('autocall_time', 'Automatic Call Confirmation Time Before', 'required');
        $this->form_validation->set_rules('check_in_alert', 'Check-in Reminder SMS', '');
        $this->form_validation->set_rules('check_out_alert', 'Check-out Reminder SMS', '');
        $this->form_validation->set_rules('sop_alert', 'SOP Alert By Calls', '');
        $this->form_validation->set_rules('sop_alert_sms', 'SOP Alert By SMS', '');
        $this->form_validation->set_rules('autoleave_plan_call', 'Automatic Leave Planning Calls', '');
        $this->form_validation->set_rules('call_gaurdhouse', 'Calls to Guard House at Sites instead of calling Staff', '');
        $this->form_validation->set_rules('sys_timezone', 'Timezone', 'required');
        $this->form_validation->set_rules('ofc_number','Office Number','');
        $timezones = $this->sysconf_model->get_timezones();
        $this->_row = $this->sysconf_model->get_row();
        $this->_row = $this->form_validation->get_input_row($this->_row);

         toolbar_process_task($this);
         
         $data = array('row' => $this->_row,'timezone'=>$timezones);
         $this->load->view('admin/system_config',$data);
    }
     protected function _save() {
         //var_dump($_POST); exit;
        if ($this->form_validation->run()) {
            $autocall_confirm = isset($_POST['autocall_confirm'])?$_POST['autocall_confirm']:'False';
            $autocall_time = isset($_POST['autocall_time'])?$_POST['autocall_time']:'0';
            
            $check_in_alert = isset($_POST['check_in_alert'])?$_POST['check_in_alert']:'False';
            $check_out_alert = isset($_POST['check_out_alert'])?$_POST['check_out_alert']:'False';
            
            $sop_alert = isset($_POST['sop_alert'])?$_POST['sop_alert']:'False';
            $sop_alert_sms = isset($_POST['sop_alert_sms'])?$_POST['sop_alert_sms']:'False';
            
            $autoleave_plan_call = isset($_POST['autoleave_plan_call'])?$_POST['autoleave_plan_call']:'False';
            $call_gaurdhouse = isset($_POST['call_gaurdhouse'])?$_POST['call_gaurdhouse']:'False';
            
            $sys_timezone = isset($_POST['sys_timezone'])?$_POST['sys_timezone']:'1';
            $ofc_number = isset($_POST['ofc_number'])?$_POST['ofc_number']:'';
            $data = array('call_gaurdhouse'=>$call_gaurdhouse,
                'autoleave_plan_call'=>$autoleave_plan_call,
                'sop_alert_sms'=>$sop_alert_sms,
                'sop_alert'=>$sop_alert,
                'check_out_alert'=>$check_out_alert,
                'check_in_alert'=>$check_in_alert,
                'autocall_time'=>$autocall_time,
                'autocall_confirm'=>$autocall_confirm,
                'sys_timezone'=>$sys_timezone,
                'ofc_number'=>$ofc_number);
            $this->sysconf_model->updateSysConf($data, array('id'=>$this->sysconf_model->id));
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
            redirect('admin/systemconf');
        }
    }

//    public function toolbar_cancel() {
//        redirect('admin/');
//    }
}
?>
