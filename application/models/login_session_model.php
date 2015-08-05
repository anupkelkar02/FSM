<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
 * @cipack login_session_library
 * @version 1.0
 * 
 */


/**
 * @ingroup login_session_library
 * This is the login session model, used to access the database for login session data.
 */

class Login_session_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Update row with values plus set the update time
	 * @param object|array $row 	row data to save
	 * @param int $id 				id of the record to save to, if FALSE then use the object id.
	 */
	public function update_row($row, $id = FALSE)
	{
		$row = $this->set_row_value($row, 'update_time',date('Y-m-d H:i:s')); 
		parent::update_row($row, $id);		
	}
	
	/**
	 * get a session row base on the session value.
	 * @param string $session_value Session value to find
	 * @return object row if found, else return FALSE if not found
	 */
	public function get_row_by_session_value($session_value)
	{
		$query = $this->db->get_where('login_session', array('session_value'=>$session_value));
		return $query->row();		
	}

	/**
	 * Generate a session value from the database. This method will loop around until it 
	 * has found a unique session value.
	 * 
	 * @return string Unique md5 session string
	 */
	public function generate_session_value()
	{
		do {
			$value = random_string('alnum', 32);
			$row = $this->get_row_by_session_value($value);
		} while ( $row );
		return $value;
	}
		
	/**
	 * Add a session value to the database.
	 * @param int $link_id			Link id of the users user model record
	 * @param string $link_key		User models unique key for this record
	 * @param string $session_value	Session value used for this login.
	 * @param int $timout			Number of seconds before this session will timout.
	 */
	public function add_session_value($link_id, $link_key, $session_value, $timeout)
	{
		if ( $link_id ) {
			$this->db->delete('login_session', array('link_id'=>$link_id));
			$data = array(
						'link_id'=>$link_id,
						'link_key'=>$link_key,
						'session_value'=>$session_value,
						'update_time'=>date('Y-m-d H:i:s'),
						'expire_time'=>$timeout
						);
			$this->db->insert('login_session', $data);
		}
	}
	
	/**
	 * Remove a session value from the database
	 * @param String $session_value 	Session value to remove.
	 */
	public function remove_session_value($session_value)
	{
		$this->db->delete('login_session', array('session_value'=>$session_value));		
	}
	
	/**
	 * Update the current record with a new timout. This is called after a user has logged in
	 * or is updating the session, so that the timeout occurs correctly.
	 * @param int $id		Id of the record to update
	 * @param int $timeout	Timeout value in seconds to timeout on.
	 */
	public function update_session($id, $timeout)
	{
		$this->db->update('login_session', array('expire_time'=>$timeout), array('id'=>$id));
	}
	
	/**
	 * Return the default sort order for listing records
	 * @return array List of sort order items
	 */
	public function get_sort_order()
	{
		$order =  'update_time = update_time'
				;
		return form_sort_order_as_array($order);
	}
	

}

?>
