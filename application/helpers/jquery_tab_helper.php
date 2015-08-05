<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * @cipack jquery_tab_helper
 * @version 1.0
 * @include css/tab
 * @include js/jquery
 * 
 */


/**
 * 
 * @defgroup jquery_tab_helper Helper - JQuery Tabs
 * @{
 * This helper displays tabs using jquery ui.
 * 
 * 
 * @}
 * 
*/

/**
 * @ingroup jquery_tab_helper
 * Function to setup the tab helper system. This is called automatcially by the other functions.
 * 
 */
function jquery_tab_setup()
{
	// make sure we have jquery
	jquery_setup();
	jquery_ui_setup('flick');
	$CI =& get_instance();
	if ( $CI->input->post('_jquery_last_tab_index') ) {
		jquery_tab_set_tab_index(intval($CI->input->post('_jquery_last_tab_index')));
	}
	$active_tab = intval($CI->session->flashdata('jquery_tabs_last_used_index'));
	if ( $CI->add_css_file('jquery_tab/jquery_tab.css') ) {
		$javascript = <<< _EOM
<script type="text/javascript">
    $("document").ready(function() {
		$("#jquery_tabs").tabs( {
			active: $active_tab, 
			activate: function(event, ui) {
				$(":input[name='_jquery_last_tab_index']").val($("#jquery_tabs").tabs("option", "active"));
			}
		});
	});
</script>
_EOM;
		$CI->add_java_script($javascript);		
	}

}

function jquery_tab_set_tab_index($index)
{
	$CI =& get_instance();
	$CI->session->set_flashdata('jquery_tabs_last_used_index', $index);	
}


/**
 * @ingroup jquery_tab_helper
 * Function to open a tab area.
 * @param array $titles			List of titles to show for each tab
 * @return string 				HTML text to show on the page.
 * 
 */
function jquery_tab_open($titles)
{	
	jquery_tab_setup();
	$result = array();
	foreach ( $titles as $index=>$title ) {
		$name = jquery_tab_convert_title_to_link($index);
		$result[] = '<li><a id="button_'.$name.'" href="#'.$name.'">'.$title.'</a></li>';
	}
	$result = '<input type="hidden" name="_jquery_last_tab_index" value="">'
			. '<div class="jquery_tab" id="jquery_tabs"><ul>'.implode("\n", $result).'</ul>';
	return $result;
}

/**
 * @ingroup jquery_tab_helper
 * Function to open a tab page.
 * @param string $index			Optional index of the page
 * @return string 				HTML text to show on the page.
 * 
 */
function jquery_tab_page_open($index)
{
	return '<div class="jquery_tab_page" id="'.jquery_tab_convert_title_to_link($index).'">';
	
}
/**
 * @ingroup jquery_tab_helper
 * Function to close a tab page.
 * 
 * @return string 				HTML text to show on the page.
 * 
 */
function jquery_tab_page_close()
{
	return "</div>";
}


/**
 * @ingroup jquery_tab_helper
 * Function to close a tab area.
 * 
 * @return string 				HTML text to show on the page.
 * 
 */
function jquery_tab_close()
{
	return "</div>";
}

function jquery_tab_header($title, $count = 0, $min_value = 1, $is_plaural = TRUE)
{
	$result = $title;
	if ( $is_plaural) {
		$result .= ( $count > 1 ? 's' : '');
	}
	$result .= ( $count > $min_value ? " ($count)" : '');
	return $result;
}

function jquery_tab_convert_title_to_link($title)
{
	return 'tab_'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $title));
}


?>
