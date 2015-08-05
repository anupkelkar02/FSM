<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Twilio_model extends MY_Model
{
    function get_row(){
     $str = 'select * from #__twilio';   
     $qry = $this->db->query($str);
      $res = $qry->result();
     return $res[0];
    }
     function updateTwilio($data,$name) {
        //echo $name;
        return $this->db->update('#__twilio', $data, $name);
        }
  function updateurl($data){
        return $this->db->update('#__twilio', $data); 
     }
function insert_call_log($data){
         return $this->db->insert('grs_call_log',$data);
     }
function updatecall_log($data,$name) {
        //echo $name;
          $str = 'update grs_call_log set reply_status='.$data.' where call_id="'.$name.'"';
          $this->db->query($str);
       // return $this->db->update('grs_call_log', $data, $name);
        }
}
?>
