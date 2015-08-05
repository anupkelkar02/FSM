<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Timesheet_model extends MY_Model
{
	
	public function get_rows($filter, $sort_order = '', $limit_start = -1, $limit_count = 0)
	{
		$where_sql = $this->get_where_sql($filter, 'ste');

		$sql = "SELECT ste.*"
				." FROM #__site AS ste"
				. ( $where_sql ? " WHERE $where_sql " : '' )
				. ( $sort_order ? " ORDER BY ".form_sort_order_as_sql($sort_order) : '' )
				. ( ($limit_start >= 0 and $limit_count > 0) ? " LIMIT $limit_start , ".$limit_count : "" )
				;
		$query = $this->db->query($sql);
		return $query->result();
	}
		
		
	public function get_row_count($filter, $sort_order = '')
	{
		$where_sql = $this->get_where_sql($filter, 'ste');

		$sql = "SELECT COUNT(ste.id) AS count"
				." FROM #__site AS ste"
				. ( $where_sql ? " WHERE $where_sql " : '' )
				. ( $sort_order ? " ORDER BY ".form_sort_order_as_sql($sort_order) : '' )
				;
		$query = $this->db->query($sql);
		$row = $query->row();
		if ( $row ) {
			return $row->count;
		}
		return 0;
	}	
	
	public function get_dropdown_list($default_title = '')
	{
		$sql = "SELECT id, name AS title "
				." FROM #__site "
				." WHERE is_published = 'True'"
				." ORDER BY name"
				;
		$query = $this->db->query($sql);
		return parent::_generate_dropdown_list($query->result(), $default_title);
	}

	public function get_staffs_dropdown_list($default_title = '')
	{
		$sql = "SELECT id, CONCAT(first_name,' ',last_name) AS title"
				." FROM #__staff "
				." WHERE id != ''"
				." ORDER BY first_name"
				;
		$query = $this->db->query($sql);
		return parent::_generate_dropdown_list($query->result(), $default_title);
	}	
	
	public function get_site_staffs_dropdown_list($siteID='') {
		$sql = "SELECT sa.staff_id,CONCAT(s.first_name,' ',s.last_name) AS staff_name FROM #__staff_assignment sa ".
			   "LEFT JOIN #__staff s ON s.id=sa.staff_id ".
			   "WHERE sa.site_id='".$siteID."' ORDER BY s.first_name";
		$query = $this->db->query($sql);
		return $query->result();
	}
			
	public function get_sort_order()
	{
		$order =  'name = name '
				;
		return form_sort_order_as_array($order);
	}
	

		
	protected function _add_filter_item_to_where_expression($name, $value)
	{
		if ( $name == 'name_match' ) {
			if ( $value == '' or $value == '0') {
				return;
			}
			return $this->_add_where_expression("ste.name like  '%$value%'");
		}
		parent::_add_filter_item_to_where_expression($name, $value);
	}
	
	public function getStaffAssigmentDataCount($searchData="") {
		if($this->session->userdata('searchData')!="") {
			$searchData = $this->session->userdata('searchData');
		}
			
		if(!empty($searchData["monthName"])) {
			$sql = "SELECT * FROM #__staff_assignment sa WHERE sa.id!='' ";
			if($searchData["siteName"]!="")
				$sql .= " AND sa.site_id='".$searchData["siteName"]."'";
			if($searchData["staffName"]!="")
				$sql .= " AND sa.staff_id='".$searchData["staffName"]."'";
		
			$query = $this->db->query($sql);
			return $query->result();
		} else {
			return 0;
		}	
	}
	
	public function getStaffAssigmentData($config,$searchData) {
		if($this->session->userdata('searchData')!="") {
			$searchData = $this->session->userdata('searchData');
		}
		$limit = $config["per_page"];
		$offset = $config["cur_page"];		
			
		if(!empty($searchData["monthName"])) {
			$sql = "SELECT * FROM #__staff_assignment sa WHERE sa.id!='' ";
			if($searchData["siteName"]!="")
				$sql .= " AND sa.site_id='".$searchData["siteName"]."'";
			if($searchData["staffName"]!="")
				$sql .= " AND sa.staff_id='".$searchData["staffName"]."'";
		
			if($searchData["siteName"]!="")
				$this->db->order_by('sa.site_id','asc');
			
			$this->db->limit($limit,$offset);
			$query = $this->db->query($sql);
			return $query->result();
		} else {
			return 0;
		}
	}

	public function getTimesheetData($searchData) {
		if(!empty($searchData["monthName"])) {
			$sql = "SELECT ss.id,ss.site_id,ss.staff_id,ss.shift_type,ss.work_status_id,ss.start_date,ss.reply_status_id,".
				   "ws.title,ws.code,ws.background_color,ws.text_color FROM #__schedule ss ".
				   "LEFT JOIN #__work_status ws ON ws.id=ss.work_status_id ".
				   "WHERE DATE_FORMAT(ss.start_date,'%Y-%m')='".$searchData["monthName"]."'";
			if($searchData["siteName"]!="")
				$sql .= " AND ss.site_id='".$searchData["siteName"]."'";
			if($searchData["staffName"]!="")
				$sql .= " AND ss.staff_id='".$searchData["staffName"]."'";
		
			$this->db->order_by('ss.start_date','asc');
			$query = $this->db->query($sql);
			return $query->result();
		} else {
			return 0;
		}
	}

}

?>
