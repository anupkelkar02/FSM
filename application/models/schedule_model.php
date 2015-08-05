<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Schedule_model extends MY_Model
{
	
	public function get_rows($filter, $sort_order = '', $limit_start = -1, $limit_count = 0)
	{
		$where_sql = $this->get_where_sql($filter, 'sch');

		$sql = "SELECT sch.*"
				." FROM #__schedule AS sch"
				." INNER JOIN #__site AS ste ON ste.id = sch.site_id"
				. ( $where_sql ? " WHERE $where_sql " : '' )
				. ( $sort_order ? " ORDER BY ".form_sort_order_as_sql($sort_order) : '' )
				. ( ($limit_start >= 0 and $limit_count > 0) ? " LIMIT $limit_start , ".$limit_count : "" )
				;
		$query = $this->db->query($sql);
		return $query->result();
	}
		
		
	public function get_row_count($filter, $sort_order = '')
	{
		$where_sql = $this->get_where_sql($filter, 'sch');

		$sql = "SELECT COUNT(sch.id) AS count"
				." FROM #__schedule AS sch"
				." INNER JOIN #__site AS ste ON ste.id = sch.site_id"
				. ( $where_sql ? " WHERE $where_sql " : '' )
				. ( $sort_order ? " ORDER BY ".form_sort_order_as_sql($sort_order) : '' )
				;
          // var_dump($sql);exit;
		$query = $this->db->query($sql);
		$row = $query->row();
		if ( $row ) {
			return $row->count;
		}
		return 0;
	}	


	public function get_distinct_month_year($filter, $sort_order = '')
	{
		$where_sql = $this->get_where_sql($filter, 'sch');

		$sql = "SELECT DISTINCT MONTH(sch.start_date) AS month, YEAR(sch.start_date) as year"
				." FROM #__schedule AS sch"
				." INNER JOIN #__site AS ste ON ste.id = sch.site_id"
				. ( $where_sql ? " WHERE $where_sql " : '' )
				. ( $sort_order ? " ORDER BY ".form_sort_order_as_sql($sort_order) : '' )
				;
		$query = $this->db->query($sql);
		return $query->result();
	}	

			
	public function get_sort_order()
	{
		$order =  'start_date = start_date,
					site_name = ste.name,
					shift_type = shift_type '
				;
		return form_sort_order_as_array($order);
	}
		
	public function get_shift_type_dropdown_list($default_title = '')
	{
		return parent::_generate_enum_dropdown_list('#__schedule', 'shift_type', $default_title);
	}			
	
	
	public function reset_attendance_request_time($id = FALSE)
	{
		if ( $id == FALSE ) {
			$id = $this->id;
		}
		$this->db->update('schedule', array('attendance_request_time'=>date('Y-m-d H:m:s'), 'reply_status_id'=>0), array('id'=>$id));
	}
	
	public function set_reply_status_id($reply_status_id, $id = FALSE)
	{
		if ( $id == FALSE ) {
			$id = $this->id;
		}
		$this->db->update('schedule', array('reply_status_id'=>$reply_status_id), array('id'=>$id));
	}
	protected function _add_filter_item_to_where_expression($name, $value)
	{
		if ( $name == 'site_id_match' ) {
			if ( $value == '' or $value == '0') {
				return;
			}
			return $this->_add_where_expression("sch.site_id = ".intval($value));
		}
		if ( $name == 'start_date' ) {
			if ( $value == '' or $value == '0') {
				return;
			}
			return $this->_add_where_expression("sch.start_date >= '$value'");
		}
		parent::_add_filter_item_to_where_expression($name, $value);
	}

public function get_schedule($where_sql, $sort_order = '', $limit_start = -1, $limit_count = 0)
	{
		$sql = "SELECT sch.*"
				." FROM #__schedule AS sch"
				." INNER JOIN #__site AS ste ON ste.id = sch.site_id"
				. ( $where_sql ? " WHERE $where_sql " : '' )
				. ( $sort_order ? " ORDER BY ".form_sort_order_as_sql($sort_order)." desc" : '' )
				. ( ($limit_start >= 0 and $limit_count > 0) ? " LIMIT $limit_start , ".$limit_count : "" )
				;
               
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	
	public function leaveschedule($month,$site_id,$leavetype)
	{
		$y=date('Y-'.$month);
		
		if($leavetype=="")
		{
		$sql = "SELECT sch.*"
				." FROM #__schedule AS sch"
				." WHERE sch.start_date like '%".$y."%' and sch.site_id='".$site_id."'"
				;
		}
		else
		{
		$sql = "SELECT sch.*"
				." FROM #__schedule AS sch"
				." WHERE sch.start_date like '%".$y."%' and sch.site_id='".$site_id."' and sch.work_status_id='".$leavetype."'"
				;	
		}
               
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	
	public function attendanc_disc($site_id)
	{
		$y=date('Y-m');
		$sql = "SELECT sch.start_date,SUM(IF(sch.reply_status_id = '1', 1,0)) AS `presentcnt`,
SUM(IF(sch.reply_status_id = '0', 1,0)) AS `not_res`,
SUM(IF(sch.reply_status_id = '6', 1,0)) AS `CB_res`,
SUM(IF(sch.reply_status_id = '3', 1,0)) AS `absent`"
				." FROM #__schedule AS sch"
				." WHERE sch.start_date like '%".$y."%' and sch.site_id='".$site_id."' GROUP BY sch.start_date"
				;
				
				
			$query = $this->db->query($sql);
		return $query->result();
	}
	
	
	
	
 public function set_onsite_status_id($onsite_status_id, $id = FALSE)
	{
		if ( $id == FALSE ) {
			$id = $this->id;
		}
		$this->db->update('schedule', array('on_site_status'=>$onsite_status_id), array('id'=>$id));
	}
	
 public function get_sop_schedule()
	{
		//$sql = "SELECT * FROM grs_duty WHERE CURTIME() BETWEEN start_time AND end_time and date_format(updated_time,'%Y-%m-%d')!=curdate()";
		$sql = "SELECT * FROM grs_duty WHERE '".date('H:i:00')
                        . "' BETWEEN start_time AND end_time and date_format(updated_time,'%Y-%m-%d')!='".date('Y-m-d')."'";
                
                //var_dump($sql);exit;
                $query = $this->db->query($sql);
		return $query->result();
	}

}

?>
