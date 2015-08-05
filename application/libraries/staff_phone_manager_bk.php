<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'libraries/manager'.EXT);
//require_once(APPPATH.'/third_party/tropo.class'.EXT);
require_once(APPPATH.'/core/site_controller'.EXT);
require_once(APPPATH.'/third_party/Services/Twilio'.EXT);

class Staff_phone_manager extends Manager
{	
	public function __construct($params = '')
	{
		parent::__construct($params);
		$this->CI->load->model(array('phone_session_model', 'schedule_model', 'staff_model', 'site_model', 'reply_status_model','twilio_model'));
		$this->_session = new StdClass;
		$this->_session->staff_id = 0;
		$this->_session->from_phone_number = '';
		$this->_session->initial_text = '';
		$this->_session->command = '';
		$this->_session->parameters = FALSE;
		$this->_session->message = '';
                 $this->_session->twilio_sid = '';
                $this->_session->twilio_token = '';
                $this->_session->twilio_number = '';
	}
	
	public function intitiate_setup($staff_id)
	{
		$this->_session->staff_id = $staff_id;
		//$this->_send_command('send-setup');
		$this->CI->staff_model->load_by_id($staff_id);
		$this->_call_voice($this->CI->staff_model->phone_number, 'Test message');
	}
	
	public function send_sms_message($phone_number, $message)
	{
		$this->_call_sms($phone_number, $message);		
	}
	
	public function request_attendance($staff_id, $site_id, $schedule_id, $shift_type, $start_time,$staff_preference)
	{
		$this->_session->staff_id = $staff_id;
		$this->CI->site_model->load_by_id($site_id);
                 $staff_number = $this->CI->staff_model->getNumber($staff_id);
                
         //*************************** Twilio Details Start*******************************//    
                $twilio_det = $this->CI->twilio_model->get_row();
                $this->_session->twilio_number = $twilio_det->twilio_number;
                $this->_session->twilio_sid = $twilio_det->twilio_sid;
                 $this->_session->twilio_token = $twilio_det->twilio_token;
          //*************************** Twilio Details Ends*******************************//  

		$this->CI->schedule_model->reset_attendance_request_time($schedule_id);
		$mins_left = (strtotime($start_time) - time() ) / 60;
		if ( $mins_left > 0 ) {
			if ( $mins_left > ( 24 * 60 ) ) {
				$time_text = 'is starting on '.date('D d M Y H:i');
			}
			else {
				$time_text = 'is about to start in '.format_minutes_as_text($mins_left);
			}
			
		}
		else {
			$time_text = 'has already started';
		}
		$reply_items = array();
		$reply_rows = $this->CI->reply_status_model->get_rows(array());
		foreach ( $reply_rows as $reply_row ) {
			if ( $reply_row->number > 0 ) {
				$reply_items[] = '('.$reply_row->number.')'.$reply_row->title;
			}
		}
		$message = "Your $shift_type shift $time_text. Please reply ".implode($reply_items, ',').".";
		if($staff_preference=='Voice'){
                    $this->_call_voice($staff_number,$message);
                }else{
                    $this->send_sms_message($staff_number,$message);
                    //$this->_send_reply($message); 
                }
//		$this->_set_command('wait-attendance', array('schedule_id'=>$schedule_id));
//		$this->_send_reply($message);

	}
	
	
	public function process_command($initial_text)
	{
		$this->_load_session($initial_text);
		switch ( $this->_session->command) {
			case 'send-sms':
				$this->_send_reply_message();
			break;
			case 'send-message':
				$this->_send_reply_message();
			break;
			case 'send-setup':
				$this->_set_command('wait-call-type');
				$this->_send_reply('Hello, welcome. Would you like to report attendance through (1) SMS or (2) Phone?');
			break;
			case 'wait-call-type':
				$value = intval($this->_session->initial_text);
				$message = 'We will be reporting through %s reply.'
						. 'When would you like to get reminder? (1) 10 mins before shift (2) 30 mins (3) 1 hr'
						;
				if ( $value ==  1 ) {
					$this->CI->staff_model->update_row(array('call_type'=>'SMS'), $this->_session->staff_id);
					$this->_set_command('wait-call-time');				
					$this->_send_reply(sprintf($message, 'SMS'));
				}
				elseif ( $value ==  2 ) {
					$this->CI->staff_model->update_row(array('call_type'=>'Voice'), $this->_session->staff_id);
					$this->_set_command('wait-call-time');				
					$this->_send_reply(sprintf($message, 'phone/IVR'));
				}
				else {
					$this->_send_reply('Invalid response. Would you like to report attendance through (1) SMS or (2) Phone?');
				}
			break;
			case 'wait-call-time':
				$value = intval($this->_session->initial_text);				
				$mins = 0;
				if ( $value == 1 ) {
					$mins = 10;
				}
				elseif ( $value == 2 ) {
					$mins = 30;
				}
				elseif ( $value == 3) {
					$mins = 60;
				}
				if ( $mins > 0 ) {
					$this->_clear_command();
					$this->CI->staff_model->update_row(array('call_minutes' => $mins), $this->_session->staff_id);
					$this->_send_reply('Thank you. You will get a reminder '.$mins.' minutes before your shift starts.');
				}
				else {
					$this->_send_reply('Invalid response. When would you like to get reminder? (1) 10 mins before shift (2) 30 mins (3) 1 hr');
				}
			break;
			case 'wait-attendance':
				$value = intval($this->_session->initial_text);
				if ( $value > 0 and $this->_session->parameters and is_array($this->_session->parameters) ) {
					$schedule_id = $this->_session->parameters['schedule_id'];
					if ( $schedule_id ) {
						$this->CI->schedule_model->set_reply_status_id($value, $schedule_id);
					}
					$this->_clear_command();
					$this->_send_reply('Thank you.');					
				}
			break;
			default:
				if ( preg_match('/setup/i', $this->_session->initial_text) ) {
					$this->_send_command('send-setup');
				}
				if ( preg_match('/call/i', $this->_session->initial_text) ) {
					$this->_call_voice('I am now calling you');
				}
				
			break;
		}
	}
	
	
	protected function _load_session($initial_text)
	{

		$tropo_session = new Session();

		$this->_session->from_phone_number = '';
		$this->_session->initial_text = '';
		$this->_session->command = '';
		$this->_session->parameters = FALSE;
		$this->_session->staff_id = 0;
		if ( $tropo_session ) {
			
			$this->_session->command = $tropo_session->getParameters('command');
			$this->_session->message = $tropo_session->getParameters('message');
			if ( $this->_session->command == 'send-sms' ) {
				$this->_session->from_phone_number = $tropo_session->getParameters('phone_number');
				return;
			}
			$this->_session->staff_id = $tropo_session->getParameters('staff_id');
			$this->_session->initial_text = $tropo_session->getInitialText();
			$this->_session->from = $tropo_session->getFrom();
			if ( isset($this->_session->from['id']) ) {
				$this->_session->from_phone_number = preg_replace('/^'.$this->_config['country_code'].'/', '', $this->_session->from['id']);
			}
			if ( $this->_session->from_phone_number ) {
				$row = reset($this->CI->phone_session_model->get_rows(array('phone_number'=>$this->_session->from_phone_number)));
				if ( $row ) {
					$this->_session->staff_id = $row->staff_id;
					$user_data = unserialize($row->user_data);
					if ( is_object($user_data) ) {
						$this->_session->command = $user_data->command;
						$this->_session->parameters = $user_data->parameters;
					}
				}
				else {
					$row = reset($this->CI->staff_model->get_rows(array('phone_number'=>$this->_session->from_phone_number)));
					if ( $row ) {
						$this->_session->staff_id = $row->id;
					}
					else {
						echo "Cannot find phone number in list: ". $this->_session->from_phone_number;
					}
				}
			}
		}
		if ( $this->_session->staff_id ) {
			$this->CI->staff_model->load_by_id($this->_session->staff_id);
			$this->_session->from_phone_number = $this->CI->staff_model->phone_number;
		}
		
	}
	protected function _send_command($command)
	{
		$this->_set_command($command);
		$session = new SessionAPI();
		$session->setBaseURL($this->_config['tropo_url']);
		
		$parameters = array('staff_id' => $this->_session->staff_id,
						'command'=> $command
						);
		$session_id = $session->createSession($this->_config['tropo_api_sms'], $parameters);
	}
	
	protected function _set_command($command, $parameters = array())
	{
		$staff_id = $this->_session->staff_id;
		$this->CI->staff_model->load_by_id($staff_id);
		$user_data = new StdClass;
		$user_data->command = $command;
		$user_data->parameters = $parameters;
		$this->CI->phone_session_model->set_value($staff_id, $this->CI->staff_model->phone_number, serialize($user_data));		
	}
	
	protected function _clear_command()
	{
		$this->CI->phone_session_model->clear_value($this->_session->staff_id);		

	}

	protected function _send_reply($message)
	{

		$session = new SessionAPI();
		$session->setBaseURL($this->_config['tropo_url']);
		
		$parameters = array('staff_id' => $this->_session->staff_id,
						'message'=> $message,
						'command'=>'send-message'
						);
		$session_id = $session->createSession($this->_config['tropo_api_sms'], $parameters);

	}

	protected function _call_voice($phone_number, $message)
	{
		// does not work at the moment

		$session = new SessionAPI();
		$session->setBaseURL($this->_config['tropo_url']);
		
		$parameters = array('staff_id' => $this->_session->staff_id,
						//'phone_number'=>$this->_session->from_phone_number,
						'phone_number'=>$phone_number,
						'message'=>$message
						);
		$session_id = $session->createSession($this->_config['tropo_api_voice'], $parameters);
	}


	protected function _call_sms($phone_number, $message)
	{

		try{    
                $session = new Services_Twilio($this->_session->twilio_sid,$this->_session->twilio_token);
//		$session = new SessionAPI();
//		$session->setBaseURL($this->_config['tropo_url']);
//		
//		
//		$parameters = array('message'=> $message,
//						'phone_number'=>$phone_number,
//						'command'=>'send-sms'
//						);
//		$session_id = $session->createSession($this->_config['tropo_api_sms'], $parameters);
                 $message = $session->account->sms_messages->create($this->_session->twilio_number, '+' . $phone_number, $message, array());
            }catch(Exception $e){
                echo $e->getMessage();
            }


	}
	
	protected function _send_reply_message()
	{
		$tropo = new Tropo();
		$tropo->call($this->_session->from_phone_number, array('network'=>'SMS'));
		$tropo->say($this->_session->message);
		$tropo->RenderJson();		
	}
}

?>
