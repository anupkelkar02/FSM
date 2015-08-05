<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
 * @cipack database_manager_library
 * @version 1.0
 * 
 */


/**
 * @ingroup database_manager_library
 */
class Database_manager_file
{
    var $from, $to, $name, $id, $_sql_folder;
    
    /**
     * 
     * Construct a new database manager file object.
     * 
     * @param int $id				Local id of the file, this has to be unique within the list.
     * @param string $name			Name of the file.
     * @param string $sql_folder	Folder where the file can be found.
     * 
     */
    public function Database_manager_file($id, $name, $sql_folder)
    {
        $this->name = $name;
		$this->id = $id;
		$this->_sql_folder = $sql_folder;
        if ( preg_match('/^Backup/i', $name) ) {
            $this->from = 0;
            $this->to = 0;
            $item = $this->_decode_backup_name($name);
            $this->datetime = $item->datetime;
            $this->database_name = $item->database_name;
            $this->version = $item->version;
        }
        else {
        }
    }

	/**
	 * 
	 * Check to see if this sql file is a 'build' file. A 'build' file does not contain
	 * any upgrade statements, it is assumed that this file can build the database
	 * from scratch.
	 * 
	 * @return boolean		True this file is a 'build' file.
	 * 
	 */
    public function is_build()
    {
        return ($this->from == 0);
    }
	
	/**
	 * Check to see if this file is a backup file. 
	 * 
	 */
	public function is_backup()
	{
		return $this->database_name != '';
	}
    
    /**
     * Full path and name of the file.
     */
    public function get_filename()
    {
		return $this->_sql_folder.'/'.$this->name;
	}
	
	/**
	 * Size of the file.
	 */
	public function get_size_text()
	{
		$size = filesize($this->get_filename());
		$mod = 1024;

		$units = explode(' ','B KB MB GB TB PB');
		for ($i = 0; $size > $mod; $i++) {
			$size /= $mod;
		}
		return round($size, 2) . ' ' . $units[$i];		
	}

	/**
	 * Build version of this sql file. The system will try to search the sql for the relivant
	 * statement to determin the version numeber.
	 */
    public function build_version()
    {
        $result = '';
        $data = '';
        $fp = fopen($this->_sql_folder."/".$this->name, 'r');
        if ( $fp ) {
            $data = fread($fp, filesize($this->_sql_folder."/".$this->name));
            fclose($fp);
        }
        if ( preg_match('/^INSERT INTO \w+_version VALUES \( 1, ([0-9\.]+),/im', $data, $matches) ) {
            $result = $matches[1];
        }
        return $result;
    }

	/**
	 * Text to show to the user, depending on the type of sql file. If it's an upgrade then show
	 * the version numbers.
	 */
    public function text()
    {
        $result = '';
        if ( $this->is_build()  ) {
            $result = $this->name.' to version '.$this->build_version();
        }
        else {
            $result = "Upgrade from version ".$this->from." to ".$this->to;
        }
        return $result;
    }
    protected function _convert_to_number($text)
    {
		if ( preg_match('/(\d+).(\d+)/', $text, $match) ) {
			$value = $match[1].".".sprintf("%02d", intval($match[2]));
			return floatval($value);
		}
		else {
			return floatval($text);
		}
		return 0;
	}
	
	protected function _decode_backup_name($name)
	{
		$result = new StdClass();
		$result->database_name = '';
		$result->datetime = 0;
		$result->version =  '';
		
		$months = array('Jan'=>1, 'Feb'=>2, 'Mar'=>3, 'Apr'=>4, 'May'=>5, 'Jun'=>6, 'Jul'=>7, 'Aug'=>8, 'Sep'=>9, 'Oct'=>10, 'Nov'=>11, 'Dec'=>12);
		if ( preg_match('/Backup_(\w+)_(\d{4})-(\w+)-(\d+)_(\d{2})-(\d{2})-(\d{2})_(\d+).(\d+)/', $name, $match) ) {
			$result->database_name = $match[1];
			$day = intval($match[2]);
//			$month = intval($months[$match[3]]);
			$month = intval($match[3]);
			$year = intval($match[4]);
			$hour = intval($match[5]);
			$min = intval($match[6]);
			$sec = intval($match[7]);
			
			$result->datetime = date('Y-m-d H:i:s', mktime($hour, $min, $sec, $month, $day, $year));
			$result->version = sprintf("%0.2f", floatval($match[8].".".$match[9]));
		}
		return $result;
	}

	protected function _decode_upgrade_name($name)
	{
		$result = new StdClass();
		$result->from = 0;
		$result->to = 0;
		if ( preg_match('/Upgrade_(\d+\.?\d+)_to_(\d+\.?\d+)\.sql/i', $name, $matches) ) {
			$result->from = sprintf('%0.2f', $this->_convert_to_number($matches[1]));
			$result->to = sprintf('%0.2f',  $this->_convert_to_number($matches[2]));
		}
		return $result;
	}
	
	
}
