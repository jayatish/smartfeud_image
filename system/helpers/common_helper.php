<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Number Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/number_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Formats a numbers as bytes, based on size, and adds the appropriate suffix
 *
 * @access	public
 * @param	mixed	// will be cast as int
 * @return	string
 */
if ( ! function_exists('invoice_number'))
{
	function invoice_number($length)
	{
		$CI =& get_instance();

		$var_MaxLength = $length;
		$activation_code = "";
		$var_Possible = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$i=0;
		while(($i < $var_MaxLength)&&(strlen($var_Possible) > 0))
		{
		$i++;
		// get rand character from possibles
		$var_Character = substr($var_Possible, mt_rand(0, strlen($var_Possible)-1), 1);
		// delete selected char from possible choices
		$var_Possible = preg_replace("/$var_Character/", "", $var_Possible);
		$activation_code .= $var_Character;
		}
		return $activation_code;
	}
}


/* End of file number_helper.php */
/* Location: ./system/helpers/number_helper.php */