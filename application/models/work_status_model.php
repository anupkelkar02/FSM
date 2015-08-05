<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Work_status_model extends MY_Model
{
	
	public function get_rows($filter, $sort_order = '', $limit_start = -1, $limit_count = 0)
	{
		$where_sql = $this->get_where_sql($filter, 'wst');

		$sql = "SELECT wst.*"
				." FROM #__work_status AS wst"
				. ( $where_sql ? " WHERE $where_sql " : '' )
				. ( $sort_order ? " ORDER BY ".form_sort_order_as_sql($sort_order) : '' )
				. ( ($limit_start >= 0 and $limit_count > 0) ? " LIMIT $limit_start , ".$limit_count : "" )
				;
		$query = $this->db->query($sql);
		return $query->result();
	}
		
		
	public function get_row_count($filter, $sort_order = '')
	{
		$where_sql = $this->get_where_sql($filter, 'wst');

		$sql = "SELECT COUNT(wst.id) AS count"
				." FROM #__work_status AS wst"
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
		$sql = "SELECT id, title "
				." FROM #__work_status "
				." ORDER BY title"
				;
		$query = $this->db->query($sql);
		return parent::_generate_dropdown_list($query->result(), $default_title);
	}
	
			
	public function get_sort_order()
	{
		$order =  'title = title'
				;
		return form_sort_order_as_array($order);
	}
	
	public function get_row_from_code($code)
	{
		return reset($this->get_rows(array('code'=>$code)));
	}

}

?>
