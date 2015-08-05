<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$jscolor_is_setup = FALSE;

function jscolor_setup()
{
	global $jscolor_is_setup;
	if ( ! $jscolor_is_setup) {
		$CI =& get_instance();
		$CI->add_javascript_file('jscolor/jscolor.js');	
		$jscolor_is_setup = TRUE;
	}
	return '';
}


function jscolor_picker($name, $value, $attribs = '')
{
	jscolor_setup();	
	$result = "<input class='color' name='$name' value='$value' $attribs>";
	
	return $result;
}
?>
