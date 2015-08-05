<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class MY_Lang extends CI_Lang
{
	public function line()
	{
		$line = func_get_arg(0);
		$text = parent::line($line);
		if ( func_num_args() > 1 and preg_match('/\%/', $text)) {
	        $args = func_get_args();
	        array_shift($args);
	        $text = vsprintf($text, $args);
		}               
		return $text;		
	}

}
