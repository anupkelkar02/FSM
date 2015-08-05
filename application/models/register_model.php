<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Register_model extends MY_Model
{
	
	public function get_rows($filter, $sort_order = '', $limit_start = -1, $limit_count = 0)
	{
		$where_sql = $this->get_where_sql($filter, 'reg');

		$sql = "SELECT reg.*"
				." FROM #__register AS reg"
				. ( $where_sql ? " WHERE $where_sql " : '' )
				. ( $sort_order ? " ORDER BY ".form_sort_order_as_sql($sort_order) : '' )
				. ( ($limit_start >= 0 and $limit_count > 0) ? " LIMIT $limit_start , ".$limit_count : "" )
				;
		$query = $this->db->query($sql);
		return $query->result();
	}
		
	public function get_dropdown_list($default_title = '')
	{
		$sql = "SELECT id, fist_name AS title "
				." FROM #__register "
				." ORDER BY first_name"
				;
		$query = $this->db->query($sql);
		return parent::_generate_dropdown_list($query->result(), $default_title);
	}
	
			
	public function get_sort_order()
	{
		$order =  'first_name = first_name '
				;
		return form_sort_order_as_array($order);
	}

}

?>
