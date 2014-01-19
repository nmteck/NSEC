<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Billing extends CI_Controller {

    public $pageType = 'billing';

	var $checked = false;

	function __construct()
	{
		parent::__construct();

        $this->load->helper(
            array(
                'billing',
            )
        );
	}

	function index($action=NULL)
	{
		if($this->input->post('billing_amount'))
			$this->session->set_userdata("Payment_Amount", $this->input->post('billing_amount'));

		if($action == NULL)
			$data['contentPage'] = 'billing';
		else
		{
			$this->load->helper('paypal');
			$data['contentPage'] = 'billing_'.$action;
		}

		$data['metadata'] = generate_meta('account', 'Billing Information', array('subsection'=>'billing'));
		$this->load->view('account', $data);
	}
}

/* End of file account.php */
/* Location: ./application/controllers/account.php */
