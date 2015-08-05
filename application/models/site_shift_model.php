<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Site_shift_model extends MY_Model
{
	
	public function get_rows($filter, $sort_order = '', $limit_start = -1, $limit_count = 0)
	{
		$where_sql = $this->get_where_sql($filter, 'sts');

		$sql = "SELECT sts.*"
				." FROM #__site_shift AS sts"
				." INNER JOIN #__site AS ste ON ste.id = sts.site_id"
				. ( $where_sql ? " WHERE $where_sql " : '' )
				. ( $sort_order ? " ORDER BY ".form_sort_order_as_sql($sort_order) : '' )
				. ( ($limit_start >= 0 and $limit_count > 0) ? " LIMIT $limit_start , ".$limit_count : "" )
				;
		$query = $this->db->query($sql);
		return $query->result();
	}
		

	public function get_sort_order()
	{
		$order =  'start_time = start_time '
				;
		return form_sort_order_as_array($order);
	}
public function insert($data){
            
            $this->db->insert('grs_site_shift', $data);
            return $this->db->insert_id();
            
        }
public function delete($id){
             $this->db->where('id', $id);
            $this->db->delete('grs_site_shift');
           
            
        }
}

?>
