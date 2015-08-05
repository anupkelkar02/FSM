<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Postal_district_model extends MY_Model
{
	
	public function get_rows($filter, $sort_order = '', $limit_start = -1, $limit_count = 0)
	{
		$where_sql = $this->get_where_sql($filter, 'ptd');

		$sql = "SELECT ptd.*"
				." FROM #__postal_district AS ptd"
				." INNER JOIN #__postal_district_sector AS pts ON pts.postal_district_number = ptd.number"
				. ( $where_sql ? " WHERE $where_sql " : '' )
				. ( $sort_order ? " ORDER BY ".form_sort_order_as_sql($sort_order) : '' )
				. ( ($limit_start >= 0 and $limit_count > 0) ? " LIMIT $limit_start , ".$limit_count : "" )
				;
		$query = $this->db->query($sql);
		return $query->result();
	}
		
	public function get_dropdown_list($default_title = '')
	{
		$sql = "SELECT number, CONCAT('number, ' - ', location) AS title "
				." FROM #__postal_district "
				." ORDER BY number"
				;
		$query = $this->db->query($sql);
		return parent::_generate_dropdown_list($query->result(), $default_title);
	}
	
			
	public function get_sort_order()
	{
		$order =  'number = number '
				;
		return form_sort_order_as_array($order);
	}

	protected function _add_filter_item_to_where_expression($name, $value)
	{
		if ( $name == 'postcode' ) {
			if ( $value == '' ) {
				return;
			}
			$value = intval(substr($value, 0, 2));
			return $this->_add_where_expression("pts.postal_sector = $value");
		}
		return parent::_add_filter_item_to_where_expression($name, $value);
	}
	
}

?>
