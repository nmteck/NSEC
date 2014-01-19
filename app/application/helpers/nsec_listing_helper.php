<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	function getListings($id = null) 
	{
		$CI = &get_instance();
		$data['curpage']='listingindex';
		$data['pagetype']='listings';
		
		if ($id != null) {
			$data['curpage'] = 'details';
			$data['pagetype'] = 'listing_details';
			$CI->db->where('ListingID', $id);
		} else {
			$CI->db->where('ForSale', 1);
		
			$CI->db->like('DateDeleted', '0000-00-00', 'after');
			$CI->db->order_by('Title');
			$CI->db->order_by('Price', 'desc');
			
			$CI->db->limit(50);
				
			$CI->db->where('t.Approved', 1);
		}
		
		$data['listings'] = $CI->db->get($CI->config->item('listingsTable') . ' t');
		$data['PageTitle'] = $CI->config->item('listingsPageTitle');
		
		return $data;
	}

	function setListingsFormValidation()
	{	
		$CI = &get_instance();
		
		//Check incoming variables
		$CI->form_validation->set_rules('Title', ' Title', 'trim|required|max_length[100]|min_length[3]');
		$CI->form_validation->set_rules('Description', 'Description', 'trim|required');
	}