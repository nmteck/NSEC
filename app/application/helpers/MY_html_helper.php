<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Div
 *
 * Generates an div with html passed in.
 *
 * @access	public
 * @param	string
 * @param	mixed
 * @return	string
 */
if ( ! function_exists('div'))
{
	function div($html, $attributes = '')
	{
		return '<div '.$attributes . '>' . $html . '</div>';
	}
}

