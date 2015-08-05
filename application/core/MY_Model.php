<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class MY_model extends CI_Model
{
	private $_row_names = FALSE;
	protected $_table_name = '';
	private $_key_name = false;
	private $_ignore_row_names = array();
	private $_where_sql = array();
	private $_default_table_prefix = '';

	public function __construct($table_name = '', $key_name = 'id')
	{
		parent::__construct();
		$this->_row_names = false;
		if ( $table_name ) {
			$this->_table_name = $table_name;
		}
		else {
			$this->_table_name = $this->_calcTableName();
		}
		$this->_key_name = $key_name;

	}
		
	public function is_loaded()
	{
		return $this->_row_names != FALSE;
	}
	public function get_row_by_id($id)
	{
		$query = $this->db->get_where($this->_table_name, array('id'=>$id));
		return $query->row();
	}
	
	public function get_row($prefix = '')
	{
		$row = new stdclass;
		if ( $this->_row_names) {
			foreach ( $this->_row_names as $name ) {
				$destination_name = ($prefix.$name);
				$row->$destination_name = $this->$name;
			}
		}
		return $row;
	}
	public function clear_as_new()
	{
		
		//$sql = "SHOW COLUMNS FROM #__".$this->_table_name;
		$sql = "SELECT * FROM information_schema.columns WHERE table_schema='public' AND table_name = '#__".$this->_table_name."'";
		$query = $this->db->query($sql);
		$this->_row_names = array();
		foreach ( $query->result() as $row ) {
			$name = $row->column_name;
			$this->_row_names[] = $name;
			$this->$name = '';
		}
		
	}
	public function load_row($row)
	{
		$this->clear();
		$obj = get_object_vars($row);
		$this->_row_names = array_keys($obj);
		foreach ( $obj as $name=>$value ) {
			$this->$name = $value;
		}
	}
	
	public function load_by_id($id)
	{
		$row = $this->get_row_by_id($id);
		if ( $row ) {
			$this->load_row($row);
		}
		return $row;
	}
	
	public function update_row($row, $id = FALSE)
	{
		if ( is_array($row) ) {
			$obj_row = new StdClass();
			foreach ( $row as $name=>$value) {
				$obj_row->$name = $value;
			}
			$row = $obj_row;
		}
		if ( $id == false ) {
			$id = $row->id;
		}
		if ( $id) {
			$update_row = new stdclass();
			foreach ( get_object_vars($row) as $name=>$value ) {
				// only take non id field & ignore any exra fields
				if ( $name == $this->_key_name or in_array($name, $this->_ignore_row_names) ) {
					continue;
				}
				$update_row->$name = $value;
			}
			$result = $this->db->update($this->_table_name, $update_row, array($this->_key_name=>$id));
			return $result;
		}
		return FALSE;
	}

	public function set_ignore_fields($field_names)
	{
		$this->_ignore_row_names = $field_names;
	}

	public function set_row_value($row, $name, $value )
	{
		if ( is_array($row) ) {
			$row[$name] = $value;
		}
		if ( is_object($row) ) {
			$row->$name = $value;
		}
		return $row;
	}
	public function add_row($row)
	{
		$this->db->insert($this->_table_name, $row);	
		return $this->db->insert_id();
	}

	
	public function delete_row($row)
	{
		return $this->db->delete($this->_table_name, array($this->_key_name=>$row->$this->_key_name));
	}
	
	public function delete_id($id)
	{
		return $this->db->delete($this->_table_name, array($this->_key_name=>$id));
	}
	
	public function delete_where($where)
	{
		return $this->db->delete($this->_table_name, $where);		
	}
	
	public function clear()
	{
		if ( $this->_row_names ) {
			foreach ( $this->_row_names as $name ) {
				unset($this->$name);
			}
			$this->_row_names = false;
		}
	}
	public function get_table_name()
	{
		return $this->_table_name;
	}
	public function set_table_name($name)
	{
		$this->_table_name = $name;
	}
		
	
	
	public function toggle_is_published($id)
	{
		$sql = "SELECT is_published "
				." FROM #__".$this->_table_name
				. " WHERE id = $id"
				;
		$query= $this->db->query($sql);
		$row = $query->row();
		if ( $row->is_published == 'True') {
			$value = 'False';
		}
		else {
			$value = 'True';
		}
		$sql = "UPDATE #__".$this->_table_name
				. " SET is_published = '$value'"
				. " WHERE id = $id"
				;
		return $this->db->query($sql);
	}	
	
	private function _calcTableName()
	{
		$table_name = '';
		$name = get_class($this);
		if ( preg_match('/^(\w+)_/', $name, $match) ) {
			$table_name = strtolower($match[1]);
		}
		if ($table_name == '' ) {
			show_error("Cannot generate the table name for model $name");
		}
		return $table_name;
	}
	
	protected function _generate_dropdown_list($rows, $default_title = '')
	{
		$result = array();
		if ( $default_title ) {
			array_unshift($result, $default_title);
		}
		foreach ( $rows as $id=>$row ) {
			if ( is_object($row) ) {
				$result[$row->id] = $row->title;
			}
			else {
				$result[$id] = $row;
			}
		}
		return $result;
		
	}
	
	protected function _generate_enum_dropdown_list($table_name, $field_name, $default_title = '')
	{
		$sql = "SHOW COLUMNS FROM $table_name where field=?";
		$query = $this->db->query($sql, $field_name);
		$row = $query->row();
		$items = array();
		if ( $default_title ) {
			$items[] = $default_title;
		}
		if ( preg_match('/enum\((.*?)\)/i', $row->Type, $match) ) {
			$values = explode(",", $match[1]);
			foreach ( $values as $value ) {
				$value = trim($value, "'");
				$items[$value] = $value;
			}
		}
		return $items;
	}
	
	public function get_where_sql($filter, $default_table_prefix = '')
	{
		$this->_default_table_prefix = $default_table_prefix;
		$this->_build_where_expression($filter);
		return implode(' AND ', $this->_where_sql);
	}
	
	protected function _build_where_expression($filter)
	{
		$this->_where_sql = array();
		if ( $filter == '' ) {
			return '';
		}
		$values = FALSE;
		if ( is_object($filter) ) {
			$values = array();
			foreach ( get_object_vars($filter) as $name=>$value ) {
				$values[$name] = $value;
			}
		} 
		else if ( is_array($filter) ) {
			$values = $filter;
		}
		else { 
			show_error("Incorrect data type of '".gettype($filter)."' as a filter passed to the Model");
		}

		foreach ( $values as $name=>$value ) {
			$this->_add_filter_item_to_where_expression($name, $value);
		}
	}
	
	protected function _add_filter_item_to_where_expression($name, $value)
	{
		$quote_value = $value;
		if ( is_string($value) ) {
			$quote_value = "'".$this->db->escape_str($value, "'")."'";
//			$quote_value = "'".$value."'";
		}
		if ( ! preg_match('/\./', $name) and $this->_default_table_prefix != '' ) {
			$name = $this->_default_table_prefix.'.'.$name;
		}
		if ( preg_match('/\s/', $name) ) {
			$this->_add_where_expression("$name $quote_value");
		}
		else {
			$this->_add_where_expression("$name = $quote_value");
		}
	}
	protected function _add_where_expression($value)
	{
		$this->_where_sql[] = $value;
	}	
}

?>
