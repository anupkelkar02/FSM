<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * @cipack toolbar_helper
 * @version 1.0
 * @include css/toolbar.css
 * @include images/admin/toolbar
 * 
 */


/**
 * 
 * @defgroup toolbar_helper Helper - Toolbar
 * @{
 * This helper displays a toolbar.
 * 
 * An example of code you need to use the toolbar helper:
 * 
 * 
 *		echo toolbar_open('User Edit');
 *		echo toolbar_item('logs','Logs', 'log_view.png');
 *		echo toolbar_save();
 *		echo toolbar_apply();
 *		echo toolbar_cancel();
 *		echo toolbar_close();
 *
 * @}
 * 
*/


/**
 * @ingroup toolbar_helper
 * Function to open the toolbar. You need to call this function before calling any other toolbar
 * helper functions.
 * 
 * @param string $title			Title to show on the toolbar.
 * @param string $sub_title		Sub title to show on the toolbar.
 * @param string $image_name 	Image to show next to the title.
 * @return string 				HTML to display on the page.
 */
function toolbar_open($title)
{
	$CI =& get_instance();
	
	$result = "\n<input type='hidden' name='toolbar_task_value' value=''>"
			. "\n<div class='row toolbar'>"
			. "\n<h3 class='pull-left'>$title</h3>"
			. "\n<div class='btn-toolbar pull-right'>"
			;

	return $result;
	
}

/** 
 * @ingroup toolbar_helper
 * Function to show a toolbar button.
 * @param string $name			Name of the field.
 * @param string $title			Title to show next on the button.
 * @return string 				HTML to display on the page.
 */
function toolbar_button($name, $title, $icon_name = '', $js_script = '', $button_class = 'btn-primary')
{
	
	$on_click = '';		
	if ( $name ) {
		$script = '$("[name=\\"toolbar_task_value\\"]").val("'.$name.'");'
				//. "$(this).parents('form').submit();"
				;
				
		$on_click = "\nonclick='$script'\n";
	}
	$result = "<div class='btn-group'>"
			. "<button class='btn $button_class' href='javascript:null;' $on_click $js_script>"
			;
	if ( $icon_name ) {
		$result .= "<span class='$icon_name'></span>&nbsp;";
	}
	$result .= "$title</button>\n"
			. "</div>"
			;
	return $result;
	
}

/** 
 * @ingroup toolbar_helper
 * Function to show a toolbar item.
 * @param string $name			Name of the field.
 * @param string $title			Title to show next on the item.
 * @param string $image_name	Name of the image to show on the toolbar item.
 * @return string 				HTML to display on the page.
 */
function toolbar_item($name, $title, $icon_name = '', $js_script = '')
{
	return toolbar_button($name, $title, $icon_name, $js_script);
}

/** 
 * @ingroup toolbar_helper
 * Function to show a standard 'Reload' item.
 * @param string $title			Optional title to show on the item.
 * @return string 				HTML to display on the page.
 */
function toolbar_reload($title = 'Reload')
{
	return toolbar_item('reload', $title, 'glyphicon glyphicon-refresh');
}


/** 
 * @ingroup toolbar_helper
 * Function to show a standard 'Back' item.
 * @param string $title			Optional title to show on the item.
 * @return string 				HTML to display on the page.
 */	
function toolbar_back($title = 'Back')
{
	return toolbar_item('back', $title, 'glyphicon glyphicon-step-backward');
}

/** 
 * @ingroup toolbar_helper
 * Function to show a standard 'Add' item.
 * @param string $title			Optional title to show on the item.
 * @return string 				HTML to display on the page.
 */	
function toolbar_add($title = 'Add')
{
	return toolbar_item('add', $title, 'glyphicon glyphicon-plus');
}

/** 
 * @ingroup toolbar_helper
 * Function to show a standard 'Delete' item.
 * @param string $title			Optional title to show on the item.
 * @return string 				HTML to display on the page.
 */	
function toolbar_delete($title = 'Delete')
{
	return toolbar_item('delete', $title, 'glyphicon glyphicon-remove');
}

/** 
 * @ingroup toolbar_helper
 * Function to show a standard 'Edit' item.
 * @param string $title			Optional title to show on the item.
 * @return string 				HTML to display on the page.
 */	
function toolbar_edit($title = 'Edit')
{
	return toolbar_item('edit', $title, 'glyphicon glyphicon-edit');
}

/** 
 * @ingroup toolbar_helper
 * Function to show a standard 'View' item.
 * @param string $title			Optional title to show on the item.
 * @return string 				HTML to display on the page.
 */	
function toolbar_view($title = 'View')
{
	return toolbar_item('view', $title, 'glyphicon glyphicon-list');
}

/** 
 * @ingroup toolbar_helper
 * Function to show a standard 'Cancel' item.
 * @param string $title			Optional title to show on the item.
 * @return string 				HTML to display on the page.
 */	
function toolbar_cancel($title = 'Cancel',$icon = 'glyphicon-circle-arrow-left')
{
	return toolbar_item('cancel', 'Cancel', 'glyphicon '.$icon);
}

/** 
 * @ingroup toolbar_helper
 * Function to show a standard 'Save' item.
 * @param string $title			Optional title to show on the item.
 * @return string 				HTML to display on the page.
 */	
function toolbar_save($title = 'Save',$icon='glyphicon-file')
{
	return toolbar_item('save', $title, 'glyphicon '.$icon);
}

/** 
 * @ingroup toolbar_helper
 * Function to show a standard 'Apply' item.
 * @param string $title			Optional title to show on the item.
 * @return string 				HTML to display on the page.
 */	
function toolbar_apply($title = 'Apply',$icon = 'glyphicon-ok')
{
	return toolbar_item('apply', $title, 'glyphicon '.$icon);
}


/** 
 * @ingroup toolbar_helper
 * Function to show a standard 'Publish' item.
 * @param string $title			Optional title to show on the item.
 * @return string 				HTML to display on the page.
 */	
function toolbar_publish($title = 'Publish')
{
	return toolbar_item('published', $title, 'icon-publish');
}

/** 
 * @ingroup toolbar_helper
 * Function to show a standard 'Unpublish' item.
 * @param string $title			Optional title to show on the item.
 * @return string 				HTML to display on the page.
 */	
function toolbar_unpublish($title = 'Unpublish')
{
	return toolbar_item('unpublished', $title, 'icon-unpublish');
}

/** 
 * @ingroup toolbar_helper
 * Function to show a standard 'Toggle Publish' item.
 * @param string $title			Optional title to show on the item.
 * @return string 				HTML to display on the page.
 */	
function toolbar_toggle_publish($title = 'Un/Publish')
{
	return toolbar_item('toggle_published', $title, 'icon-switch');
}


/** 
 * @ingroup toolbar_helper
 * Function to show a standard Spacer item.
 * @return string 				HTML to display on the page.
 */	

function toolbar_space()
{
	return toolbar_item('', '', '');
}

/** 
 * @ingroup toolbar_helper
 * Function to close the toolbar items in HTML.
 * @return string 				HTML to display on the page.
 */	
function toolbar_close()
{
	$result = "</div></div>\n"

			;
	return $result;
}


function toolbar_process_task($object, $prefix='')
{
	$CI =& get_instance();
	$name = $CI->input->post('toolbar_task_value');
	if ( $name ) {
		$method_name = 'toolbar_'.$prefix.$name;
		if ( method_exists($object, $method_name) ) {
			return $object->$method_name();
		}
		else {
			show_error("Cannot find toolbar task method '$method_name'");
		}
	}
	return FALSE;
}

function toolbar_param()
{
	$CI =& get_instance();
	return $CI->input->post('toolbar_param');
}
function toolbar_send()
{
	return toolbar_item('Send', 'Send', '');
}
