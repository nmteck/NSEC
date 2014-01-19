<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NSEC_AjaxModel extends CI_Model
{
	function __construct()
	{
       		parent::__construct();
       		$this->load->helper('nsec_query');
	}

	function getjsonheader()
	{
	  	header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		//header('Content-type: application/json');
		header('Content-type: text/html');
	}
	
	function findLocations($state = '', $city = '')
	{
	  	$sql = "SELECT city, state_name, state_prefix FROM zip_code ";
	  	$DB = live_db();
	  	if(str_replace('-', ' ', $state) != '') {
	  		$state = str_replace('_', ' ', $state);
	  	
		 	$where[] = " (state_prefix like '".strtoupper($state)."%'"
		  	. " OR state_name like '".trim(ucwords($state))."%'"
			. " OR state_name like '".trim($state)."%') "
		 	. " AND (state_name not like 'Armed%')";
	  	}
	  
	  	if(str_replace('-', '', $city) != '')
	  		$where[] = ($state != '') ? " and city like '".ucwords(trim($city))."%'" : " city like '".ucwords(trim($city))."%'";
	  
	  	if(isset($where)) {
	  		$sql .= " WHERE ".join(NULL, $where);
	  	} else {
	  		$sql .= " WHERE state_name not like 'Armed%' ";
	  	}
	  	
	  	if ($city != '') {
	  		$sql .= "  GROUP BY city, state_prefix ORDER BY state_prefix, city"; // This will get all cities possible
	  	} else {
			$sql .= "  GROUP BY state_prefix ORDER BY state_prefix"; // This will get all states possible
	  	}
	  
	  
	  	$sql .= " limit 30";
	  
	  	$query = $DB->query($sql);
	  return $query;
  	}
	
	function relay($cust_id = NULL)
	{
	  	$DB = live_db();
		foreach($_POST as $key=>$val)
		{
			$PostResponse[] = $key.": ".$val;
			$PostString[] = $key."=".urlencode($val);
		}
	  
		$memberinfo = explode(' -- ', $this->input->post('x_description'));
		$DB->where('EmployerMembershipID', $memberinfo[0]);
		$MembershipType = $DB->get('employers_memberships');
		if($MembershipType!=false && $MembershipType->num_rows()==1)
		{
			$Membership = $MembershipType->row();
			$listings = $Membership->number_of_listings;
			$membertypeid = $Membership->EmployerMembershipID;
		}
		else
		{
			$listings = 0;
			$membertypeid = 2;
		}
		
		$this->db->where('session', $cust_id);
		$signup_data = $this->db->get('signup_data');
		
		if($signup_data != false && $signup_data->num_rows() == 1)
		{
			$userdata = $signup_data->row();
			$userdata = (array) json_decode($userdata->data);
			$signupResponse = $this->organizationprofile->create($userdata, false, 'employers', 1, 1);
			
			if(!is_array($signupResponse))
			{		
				$DB->set(
					array(
						'UserID'		=> $signupResponse,
						'Post'			=> join("&", $PostString),
						'Type'			=> 2,
						'DateTime'		=> date('Y-m-d H:i:s'),
						'listings'		=> $listings,
						'Payment'		=> $this->input->post('x_amount'),
						'Processed'		=> $this->input->post('x_response_subcode'), 
						'Valid'			=> $this->input->post('x_response_code'), 
						'Response'		=> $this->input->post('x_response_reason_text') ? $this->input->post('x_response_reason_text') : 'NO RESPONSE'
					)
				);
				
				$DB->insert('signups');
						
				$DB->where('username', str_replace('gt_', '', $this->input->post('x_cust_id')));
				$userinfo = $DB->get('employers')->row();

				$DB->set('status', 1);
				$DB->set('payed', 1);
				$DB->set('membertype', $membertypeid);
				$DB->where('access_level_id', $signupResponse);
				$DB->update('employers');

				echo $this->input->post('x_response_reason_text');
			
				echo '<br><br>Your transaction has been approved. You can now access your account with the following link.';
				echo '<br><br>Click this link to login to <a href="'.site_url('login').'">'.$this->config->item('SiteName').'</a>.';
				echo '<br><br>Click this link to go back to <a href="'.site_url('signup').'">'.$this->config->item('SiteName').'</a>.';
				echo '<br /><br />NOTE: You must logout and then log back in to view your upgraded account features.';
			
				$this->db->where('session', $cust_id);
				$this->db->delete('signup_data');
			}
			else
			{
				echo join('<br>', $signupResponse);
				echo '<br><br>Your transaction has been approved but we were unable to create your account.';
				echo ' You will be contact by a sales rep to complete your signup within 48 - 72 hours.';
				echo ' Click this link to go back to <a href="'.site_url('contact').'">'.$this->config->item('SiteName').'</a>';
				echo ' and use our contact form if you have any questions.';
			}
		}
		else
		{
				echo '<br><br>Your transaction has been approved but your session timed out.<br />';
				echo ' You will be contact by a sales rep to complete your signup within 48 - 72 hours.<br />';
				echo ' Click this link to go back to <a href="'.site_url('contact').'">'.$this->config->item('SiteName').'</a>';
				echo ' and use our contact form if you have any questions.';
		}
	}
  	
	function generatePaymentForm($data)
	{
	  	$DB = live_db();
		
		$this->db->where('session', $this->session->userdata('session_id'));
		$this->db->delete('signup_data');
		
		$this->db->set('session', $this->session->userdata('session_id'));
		$this->db->set('data', json_encode($data));
		$this->db->set('date', date('Y-m-d H:i:s'));
		$this->db->insert('signup_data');
		
		$testMode = $this->config->item('AuthorizeNetTestMode');
		$amount = 0;
		
		$loginID = $this->config->item('AuthorizeNetLoginID');
		$transactionKey = $this->config->item('AuthorizeNetTransactionKey');
		$reviewInfo = $form_output = NULL;
		
		//The final price is added up and put into the form
		
		//$this->db->where('EmployerMembershipID', $this->session->userdata('membertype'));
		$membertype = $this->input->post('theamount') ? $this->input->post('theamount') : $data->membertype;
		
		$selArray[0] = 'Select one';
		
		$DB->where('Active', 1);
		$DB->where('EmployerMembershipID >=', $data->membertype);
		$DB->order_by('Order', 'asc');
		$memberships = $DB->get('employers_memberships');
		foreach($memberships->result() as $row)
		{
			if($row->EmployerMembershipID == $membertype || ($membertype == false && !isset($membership)))
				$membership = $row;
				
			$selArray[$row->EmployerMembershipID] = $row->EmployerMembershipID.' - '.$row->Membership. ' - '.$row->description. ' - '.$row->Price;
		}
		$userMemberType = $membership;
		$description = $userMemberType->Membership.' membership ('.$userMemberType->number_of_listings.' listings): $'.$userMemberType->Price;
		$customdesc = $userMemberType->EmployerMembershipID.' -- '.$userMemberType->Membership;
		
		if($this->input->post('featured')){
			$amount += 309;
			$description .= "<br />Featured Company: \$309.00";
		}
		
		if($this->input->post('spotlight')){
			$amount += 609;
			$description .= "<br />Spotlight Company: \$609.00";
		}
		
		if($this->input->post('videoselection')){
			switch($this->input->post('videoselection')){
			case 249:
				$videocost = $this->input->post('videoselection') * 3;
				$videolength = '3 months';
			break; case 199:
				$videocost = $this->input->post('videoselection') * 6;
				$videolength = '6 months';
			break; default:
				$videocost = $this->input->post('videoselection') * 12;
				$videolength = '1 year';
			}
			$amount += $videocost;
			$description .= "<br />Web Video Package ($videolength): \$$videocost.00";
		}
		
		if ($testMode)
		{
		  	$amount += $userMemberType->Price;
			$testvalue = " <INPUT type='hidden' name='x_test_request' value='1' />\n";
		}
		else
		{
		  	$amount += $userMemberType->Price;
		}

		$url = $this->config->item('AuthorizeNetUrl');
				
		$invoice = date('YmdHis'); // an invoice is generated using the date and time
		$sequence = rand(1, 1000); // a sequence number is randomly generated
		$timeStamp = time (); //Timestamp must be in UTC time (default on this server)
		
		// The following lines generate the SIM fingerprint.  PHP versions 5.1.2 and
		// newer have the necessary hmac function built in.  For older versions, it
		// will try to use the mhash library.
		if( phpversion() >= '5.1.2' )
		  $fingerprint = hash_hmac("md5", $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^", $transactionKey);
		else 
		  $fingerprint = bin2hex(mhash(MHASH_MD5, $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^", $transactionKey));
		
		$order_details = "<h2><div class='success'>Total Amount Due: \$<span id='amount'>".number_format($amount,2)."</span></div></h2> <br />\n";
		$order_details .= "<h3>Description of Services:</h3> \t\n$description <br /><br />\n";
		$order_details .= " <input type='button' id='authorize_submission' onclick=\"if(complete_submission()) document.getElementById('paymentForm').submit();\" value='Proceed with secure payment' /><br />This will take you to Authorize.Net in a new window to process your payment online\n";
		
		
		$form_output .= "<FORM method='post' id='paymentForm' action='$url' >\n";
		$form_output .= " <INPUT type='hidden' name='x_login' value='$loginID' />\n";
		$form_output .= isset($testvalue) ? $testvalue : NULL;
		$form_output .= " <INPUT type='hidden' name='x_type' value='AUTH_CAPTURE' />\n";
		$form_output .= " <INPUT type='hidden' name='x_amount' id='x_amount' value='$amount' />\n";
		$form_output .= " <INPUT TYPE='hidden' NAME='x_version' VALUE='3.1' />\n";
		$form_output .= " <INPUT type='hidden' name='x_method' value='CC' />\n";
		
		$form_output .= " <INPUT type='hidden' name='x_description' value='$customdesc' />\n";
		$form_output .= " <INPUT type='hidden' name='x_invoice_num' value='$invoice' />\n";
		$form_output .= " <INPUT type='hidden' name='x_fp_sequence' value='$sequence' />\n";
		$form_output .= " <INPUT type='hidden' name='x_fp_timestamp' value='$timeStamp' />\n";
		$form_output .= " <INPUT type='hidden' name='x_fp_hash' value='$fingerprint' />\n";
		
		
		$form_output .= " <INPUT type='hidden' name='x_company' value='".$data->company."' />\n";
		$form_output .= " <INPUT type='hidden' name='x_address' value='".$data->address1."'' />\n";
		$form_output .= " <INPUT type='hidden' name='x_city' value='".$data->city."'' />\n";
		$form_output .= " <INPUT type='hidden' name='x_state' value='".$data->state."'' />\n";
		$form_output .= " <INPUT type='hidden' name='x_zip' value='".$data->zip."'' />\n";
		$form_output .= " <INPUT type='hidden' name='x_phone' value='".$data->phone."'' />\n";
		$form_output .= " <INPUT type='hidden' name='x_cust_id' value='gt_".$data->username."' />\n";
		$form_output .= " <INPUT type='hidden' name='x_email' value='".$data->email."' />\n";
		
		$form_output .= " <INPUT type='hidden' name='x_show_form' value='PAYMENT_FORM' />\n";		
		$form_output .= " <INPUT type='hidden' name='x_relay_response' value='TRUE' />\n";
		$form_output .= " <INPUT type='hidden' name='x_relay_url' value='".base_url()."signup/relay/".$this->session->userdata('session_id')."' />\n";
		
		$form_output .= "</FORM><br /><br />\n";
		
	    return array('OrderDetails' => $order_details, 'FormFields' => $form_output);
	}
}
?>