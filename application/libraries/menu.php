<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



Class Menu
{
	private $_config;
	private $_items;
	private $_is_setup_done;
	
	public function __construct($params = array()) 
	{
		$this->CI =& get_instance();
		$this->_items  = '';
		$this->_is_setup_done = false;
		
		$this->_config = array('show_error_on_no_items'=>true,
								'plugin'=>'ddsmooth_plugin', 
								'plugin_folder'=>APPPATH.'libraries/menu_plugins',
								'base_url'=>'',
								'name'=>'items',
								);
		
		$this->_config = array_merge($this->_config, $params);
		if ( isset($params['config']) ) {
			$this->CI->load->config($params['config'], true);
			$this->_config = array_merge($this->_config, $this->CI->config->item($params['config']));
		}
		$this->load_items($this->_config['name']);
	}
	
	public function load_items($name)
	{
		if ( isset($this->_config[$name]) ) {
			$this->_items = $this->_load_menu_tree_from_text($this->_config[$name]);
		}		
	}
	public function setup()
	{
		$this->_plugin = $this->_load_plugin($this->_config['plugin']);
		if ( $this->_plugin) {
			$this->_plugin->setup();
		}
		$this->_is_setup_done = true;
	}
	
	public function show()
	{
		if ( ! $this->_is_setup_done ) {
			$this->setup();
		}
		if ( $this->_items == '' and $this->_config['show_error_on_no_items'] ) {
			show_error('No menu items set');
		}
		if ( $this->_items and $this->_plugin ) {
			return $this->_plugin->show($this->_items);
		}
		return '';
	}
	public function add_item($parent_title, $title, $link = '', $description = '', $image_name = '')
	{
		$found_item =& $this->_find_item_title($parent_title);
		if ( $found_item ) {
			$item = new StdClass;
			$item->title = $title;
			$item->link = '';
			if ( $link ) { 
				$item->link = site_url($link);
			}
			if ( $description == '' ) {
				$description = $title;
			}
			$item->description = $description;
			$item->image_name = $image_name;
			$item->level = $found_item->level + 1;
			$item->child_items = array();
			$found_item->child_items[] = $item;
		}			

	}
	public function get_items()
	{
		return $this->_items;
	}
	public function set_items($items)
	{
		$this->_items = $items;
	}	
	protected function _load_menu_tree_from_text($text)
	{
		
		$lines = explode("\n",$text);
		$tree = array();
		$last_node = null;
		$parent_nodes = array();
		$last_level = 0;
		foreach ( $lines as $line) {
			if ( trim($line) != '' ) {
				$item = $this->_get_item_from_line($line);
				if ( $item->level == 0 ) {
					$tree[] = $item;
					$parent_nodes = array($item);
				}
				else {
					if ( $last_level == $item->level ) {
						$parent_nodes[$item->level - 1]->child_items[] = $item;
						$parent_nodes[$item->level] = $item;
					}				
					if ( $item->level > $last_level ) {
						$parent_nodes[$last_level]->child_items[] = $item;
					}
					if ( $item->level < $last_level ) {
						$parent_nodes[$item->level - 1]->child_items[] = $item;
					}
				}
				$last_node = $item;		
				$last_level = $item->level;		
			}
		}
		return $tree;

	}



	protected function _get_item_from_line($line)
	{
		$item = new StdClass();
		$item->title = '';
		$item->link = '';
		$item->description = '';
		$item->level = 0;
		$item->image_name = '';
		$item->child_items = array();
		
		$values = explode(',', $line);
		$item->title = trim($values[0]);
		if ( isset($values[1]) ) {
			$item->link = site_url($this->_config['base_url'].trim($values[1]));
		}
		if ( isset($values[2]) ) {
			$item->description = trim($values[2]);
		}
		else {
			$item->description = $item->title;
		}
		if ( isset($values[3]) ) {
			$item->image_name = trim($values[3]);
		}
		if ( preg_match('/^(\t+)/', $line, $match) ) {
			$item->level = strlen($match[1]);
		}
		return $item;
	}

	
	protected function _load_plugin($name)
	{
		if ( $name == '' ) {
			show_error('Please provide a menu plugin to use');
			return false;
		}
		if ( $name == 'menu_plugin' or $name == 'menu' ) {
			show_error('cannot use a menu plugin named "menu"');
			return false;
		}
		$plugin_name = $name;
		$plugin_file = $this->_config['plugin_folder'].'/'.$plugin_name.EXT;
		if ( file_exists($plugin_file) ) {
			include_once($this->_config['plugin_folder'].'/menu_plugin.php');
			require_once($plugin_file);
		}
		else {
			show_error('Cannot find plugin file "'.$plugin_file.'"');
			return false;
		}
		if ( isset($this->_config[$plugin_name]) ) {
			$config = $this->_config[$plugin_name];	
		}
		$plugin = new $plugin_name($this, $config);
		return $plugin;
	}

	protected function _find_item_title($title, $items = '') 
	{
		if ( $items == '' ) {
			$items = $this->_items;
		}
		foreach ( $items as $item ) {
			if ( $item->title == $title ) {
				return $item;
			}
			$found_item = $this->_find_item_title($title, $item->child_items);
			if ( $found_item ) {
				return $found_item;
			}
		}
		return FALSE;
	}

}
