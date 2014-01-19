<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

    public $pageType;

    function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->load->helper('date');
        $this->load->library('user_agent');
        $this->load->library('user');
    }

    function index($type = 'default', $key = NULL)
    {
        if ($type=='signup' && $this->agent->is_referral()) {
            $this->session->set_userdata('redirect', $this->agent->referrer());
        }

        $this->pageType = strtolower($type);
        $data = getSiteContent($this->pageType, $key);

        if($this->pageType == 'help') {
            $template='popup';
        } else {
            $template='template';
        }


        $this->load->view($template, $data);
    }

    function ses($key)
    {
        if ($key == 'admin1') {
            echo '<pre>';
            print_r($this->session->userdata);;
            echo '<pre>';
        }
    }
}

?>