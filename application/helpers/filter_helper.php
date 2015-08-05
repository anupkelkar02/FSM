<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


function filter_load($name, $default)
{
	$CI =& get_instance();
	$post_data = $CI->input->post($name);
	if ( !is_array($post_data) ) {
		$post_data = array();
	}
	$result = new StdClass();
	foreach ( $default as $name=>$value) {
		if ( isset($post_data[$name]) ) {
			$result->$name = $post_data[$name];
		}
		else {
			$result->$name = $value;
		}
	}
	return $result;

}

?>
