<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function site_address($row)
{
	$result = array();
	$result[] = $row->street_number;
	$result[] = $row->street_name;
	$result[] = $row->unit_number;
	$result[] = $row->city;
	$result[] = $row->postcode;
	return implode(" ", $result);
}

function site_list_assignment_rows($rows)
{
	$result = array();
	foreach ( $rows as $row ) {
		$title = $row->name.' ('.$row->shift_type.', '.$row->assign_type.')';
		$result[] = anchor('admin/sites/edit/'.$row->site_id, $title);
	}
	return $result;
}

function site_list_assignment_staff_id($staff_id)
{
	$CI =& get_instance();
	$rows = $CI->staff_assignment_model->get_site_rows(array('staff_id'=>$staff_id));
	return site_list_assignment_rows($rows);
	
}

function site_anchor_from_id($site_id)
{
	$CI =& get_instance();
	$CI->site_model->load_by_id($site_id);
	return anchor('admin/sites/edit/'.$site_id, $CI->site_model->name);
}
