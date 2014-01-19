<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function saveListing($id = null)
{	
	$CI = &get_instance();
	
	$fields = $CI->db->list_fields($CI->config->item('listingsTable'));
	foreach($fields as $field)
	{
		if(isset($_POST[$field]))
		{
			if(is_array($_POST[$field]))
				$userInfo[$field] = $_POST[$field][0];
			else
				$userInfo[$field] = $_POST[$field];
		}
	}	
	
	$userInfo['UserID'] = $CI->session->userdata('user_id');
	$userInfo['AccessID'] = $CI->session->userdata('default_account_id');
	
	if(isset($_FILES) && (count($_FILES) > 0))
	{
		$config['upload_path'] = '.' . $CI->config->item('listingsFolder');
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '10000';
		$config['max_width']  = '1200';
		$config['max_height']  = '1200';
		$config['encrypt_name']    = TRUE;

		$CI->load->library('upload', $config);
		if ( ! $CI->upload->do_upload())
		{
			$CI->session->set_userdata('adminmsg', '<div class="error">'.$CI->upload->display_errors().'</div>');
		}	
		else
		{
			$upload_data = $CI->upload->data();
			$file = $upload_data['file_name'];
			$CI->db->set('ListingImage', $file);
			$CI->session->set_userdata('adminmsg', '<div class="success">Image Uploaded</div>');
		}
	}
	
	if($id != null)
	{
		$CI->db->set($userInfo);
		$CI->db->where('ListingID', $id);
		$CI->db->set('DateUpdated', date('Y-m-d H:i:s'));
		$CI->db->update($CI->config->item('listingsTable'));
	} else {
		$CI->db->set($userInfo);
		$CI->db->set('DateSaved', date('Y-m-d H:i:s'));
		$CI->db->set('Approved', 1);
		$CI->db->insert($CI->config->item('listingsTable'));
	}
	
	if($CI->db->affected_rows() > 0)
		$CI->session->set_userdata('adminmsg', '<div class="success">Listing Saved</div>');
	else
		$CI->session->set_userdata('adminmsg', '<div class="error">Unable to save</div>');
	
}

function deleteListing()
{
	$CI = &get_instance();
	
	$CI->db->where('ListingID', $CI->input->post('ListingID'));
	
	if($CI->session->userdata('admin'))
	{
		$CI->db->delete($CI->config->item('listingsTable'));
	}
	else
	{
		$CI->db->where('UserID', $CI->session->userdata('UserID'));
		$CI->db->set('DateDeleted', date('Y-m-d H:i:s'));
		$CI->db->update($CI->config->item('listingsTable'));
	}
	
	if($CI->db->affected_rows() > 0)
		$CI->session->set_userdata('adminmsg', '<div class="success">Listing Deleted</div>');
	else
		$CI->session->set_userdata('adminmsg', '<div class="error">Not able to delete listing.</div>');
}
