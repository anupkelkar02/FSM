<?php

date_default_timezone_set('Asia/Singapore');
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH . '/core/site_admin_controller' . EXT);
require_once(APPPATH . '/third_party/Services/Twilio' . EXT);

class Duty_Cronjob extends CI_controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('schedule_model');
        $this->load->model('duty_model');
        $this->load->model(array('twilio_model', 'sysconf_model'));
        $this->load->helper('site_helper');
    }

    public function index($row_pos = 0) {
        
    }

    public function sendsopmessage() {
        $sys_conf = $this->sysconf_model->get_row();
        if ($sys_conf->sop_alert_sms == 'True') {
            $rows = $this->schedule_model->get_sop_schedule();

            $this->load->library('staff_phone_manager');
            foreach ($rows as $row) {
                $number = $this->duty_model->get_staff_member($row->site_id, $row->shift);
                foreach ($number as $num) {
                    $this->staff_phone_manager->send_sms_message($num->phone_number, $row->duty);
                }
                $date = date('Y-m-d H:i:s');
                $data = array('updated_time' => $date);

                $id = array('id' => $row->id);
                $this->duty_model->updatedutytime($data, $id);
            }
        }else{
            echo 'turned off !! Can not send messages alerts..';
        }
    }

    public function sendsopCalls() {
        $sys_conf = $this->sysconf_model->get_row();
        
        if ($sys_conf->sop_alert == 'True') {
            $twilio_det = $this->twilio_model->get_row();
            $twilio_number = $twilio_det->twilio_number;
            $twilio_sid = $twilio_det->twilio_sid;
            $twilio_token = $twilio_det->twilio_token;
            $client = new Services_Twilio($twilio_sid, $twilio_token);
            $rows = $this->schedule_model->get_sop_schedule();

            foreach ($rows as $row) {
                $str = '<Response>
<Say voice="alice">Hello, This is duty notification call.</say><Pause length="1"/><say>' . $row->duty . '</Say>
</Response>';
                $number = $this->duty_model->get_staff_member($row->site_id, $row->shift);

                if (!empty($number)) {
                    foreach ($number as $num) {
                        try {


                            $call = $client->account->calls->create(
                                    $twilio_number, // Caller ID
                                    '+'  . $num->phone_number, // Your friend's number
                                    'http://twimlets.com/echo?Twiml=' . urlencode($str) // Location of your TwiML
                            );

                            echo $call->sid;
                            if ($call->sid) {
                                $date = date('Y-m-d H:i:s');
                                $data = array('updated_time' => $date);

                                $id = array('id' => $row->id);
                                $this->duty_model->updatedutytime($data, $id);
                            }
                        } catch (Exception $e) {

                            echo 'Error starting phone call: ' . $e->getMessage() . "\n";
                        }
                    }
                } else {
                    echo 'No staff found :(';
                }
            }
        }else{
            echo 'turned off !! Can not send calls alerts..';
        }
    }

}

?>