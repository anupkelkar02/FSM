<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Common_Model extends MY_Model
{
	public function getStaffID($frommob,$msg){
            <?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Common_Model extends MY_Model
{
	public function getStaffID($frommob,$msg){
            file_put_contents("rcvsms1.txt",$msg);
            $query=  $this->db->query('select grs_staff.id as id from grs_staff inner join grs_schedule gs on staff_id= gs.id where phone_number="'.$frommob.'" and reply_status_id=0');
            $res= $query->result();
            
            if(($res[0]->id)>0){
              $query_update=  $this->query('update grs_schedule set reply_status_id="'.$msg.'" where staff_id="'.$res[0]->id.'"');    
              $query_update->result();
            }

        }
	
}

?>
            $query=  $this->db->query('select grs_staff.id as id from grs_staff inner join grs_schedule gs on staff_id= gs.id where phone_number="'.$frommob.'" and reply_status_id=0');
            $res= $query->result();
            
            if(($res[0]->id)>0){
              $query_update=  $this->query('update grs_schedule set reply_status_id="'.$msg.'" where staff_id="'.$res[0]->id.'"');    
              $query_update->result();
            }

        }
	
}

?>