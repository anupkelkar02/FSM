<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @cipack format_datetime_helper
 * @version 1.0
 * 
 */

/**
 * @defgroup format_datetime_helper Helper - Format DateTime
 * This helper displays date and datetime values as specificed in a config file
 * 
 * 
 */


$format_datetime_config = FALSE;
$is_format_datetime_setup = FALSE;

/**
 * @ingroup format_datetime_helper
 * Function setups the datetime settings. This function will be called
 * automatically by all other functions.
 */
function format_datetime_setup()
{
	global $is_format_datetime_setup, $format_datetime_config;
	if ( ! $is_format_datetime_setup ) {
		$format_datetime_config = array(
						'datetime'=>'d M Y H:i:s',
						'datetime_zero'=>'-- None --',
						'date'=>'d M Y',
						'date_zero'=>'-- None --'
					);
		$CI =& get_instance();
		$CI->load->config('format_datetime', TRUE, TRUE);
		if ( $CI->config->item('datetime')) {
			$format_datetime_config = array_merge($config, $CI->config->item('format_datetime'));
		}
		$is_format_datetime_setup = TRUE;		
	}
}

/**
 * @ingroup format_datetime_helper
 * Function to return formated datetime.
 * @param string $datetime 		Datetime to format
 * @return string		Format date time.
 */
function format_datetime($datetime)
{
	global $format_datetime_config;
	format_datetime_setup();
	
	if ( $datetime == '0000-00-00 00:00:00' ) {
		return $format_datetime_config['datetime_zero'];
	}
	return date($format_datetime_config['datetime'], strtotime($datetime) );
	
}

/**
 * @ingroup format_datetime_helper
 * Function to format a date value
 * @param string $date 		Date to format
 * @return string		Format date.
 */
function format_date($date)
{
	global $format_datetime_config;
	format_datetime_setup();

	if ( $date == '0000-00-00' ) {
		return $format_datetime_config['date_zero'];
	}
	return date($format_datetime_config['date'], strtotime($date) );
	
}


/**
 * @ingroup format_datetime_helper
 * Function to format seconds as hour minute second text.
 * 
 * Possible formats are:
 * 
 * - xx hours
 * - xx minutes
 * - xx seconds
 * 
 * @param int $seconds			Seconds to format
 * @return string 				Number of seconds formated to text
 */
function format_seconds_as_text($seconds)
{
	$minutes = 0;
	$hours = 0;
	if ( $seconds > 60 * 60 ) {
		$hours = floor($seconds / (60 * 60));
		$seconds -= ($hours * 60 * 60);		
	}
	if ( $seconds > 60 ) {
		$minutes = floor($seconds / 60);
		$seconds = floor($seconds - ($minutes * 60));
	}
	$result = array();
	if ( $hours > 0 ) {
		$result[] = $hours." hour".( $hours > 1 ? 's': '');
	}
	if ( $minutes > 0 ) {
		$result[] = $minutes." minute".( $minutes > 1 ? 's': '');
	}
	if ( $seconds > 0 ) {
		$result[] = $seconds." second".( $seconds > 1 ? 's': '');
	}
	return implode(' ', $result);
}

/**
 * @ingroup format_datetime_helper
 * Function to format number of minutes as text in Days, Hours, Minutes
 * 
 * Possible formats are:
 * - xx days
 * - xx hours
 * - xx minutes
 * 
 * @param string $value 			Number of minutes
 * @return string					Minutes as text 
 */
function format_minutes_as_text($value, $include_days = TRUE)
{
	$mins = 0;
	$hours = 0;
	$days = 0;
	if ( $value < 60  ) {
		$mins = $value;
	}
	else {
		$hours = floor($value / 60);
		$mins = $value - ($hours * 60);
	}
	if ( $hours > 24 and $include_days) {
		$days = floor($hours / 24);
		$hours -= $days * 24;
	}
	$result = array();
	
	if ( $days > 0 ) {
		$result[] = floor($days)." day".($days > 1 ? 's': '');
	}
	if ( $hours > 0 ) {
		$result[] = floor($hours)." hour".($hours > 1 ? 's': '');
	}
	if ( $mins > 0 ) {
		$result[] = floor($mins)." minute".($mins > 1 ? 's': '');
	}
	if ( $mins == 0  and $hours == 0 and $days == 0) {
		$result[] = "0 minutes";
	}
	return implode(' ', $result);
	
}

/**
 * @ingroup format_datetime_helper
 * Function format week number as a start and end date of week.
 * @param int $year				Year or Year and week number together.
 * @param int $week_number		Week number if only the year is supplied.
 * @param boolean $short_style	Show the date format in a shorter style.
 * @return string				Text returned is #ww start_date - end_date
 * 
 */
function format_week_number_to_text($year, $week_number = 0, $short_style = FALSE)
{	
	if ( $week_number == 0 and $year > 2000) {
		$value = strval($year);
		if ( strlen($value) > 4 ) {
			$year = intval(substr($value, 0, 4));
			$week_number = intval(substr($value, 4));
		}
	}
	$tim = mktime(0, 0, 0, 1, 1, $year);
	$first_day_of_week = intval(date('N', $tim));
	$first_week_date = date('Y-m-d', $tim - (($first_day_of_week - 8) * ( 60 * 60 * 24)));
	$tim = strtotime($first_week_date);
	$start_tim = mktime(0, 0, 0, date('m', $tim), date('d', $tim) + ( ($week_number - 1) * 7), date('Y', $tim));
	$end_tim = mktime(0, 0, 0, date('m', $start_tim), date('d', $start_tim) + 6, date('Y', $start_tim));
	if ( $short_style ) {
		$month_start = date('M', $start_tim);
		$month_end = date('M', $end_tim);
		if ( $month_start == $month_end ) {
			$result = date('d', $start_tim).'/'.date('d M Y', $end_tim);
		}
		else {
			$result = date('d M', $start_tim).'/'.date('d M Y', $end_tim);
		}
	}
	else {
		$result = "#$week_number ".date('D d M', $start_tim).' - '.date('D d M Y', $end_tim);
	}
	return $result;

}

/**
 * @ingroup format_datetime_helper
 * Function day count to text. Possible returns are:
 * 
 * - 'No Days'
 * - '1 Day'
 * - 'xx Days'
 * - 'xx Weeks'
 * - 'xx Months'
 * 
 * @param int $day_count 		Number of days
 * @return string				Day count as readable text
 */
function format_days_to_text($day_count)
{
	if ( $day_count == 0 ) {
		return "No days";
	}
	if ( $day_count == 1 ) {
		return "1 day";
	}

	if ( $day_count < 7 ) {
		return "$day_count days";
	}
	if ( $day_count < 32 ) {
		$week_count = floor($day_count / 7);
		$day_count -= $week_count * 7;
		$result = "$week_count week".(($week_count > 1 ) ? 's' : '');
		if ( $day_count > 0  and $week_count <= 2 ) {
			$result .= ' '.format_days_to_text($day_count);
		}
		return $result;
	}
	$month_count = floor($day_count / 30);
	$result = "$month_count month".(($month_count > 1 ) ? 's' : '');
	$day_count -= $month_count * 30;
	if ( $day_count > 0  and $month_count <= 2 ) {
		$result .= ' '.format_days_to_text($day_count);
	}
	return $result;
}
?>
