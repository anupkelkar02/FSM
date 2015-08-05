<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



/*
 * @cipack database_manager_library
 * @version 1.0
 * 
 */


/**
 * @defgroup database_manager_library Library - Database Manager
 * @{
 * Library to provide database services and utilities that are not normally provided by the
 * framework.
 * 
 * @}
 * 
*/

include_once('database_manager_file'.EXT);



/**
 * @ingroup database_manager_library
 */
class Database_manager
{
	private $_config;
	private $_error;
	private $_commands = array('execute', 'set_version', 'set_password');
	
	/**
	 * No error
	 */
	const DATABASE_MANAGER_ERROR_NONE = 0;
	
	/**
	 * File Error occured
	 */
	const DATABASE_MANAGER_ERROR_NO_FILE = 1;
	
	/**
	 * SQL Error occured
	 */
	const DATABASE_MANAGER_ERROR_SQL = 2;
	
	/**
	 * 
	 * Library constructor
	 * 
	 * @param array $params	list of options to parse to this library
	 */	 
	public function __construct($params = array())
	{
		
		$this->CI =& get_instance();
		$this->_config = array();
		$this->_config = array_merge($this->_config, $params);
		$this->CI->load->config('database_manager', true);
		if ( $this->CI->config->item('database_manager') ) {
			$this->_config = array_merge($this->_config, $this->CI->config->item('database_manager'));
		}
		$this->_set_error();
	}
	
	/**
	 * Get the backup list of files in the backup folder.
	 * 
	 * @return array		List of database_manager_file objects
	 * 
	 */
	public function get_backup_file_list()
	{
		$folder = $this->_get_backup_folder();
		return $this->_get_file_list($folder);
	}
	
	/**
	 * Get the list of files that can be used to upgrade the database.
	 * 
	 * @return array		List of database_manager_file objects
	 * 
	 */	
	public function get_upgrade_file_list()
	{
		$folder = $this->_get_sql_folder();
		return $this->_get_file_list($folder);
	}

	/** 
	 * Get the current version of the connected database.
	 * 
	 * @return float		Version number of the database in the form 0.00.
	 * 
	 */
	public function get_current_version()
	{
		$result = '0.00';
		if ( $this->CI->db->table_exists('version') ) {
			$sql = "SELECT MAX(version) AS version "
					. " FROM #__version ";
			$query = $this->CI->db->query($sql);
			$row = $query->row();
			$result = sprintf("%0.02f", $row->version);
		}
		return $result;
	
	}
	
	/**
	 * 
	 * Used to sort the backup and upgrade files
	 * 
	 * @param string $fileA		Name of the File A.
	 * @param string $fileB		Name of the File B.
	 * 
	 * @return int				0, 1 or -1 depending on the sort orde.
	 * 
	 */
	public function sort_file($fileA, $fileB)
	{
		if ( $fileA->from < $fileB->from ) {
			return 1;
		}
		if ( $fileA->from > $fileB->from ) {
			return -1;
		}
		if ( $fileA->name > $fileB->name ) {
			return -1;
		}
		if ( $fileA->name < $fileB->name ) {
			return 1;
		}
		return 0;
	}
	
	/**
	 * Generate a backup file name based on the database name, date and version of the current 
	 * database.
	 * 
	 * @return string 		Formated filename that can be used as the backup file name.
	 * 
	 */
	public function generate_backup_filename()
	{
		$version = preg_replace('/\./', '-', $this->get_current_version());
		return "Backup_".$this->CI->db->database."_".date('Y-m-d_H-i-s')."_".$version.".sql";
	}
	
	/**
	 * Backup the database to a file.
	 * 
	 * @param string $file			Name of the file to write and compress the backup too.
	 * 
	 * @return string				If successfull then return the actual filename used,
	 * 								else return ''.
	 * 
	 */
    public function backup($file)
    {
        $result = '';
        $folder = $this->_get_backup_folder();

        //$this->CI->load->dbutil();
		//$backup_sql =& $this->CI->dbutil->backup();

		$this->CI->load->helper('file');
		$backup_filename = $folder.'/'.$file.'.gz';
		$cmd = 'mysqldump --user='.quotemeta($this->CI->db->username).' --password='.quotemeta($this->CI->db->password).' '.$this->CI->db->database.' > '.$backup_filename;
		$output = array();
		exec($cmd, $output);
		//write_file($backup_filename, $backup_sql);
		if ( file_exists($backup_filename) ) {
            @chmod($backup_filename, 0666);
		}
		else { 
			$backup_filename = '';
		}
        return $backup_filename;
    }
	
	/**
	 * 
	 * Remove the backup from the folder.
	 * 
	 * @param sting $name 		Name of the file to remove.
	 * 
	 * @return boolean			If the removal was successfull.
	 * 
	 */
	public function remove_backup_file($name)
	{
        $folder = $this->_get_backup_folder();
		
		return @unlink($folder.'/'.$name);
	}
	
	/**
	 * Restore a database from a file to the current database. If the file has the extension .gz
	 * then automatically uncompress the file before restoring the database.
	 * 
	 * @param string $file 			File name to restore the databsae too.
	 * 
	 * @return string			Return any errors that may have occured during the restore process.
	 * 
	 */
    public function restore_database($file)
    {
        $result = FALSE;
        $folder = $this->_get_backup_folder();
        $restore_filename = $folder.'/'.$file;
        if ( file_exists($restore_filename) ) {
        	if ( preg_match('/\.gz$/i', $file) ) {
        		$tempName = tempnam($folder, 'restore_');
        		$this->_uncompress_file($restore_filename, $tempName);
            	$result = $this->execute( $tempName );
            	unlink($tempName);				
        	}
        	else {
            	$result = $this->execute($restore_filename);
            }
        }
        else {
			$this->_set_error(Database_manager::DATABASE_MANAGER_ERROR_NO_FILE, "File: '$file' does not exists");
        }
        return $result;
		
    }

	
	/**
	 * 
	 * Execute a file with the special sql scripting. This method will execute normal MySQL scripts
	 * but also process special commands.
	 * 
	 * @param string $file		Name of the file to execute.
	 * 
	 * @return boolean			Return TRUE if all ok, else return FALSE. You need to access the 
	 * 							method get_error() to find out why this execute failed.
	 * 
	 */
    public function execute($file)
    {
		$result = TRUE;
		$filename = '';
		if ( is_object($file) ) {
        	$filename = $file->get_filename();
        }
        else {
        	$filename = $file;
		}

		if ( !file_exists($filename) and $filename ) {
			$this->_set_error(Database_manager::DATABASE_MANAGER_ERROR_NO_FILE, "File '".basename($filename)."' not found");
			return FALSE;
		}
        $fp = fopen($filename, 'r');
        $sql = '';
        while ( !feof( $fp) ) {
            $line = fgets($fp);
            if (  preg_match('/^#.*?$/ms', $line) or preg_match('/^\/\*.*?\*\//ms', $line) ) {
                $line = '';
            }
            $sql .= $line;
            if ( preg_match('/;\s*$/ms', $sql) ) {
                if ( !$this->_execute_command_line($sql, $file) ) {
                    if ( !$this->CI->db->simple_query($sql) ) {
                        $message = "Sql error: <code>".$this->CI->db->_error_message()."</br>$sql</code>";
						$this->_set_error(Database_manager::DATABASE_MANAGER_ERROR_SQL, $message);
						$result = FALSE;
                        break;
                    }
                }
                unset($sql);
				$sql = '';
            }
        }
        fclose($fp);
        return $result;
    }
		
	/**
	 * Return the last known error that had occured.
	 * 
	 * @return string 	Return the last MySQL error.
	 * 
	 */
	public function get_error()
	{
		return $this->_error;
	}
	

    private function _execute_command_line(&$sql, $file)
    {
        $result = false;
        foreach ( $this->_commands as $command) {
			if ( preg_match('/'.preg_quote($command, '/').'\s+(.+)/i', $sql, $matches ) ) {
				$param_str = preg_replace('/;$/', '', $matches[1]);
				$command_method = "_".$command."_command";
				$this->$command_method($file, $param_str);
				$result = true;
				break;
			}
		}		
        return $result;
    }
	

	private function _read_param_str(&$i, $str)
	{
		$result = '';
		$quote = $str[$i];
		for ( $i++ ; $i < strlen($str); $i ++ ) {
			if ( $str[$i] == $quote ) {
				$i ++;
				return $result;
			}
			$result .= $str[$i];
		}
		return $result;
	}
	
	private function _read_params($str)
	{
		$params = array();
		$param = '';
		for ( $i = 0; $i < strlen($str); $i ++ ) {
			if ( $str[$i] == '"' or $str[$i] == "'" ) {
				$param = $this->_read_param_str($i, $str);
			}
			if ( $i < strlen($str) ) {
				if ( $str[$i] == ',' ) {
					$params[] = $param;
					$param = '';
				}
			}
		}
		$params[] = $param;
		return $params;
	}

    private function _process_auto_inc_command($file, $param_str)
    {
        if ( preg_match("/create table/i", $sql ) ) {
            $this->_auto_inc = 1;
        }
        if ( preg_match("/^\s?insert/i", $sql) ) {
            if ( preg_match('/\$AUTO_INC/', $sql) ) {
                $sql = preg_replace('/\$AUTO_INC/', $this->_auto_inc, $sql);
                $this->_auto_inc ++;
            }
        }
        
        return $sql;
    }
    private function _execute_command($file, $param_str)
    {
		$filename = $this->_sql_folder.'/'.preg_replace('/\.sql/', EXT, $file->name);
		if ( file_exists($filename) ) {
        	include_once($filename);
        	if ( function_exists($param_str) ) {
        		call_user_func($param_str);
        	}
        	else {
				show_error("Cannot find php function '$param_str'");
			}
        }
        else {
			show_error("Cannot find supporting php file '".basename($filename)."'");
		}
    }
    private function _set_version_command($file, $param_str)
    {
		$number = floatval($param_str);
        $this->CI->db->query("UPDATE #__version SET version=$number, update_datetime=NOW()");
    }
    
	private function _set_password_command($file, $param_str)
	{
		$params = $this->_read_params($param_str);
		if ( count($params) < 2 ) {
			show_error("SET_PASSWORD must have two parameters");
			return;
		}
		if ( $this->user->db_get_username($params[0]) ) {
			$this->user->set_password($params[1]);
			$this->user->db_update();
		}
		else {
			show_error('Cannot find user '.$params[0]);
		}
	}

	
	protected function _set_error($code = Database_manager::DATABASE_MANAGER_ERROR_NONE, $message = '')
	{
		$this->_error = new StdClass();
		$this->_error->code = $code;
		$this->_error->message = $message;
	}
	protected function _get_file_list($folder)
	{
		$result = array();
		$this->CI->load->helper('directory');
		$files = directory_map($folder);
		$this->_files = array();
		if ( $files ) {
			foreach ( $files as $filename) {
				if ( preg_match('/\.sql|(\.gz)$/i', $filename) ) {
					$file = new Database_manager_file(md5($filename), $filename, $folder);
					if ( $file->is_build() or $file->is_backup() ) {
						$file->to = $file->build_version();
					}
					$result[] = $file;
				}
			}
			if ( count($result) > 1 ) {
				usort($result, array($this, 'sort_file'));
			}
		}
		return $result;
	}
	
	protected function _get_backup_folder()
	{
		if ( ! $this->_config['backup_folder']  ) {
			show_error('backup folder is not defined');
			return false;
		}
		if ( ! file_exists($this->_config['backup_folder']) ) {
			show_error('backup folder "'.$this->_config['backup_folder'].'" does not exist');
			return false;
		}
		return $this->_config['backup_folder'];
	}

	protected function _get_sql_folder()
	{
		if ( ! $this->_config['sql_folder']  ) {
			show_error('sql folder is not defined');
			return false;
		}
		if ( ! file_exists($this->_config['sql_folder']) ) {
			show_error('SQL folder "'.$this->_config['sql_folder'].'" does not exist');
			return false;
		}
		return $this->_config['sql_folder'];
	}

    protected function _uncompress_file($filename, $out_filename)
    {
        $zp = gzopen($filename, 'rb');
        $fp = fopen($out_filename, 'w');
        do {
           	$buffer = gzread($zp, 10240);
           	if ( $buffer != '' ) {
                fwrite($fp , $buffer);
           	}
        } while ( $buffer != '' );
        gzClose($zp);
        fclose($fp);
        @chmod($out_filename, 0666);
    }    

    protected function _extract_sql_from_file($path, $filename)
    {
        $result = array();
        $text = '';
        $lines = implode("\n", file($path."/".$filename));
        $lines = preg_replace('/^#.*?$/ms', '', $lines);
        $lines = preg_replace('/^\/\*.*?\*\//ms', '', $lines);
        $lines = preg_replace('/^\s*/ms', '', $lines);
        $result = preg_split('/;\s*$/ms', $lines);
        return $result;
    }


}



?>
