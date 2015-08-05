<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Staff_model extends MY_Model
{
	
	public function get_rows($filter, $sort_order = '', $limit_start = -1, $limit_count = 0)
	{
		$where_sql = $this->get_where_sql($filter, 'stf');

		$sql = "SELECT stf.*"
				." FROM #__staff AS stf"
				. ( $where_sql ? " WHERE $where_sql " : '' )
				. ( $sort_order ? " ORDER BY ".form_sort_order_as_sql($sort_order) : '' )
				. ( ($limit_start >= 0 and $limit_count > 0) ? " LIMIT $limit_start , ".$limit_count : "" )
				;
		$query = $this->db->query($sql);
//var_dump($query->result());
		return $query->result();
	}

  public function getStaffList(){
            	$sql = "SELECT stf.*"
				." FROM #__staff AS stf";
		$query = $this->db->query($sql);
		return $query->result();
        }

public function getStaffIds(){
            $sql = "select gsa.site_id,gs.name from grs_staff_assignment gsa inner join grs_site gs on gsa.site_id=gs.id group by gs.id";
            $query = $this->db->query($sql);
	    return $query->result();
        }
        
        public function getStaffAssignList($site_id){
            
                    
               $sql = "select gsa.id as id,gstaff.first_name as fname,gsite.name as sitename,assign_type,shift_type,off_day_names from grs_staff_assignment gsa inner join grs_staff gstaff on gsa.staff_id=gstaff.id inner join grs_site gsite 
on gsa.site_id =gsite.id where gsa.site_id=".$site_id;
		$query = $this->db->query($sql);
		return $query->result();
        }
	


		  
		
	public function get_row_count($filter, $sort_order = '')
	{
		$where_sql = $this->get_where_sql($filter, 'stf');

		$sql = "SELECT COUNT(stf.id) AS count"
				." FROM #__staff AS stf"
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
               // echo $sql;exit;
		$query = $this->db->query($sql);
		return parent::_generate_dropdown_list($query->result(), $default_title);
	}
	
	public function get_call_type_dropdown_list($default_title = '')
	{
		return parent::_generate_enum_dropdown_list('#__staff', 'call_type', $default_title);
	}			
	
	
			
	public function get_sort_order()
	{
		$order =  'last_name = last_name, '
				. 'first_name = first_name'
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
         function add_staffdata($datauser)
 {
  $this->db->insert('grs_staff',$datauser);
  return $this->db->insert_id();
 } 
  
 function checknumber($number)
 {
     $query=$this->db->query('select fin_number from grs_staff where fin_number="'.$number.'"');
     $res=$query->result();
     return $res;
     
 } public function getIdFromNumber($staff_num) {
             $query = $this->db->query('select id from grs_staff where phone_number="'.$staff_num.'"');
            $res = $query->result();
            return $res[0]->id;
 }

   public function getStaffId($fin_number) {
         // echo 'select id from grs_staff where fin_number="'.$fin_number.'"';exit;
          $query = $this->db->query('select id from grs_staff where fin_number="'.$fin_number.'"');
          $res = $query->result();
          //var_dump(count($res[0]->id));exit;
          if($query->num_rows()==1){
              return $res[0]->id;
          }else{
              return 0;
          }
          
        }
        
        public function insertactivestaff($data){
            //var_dump($data);
            $this->db->insert('grs_activestaff_details', $data);
            return $this->db->insert_id();
            
        }
   
        function update_staffdata($ispublished,$staff_id)
        {
            //echo $id; 
            return $this->db->update('grs_staff',$ispublished,$staff_id); 
        }

function updateispublished($datauser,$number)
         {
            //echo $id; 
           return $this->db->update('grs_staff',$datauser,$number); 
         } 
 

// function update_staffdata($datauser,$number)
// {
//    //echo $id; 
//   return $this->db->update('grs_staff',$datauser,$number); 
// }
}
?>
