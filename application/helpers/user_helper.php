<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function user_name($row, $last_name = '')
{
	$values = array();
	if ( is_object($row) ) {
		$values = array($row->first_name, $row->last_name);
	}
	if ( is_string($row) ) {
		$values = array($row, $last_name);
	}
	$result = implode(' ', $values);
	if ( $result ) {
		return $result;
	}
	return "Bad name params!";
}
function user_name_anchor($link, $row)
{
	$values = array();
	if ( is_object($row) ) {
		$values = array($row->first_name, $row->last_name);
	}
	$result = trim(implode(' ', $values));
	if ( $result ) {
		return anchor($link, $result);
	}
	if ( $row ) {
		return anchor($link, 'User #'.$row->id);
	}
	return "";
}


function user_id_anchor($user_id)
{
	$CI =& get_instance();
	$row = $CI->user_model->get_row_by_id($user_id);
	return user_name_anchor('users/edit/'.$user_id, $row);
	
}


