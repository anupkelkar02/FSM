<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



function work_status_show_text($work_status_id)
{
	if ( $work_status_id ) {
		$CI =& get_instance();
		$CI->work_status_model->load_by_id($work_status_id);
		return $CI->work_status_model->title;
	}
	return 'None';
}
