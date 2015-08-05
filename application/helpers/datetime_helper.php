<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @cipack datetime_helper
 * @version 1.0
 * 
 */


/**
 * 
 * @defgroup datetime_helper Helper - DateTime
 * @{
 * This helper add support to many different types of datetime formating.
 * 
 * @}
 * 
*/

$check_id_counter = 0;

/**
 * @ingroup datetime_helper
 * Function to return the current configuration date, if set in the configuration file
 * @return string current date or coded test date.
 */
function datetime_get_config_date()
{
	$date = '';

	if ( defined('get_cookie') ) {
		if ( get_cookie("test_system_date") and SERVER_TYPE != 'production') {
			// for testing , set the current system date
			$date = get_cookie("test_system_date" );
		}
	}
	$CI =& get_instance();
	$date = $CI->config->item('today_date');
	return $date;
}

/**
 * @ingroup datetime_helper
 * Function to return the current today's date, or the date passed.
 * @param string $date		Optional date in the format 'YYYY-mm-dd' which will be returned instead.
 * @return string Today's date
 */
function datetime_today($date = '')
{
	
	if ( $date == '' ) {
		$today_date = datetime_get_config_date();
		if ( $today_date ) {
			return date('Y-m-d', strtotime($today_date));
		}
		return date('Y-m-d');
	}
	return date('Y-m-d', strtotime($date));
}

/**
 * @ingroup datetime_helper
 * Function to return the current today's datetime, or the date passed.
 * @param string $datetime		Optional date in the format 'YYYY-mm-dd HH:ii:ss' which will be returned instead.
 * @return string 				Today's datetime
 */
function datetime_now($datetime = '' ) 
{
	if ( $datetime == '' ) {
		$today_date = datetime_get_config_date();
		if ( $today_date ) {
			$tim = strtotime($today_date);
			
			return date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m', $tim), date('d', $tim), date('Y', $tim)));
		}
		return date('Y-m-d H:i:s');
	}
	return date('Y-m-d H:i:s', strtotime($datetime));
}

/**
 * @ingroup datetime_helper
 * Function to return the first date of the current month or the month from the date supplied
 * @param string $date 		Date to use, if blank then use today's date.
 * @return string			First date of the month.
 */
function datetime_start_month($date = '')
{
	$today = datetime_today($date);
	$tim = strtotime($today);
	return date('Y-m-d', mktime(0, 0, 0, date('m', $tim), date('d', 1), date('Y', $tim)));
}

/**
 * @ingroup datetime_helper
 * Function to return the last date of the current month or the month from the date supplied
 * @param string $date 		Date to use, if blank then use today's date.
 * @return string			Last date of the month.
 */
function datetime_end_month($date = '')
{
	$today = datetime_today($date);
	$tim = strtotime($today);
	// first day of the next month
	$tim = mktime(0, 0, 0, date('m', $tim) + 1, date('d', 1), date('Y', $tim));
	// back on day
	return date('Y-m-d', mktime(0, 0, 0, date('m', $tim), date('d', $tim) - 1, date('Y', $tim)));
}


/**
 * @ingroup datetime_helper
 * Function to add days to a given date.
 * @param int $days			The number of days to add
 * @param string $date		The optional date to use, if blank then use today's date.
 * @return string 			The new date + $days
 */
function datetime_add_days($days, $date = '') 
{
	$today = datetime_today($date);	
	$tim = strtotime($today);
	return date('Y-m-d', mktime(0, 0, 0, date('m', $tim), date('d', $tim) + $days, date('Y', $tim)));
}

/**
 * @ingroup datetime_helper
 * Function to add months to a given date.
 * @param int $months		The number of months to add
 * @param string $date		The optional date to use, if blank then use today's date.
 * @return string 			The new date + $months
 */
function datetime_add_months($months, $date = '') 
{
	$today = datetime_today($date);	
	$tim = strtotime($today);
	return date('Y-m-d', mktime(0, 0, 0, date('m', $tim) + $months, date('d', $tim), date('Y', $tim)));
}

/**
 * @ingroup datetime_helper
 * Function to add years to a given date.
 * @param int $years		The number of years to add
 * @param string $date		The optional date to use, if blank then use today's date.
 * @return string 			The new date + $years
 */
function datetime_add_years($years, $date = '') 
{
	$today = datetime_today($date);	
	$tim = strtotime($today);
	return date('Y-m-d', mktime(0, 0, 0, date('m', $tim), date('d', $tim), date('Y', $tim) + $years));
}

/**
 * @ingroup datetime_helper
 * Function to return the number of days between two dates.
 * @param string $date_to		The end date to subtract from
 * @param string $date_from		The optional start date, if blank then use today's date.
 * @return int					The number of days difference between the two dates.
 */
function datetime_diff_days( $date_to, $date_from  = '')
{
	$date_from = datetime_today($date_from);	
	$tim_from = strtotime($date_from);
	$tim_to = strtotime($date_to);
	$diff = $tim_to - $tim_from;
	return floor($diff / (60 * 60 * 24));
	
}


/**
 * @ingroup datetime_helper
 * Function to return the start of the week date based on an optional date.
 * @param string $date					The date to calculate the first Monday from, if blank then use today's date.
 * @param boolean $round_to_next_week	If set to TRUE then calculate the start of next week.
 * @return string						The next or first monday date for that week.
 */
function datetime_start_week($date = '', $round_to_next_week = TRUE) 
{
	return datetime_start_week_day('Mon', $date, $round_to_next_week);
}

/**
 * @ingroup datetime_helper
 * Function to return the start of the week based on a day value.
 * @param string $week_day				The week day to find the start date on, can be 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'
 * @param string $date					Date to find the start week date on , if blank then use today's date.
 * @param boolean $round_to_next_week	If set to TRUE then calculate the start of next week.
 * @return string						The start of the week date.
 * 
 */
function datetime_start_week_day($week_day = '', $date = '', $round_to_next_week = true)
{
	$today = datetime_today($date);
	$week_days = array('Mon'=>1, 'Tue'=>2, 'Wed'=>3, 'Thu'=>4, 'Fri'=>5, 'Sat'=>6, 'Sun'=>7);
	$start_tim = strtotime($today);
	$start_day_no = date('N', $start_tim);
	$day_no = $start_day_no;
	if ( isset($week_days[$week_day]) ) {
		$day_no = $week_days[$week_day];
	}
	$date = date('Y-m-d', mktime(0, 0, 0, date('m', $start_tim), date('d', $start_tim) +($day_no - $start_day_no), date('Y', $start_tim)));
	if ( $date < $today ) {
		if ( $round_to_next_week ) {
			$date = datetime_add_days(7, $date);
		}
	}	
	return $date;
	
}

/**
 * @ingroup datetime_helper
 * Function to calculate the difference between two dates in months
 * @param string $date_of_birth			Date of the start date
 * @param string $date_today			Date to compare upto , if blank then use today's date.
 * @return int							Return the number of months between the two dates.
 */
function datetime_age_in_months($date_of_birth, $date_today = '')
{
	if ( preg_match('/(\d{4})-(\d{2})-(\d{2})/', $date_of_birth, $match)
				and $date_of_birth != '0000-00-00'   ) {
		$dateOfBirthCal = array('year'=>$match[1], 'month'=>$match[2], 'day'=>$match[3]);
		$today = datetime_today($date_today);
		$tim = strtotime($today);
		$todayCal = array('year'=>date('Y', $tim), 'month'=>date('m', $tim), 'day'=>date('d', $tim));

		$todayMonths = ($todayCal['year'] * 12) + ($todayCal['month'] - 1);
		$dateOfBirthMonths = ($dateOfBirthCal['year'] * 12) + ($dateOfBirthCal['month'] - 1);
		return $todayMonths - $dateOfBirthMonths;
	}
	return 0;
}

/**
 * @ingroup datetime_helper
 * Function to calculate the first date of a year/week number.
 * @param int $year			The year can be a single year or a year/week number
 * @param int $week_number	If only the year is used, you need to provide a week number as well.
 * @return string			Date of the first Monday for that Year/Week Number.
 */
function datetime_start_date_of_week_number($year, $week_number = 0)
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
	return date('Y-m-d', $start_tim);
}
	
/**
 * @ingroup datetime_helper
 * Function to calculate the difference in weeks between two year/week numbers
 * @param string $start_week		Starting year/weeknumber
 * @param string $end_week			End year/weeknumber
 * @return int						Number of weeks between the two values.
 */
function datetime_weeks_diff_in_weeks($start_week, $end_week)
{
	$weeks = 0;
	$start_year = intval(substr($start_week, 0, 4));
	$start_week_no = intval(substr($start_week, 4, 2)) - 1;

	$end_year = intval(substr($end_week, 0, 4));
	$end_week_no = intval(substr($end_week, 4, 2)) - 1;
	$weeks = ($end_year - $start_year) * 52;
	$weeks += $end_week_no;
	$weeks -= $start_week_no;
	return $weeks;

}


function datetime_add_weeks_to_week_number($year_week_number, $week_count  = 1)
{
	$week_number = intval(substr(strval($year_week_number), 0, 4)) * 52;
	$week_number += (intval(substr(strval($year_week_number), 4, 2)) - 1 );
	$week_number += $week_count;

	$year = floor($week_number / 52);
	$week_number -= $year * 52;
	return sprintf("%04d%02d", $year, $week_number + 1);
}
	
?>
