<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Uniforms_model extends MY_Model
{
	
	
	public function get_uniforms($grp_id)
		{
			
			$sql = "SELECT uni.* FROM #__uniforms AS uni where uni.group_id='".$grp_id."'";
			$query = $this->db->query($sql);
	//var_dump($query->result());
			return $query->result();
		}
		
		
		public function get_sizes()
		{
		
			$sql = "SELECT siz.*"
					." FROM #__sizes AS siz";
			$query = $this->db->query($sql);
	//var_dump($query->result());
			return $query->result();
		}
		
		
		public function get_staff($grp_id)
		{
		
			$sql = "SELECT stf.*"
					." FROM #__staff AS stf where stf.group_id='".$grp_id."'";
			$query = $this->db->query($sql);
	//var_dump($query->result());
			return $query->result();
		}
		
		
		
		public function get_uniforms_stock($unid,$s_id)
		{
		
			//$sql = "SELECT siz.*,sum(stk.qty) as totalstock from grs_sizes siz 
			//LEFT JOIN  grs_add_issue_return stk on siz.s_id=stk.s_id where stk.un_id='".$unid."'";
			
			
			$sql = "SELECT sum(stk.qty) as totalstock from grs_add_issue_return stk where stk.un_id='".$unid."' and stk.s_id='".$s_id."' and type='Add'";
			$query = $this->db->query($sql);
	//var_dump($query->result());
			return $query->result();
		}
		
		
		public function get_uniforms_issue($unid,$s_id)
		{
			
			$sql = "SELECT sum(stk.qty) as totalissued from grs_add_issue_return stk where stk.un_id='".$unid."' and stk.s_id='".$s_id."' and type='Issued'";
			$query = $this->db->query($sql);
	
			return $query->result();
		}
		
		
		public function get_uniforms_return($unid,$s_id)
		{
				
			$sql = "SELECT sum(stk.qty) as totalreturn from grs_add_issue_return stk where stk.un_id='".$unid."' and stk.s_id='".$s_id."' and type='Return'";
			$query = $this->db->query($sql);
	
			return $query->result();
		}
		
		
		 function add_record($table,$data)
		 {
		  $this->db->insert($table,$data);
		  return $this->db->insert_id();
		 }
		 
		  function update_record($table,$data,$id)
		 {
		 
		  return $this->db->update($table, $data,$id);
		 
		 } 
		 
		  
		 
		 public function get_uniforms_title($un_id)
		{
				
			$sql = "SELECT title from grs_uniforms where u_id='".$un_id."'";
			$query = $this->db->query($sql);
	
			return $query->result();
		}
		
		
		 public function get_uniforms_size($s_id)
		{
				
			$sql = "SELECT size from grs_sizes where s_id='".$s_id."'";
			$query = $this->db->query($sql);
	
			return $query->result();
		}
		
		
		public function get_uniforms_history($grp_id, $limit_start = -1, $limit_count = 0)
		{
			
			$sql = "SELECT uni.*,his.*,stf.* FROM grs_uniforms AS uni,grs_add_issue_return AS his 
		 LEFT JOIN  grs_staff AS stf on his.name=stf.id where uni.group_id='".$grp_id."' and his.un_id=uni.u_id ". ( ($limit_start >= 0 and $limit_count > 0) ? " LIMIT $limit_start , ".$limit_count : "" );
			$query = $this->db->query($sql);
	//var_dump($query->result());
			return $query->result();
		}
		
		
		public function get_row_count($grp_id)
		{
		
		 $sql = "SELECT uni.*,his.* FROM grs_uniforms AS uni,grs_add_issue_return AS his where uni.group_id='".$grp_id."' and his.un_id=uni.u_id"
				;
		$query = $this->db->query($sql);
		//$row = $query->row();
		$result= $query->result();
		$row = count($result);
		
		if ( $row ) {
			return $row;
		}
		return 0;
	}	
 

}
?>
