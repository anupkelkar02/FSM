<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



function reply_status_show_text($reply_status_id)
{
	if ( $reply_status_id ) {
		$CI =& get_instance();
		$CI->reply_status_model->load_by_id($reply_status_id);
		return $CI->reply_status_model->title;
	}
	return 'None';
}
