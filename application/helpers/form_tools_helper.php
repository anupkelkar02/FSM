<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// form_tools_helper


$form_row_color_bars_toggle = 0;				// row colors
$form_sort_order_items = FALSE;
$is_form_jquery_ui_setup = FALSE; 

function form_tools_setup()
{
	
	$CI =& get_instance();
	if ( $CI->add_javascript_file('form_tools.js') ) {
		$CI->add_css_file('form_tools.css');
		$CI->load->config('form_tools', TRUE);
	}
}


function form_checkids_header($name = 'checkid')
{
	form_tools_setup();
	$result = "<input type='checkbox' name='check_all' value='' onChange='clickOnCheckids(this, \"$name\");'>&nbsp;#&nbsp;";
	return $result;
}


function form_checkids_item($id, $value = '', $name = 'checkid') 
{
	form_tools_setup();
	if ( $value == '' ) {
		$value = $id;
	}
	$result = "<input class='checkid' type='checkbox' name='${name}[$id]' value='$value'>";
	return $result;
}

function form_checkids_ids($name = 'checkid')
{
	$CI =& get_instance();
	$checkids = $CI->input->post($name);
	if ( $checkids ) {
		return $checkids;
	}
	return array();

}

function form_checkids_id_value($name= 'checkid')
{
	$checkids = form_checkids_ids($name);
	if ( count($checkids) > 0 ) {
		return reset($checkids);
	}
	return FALSE;
}


function form_dropdown_boolean($name, $value, $js = '', $default_title = '')
{
	form_tools_setup();
	$CI =& get_instance();
	$CI->load->helper('form');
	if ( $default_title ) {
		$items[''] = $default_title;
	}
	$items['True']='Yes';
	$items['False']='No';
	return form_dropdown($name, $items, $value, $js);

}

function form_is_published($id, $value, $name='checkid')
{
	form_tools_setup();

	$CI =& get_instance();
	$config = $CI->config->item('form_tools');
	if ( !isset($config['image_path']) ) {
		show_error("Cannot find form_tools config file");
	}
	$image_path = $config['image_path'];

	if ( intval($value) or $value == 'True' ) {
		$img = "<img src='".base_url()."$image_path/tick.png' border='0'>";
	}
	else {
		$img = "<img src='".base_url()."$image_path/publish_x.png' border='0'>";
	}
	$result = "<a href='#' onClick=\"toggleIsPublished($id, '$name');\">$img</a>";
	return $result;

}


function form_row_color_open()
{
	global $form_row_color_bars_toggle;
	
    $result ='<tr class="'.($form_row_color_bars_toggle > 0 ? 'row_on' : 'row_off').'">';
	$form_row_color_bars_toggle = 1 - (1 * $form_row_color_bars_toggle);
	return $result;
}

function form_row_color_close()
{
	return "</tr>";
}

function form_sort_order($sort_order_items)
{
	global $form_sort_order_items;
	$form_sort_order_items = $sort_order_items;
	
	$result = array();
	foreach ( $sort_order_items as $order_item) {
		$result[] = $order_item->name.' '.$order_item->direction;
	}
	return "<input type='hidden' name='sort_order' value='".implode(", ", $result)."'>";
}


function form_sort_header($name, $title)
{
	global $form_sort_order_items;
	
	form_tools_setup();
	
	$CI =& get_instance();

	$config = $CI->config->item('form_tools');
	if ( !isset($config['image_path']) ) {
		show_error("Cannot find form_tools config file");
	}
	$image_path = base_url().$config['image_path'];
	$on_click_up = "onClick='sort_change_direction(\"".$name."\", \"ASC\");'";
	$on_click_down = "onClick='sort_change_direction(\"".$name."\", \"DESC\");'";
	$image = 'arrows-grey.png';
	$on_click = $on_click_up;
	
	$order_count = 0;
	if ( isset($config['sort_order_count']) ){
		$order_count = intval($config['sort_order_count']);
	}
	if ( $form_sort_order_items ) {
		foreach ( $form_sort_order_items as $index=>$order_item) {
			if ( $order_item->name == $name and ($index < $order_count or $order_count == 0) ) {
				if ( $order_item->direction == 'ASC') {
					$image = 'arrows-up-green.png';
					$on_click = $on_click_down;
				}
				if ( $order_item->direction == 'DESC') {
					$image = 'arrows-down-green.png';
					$on_click = $on_click_up;
				}
			}		
		}
	}
	$extra = '';
	$extra = '<a href="javascript:null;" '.$on_click.'><img style="padding-left: 2px;" src="'.$image_path.'/'.$image.'"></a>';
	$result = "<th  style='white-space:nowrap;'>"
			. "$title"
			. $extra
			. "</th>\n";
	
	return $result;
}

function form_sort_order_as_array($sort_order_text)
{
	$result = array();
	$lines = explode(',', $sort_order_text);
	foreach ( $lines as $line ) {
		$values = explode('=', $line);
		$order = new StdClass();
		$order->name = trim($values[0]);
		$sub_fields = explode(' ', trim($values[1]));
		$order->field = trim($sub_fields[0]);
		$order->direction = 'ASC';
		if ( count($sub_fields) > 1 ) {
			$order->direction = trim(strtoupper($sub_fields[1]));
		}
		$result[] = $order;
	}
	return $result;
}
function form_sort_order_as_sql($sort_order_items)
{
	if ( is_string( $sort_order_items ) ) {
		return $sort_order_items;
	}
	$result = array();
	foreach ( $sort_order_items as $order_item ) {
		$result[] = $order_item->field.' '.$order_item->direction;
	}
	return implode(', ', $result);
}

function form_sort_order_apply($sort_order_items) 
{
	$CI =& get_instance();
	$order = $CI->input->post('sort_order');
	if ( $order ) {
		$direction = 'ASC';
		$values = explode(' ', trim($order));
		$name = $values[0];
		if ( count($values) > 1 ) {
			$direction = $values[1];
		}
		
		$top_index = 0;
		foreach ( $sort_order_items as $index=>$order_item ) {
			if ( $order_item->name ==  $name ) {
				$top_index = $index;
				$order_item->direction = $direction;
			}
		}
		if ( $top_index > 0 ) {
			$item = $sort_order_items[$top_index];
			unset($sort_order_items[$top_index]);
			array_unshift($sort_order_items, $item);
		}
	}
	return $sort_order_items;
}


function form_row_merge($to_row, $from_row, $prefix = '')
{
	foreach ( get_object_vars($from_row) as $name=>$value ) {
		$use_name = $prefix.$name;
		$to_row->$use_name = $value;
	}
	return $to_row;
}

function form_row_extract($row, $regexp_match)
{
	$result = new stdclass();
	foreach ( get_object_vars($row) as $name=>$value ) {
		$isFound = FALSE;
		if ( is_array($regexp_match) ) {
			if ( in_array($name, $regexp_match) ) {
				$isFound = TRUE;
			}
		}
		else {
			if ( preg_match($regexp_match, $name) ) {
				$name = preg_replace($regexp_match, '', $name);
				$isFound = TRUE;
			}
		}
		if ( $isFound ) {
			$result->$name = $value;
		}
	}
	return $result;
}

function form_slider($name, $value, $params = array(), $extra_script = '')
{	
	global $is_form_jquery_ui_setup;
	$CI =& get_instance();
	
	if ( $is_form_jquery_ui_setup == FALSE ) {
		
		
		$css_file = 'jquery-ui/ui-lightness/jquery-ui-1.8.20.custom.css';
		$CI->add_css_file($css_file);

		$CI->add_javascript_file('jquery-ui-1.8.20.custom.min');
		
		$is_form_jquery_ui_setup = TRUE;
	}

	$param_text = array();
	foreach ( $params as $param_name=>$param_value ) {
		if ( is_string($param_value) and ! preg_match('/^function/', ltrim($param_value))) {
			$param_value = '"'.addslashes($param_value).'"';
		}
		if ( is_bool($param_value) ) {
			$param_value = $param_value ? 'True' : 'False';
		}
		$param_text[] = $param_name.':'.$param_value;
	}
	$param_text = implode(", \n", $param_text);
	$script = <<< _EOM
		
<script type="text/javascript">
	$(function() {
		$( "#slider_$name" ).slider({ $param_text });
	});
</script>

_EOM;

	$CI->add_java_script($script.$extra_script);

	return "<div id='slider_$name'></div>";
	
}

function form_image_toggle($id, $image_list, $value)
{
	form_tools_setup();

	$CI =& get_instance();
	$config = $CI->config->item('form_tools');
	if ( !isset($config['image_path']) ) {
		show_error("Cannot find form_tools config file");
	}
	$image_path = $config['image_path'];

	if ( !isset($image_list[$value]) ) {
		show_error("Cannot find '$value' in image list");
	}
	$image_file = $image_list[$value];
	$img = "<img src='".base_url()."$image_path/$image_file' border='0' alt='$value'>";

	$result = "<a href='#' onClick='toggleImage(".$id.");' alt='$value'>".$img."</a>";
	return $result;

}


function form_help_hint($text)
{
	$CI =& get_instance();
	$CI->load->helper('qtip_helper');
	qtip_setup();
	$config = $CI->config->item('form_tools');
	if ( !isset($config['image_path']) ) {
		show_error("Cannot find form_tools config file");
	}
	$image_path = $config['image_path'];
	$img = "<img src='".base_url()."$image_path/help-hint.png' border='0' alt='help hint'>";
	return anchor('#', $img, "title=\"".htmlspecialchars($text)."\"");
}


function form_toggle_button($id, $value, $toolbar_name = 'toggle_publish')
{
	if ( $value ) {
		$icon = "glyphicon glyphicon-ok glyphicon-white";
		$button_style = "btn-success";
	}
	else {
		$icon = "glyphicon glyphicon-remove glyphicon-white";
		$button_style = "btn-inverse";
	}
	
	$result = "<input type='hidden' value='0' name='checkid[".$id."]'>"
			. "<a class='btn btn-mini $button_style' "
			. "onClick=\"$('[name=\'checkid\[".$id."\]\']').val('".$id."');\n $('[name=\'toolbar_task_value\']').val('".$toolbar_name."');\n $(this).parents('form').submit();\" >"
			. "<i class='$icon'></i>"
			. "</a>"
			;
	return $result;
	
}

?>
