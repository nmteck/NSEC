<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends MY_Controller {

    public $pageType = 'account';

    public $profile;

    function __construct()
    {
        parent::__construct();

        $this->load->library(
            array(
                'user',
                'form_validation',
                'pagination',
                'simplelogin'
            )
        );
    }

    function session()
    {
        print '<pre>';
        print_r($this->session->userdata);
        exit;
    }

    function index()
    {
        $this->dashboard();
    }

    function jsfunctions($action = 'get_campaigns', $id=NULL)
    {
        if (method_exists($this->userprofile, $action)) {
            $this->userprofile->$action($id);
        } else {
            echo 'Unable to perform action: JsFuncs ('
                . str_replace('_', ' ', $action)
                . ')</div>';
        }
    }

    function dashboard()
    {
        $data['contentPage'] = '_dash';
        $data['metadata'] = generate_meta('account', 'Manage Account', array('subsection'=>'dashboard'));
        $this->load->view('account', $data);
    }

    function faqs()
    {
        $data['contentPage'] = strtolower(getUserAccountInfo('account_type')).'_faqs';
        $data['metadata'] = generate_meta('account', 'FAQs', array('subsection'=>'faqs'));
        $this->load->view('account', $data);
    }

    function billing($action=NULL)
    {
        if($this->input->post('refill_amount'))
            $this->session->set_userdata("Payment_Amount", $this->input->post('refill_amount'));

        if($action == NULL)
            $data['contentPage'] = strtolower(getUserAccountInfo('account_type')).'_billing';
        else
        {
            $this->load->helper('paypal');
            $data['contentPage'] = 'billing_'.$action;
        }

        $data['metadata'] = generate_meta('account', 'Billing Information', array('subsection'=>'billing'));
        $this->load->view('account', $data);
    }

    function profile()
    {
        if ($this->input->post('updatePersonalInfo')) {
            $this->memberprofile->updateMemberProfile($this->session->userdata('user_id'));
        } elseif($this->input->post('updateLoginInfo')) {
            $this->memberprofile->updateMemberProfile($this->session->userdata('user_id'), 'users');
        }

        $data['Errors'] = $this->form_validation->error_string;

        $data['contentPage'] = 'profile';
        $data['metadata'] = generate_meta('account', 'My Profile', array('subsection'=>'profile'));
        $this->load->view('account', $data);
    }

    function logout()
    {
        //Logout
        $this->simplelogin->logout();
        redirect('login');
    }


}

/* End of file account.php */
/* Location: ./application/controllers/account.php */
