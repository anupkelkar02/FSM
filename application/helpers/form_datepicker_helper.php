<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @cipack datepicker_helper
 * @version 1.0
 * @require jquery_helper
 * 
 */


/**
 * 
 * @defgroup datepicker_helper Helper - DatePicker
 * @{
 * This helper enables allows you to easily load the jQuery date picker.
 * 
 * @}
 * 
*/


$is_amcharts_setup = FALSE;

/**
 * @ingroup datepicker_helper
 * Setup and load in the datepicker javascript library.
 * 
 * This function will be automatically called by the form_datepicker_input function.
 */
function form_datepicker_setup()
{
	
		// setup using the jquery_helper
		jquery_setup();
		if ( jquery_ui_setup('lightness') ) {
	
			$script = <<< _EOM
		
<script type="text/javascript">
	$(function() {
		$( "#datepicker" ).datepicker( { dateFormat: "yy-mm-dd",
										showButtonPanel: true });
	});
</script>

_EOM;
		$CI =& get_instance();
		$CI->add_java_script($script);
				
	}
}


/**
 * @ingroup datepicker_helper
 * Show the datepiker input form code
 * @param string $name 		The field name.
 * @param string $value		The date value to edit.
 * @param string $options 	Options to show at the end of the field, can be optional.
 * @return string			The HTML code to show the input field for the datepicker.
 */
function form_datepicker_input($name, $value, $options='')
{
	form_datepicker_setup();
	$result = form_input($name, $value, "id='datepicker' size='10'" . $options);
	return $result;	
}
?>
