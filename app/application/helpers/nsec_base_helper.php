<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function is_logged_in(){
    $CI =& get_instance();

    $loggedIn = false;
    if ($CI->session->userdata('logged_in')) {
        $loggedIn = true;
    }

    return $loggedIn;
}

function getUserProfile($id)
{
    $CI =& get_instance();
    $CI->load->library('user');

    $userprofile = new User();
    $profile = $userprofile->getUserProfile($id);
}

function array_to_object($array = array())
{
    if (!empty($array)) {
        $data = new stdClass();
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $data->{$key} = array_to_object($val);
            } else {
                $data->{$key} = $val;
            }
        }
        return $data;
    }
    return false;
}

function getLoggedInUserProfile()
{
    $CI =& get_instance();
    $CI->load->library('user');

    getUserProfile(
        $CI->session->userdata('id')
    );
}

function is_homepage($content)
{
    $isHomepage = false;
    if(isset($content->TypeID)
        && $content->TypeID == 1
        && $content->ContentName == 'index'
    ) {
        $isHomepage = true;
    }

    return $isHomepage;
}

function is_admin_section($data = array())
{
    $CI =& get_instance();
    return in_array(
        $data['section'],
        $CI->config->item('restricted_sections')
    );
}

function generate_meta($page='home', $title=NULL, $data=array())
{
    extract($data);
    $metadata = array();

    $metadata['title'] = $title;
    $metadata['section'] = url_title($page);
    $metadata['subsection'] = isset($subsection)
        ? $subsection
        : NULL;

    return $metadata;
}

function check_for_last_page()
{
    $CI =& get_instance();
    $CI->load->library('user_agent');

    if (!$CI->agent->is_referral()) {
        $referrer = trim(
            str_replace(
                base_url(),
                '',
                $CI->agent->referrer()
            ),
            '/'
        );
        echo '<input name="last_page" type="hidden" value="'.$referrer.'" />';
    }
}

function loadfile($url)
{

    $ch = curl_init ();
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
    $content = curl_exec ($ch);
    curl_close ($ch);

    return $content;
}

function format_phone($phone)
{
    if (substr($phone, 0, 1) == "1") {
        $phone = substr($phone, 1);
    }

    $phone = preg_replace("/[^0-9]/", "", $phone);

    if (strlen($phone) == 7) {
        $phone = preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
    } elseif(strlen($phone) == 10) {
        $phone = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
    }

    return $phone;
}

function jsonHeader(){
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: text/html');
}

function convertBool($val)
{
    return (!$val)
        ? 'No'
        : (($val)
            ? 'Yes'
            : $val
        );
}