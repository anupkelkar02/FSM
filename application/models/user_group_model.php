<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// require_once(APPPATH.'core/MY_Model'.EXT);

class User_group_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	

	public function get_dropdown_list($default_title = '')
	{
		$sql = "SELECT id, title "
				." FROM #__user_group "
				." ORDER BY title"
				;
		$query = $this->db->query($sql);
		return parent::_generate_dropdown_list($query->result(), $default_title);
	}
	




}

?>
