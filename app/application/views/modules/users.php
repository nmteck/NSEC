<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//BOF User table
echo $this->form_validation->error_string;
if($this->session->userdata('logged_in')) {
	//Grab user data from database
	
	if(count($user_array) > 0) {
		echo '<div id="user_table">';
			echo '<h4><a href="/admin/users">'.ucwords($this->config->item('user_title')).'</a> | <a href="/admin/user_create">Create User</a></h4>';
			echo '<h3>User Table</h3>';
			echo '<table width="100%">';
				echo '<tr>';
					echo '<th align="left" width="30">';
						echo 'ID';
					echo '</th>';
					echo '<th align="left" width="150">';
						echo anchor('/admin/users/sortby/username', 'Username');
					echo '</th>';
					echo '<th align="left" width="150">';
						echo anchor('/admin/users/sortby/first_name', 'Name');
					echo '</th>';
					echo '<th align="left" width="100">';
						echo anchor('/admin/users/sortby/email', 'Email');
					echo '</th>';
					echo '<th align="center" width="100">';
						echo anchor('/admin/users/sortby/status', 'Approved');
					echo '</th>';
				echo '</tr>';
				foreach($user_array as $ua) {
					
					$userid = $ua['user_id'];
					$checked = "selected";
					
					if($ua['active'] == 1)
						$Status = 'Yes';
					else
						$Status = '<b style="color:#FF0000;">No</b>';
					
					echo '<tr>';
						echo '<td>';
							if(usertype('admin')) {
								echo '<a href="/admin/user_delete/' . $userid 
								. '" onclick="return confirm(\'Are you sure you want to delete this user?\')">'
								. '<img src="/-/images/delete.png" border="0"></a> ';
							}
							
							echo $userid.'. </td>';
						echo '<td>'.anchor("admin/user_edit/$userid", $ua['username']).'</td>';
						echo '<td>'.$ua['contact_name'].'</td>';
						echo '<td>'.$ua['email_address'].'</td>';
						echo '<td align="center">'.$Status.'</td>';
					echo '</td></tr>';
				}
			echo '</table>';
		echo '</div>';
	}
}
//EOF User table
?>