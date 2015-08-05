<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



function format_currency($value)
{
	$CI =& get_instance();
	$CI->load->config('format_currency', TRUE, TRUE);
	$config = array(
					'format'=>'$%0.2f',
					'zero_value'=>'$0.00',
				);
	if ( $CI->config->item('format_currency')) {
		$config = array_merge($config, $CI->config->item('format_currency'));
	}
	if ( $value == '0' ) {
		return $config['zero_value'];
	}
	return sprintf($config['format'], $value );
	
}




?>
