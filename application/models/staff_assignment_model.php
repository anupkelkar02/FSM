<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Staff_assignment_model extends MY_Model
{
	
	
	public function get_rows($filter, $sort_order = '', $limit_start = -1, $limit_count = 0)
	{
		$where_sql = $this->get_where_sql($filter, 'sag');

		$sql = "SELECT sag.*"
				." FROM #__staff_assignment AS sag"
				. ( $where_sql ? " WHERE $where_sql " : '' )
				. ( $sort_order ? " ORDER BY ".form_sort_order_as_sql($sort_order) : '' )
				. ( ($limit_start >= 0 and $limit_count > 0) ? " LIMIT $limit_start , ".$limit_count : "" )
				;
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	public function get_staff_rows($filter, $sort_order = '', $limit_start = -1, $limit_count = 0)
	{
		$where_sql = $this->get_where_sql($filter, 'sag');

		$sql = "SELECT sag.*"
				.", stf.*"
				." FROM #__staff_assignment AS sag"
				." INNER JOIN #__staff AS stf ON stf.id = sag.staff_id "
				. ( $where_sql ? " WHERE $where_sql " : '' )
				. ( $sort_order ? " ORDER BY ".form_sort_order_as_sql($sort_order) : '' )
				. ( ($limit_start >= 0 and $limit_count > 0) ? " LIMIT $limit_start , ".$limit_count : "" )
				;
		$query = $this->db->query($sql);
		return $query->result();
	}


	public function get_site_rows($filter, $sort_order = '', $limit_start = -1, $limit_count = 0)
	{
		$where_sql = $this->get_where_sql($filter, 'sag');

		$sql = "SELECT sag.*"
				.", ste.*"
				." FROM #__staff_assignment AS sag"
				." INNER JOIN #__site AS ste ON ste.id = sag.site_id "
				. ( $where_sql ? " WHERE $where_sql " : '' )
				. ( $sort_order ? " ORDER BY ".form_sort_order_as_sql($sort_order) : '' )
				. ( ($limit_start >= 0 and $limit_count > 0) ? " LIMIT $limit_start , ".$limit_count : "" )
				;
		$query = $this->db->query($sql);
		return $query->result();
	}
		
	public function get_dropdown_list($default_title = '')
	{
		$sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS title "
				." FROM #__staff_assignment "
				." ORDER BY last_name, first_name"
				;
		$query = $this->db->query($sql);
		return parent::_generate_dropdown_list($query->result(), $default_title);
	}
	
	public function get_shift_type_dropdown_list($default_title = '')
	{
		return parent::_generate_enum_dropdown_list('#__staff_assignment', 'shift_type', $default_title);
	}			

	public function get_assign_type_dropdown_list($default_title = '')
	{
		return parent::_generate_enum_dropdown_list('#__staff_assignment', 'assign_type', $default_title);
	}			
	
	public function get_sort_order()
	{
		$order =  'last_name = last_name, '
				. 'first_name = first_name'
				;
		return form_sort_order_as_array($order);
	}

}

?>
