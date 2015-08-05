<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Attendance_model extends MY_Model
{
    function get_row(){
     $str = 'select * from #__attendance';   
     $qry = $this->db->query($str);
      $res = $qry->result();
     return $res[0];
    }
     function update($data,$name) {
       
        return $this->db->update('#__attendance', $data, $name);
        }
  
    function insert($data){
         return $this->db->insert('#__attendance',$data);
     }

}
?>
