<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Phone_session_model extends MY_Model
{
	
	public function get_rows($filter, $sort_order = '', $limit_start = -1, $limit_count = 0)
	{
		$where_sql = $this->get_where_sql($filter, 'phs');

		$sql = "SELECT phs.*"
				." FROM #__phone_session AS phs"
				. ( $where_sql ? " WHERE $where_sql " : '' )
				. ( $sort_order ? " ORDER BY ".form_sort_order_as_sql($sort_order) : '' )
				. ( ($limit_start >= 0 and $limit_count > 0) ? " LIMIT $limit_start , ".$limit_count : "" )
				;
		$query = $this->db->query($sql);
		return $query->result();
	}
		
	public function set_value($staff_id, $phone_number, $value)
	{
		$query = $this->db->get_where('phone_session', array('staff_id'=>$staff_id));
		$row = $query->row();
		
		$data = array('staff_id'=>$staff_id,
					'phone_number'=>$phone_number,
					'user_data'=>$value,
					'last_activity'=>date('Y-m-d H:i:s')
					);
		if ( $query->num_rows() > 0 ) {
			$this->db->update('phone_session', $data, array('id'=>$row->id));
		}
		else {
			$this->db->insert('phone_session', $data);
		}
	}
	
	public function clear_value($staff_id)
	{
		$this->db->delete('phone_session', array('staff_id'=>$staff_id));
	}
	
	public function get_sort_order()
	{
		$order =  'last_activity = last_activity '
				;
		return form_sort_order_as_array($order);
	}
	

}

?>
