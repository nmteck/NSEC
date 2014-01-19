<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nsec_userprofile extends CI_Model
{	
	public $id;	
	
	public $logged_in = true;
	
	public $profileData;
	
	private $_fields = array(
		'account_type',
		'email_address',
		'username',
		'contact_name',
		'default_account_id',
		'active',
		'comments',
		'date_created'
	);
	
	public function __construct($id = null)
	{
		parent::__construct();
		$this->id = $id;
		
		$this->profileData = getUserAccountInfo($this->_fields, $id);
	}
	
}