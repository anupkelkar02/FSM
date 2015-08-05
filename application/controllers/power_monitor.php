<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/core/site_controller'.EXT);

class Power_monitor extends Site_Controller 
{

	function __construct()
	{
		parent::__construct();

	}


	public function index()
	{
		$this->add_javascript_file(base_url().'toolkits/rickshaw/vendor/d3.min.js', TRUE);
		$this->add_javascript_file(base_url().'toolkits/rickshaw/vendor/d3.layout.min.js', TRUE);
		$this->add_javascript_file(base_url().'toolkits/rickshaw/rickshaw.min.js', TRUE);
		$this->add_css_file(base_url().'toolkits/rickshaw/rickshaw.min.css', TRUE);
		$db = $this->load->database('power', TRUE);
		
		$date_list = [];
		$query = $db->query('SELECT DISTINCT DATE(timestamp) as date FROM data_item ORDER BY timestamp');
		foreach ( $query->result() as $row ) {
			$date = $row->date;
			$date_list[$date] = date('l d F Y', strtotime($date));
			
		}
		$date = $this->input->post('date', $date);
		
		
		$query = $db->query("SELECT * FROM data_item WHERE timestamp >= '$date 00:00:00' AND timestamp <= '$date 23:59:59' ORDER BY timestamp");
		
		$rows = $query->result();
		$items = [];
		//$value = 0;
		//$time_index = 0;
		//foreach ( $rows as $row ) {
			//if ( $time_index > 0 ) {
				//$next_time_index = strtotime($row->timestamp);
				//for ( $i = $time_index; $i < $next_time_index ; $i ++ ) {
					//$items[$i] = $value;
				//}
			//}
			//$time_index = strtotime($row->timestamp);
			//$value = $row->value;
			//$items[$time_index] = $row->value;
		//}
		$data = array('items'=>$items, 
					'date_list'=>$date_list,
					'date'=>$date,
					'rows'=>$rows);
		$this->load->view('power_monitor', $data);
		
	}
	
}

