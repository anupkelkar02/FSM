<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH . '/third_party/Services/Twilio' . EXT);

class Staff_call extends CI_Controller {

    var $callsid;

    public function __construct() {
        parent::__construct();
        $this->load->model(array('site_model', 'twilio_model', 'staff_model', 'reply_status_model', 'schedule_model', 'site_shift_model', 'sysconf_model'));
    }

    public function call_staff() {
        $sys_conf = $this->sysconf_model->get_row();
        if ($sys_conf->autocall_confirm == 'True') {
            $day_number = date('N', time());

            $rows = $this->schedule_model->get_schedule('start_date="' . date('Y-m-d') . '" and work_status_id in (1,2) and on_site_status=0');

            date_default_timezone_set($this->sysconf_model->get_timezone_name($sys_conf->sys_timezone));
            echo 'Current Time:' . date('d-m-Y H:i:s');
            $call_time = $sys_conf->autocall_time;
            foreach ($rows as $row) {

                $this->_filter = filter_load('filter', array('site_id' => $row->site_id,'shift_type!='=>'3')
                );
                $shiftres = $this->site_shift_model->get_rows($this->_filter);
                $wdays = $this->site_model->get_workingdays_rows($this->_filter);
                $working_days = array();
                foreach ($wdays as $w) {
                    array_push($working_days, $w->working_day);
                }

                $shift_name = '';
                //echo '<br/>#####'.in_array($day_number, $working_days); exit;
                if (in_array($day_number, $working_days)) {
                    foreach ($shiftres as $shift) {

                        if ($call_time == 0) {
                            $compare_time = date('H:i:00');
                        } else {
                            $selectedTime = date('H:i:00');
                            $endTime = strtotime("+$call_time minutes", strtotime($selectedTime));

                            $compare_time = date('H:i:s', $endTime);
                        }
                        // echo '<br/>'.$compare_time; exit;
                        if ($shift->start_time <= $compare_time && $shift->end_time >= $compare_time) {
                            $shift_name = $shift->shift_type;
                            echo $shift_name;
                            $this->Random_Call($row->staff_id, $row->id);
                        }
                    }

                    if ($row->shift_type == $shift_name) {

                        echo $row->staff_id . '<br/>';
                    }
                }
            }
        }
    }

    public function Random_Call($staff_id, $sch_id) {
        //echo $staff_id; exit;
        //break;
        $this->load->model(array('twilio_model', 'staff_model'));
        $twilio_det = $this->twilio_model->get_row();
        //var_dump($twilio_det); exit;
        $twilio_number = $twilio_det->twilio_number;
        $twilio_sid = $twilio_det->twilio_sid;
        $twilio_token = $twilio_det->twilio_token;
        $staff_number = $this->staff_model->getNumber($staff_id);
        $numbers = array($staff_number);
        $client = new Services_Twilio($twilio_sid, $twilio_token);

        foreach ($numbers as $number) {
            $randomnum = rand(1, 10000000);

            try {
                $call = $client->account->calls->create(
                        $twilio_number, // Caller ID
                        '+'  . $number, // Your friend's number
                        base_url() . 'index.php/admin/staff_call/poll/' . $randomnum . '/' . $sch_id  // Location of your TwiML
                );

                $data = array('call_staff_id' => $staff_id, 'call_sid' => $call->sid, 'call_date_time' => date('Y-m-d H:i:s'), 'random_number' => $randomnum, 'sch_id' => $sch_id);
                $this->twilio_model->insert_call_log($data);
                //echo "Started call: $call->sid\n";
                $this->callsid = $call->sid;
            } catch (Exception $e) {
                $data = array('call_staff_id' => $staff_id, 'call_sid' => '', 'call_date_time' => date('Y-m-d H:i:s'), "failure_reason" => $e->getMessage());
                echo 'Error starting phone call: ' . $e->getMessage() . "\n";
            }
        }
    }

    public function poll($randomnum, $sch_id) {
        $this->load->model(array('reply_status_model'));
        $response = new Services_Twilio_Twiml();
        $gather = $response->gather(array(
            'action' => base_url() . 'index.php/admin/staff_call/process_poll/' . $randomnum . '/' . $sch_id,
            'method' => 'GET',
            'numDigits' => '1'
                ));
        $reply_items = array();
        $reply_rows = $this->reply_status_model->get_rows(array());
        $gather->say("Attendance status call.");
        $gather->say("Shift is started already. ");
        foreach ($reply_rows as $reply_row) {
            if ($reply_row->number > 0) {
                $gather->say("Press " . $reply_row->number . ' for ' . $reply_row->title . '. ');
                $gather->pause('1');
                //$reply_items[] = '('.$reply_row->number.')'.$reply_row->title;
            }
        }
        header('Content-Type: text/xml');
        print $response;
    }

    public function process_poll($randomid, $sch_id) {

        $this->load->model(array('reply_status_model', 'schedule_model'));
        $digit = isset($_REQUEST['Digits']) ? $_REQUEST['Digits'] : null;
        $reply_rows = $this->reply_status_model->get_rows(array());
        foreach ($reply_rows as $reply_row) {
            if ($reply_row->number == $digit) {
                $flag = true;
                $this->reply_status_model->updateReplyStatus($digit, $randomid);
                $this->schedule_model->set_reply_status_id($digit, $sch_id);
                $this->schedule_model->set_onsite_status_id($digit, $sch_id);
                break;
            } else {
                $flag = false;
            }
        }
        if ($flag == false) {
            $say = "Sorry, You have choosen incorrect availability";
        } else {
            $say = "Thank you. We have marked your availability";
        }
        $response = new Services_Twilio_Twiml();
        $response->say($say);
        $response->hangup();
        header('Content-Type: text/xml');
        print $response;
    }

    public function send_checkin_sms() {

        $sys_conf = $this->sysconf_model->get_row();
        date_default_timezone_set($this->sysconf_model->get_timezone_name($sys_conf->sys_timezone));
        echo 'Current Time:' . date('d-m-Y H:i:s');
        if ($sys_conf->check_in_alert == true) {
            $twilio_det = $this->twilio_model->get_row();

            $twilio_number = $twilio_det->twilio_number;
            $twilio_sid = $twilio_det->twilio_sid;
            $twilio_token = $twilio_det->twilio_token;
            $rows = $this->schedule_model->get_schedule('start_date="' . date('Y-m-d') . '" and on_site_status in (1)');
            $session = new Services_Twilio($twilio_sid, $twilio_token);
            foreach ($rows as $row) {

                $this->_filter = filter_load('filter', array('site_id' => $row->site_id)
                );
                $shiftres = $this->site_shift_model->get_rows($this->_filter);
                $staff_number = $this->staff_model->getNumber($row->staff_id);
                $message = 'Your shift is started . Kindly check in to the site. ';
                foreach ($shiftres as $shift) {

                    if ($shift->start_time == date('H:i:00')) {
                        $shift_name = $shift->shift_type;
                        $message = $session->account->sms_messages->create($twilio_number, '+' . $staff_number, $message, array());
                    }
                }
            }
        }
    }

    public function send_checkout_sms() {

        $sys_conf = $this->sysconf_model->get_row();
        date_default_timezone_set($this->sysconf_model->get_timezone_name($sys_conf->sys_timezone));
        echo 'Current Time:' . date('d-m-Y H:i:s');
        if ($sys_conf->check_out_alert == true) {
            $twilio_det = $this->twilio_model->get_row();

            $twilio_number = $twilio_det->twilio_number;
            $twilio_sid = $twilio_det->twilio_sid;
            $twilio_token = $twilio_det->twilio_token;
            $rows = $this->schedule_model->get_schedule('start_date="' . date('Y-m-d') . '" and on_site_status in (1)');
            $session = new Services_Twilio($twilio_sid, $twilio_token);
            foreach ($rows as $row) {

                $this->_filter = filter_load('filter', array('site_id' => $row->site_id)
                );
                $shiftres = $this->site_shift_model->get_rows($this->_filter);
                $staff_number = $this->staff_model->getNumber($row->staff_id);
                $message = 'Your shift is started . Kindly check in to the site. ';
                foreach ($shiftres as $shift) {

                    if ($shift->end_time == date('H:i:00')) {
                        $shift_name = $shift->shift_type;
                        $message = $session->account->sms_messages->create($twilio_number, '+' . $staff_number, $message, array());
                    }
                }
            }
        }
    }

    function get_livestate() {
        $this->load->model(array('site_model', 'schedule_model', 'staff_model'));

        $this->_filter = filter_load('filter', array('is_published' => 'True')
        );
        $sites_rows = $this->site_model->get_rows($this->_filter);
        $html = '';
        foreach ($sites_rows as $site) {

            $sch_rows = $this->schedule_model->get_schedule('start_date="' . date('Y-m-d') . '" and site_id =' . $site->id);
            if (!empty($sch_rows)) {

                $presentcnt = $absent = $not_res = $CB_res = 0;
                $str = '';
                foreach ($sch_rows as $sch) {
                    if ($sch->reply_status_id == 1) {
                        $presentcnt +=1;
                        $class = 'success';
                    } else if ($sch->reply_status_id == 0) {
                        $not_res +=1;
                        $class = 'warning';
                    } else if ($sch->reply_status_id == 6) {
                        $CB_res +=1;
                        $class = 'alert-info';
                    } else {
                        $absent +=1;
                        $class = 'alert-danger';
                    }
                }
                $str .='<td><a href="index.php/admin/home/att_report/1/' . $site->id . '">' . $presentcnt . '</a></td><td><a href="index.php/admin/home/att_report/2/' . $site->id . '">' . $absent . '</a></td><td><a href="index.php/admin/home/att_report/3/' . $site->id . '">' . $not_res . '</a></td><td><a href="index.php/admin/home/att_report/4/' . $site->id . '">' . $CB_res . '</a></td>';
                $html .='<tr ><td class="' . $class . '"><a href="admin/schedule/assignment/true/' . $site->id . '" >' . $site->code . '</a></td>' . $str . '</tr>';
            } else {
                
            }
        }
        if ($html == '') {
            $html .='<tr class="alert-danger text-center"><td colspan="100%">No schedule found for today ! </td></tr>';
        }
        echo $html;
    }
	
	
	function attendance_discrepancey() {
        $this->load->model(array('site_model', 'schedule_model', 'staff_model'));

        $this->_filter = filter_load('filter', array('is_published' => 'True')
        );
         $sites_rows = $this->site_model->get_rows($this->_filter);
	
        $html = '';
        foreach ($sites_rows as $site) {

            $sch_rows = $this->schedule_model->attendanc_disc($site->id);
            if (!empty($sch_rows)) {

                $presentcnt = $absent = $not_res = $CB_res = 0;
                $str = '';
				
                foreach ($sch_rows as $sch) {
					
				
					
					$html .='<tr ><td class="' . $class . '"><a href="admin/schedule/assignment/true/' . $site->id . '" >' . $site->code . '</a></td><td><a href="index.php/admin/home/att_report/1/' . $site->id . '">' . $sch->start_date . '</a></td><td><a href="index.php/admin/home/att_report/1/' . $site->id . '">' . $sch->presentcnt . '</a></td><td><a href="index.php/admin/home/att_report/1/' . $site->id . '">'.$sch->absent  . '</a></td><td><a href="index.php/admin/home/att_report/2/' . $site->id . '">' . $sch->CB_res . '</a></td><td><a href="index.php/admin/home/att_report/3/' . $site->id . '">' . $sch->not_res . '</a></td><td><a href="index.php/admin/home/att_report/4/' . $site->id . '">'  . '</a></td></tr>';
					
					
                
			     }
				 
				 
                
            } else {
                
            }
        }
        if ($html == '') {
            $html .='<tr class="alert-danger text-center"><td colspan="100%">No schedule found for today ! </td></tr>';
        }
        echo $html;
    }
	
	
	function leave_plane() 
	
	{
        $this->load->model(array('site_model', 'schedule_model', 'staff_model'));

        $this->_filter = filter_load('filter', array('is_published' => 'True'));
       
	 
	 $sites_rows = $this->site_model->get_rows($this->_filter);
		
        $html = '';
     
	 foreach ($sites_rows as $site) {
        $html = '';
		$lastmont=date('m');
		for($i=1;$i<=$lastmont;$i++)
		 {
			if($i<=9)
			{
			$month='0'.$i;	
			}
			else
			{
			$month=$i;	
			}
			
			if($i==1)
			{
			$monthname="Jan";	
			}
			if($i==2)
			{
			$monthname="Feb";	
			}
			if($i==3)
			{
			$monthname="Mar";	
			}
			if($i==4)
			{
			$monthname="Apr";	
			}
			if($i==5)
			{
			$monthname="May";	
			}
			if($i==6)
			{
			$monthname="Jun";	
			}
			if($i==7)
			{
			$monthname="Jul";	
			}
			if($i==8)
			{
			$monthname="Aug";	
			}
			if($i==9)
			{
			$monthname="Sep";	
			}
			if($i==10)
			{
			$monthname="Oct";	
			}
			if($i==11)
			{
			$monthname="Nov";	
			}
			if($i==12)
			{
			$monthname="Dec";	
			}
			
			$leavetype=$this->input->post('leave_type');
     
            $sch_rows = $this->schedule_model->leaveschedule($month,$site->id,$leavetype);
            
                $stf_leave = $CB_res = 0;
                $str = '';
						//For Call Back////
						foreach ($sch_rows as $sch) 
							{
									
								if ($sch->reply_status_id == 6) {
									$CB_res +=1;
									$class = 'alert-info';
								}
								else if ($sch->work_status_id == 2) {
									$stf_leave +=1;
									$class = 'alert-info';
								} else if ($sch->work_status_id == 3) {
									$stf_leave +=1;
									$class = 'alert-info';
								}
								else if ($sch->work_status_id == 4) {
									$stf_leave +=1;
									$class = 'alert-info';
								}
								else if ($sch->work_status_id == 7) {
									$stf_leave +=1;
									$class = 'alert-info';
								}    
						   }
						   
				  /*  $y=date('Y-'.$month);
					$sql = "SELECT count(att.id) as countstaff"
							." FROM #__attendance AS att"
							." WHERE att.att_date like '%".$y."%' and att.site_id='".$site->id."'"
							;
					  // var_dump($sql);exit;
					$query = $this->db->query($sql);
					$row = $query->row();*/
					
						 if($stf_leave >0 or $CB_res>0)
						 {  
						   $html .='<tr ><td class="' . $class . '"><a href="admin/schedule/assignment/true/' . $sch->id . '" >' . $site->code . '</a></td><td><a href="index.php/admin/home/att_report/1/' . $sch->id . '">' . date('Y')."-".$monthname . '</a></td><td><a href="index.php/admin/home/att_report/1/' . $sch->id . '">' . $stf_leave . '</a></td><td><a href="index.php/admin/home/att_report/4/' . $sch->id . '">' . $CB_res . '</a></td></tr>';
						 }
					
		      }
         echo $html;
    	}
	}
	

    function add_site_shifts($siteid, $start_time, $end_time) {
        $str = array('site_id' => $siteid, 'shift_type' => 'Day', 'start_time' => $start_time, 'end_time' => $end_time);
        $this->site_shift_model->insert($str);
    }

    function del_site_shifts($id) {

        $this->site_shift_model->delete($id);
    }
     
}




?>
