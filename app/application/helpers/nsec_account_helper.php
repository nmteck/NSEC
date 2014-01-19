<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function update_entity($type='users', $params=array())
{
    $CI =& get_instance();
    extract($params);

    if(!isset($data) || !is_array($data) || count($data) == 0 || !isset($key) || !isset($key_value)) {
        return false;
    }

    $CI->db->where($key, $key_value);
    $CI->db->set($data);
    $CI->db->update($type);

    return $CI->db->last_query();
}

function getUserAccountInfo($var = 'default_account_id', $id=NULL, $type ='users')
{
    $CI =& get_instance();
    $vars = null;

    if (is_logged_in()) {
        if ($id == NULL) {
            $id = $CI->session->userdata('user_id');
        }

        $userKey = $id . $type;

        if (isset($CI->userAccountInfo) && is_array($CI->userAccountInfo) && array_key_exists($userKey, $CI->userAccountInfo)) {
            $userinfo = $CI->userAccountInfo[$userKey];
        } else {
            if ($id == NULL) {
                $CI->db->where('username', $CI->session->userdata('username'));
            }

            $CI->db->where('user_id', $id);

            if (($var == 'default_account_id') && !is_array($var)) {
                $CI->db->select('users.' . $var);
            }

            $CI->db->select('users.*, accounts.account_type, accounts.account_name');
            $CI->db->join('accounts', 'accounts.account_id=users.default_account_id');
            $userinfo = $CI->db->get('users')->row();

            $CI->userAccountInfo[$userKey] = $userinfo;
        }

        if (is_array($var)) {
            $vars = array();

            foreach ($var as $key) {
                $vars[$key] = isset($userinfo->{$key}) ? $userinfo->{$key} : NULL;
            }
        } else {
            $vars = isset($userinfo->{$var}) ? $userinfo->{$var} : NULL;
        }
    }

    return $vars;
}

function usertype($type){
    $CI =& get_instance();

    if (isset($CI->userType) && is_array($CI->userType) && in_array($type, $CI->userType)) {
        return true;
    } elseif(strtolower(getUserAccountInfo('account_type')) == strtolower($type)) {
        $CI->userType[] = $type;
        return true;
    } else
        return false;
}

function checkUserAccessByRef($type)
{
    $CI =& get_instance();

    if (!isset($CI->userAccessLevels) || !is_array($CI->userAccessGroups)) {
        getUserAccessData();
    }
    if(in_array($type, $CI->userAccessLevels) || array_key_exists($type, $CI->userAccessLevels)) {
        return true;
    }

    return false;
}

function getUserAccessData($id = null) {
    $CI = &get_instance();

    if (!isset($CI->userAccessGroups) || !is_array($CI->userAccessGroups) || !isset($CI->userAccessGroups) || !is_array($CI->userAccessGroups)) {
        $access = getUserAccountInfo('AccessLevels', $id);
        $accessLevelArray = explode(',', $access);

        $CI->db->order_by('AccessName');
        $accessLevelData = $CI->db->get('access_types');

        foreach ($accessLevelData->result() as $accessLevel) {
            $CI->userAccessGroups[$accessLevel->AccessLevelID] = $accessLevel->AccessName;
            if (in_array($accessLevel->AccessLevelID, $accessLevelArray)) {
                $CI->userAccessLevels[$accessLevel->AccessLevelID] = $accessLevel->AccessRef;
                $CI->userAccessTypes[] = $accessLevel->AccessLevelID;
            }
        }
    }

    return array($CI->userAccessGroups, $CI->userAccessTypes);
}

/* End of file nsec_account_helper.php */
/* Location: ./application/helpers/nsec_account_helper.php */

