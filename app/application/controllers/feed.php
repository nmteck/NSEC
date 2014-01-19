<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feed extends CI_Controller {

    public $pageType = 'feed';

    function __construct()
    {
        parent::__construct();
        $this->load->helper('xml');
    }

    function index($type=NULL, $key=NULL)
    {
        $data['encoding'] = 'utf-8';
        $data['feed_name'] = $this->config->item('SiteName');
        $data['feed_url'] = base_url();
        $data['page_description'] = 'Come and visit '.$this->config->item('SiteName');
        $data['page_language'] = 'en-ca';
        $data['creator_email'] = $this->config->item('SiteName').' Webmaster';

        $this->db->where('TypeURL', $type);
        $query = $this->db->get('article_types', $type);
        if($query!=false && $query->num_rows()==1)
        {
            $row = $query->row();
            $type = $row->TypeID;
        }
        else
            $type = NULL;

        $this->db->where('ContentName', $key);
        $query = $this->db->get('articles', $key);
        if($query!=false && $query->num_rows()==1)
        {
            $row = $query->row();
            $key = $row->ContentID;
        }
        else
            $key = NULL;


        $data['posts'] = $this->contentmodel->getRecentPosts($type, $key);

        header("Content-Type: application/rss+xml");
        $this->load->view('feed', $data);
    }
}
?>