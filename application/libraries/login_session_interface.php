<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @cipack login_session_library
 * @version 1.0
 */


/**
 * @ingroup login_session
 * The user model that supports login sessions must use this interface
 */

interface Login_session_interface
{
	/**
	 * Return the model row based on a given username.
	 * @param String $username 		Username to search for.
	 * @param String $is_published 	If set to 'True' or 'False' if only is_published users are searched, defaults
	 * 		to all users.
	 * @return Object user row containing the username, and password properties.
	 */
	public function get_row_by_username($username, $is_published = NULL);
	
	/**
	 * Called after a successfull login, the model object has already been loaded with
	 * the correct id of the logged in user.
	 */
	public function update_after_login();
	
	/**
	 * Generate a unique string based on the current loaded object. This is a security feature that 
	 * checks to see if the logged in user is the same as the one loaded from the database.
	 * @return String MD5 text of the unique values for this user.
	 */
	public function generate_session_key();
		
}

?>
