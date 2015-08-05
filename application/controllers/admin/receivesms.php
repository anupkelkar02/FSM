<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once(APPPATH.'/third_party/Services/Twilio'.EXT);
class Receivesms extends CI_Controller {
    
   public function __construct() {
        parent::__construct();
        $this->load->model(array('reply_status_model','twilio_model','staff_model','schedule_model'));
        
    }

    
    public function rcvsms(){
         $frommob = $_REQUEST['From'] ? trim($_REQUEST['From']) : '';
         $tomob = $_REQUEST['To'] ? trim($_REQUEST['To']) : '';
         $msg = $_REQUEST['Body'] ? trim($_REQUEST['Body']) : '';
       $twilio_det = $this->twilio_model->get_row();
                $twilio_number = $twilio_det->twilio_number;
                $twilio_sid = $twilio_det->twilio_sid;
                 $twilio_token = $twilio_det->twilio_token;
         $frommob=ltrim($frommob, '+');
         file_put_contents("rcvsms.txt", $frommob);
         $tomob=ltrim($tomob, '+');
        $msg= str_replace('Sent from your Twilio trial account - ','', $msg);
         $reply_rows = $this->reply_status_model->get_rows(array());
        foreach ($reply_rows as $reply_row) {
            if ($reply_row->number == $msg) {
                $flag = true;
                break;
            } else {
                $flag = false;
            }
        }
          $session = new Services_Twilio($twilio_sid,$twilio_token);
        if ($flag == false) {
            $message = 'Sorry, You have choosen incorrect availability';
          
            $resmessage = $session->account->sms_messages->create($twilio_number, '+' . $frommob, $message, array());
        }else{
            //store in DB
            
            $staff_id = $this->staff_model->getIdFromNumber($frommob,$msg);
            
            if($staff_id!=0 && $staff_id!='' && $staff_id!=null){
                
                 $res = $this->schedule_model->get_schedule('staff_id='.$staff_id.' and attendance_request_time !="0000-00-00 00:00:00"','id',0,1);
                 file_put_contents("rcvsms.txt", $res[0]->id);
                 $this->schedule_model->set_reply_status_id($msg,$res[0]->id);
            }else{
                 $message = 'Sorry, Your mobile number is not registered with us. Kindly contact system administrator.';
          
                $resmessage = $session->account->sms_messages->create($twilio_number, '+' . $frommob, $message, array());  
            }
        }
    //     $this->Common_Model->getStaffID($frommob,$msg);
    }
    
    
}

