<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @cipack message_note_helper
 * @version 1.0
 * @include css/message_note.css
 * @include images/icons/notifications
 * 
 */


/**
 * 
 * @defgroup message_note_helper Helper - Message Note
 * @{
 * This helper displays a message note on the page. The possible types of messages are:
 * 
 * - success	@image html "accept.png"
 * - warning
 * - failure
 * - information
 * - message
 * - lightbulb
 * 
 * @}
 * 
*/


$message_note_data = false;
$is_message_note_setup = false;

define('MESSAGE_NOTE_SUCCESS',		'success');
define('MESSAGE_NOTE_WARNING', 		'warning');
define('MESSAGE_NOTE_FAILURE', 		'failure');
define('MESSAGE_NOTE_INFORMATION', 	'information');
define('MESSAGE_NOTE_MESSAGE', 		'message');
define('MESSAGE_NOTE_LIGHTBULB', 	'lightbulb');

// Note message helper

// shows note with style

/**
 * @ingroup message_note_helper
 * Function to setup the message note files. This will be called automatically be the other functions.
 */
function message_note_setup()
{	
	global $is_message_note_setup;
	if ( $is_message_note_setup == false ) {
		$CI =& get_instance();
		$css_file = 'message_note.css';
		if ( file_exists('css/'.$css_file) ) {
			$CI->add_css_file($css_file);
		}


		$is_message_note_setup = true;
	}
}

/**
 * Function to show a message note on the page. If all the fields are blank then the default
 * option is to read the temporary storage for the message.
 * 
 * @param string $message 			Message to show.
 * @param string $type				Type of message to show.
 * @param string $type_text			Type of text to show.
 * @return string					HTML text to show on the page.
 */
function message_note($message = '', $type = 'success', $type_text = '')
{
	message_note_setup();
	
	// if blank then show the saved message and message type
	if ( $message == '' ) {
		$CI =& get_instance();
		$message = get_message_note_message();
		if ( $message ) {
			$CI->session->set_flashdata('message_note', '');
			return message_note($message, get_message_note_type() );
		}
		if ( $CI->session->flashdata('message_note') ) {
			$data = $CI->session->flashdata('message_note');
			$CI->session->set_flashdata('message_note', '');
			if ( $data['message'] ) { 
				return message_note($data['message'], $data['type']);
			}
		}
		
		return '';
	}
	
	
	$message_note_types = array('success', 'failure', 'warning', 'information', 'message', 'lightbulb');
	
	$type = strtolower($type);
	if ( !in_array($type, $message_note_types) ) {
		show_error("Invalid message not type '$type'");
	}
	if ( $type_text == '' ) {
		$type_text = strtoupper($type);
	}
	
	$type = 'n'.ucfirst($type);

	$result = '<div class="nNote '.$type.'">'
			.'<p><strong>'.strtoupper($type_text).': </strong>'
			. $message
			. '</p>'
			. '</div>'
			;
	return $result;

}
/**
 * Function to set the message note, so that it can be displayed on the next call to the message_note function.
 * 
 * @param string $message 		Message to show.
 * @param string $type			Message type.
 */
function set_message_note($message, $type = MESSAGE_NOTE_SUCCESS)
{
	global $message_note_data;
	
	$message_note_data = array('message'=>$message, 'type'=> $type);
	$CI =& get_instance();
	$CI->session->set_flashdata('message_note', $message_note_data);

}

/**
 * Function to get the currently stored message. If none then return ''.
 * 
 * @return sting 		The message.
 */
function get_message_note_message()
{
	global $message_note_data;
	
	if ( $message_note_data ) {
		return $message_note_data['message'];
	}
	
	return '';
}

/**
 * Function to get the currently stored message type. If none then return ''.
 * 
 * @return string			The message type.
 */
function get_message_note_type()
{
	global $message_note_data;
	
	
	if ( $message_note_data ) {
		return $message_note_data['type'];
	}
	return '';
}


?>
