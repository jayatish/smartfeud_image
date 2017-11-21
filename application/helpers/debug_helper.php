<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * array and object print
 *
 * @access	public
 * @param	array
 * @param	bool
 */
if ( ! function_exists('debug'))
{
	function debug($arr = array(), $is_die = true)
	{
		echo '<pre>';
		print_r($arr);
		echo '</pre>';
		if($is_die){
			die;
		}
	}
}

// --------------------------------------------------------------------

