<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Site_model extends MY_Model
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
	
	
	
	
  public function getSiteList(){
            $sql = "SELECT ste.*"
				." FROM #__site AS ste";
				
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

        public function insertSite($data){
        $this->db->insert('grs_site', $data);
        return $this->db->insert_id();
            
        }
	
 public function insertsiteshift($data){
            $this->db->insert('grs_site_shift', $data);
            return $this->db->insert_id();
        }

public function deletesiteshift($siteid){
         $this->db->where('site_id', $siteid);
        return $this->db->delete('grs_site_shift');

}
	

        function updateSite($data,$name) {
        //echo $name;
        return $this->db->update('grs_site', $data, $name);
        }

         function matchsitecode($name){
         //echo 'select count(id) as cnt from grs_site where code="'.$name.'"'; exit;
         $query = $this->db->query('select count(id) as cnt from grs_site where code="'.$name.'"');
        $res = $query->result();
        return $res[0]->cnt;
    }

   function getSiteId($sitecode){
            //echo 'select id from grs_site where code="'.$sitecode.'"';
            $query=  $this->db->query('select id from grs_site where code="'.$sitecode.'"');
            $res=$query->result();
            return $res[0]->id;
        }
        
        function getSiteName($siteid){
            $query=  $this->db->query('select code from grs_site where code="'.$siteid.'"');
            $res=$query->result();
            return $res[0]->code;
        }	
        function add_sitedata($datauser)
 {
  $this->db->insert('grs_site',$datauser);
  return $this->db->insert_id();
 } 
 function update_sitedata($datauser,$code)
 {
    //echo $id; 
   return $this->db->update('grs_site',$datauser,$code); 
 } 
 function checkcode($code)
 {
     $query=$this->db->query('select id from grs_site where code="'.$code.'"');
     $res=$query->result();
     return $res;
     
 }
function updateSiteWorkingDays($data, $id) {
        //echo $name;
        return $this->db->update('grs_working_days', $data, $id);
    }

    public function get_row_workingday_count($filter, $sort_order = '') {
        $where_sql = $this->get_where_sql($filter, 'ste');

        $sql = "SELECT COUNT(ste.id) AS count"
        . " FROM #__working_days AS ste"
        . ( $where_sql ? " WHERE $where_sql " : '' )
        . ( $sort_order ? " ORDER BY " . form_sort_order_as_sql($sort_order) : '' )
        ;
        $query = $this->db->query($sql);
        $row = $query->row();
        if ($row) {
            return $row->count;
        }
        return 0;
    }

    function insertSiteWorkingDays($data) {
        //echo $name;
        $qry = $this->db->insert('grs_working_days', $data);
        return $this->db->insert_id();
    }

    public function get_workingdays_rows($filter, $sort_order = '') {
        $where_sql = $this->get_where_sql($filter, 'ste');

        $sql = "SELECT ste.*"
                . " FROM #__working_days AS ste"
                . ( $where_sql ? " WHERE $where_sql " : '' )
                . ( $sort_order ? " ORDER BY " . form_sort_order_as_sql($sort_order) : '' )
        ;
        $query = $this->db->query($sql);
        return $query->result();
    }

}
?>
