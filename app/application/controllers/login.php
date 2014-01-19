<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

    public $pageType = 'login';

    function __construct()
    {
        parent::__construct();

        if($this->session->userdata('logged_in'))
            redirect('account');

        $this->load->library('form_validation');
        $this->load->library('simplelogin');
        $this->load->helper('nsec_account');
    }

    function index()
    {
        if($this->session->userdata('logged_in'))
            redirect('account');

        $this->form_validation->set_error_delimiters('<div class="errors">', '</div>');
        $data['Errors'] = NULL;

        //Check incoming variables
        $this->form_validation->set_rules('login_username', 'Username', 'required');
        $this->form_validation->set_rules('login_password', 'Password', 'required');

        $data['contentPage'] = 'login';
        $data['metadata'] = generate_meta('login', 'Member Login');

        if ($this->form_validation->run() == false) {

        } else {
            //Create account
            if($this->simplelogin->login($this->input->post('login_username'), $this->input->post('login_password'))) {
                if($this->input->post('last_page') && (strstr($this->input->post('last_page'), 'account') != false))
                    redirect($this->input->post('last_page'));
                else
                    redirect('account');

            } else {
                $data['Errors'] = '<div class="errors">Invalid Login Id/Password combination</div>';
            }
        }

        $this->load->view('login', $data);
    }


}
?>
