<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


function staff_show_name($row)
{
	return $row->first_name.' '.$row->last_name;
	
}

function staff_anchor_from_id($staff_id)
{
	$CI =& get_instance();
	$row = $CI->staff_model->get_row_by_id($staff_id);
	return anchor('admin/staff/edit/'.$row->id, staff_show_name($row));
}
