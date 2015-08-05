<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/core/site_admin_controller'.EXT);

class System_control extends Site_admin_controller
{	
	public function index()
	{	
		
		toolbar_process_task($this);
	
		$data = array(
					);

		$this->load->view('admin/system_control', $data);		
	}

	public function toolbar_build_data()
	{
		$this->_build_site_data();
		$this->_build_staff_data();
		$this->_build_staff_assignments();
	}
	public function build_test_data()
	{
		$this->_build_site_data();
	}
	
	public function toolbar_build_schedule()
	{
		$this->load->model(array('site_model', 
									'site_shift_model',
									'staff_model', 
									'staff_assignment_model',
									'schedule_model',
									'work_status_model',
									)
						);

		$site_rows = $this->site_model->get_rows(array('is_published'=>'True'));
		foreach ( $site_rows as $site_row ) {
			$this->_build_schedule_for_site($site_row);
		}
		
	}
	
	protected function _build_schedule_for_site($site_row)
	{
		$site_shift_rows = $this->site_shift_model->get_rows(array('site_id'=>$site_row->id));
		$work_status_rows = $this->work_status_model->get_rows(array());
		$start_date = datetime_start_month();
		$end_date = datetime_add_days(60, $start_date);
		$current_date = $start_date;
		while ( $current_date < $end_date ) {
			$day_of_week_name = date('D', strtotime($current_date));
			$used_staff_ids = array();
			$relief_counts = array();
			foreach ( $site_shift_rows as $site_shift_row) {
				$this->schedule_model->delete_where(array('start_date'=>$current_date, 
																	'site_id'=>$site_row->id, 
																	'shift_type'=>$site_shift_row->shift_type
													)
											);
				$staff_rows  = $this->staff_assignment_model->get_staff_rows(array('site_id'=>$site_row->id, 
																					'shift_type'=>$site_shift_row->shift_type,
																					'assign_type'=>'FullTime'
																					)
															);
				$relief_counts[$site_shift_row->shift_type] = 0;
				for ( $count = 0; $count < $site_shift_row->staff_count; $count++ ) {
					if ( count($staff_rows) > 0 ) {
						
						$staff_row = array_pop($staff_rows);
						$work_status_row = $this->work_status_model->get_row_from_code('W'. strtoupper(substr($site_shift_row->shift_type, 0, 1)));
						$off_day_names = explode(",", $staff_row->off_day_names);
						
						if ( in_array($day_of_week_name, $off_day_names) ) {
							$work_status_row = $this->work_status_model->get_row_from_code('O');
							$relief_counts[$site_shift_row->shift_type] ++;
						}
						else {
							if ( rand(0, 100) > 80  ) {
								$index = array_rand($work_status_rows, 1);
								if ( $index > 3 ) {
									$work_status_row = $work_status_rows[$index];
									$relief_counts[$site_shift_row->shift_type] ++;
								}
							}
						}
						$used_staff_ids[] = $staff_row->staff_id;
						$data = array('start_date'=>$current_date,
									'site_id'=>$site_row->id,
									'staff_id'=>$staff_row->staff_id,
									'shift_type'=>$site_shift_row->shift_type,
									'work_status_id'=> $work_status_row->id
									);
						$this->schedule_model->add_row($data);
					}
				}
			}
			$work_status_row = $this->work_status_model->get_row_from_code('WD');
			foreach ( $relief_counts as $shift_type=>$relief_count) {
				if ( $relief_count > 0 ) {
					$staff_rows  = $this->staff_assignment_model->get_staff_rows(array('site_id'=>$site_row->id, 
																						'assign_type'=>'Relief'
																						)	
																		);
					$staff_indexes = array_rand($staff_rows, $relief_count);
					if ( is_numeric($staff_indexes) ) {
						$staff_indexes = array($staff_indexes);
					}
					
					foreach ( $staff_indexes as $staff_index ) {
						$staff_row = $staff_rows[$staff_index];
						$data = array('start_date'=>$current_date,
									'site_id'=>$site_row->id,
									'staff_id'=>$staff_row->staff_id,
									'shift_type'=>$shift_type,
									'work_status_id'=> $work_status_row->id
									);
						$this->schedule_model->add_row($data);
					}
				}
			}
			$current_date = datetime_add_days(1, $current_date);
		}		
	}
	
	protected function _find_best_staff($assign_type, $staff_rows, $shift_type, $used_staff_ids)
	{
		foreach ( $staff_rows as $staff_row ) {
			if ( $staff_row->assign_type == $assign_type and !in_array($staff_row->staff_id, $used_staff_ids) ) {
				if ( $staff_row->shift_type == $shift_type) {
					$staff_row;
					break;
				}
			}
		}
		return FALSE;
	}
	

	protected function _build_site_data()
	{
		$this->load->model(array('site_model', 'site_shift_model'));
		$fp = fopen(APPPATH.'sql/MCST.csv', 'r');
		if ( $fp ) {
			$data = fgetcsv($fp);
			while( ($data = fgetcsv($fp, 0, "\t")) ) {
				$name = trim($data[2]);
				if ( $data[5] == 'ACTIVE' and $name and trim($data[4])) {
					$rows = $this->site_model->get_rows(array('name'=>$name));
					if ( !$rows ) {
						$address = $this->_split_address($data[4]);
						$day_count = 0;
						$day_relief = 0;
						if ( rand(0, 100) > 95 ) {
							$day_count = rand(1, 3);
							$day_relief = $day_count + 1;
						}
						$night_count = 0;
						$night_relief = 0;
						if ( $day_count > 0 and rand(0, 100) > 40 ) {
							$night_count = rand(1, 3);
							$night_relief = $night_count + 1;
						}
						$row = array('name'=>$name,
									'street_number'=>$address->street_number,
									'street_name'=>$address->street_name,
									'unit_number'=>$address->unit_number,
									'city'=>'Singapore',
									'country'=>'Singapore',
									'postcode'=>$address->postcode,
									'update_time'=>date('Y-m-d H:i:s')
								);
						$site_id = $this->site_model->add_row($row);
						$row = array('site_id'=>$site_id,
									'start_time'=>'06:00:00',
									'end_time'=>'18:00:00',
									'shift_type'=>'Day',
									'staff_count'=>$day_count,
									'max_relief_count'=>$day_relief,
									'update_time'=>date('Y-m-d H:i:s')
									);
						$this->site_shift_model->add_row($row);
						$row = array('site_id'=>$site_id,
									'start_time'=>'18:00:00',
									'end_time'=>'06:00:00',
									'shift_type'=>'Night',
									'staff_count'=>$night_count,
									'max_relief_count'=>$night_relief,
									'update_time'=>date('Y-m-d H:i:s')
									);
						$this->site_shift_model->add_row($row);
						if ( $day_count == 0 and $night_count == 0 ) {
							$this->site_model->update_row(array('is_published'=>'False'), $site_id);
						}
					}
				}
				
			
			}
			fclose($fp);
		}
		
		
	}
	
	protected function _build_staff_data()
	{
		$this->load->model('staff_model');
		$first_names = $this->_load_names('first_names.csv');
		$last_names = $this->_load_names('last_names.csv');

		$counter = 1000;
		while ( $counter > 0 ) {
			$first_name = $first_names[rand(0, count($first_names) - 1)];
			$last_name = $last_names[rand(0, count($last_names) - 1)];
			$row = reset($this->staff_model->get_rows(array('last_name'=>$last_name, 'first_name'=>$first_name)));
			if ( !$row ) {
				$data = array('last_name'=>$last_name,
							'first_name'=>$first_name,
							'phone_number'=>'91234567',
							'update_time'=>date('Y-m-d H:i:s'),
							'fin_number'=> 'S'.random_string('numeric', 7).strtoupper(random_string('alpha', 1)),
							);
				$this->staff_model->add_row($data);
				$counter --;
			}
		}
		
	}
	
	protected function _build_staff_assignments()
	{
		$this->load->model('staff_assignment_model');
		$staff_rows = $this->staff_model->get_rows(array('is_published'=>'True'));
		$site_rows = $this->site_model->get_rows(array('is_published'=>'True'));
		foreach ( $site_rows as $site_row) {
			$site_shift_rows = $this->site_shift_model->get_rows(array('site_id'=>$site_row->id));
			foreach ( $site_shift_rows as $site_shift_row) {
				for ( $staff_count = 0; $staff_count < $site_shift_row->staff_count; $staff_count ++ ) {
					$staff_index = array_rand($staff_rows, 1);
					$this->_assign_staff_to_site($site_row, $staff_rows[$staff_index], $site_shift_row, 'FullTime');
				}
				if ( $site_shift_row->max_relief_count > 0 ) {
//					$relief_max = rand(1, $site_shift_row->max_relief_count);
					$relief_max = $site_shift_row->max_relief_count;
					for ( $relief_count = 0; $relief_count < $relief_max ; $relief_count ++ ) {
						$staff_index = array_rand($staff_rows, 1);
						$this->_assign_staff_to_site($site_row, $staff_rows[$staff_index], $site_shift_row, 'Relief');
					}
				}
			}
		}
	}
	
	
	protected function _assign_staff_to_site($site_row, $staff_row, $site_shift_row, $assign_type)
	{
		$off_day_names = array();
		$day_names = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
		if ( $assign_type == 'FullTime') {
			$day_count = rand(1, 2);
			for ( $i = 0; $i < $day_count; $i ++ ) {
				$day_value = rand(0, 6);
				$off_day_name = $day_names[$day_value];
				if ( !in_array($off_day_name, $off_day_names)) {
					$off_day_names[] = $off_day_name;
				}
			}
		}
		$data = array('site_id'=>$site_row->id,	
					'staff_id'=>$staff_row->id,
					'assign_type'=>$assign_type,
					'shift_type'=>$site_shift_row->shift_type, 
					'off_day_names'=>implode(",", $off_day_names),
					);
		$this->staff_assignment_model->add_row($data);
	}
	protected function _load_names($filename)
	{
		$names = array();
		$data = file_get_contents(APPPATH.'sql/'.$filename);
		if ( $data ) {
			$names = explode("\r", $data);
			array_shift($names);
		}
		return $names;
	}
	
	protected function _split_address($text)
	{
		$address = new StdClass();
		$address->street_number = '';
		$address->street_name = '';
		$address->unit_number = '';
		$address->postcode = '';
		if ( preg_match('/(\w+\-\w+)/i', $text, $match) ) {
			$address->unit_number = $match[1];
			$text = preg_replace('/'.preg_quote($match[0], '/').'/', '', $text);
		}
		$text = preg_replace('/- /', '', $text);
		if ( preg_match('/SINGAPORE (.*)$/', $text, $match) ) {
			$address->postcode = $match[1];
			$text = preg_replace('/'.preg_quote($match[0], '/').'/', '', $text);
		}
		if ( preg_match('/^(\S+)\s/', $text, $match) ) {
			$address->street_number = $match[1];
			$text = preg_replace('/'.preg_quote($match[0], '/').'/', '', $text);
		}
		$address->street_name = trim($text);
		return $address;
	}

	
}


?>
