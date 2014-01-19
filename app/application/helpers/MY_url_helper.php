<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Div
 *
 * Generates an div with html passed in.
 *
 * @access    public
 * @param    string
 * @param    mixed
 * @return    string
 */

if ( ! function_exists('redirect'))
{
    function redirect($uri = '', $method = 'location', $http_response_code = 302)
    {
        switch($method)
        {
            case 'refresh'    : header("Refresh:0;url=".$uri);
                break;
            default            : header("Location: ". base_url() . $uri, TRUE, $http_response_code);
                break;
        }
        exit;
    }
}