<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function deletePage($type)
{
    $CI= &get_instance();

    if (!$CI->input->post('postid')) {
        return;
    }

    if ($CI->db->field_exists('ContentID', "content_$type")) {
        $CI->db->where('ContentID', $CI->input->post('postid'));
        $CI->db->delete("content_$type");
    } elseif ($type != NULL) {
        $CI->db->where('id', $CI->input->post('postid'));
        $CI->db->delete($type);
    }
}

function saveContent($url, $key = null)
{
    $CI= &get_instance();

    $page_info = array(
        'ContentTitle' => $CI->input->post('title'),
        'ContentName' => $url,
        'ContentType' => $CI->input->post('articletype'),
        'Content' => $CI->input->post('TinyMCE'),
        'MetaTitle' => $CI->input->post('metatitle'),
        'MetaTags' => $CI->input->post('metakeywords'),
        'MetaDesc' => $CI->input->post('metadesc')
    );

    $date = date('Y-m-d H:i:s');

    if ($key != null) {
        $page_info['DateModified'] = $date;
        $page_info['Access'] = $CI->input->post('access_type');
        $CI->db->where('ContentID', $key);
        $CI->db->update('content_articles', $page_info);
    } else {
        $page_info['DateAdded'] = $date;
        $CI->db->insert('content_articles', $page_info);
    }

    if($CI->db->affected_rows() > 0)
        $CI->session->set_userdata('adminmsg', '<div class="success">'.ucfirst($type).' Updated</div>');
    else
        $CI->session->set_userdata('adminmsg', '<div class="error">Error updating '.$type.'</div>');
}

function saveTemplate($url, $key = null)
{
    $CI= &get_instance();

    $url = url_title($url, 'underscore');
    $page_info = array(
       'ContentTitle' => $CI->input->post('title'),
       'ContentName' => $url,
       'Content' => $CI->input->post('TinyMCE'),
       'MetaTitle' => $CI->input->post('metatitle'),
       'MetaTags' => $CI->input->post('metakeywords'),
       'MetaDesc' => $CI->input->post('metadesc')
    );

    $date = date('Y-m-d H:i:s');

    if ($key != null) {
        $page_info['DateModified'] = $date;
        $page_info['Access'] = $CI->input->post('access_type');
        $CI->db->where('ContentID', $key);
        $CI->db->update('content_templates', $page_info);
    } else {
        $page_info['DateAdded'] = $date;
        $CI->db->insert('content_templates', $page_info);
    }

    if ($CI->db->affected_rows() > 0)
        $CI->session->set_userdata('adminmsg', '<div class="success">Template Saved</div>');
    else
        $CI->session->set_userdata('adminmsg', '<div class="error">Error saving template</div>');
}

function saveBlock($url, $key = null)
{
    $CI= &get_instance();

    $url = url_title($url, 'underscore');
    $page_info = array(
       'ContentTitle' => $CI->input->post('title'),
       'Content' => $CI->input->post('TinyMCE'),
          'ContentName' => $url
    );

    $date = date('Y-m-d H:i:s');

    if ($key != null) {
        $page_info['DateModified'] = $date;
        $page_info['Access'] = $CI->input->post('access_type');
        $CI->db->where('ContentID', $key);
        $CI->db->update('content_blocks', $page_info);
    } else {
        $page_info['DateAdded'] = $date;
        $CI->db->insert('content_blocks', $page_info);
    }

    if ($CI->db->affected_rows() > 0)
        $CI->session->set_userdata('adminmsg', '<div class="success">Block Item Saved</div>');
    else
        $CI->session->set_userdata('adminmsg', '<div class="error">Error saving block item</div>');

}

function getBlockDetails($key)
{
    $CI= &get_instance();

    $CI->db->limit(1);
    $CI->db->where("ContentName", $key);
    $query = $CI->db->get("content_blocks");

    if ($query->num_rows() > 0) {
        $row = $query->row();
        $data['id'] = $row->ContentID;
        $data['PageTitle'] = $row->ContentTitle;
        $data['content'] = $row->Content;
        $data['page'] = $row->ContentName;
        $data['access_type'] = $row->Access;

        $return = array('data' => $data, 'key' => $data['id']);
    } else {
        $return = array();
    }

    return $return;

}

function getContentDetails($id = null, $key = null, $type = 'articles')
{
    $CI= &get_instance();

    $table = 'content_'.$type;;
    $CI->db->limit(1);

    if($type!='templates') {
        if ($id != null)
            $CI->db->where("ContentName", $id);

        if ($key != null)
            $CI->db->where("ContentType", $key);
    } else {
        $CI->db->where("ContentName", $key);
    }

    $query = $CI->db->get($table);

    if ($query->num_rows() > 0) {
        $row = $query->row();
        $data['id'] = $row->ContentID;
        $data['PageTitle'] = $row->ContentTitle;
        $data['title'] = $row->MetaTitle;
        $data['keywords'] = $row->MetaTags;
        $data['description'] = $row->MetaDesc;
        $data['content'] = $row->Content;
        $data['access_type'] = $row->Access;
        $data['page'] = $row->ContentName;

        if($type!='templates')
            $data['articletype'] = $row->ContentType;

        $return = array('data' => $data, 'key' => $data['id']);
    } else {
        $return = array();
    }

    return $return;
}

function setContentFormValidation()
{
    $CI = &get_instance();

    //Check incoming variables
    $CI->form_validation->set_rules('TinyMCE', 'Content');
    $CI->form_validation->set_rules('title', 'Page Title', 'trim|required|max_length[100]|min_length[3]');
    $CI->form_validation->set_rules('page', 'Page Url', 'trim|max_length[50]');
}