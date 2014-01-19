<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Listings extends CI_Controller {

    public $pageType = 'listings';

    function __construct()
    {
        parent::__construct();
        $this->load->library('userprofile');
        $this->load->library('form_validation');
        $this->load->helper('listing_helper');
    }

    function index($type='forsale', $id=NULL)
    {
        $data = getListings($id);
        $data['metadata'] = generate_meta(
            'listings',
            $this->config->item('listingsPageTitle'),
            array(
                'subsection' => $data['pagetype']
            )
        );

        $data['contentPage'] = 'listings_index';
        $this->load->view('account', $data);
    }

    function manage($listingid=NULL)
    {
        if(!$this->session->userdata('logged_in')) {
            redirect('login');
        }

        $this->load->helper(
            array(
                'listing',
                'account',
                'admin'
            )
        );
        $this->load->model('filters');

        if(isset($_POST['Title']))
        {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            setListingsFormValidation();
        }

        if($this->form_validation->run() == false)
        {

        } else {
            if ($this->input->post('update') || $this->input->post('save')) {
                saveListing($this->input->post('ListingID'));
            } elseif($this->input->post('delete')) {
                deleteListing($listingid);
            }
        }

        $this->db->order_by('DateSaved', 'desc');
        $data = getListings($listingid);

        $data['curpage'] = 'listings';
        $data['contentPage'] = 'listings_manage';
        $data['PageTitle'] = $this->config->item('SiteName')." - Manage Listings";

        $data['metadata'] = generate_meta(
            $data['curpage'],
            $data['PageTitle'],
            array(
                'subsection' => $data['contentPage']
            )
        );

        $this->load->view('account', $data);
    }
}

?>