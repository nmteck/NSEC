<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function live_db(){
    $CI =& get_instance();
    $DB = $CI->load->database($CI->config->item('live_db'), TRUE);
    return $DB;
}

function memberTable()
{
    $CI =& get_instance();
    return $CI->config->item('member_table');
}

function get_latest_news($limit = 2, $type = null)
{
    $CI =& get_instance();
    if($type == null) {
        $type = $CI->config->item('news_content_urls');
    }
    $CI->db->select('c.ContentName');
    $CI->db->order_by('c.DateAdded', 'desc');
    $CI->db->limit($limit);
    $CI->db->join('content_types ct', 'c.ContentType=ct.TypeID');
    $CI->db->where_in('ct.TypeURL', $type);
    $CI->db->where('c.ContentName != ', 'index');
    $list = $CI->db->get('content_articles c');

    return $list;
}