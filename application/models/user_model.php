<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once (APPPATH.'/libraries/login_session_interface'.EXT);

class User_model extends MY_Model implements Login_session_interface
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function update_row($row, $id = false)
	{
		$row = $this->set_row_value($row, 'update_time',date('Y-m-d H:i:s')); 
		parent::update_row($row, $id);		
	}

	// this is needed by login_session
	public function get_row_by_username($username, $is_published = NULL)
	{
		$filter = array('username'=>$username);
		if ( $is_published != NULL ) {
			$filter['is_published'] = $is_published;
		}
		$query = $this->db->get_where('user', $filter );
		
		
		$udata=$query->row();
		
		$sess_array = array('group_id' =>$udata->group_id);
		$this->session->set_userdata('logged_in', $sess_array);
		
		
		return $query->row();
	}
	
	// called by the login session after login
	public function update_after_login()
	{
		$row = $this->get_row();
		$row->last_login = date('Y-m-d H:i:s');
		$this->update_row($row);
	}
	
	// called by login session to make sure that this 
	// record is valid
	
	public function generate_session_key()
	{
		$data = array('id'=>$this->id,
						'group_id'=>$this->group_id,
						'username'=>$this->username,
						'password'=>$this->password
					);
					
		return md5(implode(': - :', $data));					
	}
	
	
	
	public function get_row_by_email($email, $is_published = NULL)
	{
		$filter = array('email'=>$email);
		if ( $is_published != NULL ) {
			$filter['is_published'] = $is_published;
		}
		$query = $this->db->get_where('user', $filter);
		return $query->row();
	}
	
	public function get_row_by_activate_ref($activate_id)
	{
		$query = $this->db->get_where('user', array('activate_ref'=>$activate_id));
		return $query->row();
	}

	public function get_row_by_owner($owner_type, $owner_id)
	{
		$query = $this->db->get_where('user', array('owner_type'=>$owner_type, 'owner_id'=>$owner_id));
		return $query->row();
	}
	public function get_rows_by_owner($owner_type, $owner_id)
	{
		$query = $this->db->get_where('user', array('owner_type'=>$owner_type, 'owner_id'=>$owner_id));
		return $query->result();
	}
	public function get_row_by_facebook_access($fb_user_id, $fb_access_token = '')
	{
		$filter = array('fb_user_id'=>$fb_user_id, 'is_published'=>'True');
		if ( $fb_access_token ) {
			$filter['fb_access_token']=$fb_access_token;
		}
		$query = $this->db->get_where('user', $filter);
		return $query->row();
	}

	
	public function get_rows($filter, $sort_order = '', $limit_start = -1, $limit_count = 0)
	{
		$where_sql = $this->get_where_sql($filter, 'u');

		$sql = "SELECT u.*"
				. ", g.title AS group_title, g.name AS group_name"
				." FROM #__user AS u"
				." LEFT JOIN #__user_group AS g ON u.group_id = g.id"
				. ( $where_sql ? " WHERE $where_sql " : '' )
				. ( $sort_order ? " ORDER BY ".form_sort_order_as_sql($sort_order) : '' )
				. ( ($limit_start >= 0 and $limit_count > 0) ? " LIMIT $limit_start , ".$limit_count : "" )
				;
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	
	public function get_sort_order()
	{
		$order =  'name = name ASC, '
				. 'username = username ASC,'
				. 'email = email ASC,'
				. 'group_title = g.title,'
				. 'update_time = update_time DESC, '
				. 'last_login = last_login DESC'
				;

		return form_sort_order_as_array($order);
	}
	

	


	public function get_group_id_by_name($name)
	{
		$sql = "SELECT id FROM #__user_group WHERE name = '".strtolower($name)."'";
		$query = $this->db->query($sql);
		$row = $query->row();
		if ( $row ) {
			return $row->id;
		}
		return 0;
	}


	public function get_dropdown_list($default_title = '')
	{
		$sql = "SELECT id, CONCAT(first_name, ' ',last_name, ': ', email) AS title "
				." FROM #__user "
				." ORDER BY UCASE(first_name), UCASE(last_name)"
				;
		$query = $this->db->query($sql);
		return parent::_generate_dropdown_list($query->result(), $default_title);
	}
	
	public function get_group_dropdown_list($default_title = '')
	{
		$sql = "SELECT id, title "
				." FROM #__user_group "
				." ORDER BY title"
				;
		$query = $this->db->query($sql);
		return parent::_generate_dropdown_list($query->result(), $default_title);
	}
	
	public function get_owner_type_dropdown_list($default_title = '')
	{
		$result = array('None' => 'None',
						 'Participant' => 'Participant', 
						 'Worksite' => 'Worksite', 
						 'Company' => 'Company'
						);
		return parent::_generate_dropdown_list($result, $default_title);
	}


	public function is_username_used($username)
	{
		$this->load->config('users');
		if ( in_array(strtolower($username) , $this->config->item('user_reserve_username')) ) {
			return true;
		}

		$sql = "SELECT COUNT(id) as num "
				. " FROM #__user"
				. " WHERE username = '$username'"
				;
		$query= $this->db->query($sql);
		$row = $query->row();
		return ($row->num > 0);
	}
	
	public function is_group($name)
	{
		return $this->get_group_name() == $name;
	}
	
	
	public function get_group_name($group_id = 0)
	{
		if ( $group_id == 0) {
			$group_id = $this->group_id;	
		}
		$group_name = '';
		$sql = "SELECT name FROM #__user_group WHERE id = ?";
		$query = $this->db->query($sql, $group_id);
		$row = $query->row();
		if ( $row ) {
			$group_name = strtolower($row->name);
		}
		return $group_name;		
	}
	
	public function validate_username($username)
	{
		if ( $username == '' ) {
			return 'Please enter in a username';
		}
		if (  preg_match('/[^a-z0-9_\-]/i', $username) ) {
			return 'Your username must only contain A to Z, 0 to 9 and -_ characters';
		}
		if ( strlen($username) < 4 ) {
			return 'Please enter in an username that has more than 3 characters';
		}
		if ( $this->is_username_used($username) ) {
			return 'The username "'.$username.'" is already in use, please enter a different username.';
		}
		return '';
		
	}
	
	public function validate_password($password, $password_again)
	{
		if ( trim($password) == '' ) {
			return 'Please enter in a password';
		}
		if ( $password != $password_again)  {
			return 'Both passwords do not match';
		}
		if ( strlen($password) < 5 ) {
			return 'Please enter in a password that has more than 4 characters';
		}
		
		return '';
	}
	

	public function reactivate($activate_ref, $id = FALSE)
	{
		if ( $id == FALSE ) {
			$id = $this->id;
		}
		$data = array('activate_ref'=>$activate_ref);
		$this->update_row($data, $id); 
	}
	
	public function activate($password, $id = FALSE)
	{
		if ( $id == FALSE ) {
			$id = $this->id;
		}
		$data = array('activate_ref'=>'',
						'password'=>$password
					);
		$this->update_row($data, $id); 
	}

	public function set_facebook_user_info($fb_user_id, $fb_access_token, $id = 0)
	{
		if ( $id == 0 ) {
			$id = $this->id;
		}
		$this->db->update('user', array('fb_user_id'=>'', 'fb_access_token'=>''), array('fb_user_id'=>$fb_user_id));
		$data = array('fb_access_token'=>$fb_access_token,
						'fb_user_id'=>$fb_user_id);
		$this->update_row($data, $id);
		
	}
	

	protected function _add_filter_item_to_where_expression($name, $value)
	{
		if ( $name == 'username_match' ) {
			if ( $value == '' ) {
				return;
			}
			return $this->_add_where_expression("u.username LIKE '%$value%'");
		}
		if ( $name == 'name_match' ) {
			if ( $value == '' ) {
				return;
			}
			return $this->_add_where_expression("u.name LIKE '%$value%'");
		}
		if ( $name == 'group_id' and intval($value) == 0) {
			return;
		}
		if ( $name == 'owner_type_in' ) {
			if ( $value == '' ) {
				return;
			}
			return $this->_add_where_expression("u.owner_type IN ('".implode("','", $value)."')");
		}
		parent::_add_filter_item_to_where_expression($name, $value);
	}

}

?>
