<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


 
require_once(APPPATH.'/third_party/tropo.class'.EXT);
require_once(APPPATH.'/core/site_controller'.EXT);

class Tropo_api extends Site_Controller 
{


	public function index()
	{
		echo 'Yes we are here';
	}
	
	public function send_test_sms()
	{
		$this->set_output_mode(MY_Controller::OUTPUT_NORMAL);		
		$phone_number = $this->input->post('phone_number');
		$this->load->library('staff_phone_manager');
		if ( $phone_number) { 
			$this->staff_phone_manager->send_sms_message($phone_number, 'The test call button has been pressed');
			echo 'OK\n';
		}
		else {
			echo 'ERROR\n';
		}
	}
	
	public function sms($number = '', $initial_text = '')
	{
		$this->load->library('staff_phone_manager');
		$this->set_output_mode(MY_Controller::OUTPUT_NORMAL);		
		$this->staff_phone_manager->process_command($initial_text);
	}
	
	public function voice($number = '', $initial_text = '')
	{
		$this->set_output_mode(MY_Controller::OUTPUT_NORMAL);
		$session = new Session();
		
		$phone_number = $session->getParameters('phone_number');
		$message = $session->getParameters('message');

		$tropo = new Tropo();
		$tropo->call("sip:$phone_number@shims.starhub.net.sg");
		$tropo->wait(1000);
		$tropo->say($message);		
		$caller = $session->getFrom();
		$called = $session->getTo();
		$tropo->say('Your number is '.$caller['id']);
		
		//$tropo->say('This is via '.$called['channel']);
		$tropo->ask('Press 1 to hangup or 2 to continue', array('choices'=>"1,2", 'name'=>'digit', 'timeout'=>60));
		$tropo->on(array('event'=>'continue', 'next'=>'voice_continue'));
		$tropo->RenderJson();		
		
	}
	
	public function voice_continue()
	{
		$this->set_output_mode(MY_Controller::OUTPUT_NORMAL);
		$tropo = new Tropo();
		@$result = new Result();
		$answer = $result->getValue();
		$tropo->say("You said " . $answer);
		$tropo->RenderJson();
	}
		
}

