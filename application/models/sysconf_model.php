<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Sysconf_model extends MY_Model
{
    function get_row(){
     $str = 'select * from #__sysconf';   
     $qry = $this->db->query($str);
      $res = $qry->result();
     return $res[0];
    }
     function updateSysConf($data,$name) {
        //echo $name;
        return $this->db->update('#__sysconf', $data, $name);
        }
  function get_timezones(){
      $str = 'select * from #__timezones';   
     $qry = $this->db->query($str);
      $res = $qry->result();
      return $res;
  }
  function get_timezone_name($id){
      $str = 'select timezone from #__timezones where id='.$id;   
      $qry = $this->db->query($str);
      $res = $qry->result();
      return $res[0]->timezone;
  }
function insert_bday($data){
    $this->db->insert('grs_bday_template',$data);
}
function get_bday(){
     $str = 'select bday_template from #__bday_template order by id limit 0,1';   
      $qry = $this->db->query($str);
      $res = $qry->result();
      if(!empty($res)){
        return $res[0]->bday_template;
      }else{
        return false;
      }
}
function update_bday($data){
    $this->db->update('grs_bday_template',$data,array('id'=>'1'));
}
}
?>
