<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @cipack jquery_helper
 * @version 1.0
 * @include js/jquery
 * @include css/jquery-ui
 * 
 */


/**
 * 
 * @defgroup jquery_helper Helper - JQuery
 * @{
 * This helper adds in jQuery java script library
 * 
 * @}
 * 
*/


/**
 * @ingroup jquery_helper
 * Function to setup the java script libraries for JQuery.
 * This function will needs to be called before jquery can be used.
 */
function jquery_setup()
{
//	$CI =& get_instance();
//	return $CI->add_javascript_file('jquery/jquery-1.7.2.min.js');
}

/**
 * @ingroup jquery_helper
 * Function to setup the java script ui libraries for JQuery.
 * This function will needs to be called before ui part of jquery can be used.
 * @param string $style_name 		Name of the style to use for the jquery UI.
 */
function jquery_ui_setup($style_name)
{
	jquery_setup();
	$CI =& get_instance();
	if ( $CI->add_javascript_file('jquery-ui-1.9.2.custom.min') ) {
		$css_file = 'jquery-ui/ui-'.strtolower($style_name).'/jquery-ui-1.9.2.custom.css';
		$CI->add_css_file($css_file);
		return TRUE;
	}
	return FALSE;
}
?>
