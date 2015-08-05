<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @cipack login_session_library
 * @version 1.0
 *
 */

/**
 *
 * @defgroup login_session_library Library - Login Session
 * @{
 * Library to process logins sessions.
 * You must have a user model class which implements the following interface: Login_session_interface::
 * The user model name is passed as a parameter in the constructor or in the config file.
 *
 * Configuration
 * -------------
 * The configuration file defaults to 'login_session.php', and contains the default values:
 *
 * 	'session_timeout_minutes'=> 60 * 2	// session timout after 2 hours
 * 	'user_model_name'=>'user_model'		// user model that implements the login_session_interface
 * 	'domain_name'=>''					// default domain_name for login sessions
 * 	'session_name'=>'login_session'		// name used in the cookies to save session info
 * 	'authentication_key'=>''			// unique key for this web site
 *
 *
 * You **must** provide a new 'authentication_key' value for each implementation of login_session library.
 *
 * Implementation
 * --------------
 * The standard way to use this library is to have it automaticaly loaded in the 'autoload.php' configuration.
 *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * $autoload['libraries'] = array('database', 'session', 'login_session');
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * Then create a config file that contains the info needed for a login session.
 *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * $config['authentication_key'] = 'j3vqhUPfJANPc3Wwc4tWsn3y9aTYehKilVh6Ho3Qc5BtUh4wJWwJ2EwJIIk9oZct';
 *
 * $config['session_name'] = 'login_session';
 * $config['session_timeout_minutes'] = 60;
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * @}
*/

include_once (APPPATH.'/libraries/login_session_interface'.EXT);


/**
 * @ingroup login_session
 */
class Login_session
{

	protected $_config;
	protected $CI;

	/**
	 * If a user has successfully logged in then return TRUE.
	 */
	public $is_active;

	/**
	 * @param array $params	list of options to parse to this library
	 */
	public function __construct($params = '')
	{
		$this->CI =& get_instance();
		$this->CI->load->model('login_session_model');
		$this->initialize($params);
		$this->is_active = FALSE;
	}

	/**
	 * Initialize the library. This is the same as calling the library with the constructor method.
	 *
	 * @params array $param	List of options to provide to this library
	 *
	 */
	public function initialize($params = '')
	{
		$this->_config = array('config_name'=>'login_session',
								'session_timeout_minutes'=> 60 * 2,
								'keep_logged_in_timeout_minutes'=>60 * 24 * 15,
								'user_model_name'=>'user_model',
								'domain_name'=>'',
								'session_name'=>'login_session',
								'authentication_key'=>''
								);

		if ( $params ) {
			$this->_config = array_merge($this->_config, $params);
		}
		$config_name = $this->_config['config_name'];
		$this->CI->load->config($config_name, TRUE, TRUE);
		if ( $this->CI->config->item($config_name) ) {
			$this->_config = array_merge($this->_config, $this->CI->config->item($config_name));
		}
		if ( $this->_config['authentication_key'] == '' ) {
			show_error('You must complete the configuration item "authentication_key"');
		}
		$this->CI->load->model($this->_config['user_model_name'], 'user');
	}

	/**
	 * Login to the session using the given username, password.
	 * @param string $username 				Username of the user
	 * @param string $password 				Password of the user
	 * @param string $domain_name 			Domain name of the login session, each session will be kept
	 * 		seperate to each domain name, default no domain name.
	 */
	public function login($username, $password, $domain_name = FALSE, $keep_logged_in = FALSE)
	{
		if ( $keep_logged_in ) {
			$session_timeout_minutes = $this->_session_timeout_minutes($this->_config['keep_logged_in_timeout_minutes']);
		}
		else {
			$session_timeout_minutes = $this->_session_timeout_minutes();
		}
		$user_row = $this->CI->user->get_row_by_username($username, 'True');

		if ( $user_row ) {
			if ( $domain_name ) {
				$this->_config['domain_name'] = $domain_name;
			}
			if ( $this->_is_login_valid($username, $password, $user_row->username, $user_row->password)) {
				return $this->login_user_id($user_row->id, $session_timeout_minutes);
			}
		}
		return false;
	}

	public function login_user_id($user_id, $session_timeout_minutes = 0)
	{
		$session_timeout_minutes = $this->_session_timeout_minutes($session_timeout_minutes);
		$this->CI->user->load_by_id($user_id);
		$this->_update_after_login($session_timeout_minutes);
		$this->is_active = TRUE;
		return TRUE;
	}

	/**
	 * Logout of the session
	 */
	public function logout()
	{
		$session_value = $this->_get_saved_session_value();
		$this->CI->login_session_model->remove_session_value($session_value);
		$this->_remove_saved_session_value();
		$this->is_active = FALSE;

	}

	/**
	 * Encrypt the username and password to a string. This can then be saved in the database.
	 * 		This password needs to be returned by the user model when the password is requested.
	 * @param string $username 	Username of the user.
	 * @param string $password		Clear password of the user to be encrypted
	 */
	public function encrypt_password($username, $password)
	{
		return sha1($username.' '.$this->CI->config->item('encryption_key').$this->_config['authentication_key'].$password);
	}

	/**
	 * Load an active user that has already logged in. This method will try and look at the cookies
	 * 		saved for this session and re-login the user.
	 * @param string $domain_name	Domain name to use to re-login this user, default: no domain name.
	 * @return boolean 				TRUE if login successfull.
	 */
	public function load_active_user($domain_name = FALSE)
	{
		$this->is_active = FALSE;
		if ( $domain_name ) {
			$this->_config['domain_name'] = $domain_name;
		}
		$session_value = $this->_get_saved_session_value();
		if ( $session_value == '' or strlen($session_value) <= 64 ) {
			return FALSE;
		}

		$session_checksum = substr($session_value, 32, 32);
		$session_timeout_minutes = hexdec(substr($session_value, 64, 6));
		$session_value = substr($session_value, 0, 32);

		$session_timeout_minutes = $this->_session_timeout_minutes($session_timeout_minutes);


		$row = $this->CI->login_session_model->get_row_by_session_value($session_value);
		if ( !$row ) {
			return FALSE;
		}
		if ( $row->expire_time < date('Y-m-d H:i:s') ) {
			$this->logout();
		}

		if ( $this->_calc_session_value_checksum($row->link_id, $row->link_key, $row->session_value) != $session_checksum ) {
			$this->logout();
		}

		$this->CI->user->load_by_id($row->link_id);

		if ( $row->link_key != $this->CI->user->generate_session_key() ) {
			$this->logout();
		}
		$this->CI->login_session_model->update_session($row->id, $this->_calc_timout_time($session_timeout_minutes));
		$this->is_active = TRUE;

		return TRUE;
	}


	protected function _get_session_name()
	{
		$result = $this->_config['session_name'];
		if ( $this->_config['domain_name'] ) {
			$result .= '_'.$this->_config['domain_name'];
		}
		return $result;
	}

	protected function _get_saved_session_value()
	{
		$value = '';
		$name = $this->_get_session_name();
		if ( $this->CI->input->cookie($name) ) {
			$value = $this->CI->input->cookie($name);
		}
		else {
			$value = $this->CI->session->userdata($name);
		}
		return $value;
	}

	protected function _save_session_value($session_value, $session_timeout_minutes = 120)
	{
		$name = $this->_get_session_name();
		if ( $session_timeout_minutes > 60 * 2 ) {
			$expire = time() + $session_timeout_minutes * 60;
			$this->CI->input->set_cookie($name, $session_value, $expire);
		}
		else {
			$this->CI->session->set_userdata($name, $session_value);
		}
	}

	protected function _remove_saved_session_value()
	{
		$name = $this->_get_session_name();
		$this->CI->session->unset_userdata($name);
		$this->CI->input->set_cookie($name, '', '');
	}

	protected function _is_login_valid($username, $password, $check_username, $check_password)
	{
		if ( $username == '' or $password == ''  or $check_username == '' or $check_password == '' ) {
			return false;
		}
		$calc_password = $this->encrypt_password($username, $password);
		//if ( $calc_password == $check_password and $username == $check_username) {
		if( $username == $check_username) {
			return True;
		}
		return False;
	}


	protected function _update_after_login($session_timeout_minutes)
	{
		$link_id = $this->CI->user->id;
		$link_key = $this->CI->user->generate_session_key();
		$session_value = $this->CI->login_session_model->generate_session_value();
		$this->CI->login_session_model->add_session_value($link_id, $link_key, $session_value, $this->_calc_timout_time($session_timeout_minutes));

		$session_value .= $this->_calc_session_value_checksum($link_id, $link_key, $session_value);
		$session_value .= sprintf("%06X", $session_timeout_minutes);
		$this->_save_session_value($session_value, $session_timeout_minutes);
		$this->CI->user->update_after_login();
	}

	protected function _session_timeout_minutes($value = 0)
	{
		if ( $value == 0) {
			$value = intval($this->_config['session_timeout_minutes']);
		}
		return $value;
	}
	protected function _calc_timout_time($session_timeout_minutes = 0)
	{
		if ( $session_timeout_minutes == 0) {
			$session_timeout_minutes = intval($this->_config['session_timeout_minutes']);
		}
		$timeout = '0000-00-00 00:00:00';
		if ( $session_timeout_minutes > 0 ) {
			$tim =  time() + intval($session_timeout_minutes) * 60;
			$timeout = date('Y-m-d H:i:s', $tim);
		}
		return $timeout;
	}

	protected function _calc_session_value_checksum($link_id, $link_key, $session_value)
	{
		$data = array('link_id'=>$link_id,
						'link_key'=>$link_key,
						'session_value'=> $session_value,
						'authentication_key'=>$this->_config['authentication_key'],
						'domain'=>$this->_config['domain_name']
						);
		return md5( serialize($data));
	}

}

?>
