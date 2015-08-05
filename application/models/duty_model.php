<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Duty_model extends MY_Model
{
	
	public function get_rows($filter, $sort_order = '', $limit_start = -1, $limit_count = 0)
	{
		$where_sql = $this->get_where_sql($filter, 'd');

		$sql = "SELECT d.*,code FROM #__duty AS d inner join #__site s on d.site_id=s.id"
				. ( $where_sql ? " WHERE $where_sql " : '' )
				. ( $sort_order ? " ORDER BY ".form_sort_order_as_sql($sort_order) : '' )
				. ( ($limit_start >= 0 and $limit_count > 0) ? " LIMIT $limit_start , ".$limit_count : "" )
				;
		$query = $this->db->query($sql);
		return $query->result();
	}
        
        
        public function get_staff_member($site_id,$shift_type){
            
            $sql = "select staff.phone_number from grs_duty gd inner join grs_staff_assignment gsa 
on gd.site_id = gsa.site_id inner join grs_schedule gs on gs.staff_id=gsa.staff_id inner join grs_site gsite 
on gsite.id=gsa.site_id inner join grs_staff staff on staff.id=gsa.staff_id where gs.site_id='".$site_id."' and gs.shift_type='".$shift_type."' and gs.on_site_status='1' and start_date=".date('Y-m-d')." and work_status_id in (1,2) group by staff.id";  
            //echo $sql;exit;
            $query = $this->db->query($sql);
            return $query->result();
            
        }

  public function insertDuty($data){
            //var_dump($data);
            $this->db->insert('grs_duty', $data);
            return $this->db->insert_id();
            
        }
		
		
	public function get_row_count($filter, $sort_order = '')
	{
		$where_sql = $this->get_where_sql($filter, 'd');

		$sql = "SELECT COUNT(d.id) AS count"
				." FROM #__duty AS d"
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
		$sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS title "
				." FROM #__staff "
				." ORDER BY last_name, first_name"
				;
		$query = $this->db->query($sql);
		return parent::_generate_dropdown_list($query->result(), $default_title);
	}
	
	public function get_call_type_dropdown_list($default_title = '')
	{
		return parent::_generate_enum_dropdown_list('#__staff', 'call_type', $default_title);
	}			
	
	
			
	public function get_sort_order()
	{
		$order =  'id=id'
				;
		return form_sort_order_as_array($order);
	}

	protected function _add_filter_item_to_where_expression($name, $value)
	{
		if ( $name == 'name_match' ) {
			if ( $value == '' or $value == '0') {
				return;
			}
			return $this->_add_where_expression("( (stf.first_name LIKE  '%$value%') OR ( stf.last_name LIKE  '%$value%') )");
		}
		parent::_add_filter_item_to_where_expression($name, $value);
	}

        public function insertStaff($data){
            //var_dump($data);
            $this->db->insert('grs_staff', $data);
            return $this->db->insert_id();
            
        }
 
        public function insertStaffAssignment($data){
 
            $this->db->insert('grs_staff_assignment',$data);
        
        }

    function checkICExist($ic){
            //echo 'select id from grs_staff where fin_number="'.$ic.'"';
            $query = $this->db->query('select id from grs_staff where fin_number="'.$ic.'"');
            $res = $query->result();
            return $res[0]->id;
      }
      function updateStaff($data,$ic) {
        //echo $name;
        return $this->db->update('grs_staff', $data, $ic);
      }

function deleteStaffAsssign($staff_id) {
        //echo 'staffid='.$staff_id;
        $this->db->where('staff_id', $staff_id);
        return $this->db->delete('grs_staff_assignment');
    }

	public function getNumber($staff_id) {
             $query = $this->db->query('select phone_number from grs_staff where id="'.$staff_id.'"');
            $res = $query->result();
            return $res[0]->phone_number;
        }

        public function getDuty(){
            $query=  $this->db->query('SELECT id,site_id,duty,shift FROM `grs_duty` WHERE curtime() BETWEEN `start_time` AND `end_time` OR (curtime() BETWEEN `end_time` AND `start_time` AND `start_time` > `end_time`)');
            return $query->result();
        }
    public function siteid($id)
    {
        $query= $this->db->query('SELECT site_id FROM `grs_duty` WHERE  id="'.$id.'"'); 
        $res= $query->result();
        return $res[0]->site_id;
    }
    public function staff_member($id)
    {
        $query= $this->db->query('SELECT staff_id FROM `grs_staff_assignment` where site_id="'.$id.'"');
        return $query->result();
    }
    public function get_message($id)
    {
        $query= $this->db->query('SELECT duty FROM `grs_duty` where id="'.$id.'"');
        $res = $query->result();
        return $res[0]->duty;
    }
    public function get_staff_number($staff_id)
    {
        $query= $this->db->query('SELECT phone_number FROM `grs_staff` where id="'.$staff_id->staff_id.'"');
        $res = $query->result();
        return $res[0]->phone_number;
    }

     function updatedutytime($data,$id) {
        //echo $data;
        return $this->db->update('grs_duty', $data, $id);
      }
}
?>