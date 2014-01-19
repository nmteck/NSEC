<?php

function getNsecSiteSetting($setting_name) {
    $CI = &get_instance();

    $CI->db->where('setting_name', $setting_name);
    $setting = $CI->db->get('nsec_settings')->row();

    return $setting;
}