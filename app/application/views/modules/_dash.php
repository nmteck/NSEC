<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	echo "<div class='user_dashboard'>";
	echo "<div id=\"welcome\"><span id=\"name\">".$this->session->userdata('contact_name')."</span>, welcome to your dashboard</div>";
		
	$hasresults=NULL;
	$class = NULL;
	
	echo "<div id=\"messages\">";
	if(isset($CONTENT['dashboard_item']))
	{
		foreach ($table->result() as $result)
		{
			$thisMessage = $CONTENT['dashboard_item'];
			//echo $thisMessage;
			// Get Image
			if(!$this->session->userdata('members'))
			{
				$folder = 'employers';
				$this->db->where('UserID', $result->employeerID);
				$this->db->where('PhotoType', 2);
			}
			else
			{
				$folder = 'candidates';
				$this->db->where('UserID', $result->id);
				$this->db->where('PhotoType', 1);
			}
				
			$photos = $this->db->get('users_photos');
			if($photos!= false && $photos->num_rows() == 1)
			{
				$photo = $photos->row();
				$thisMessage = str_replace("%%USERDASHIMAGE%%", '/images/users/'.$folder.'/'.$photo->PhotoUrl, $thisMessage);
			}
			else
				$thisMessage = str_replace('%%USERDASHIMAGE%%', '/images/noimage.gif', $thisMessage);
			
			$thisMessage = str_replace("%%USERDASHLINK%%", '/user/messages/view/'.$result->MessageID, $thisMessage);
			
			foreach($result as $row=>$data)
			{
				$fieldName = '<!--USERDASH'.strtoupper($row).'-->';
				$fieldName2 = '%%USERDASH'.strtoupper($row).'%%';
				$fieldName3 = '<!--'.strtoupper($row).'-->';
				if(strstr($thisMessage, $fieldName) || strstr($thisMessage, $fieldName2) || strstr($thisMessage, $fieldName3))
				{
					if((strtoupper($row) == 'MESSAGE') && strlen($data) > 30)
						$data = substr($data, 0, 30).'...';
					elseif(strstr(strtoupper($row), 'DATE') && ($data!=NULL))
					{
						$date = mysql_to_unix($data);
						$data = unix_to_human($date);
						$data = explode(' ', $data, 2);
						$data = join('<br />', $data);
					}
					$fieldArray = array($fieldName, $fieldName2, $fieldName3);
					$thisMessage = str_replace($fieldArray, $data, $thisMessage);
				}
			
				if($row == 'DateRead')	
				{
					if($data==NULL)
						$class = 'newItem';
					else
						$class = NULL;
				}
			}
			
			
			
			$thisMessage = "<div id='update' class='$class'>".$thisMessage."</div>";
			$message[] = $thisMessage;
			$hasresults=TRUE;
		}
	}
	
	if($this->session->userdata('member') && isset($CONTENT['member_dashboard']))
		echo $CONTENT['member_dashboard'];
	elseif($this->session->userdata('users') && isset($CONTENT['user_dashboard']))
		echo $CONTENT['user_dashboard'];
			
	if($hasresults==TRUE)
	{
		echo "<h2 class=\"title\">Dashboard: Latest Activity</h2>";
		echo join("\n", $message);
	}
	else
		echo "<h2 class=\"title\">Dashboard: No recent activity</h2>";
	
		
	echo "</div></div>";
		
			
?>