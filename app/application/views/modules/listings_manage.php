<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<h2>
  <?php echo $PageTitle?>
</h2>
<style type="text/css">
#myController span{
margin-right:10px;
cursor:pointer;
padding:2px
}

#jFlowSlide{
border-top:5px solid #cecece;
padding-top: 10px;
}

#myController span.jFlowSelected {
background:#cecece;
padding:5px;
color:#000;
}


.jFlowPrev, .jFlowNext{
cursor:pointer;
}
</style>
<?php echo jFlowScript(); ?>

<div id="myController">
<span class="jFlowControl">Listings</span>
<?php echo '<span class="jFlowControl">Edit Listings</span>'; ?>
<?php echo '<span class="jFlowControl">Add New Listing</span>'; ?>
</div>


<div id="mySlides">
<div>
<?php
$listings2 = $listings;

$fields = $this->db->field_data($this->config->item('listingsTable'));
$listingsform = NULL;
foreach ($fields as $field)
{
   //$allfields[$field->name] = $field->name;
   $allfields[$field->name]['type'] = $field->type;
   $allfields[$field->name]['length'] = $field->max_length;
   $allfields[$field->name]['isKey'] = $field->primary_key;
   
   if($field->primary_key != 1)
   {
		if($this->input->post($field->name))
			$value = $this->input->post($field->name);
		else
			$value = NULL;
			
		$listingsform .= $this->filters->getSettingField($field->name, NULL, false);
   }
   
} 
if($listings==false || $listings->num_rows()==0)
{
?>
<div class="error">No listings</div>
<?php
}
else
{
	$hasresults=NULL;
	if(isset($CONTENT['listing']))
	{
		if(isset($EmployerID) && ($EmployerID != NULL) && isset($CONTENT['description']))
		{
			$MemberInfo = $this->usermodel->getUsers($EmployerID, NULL, NULL, 'employers', 'employeerID');
			$Member = $MemberInfo->row();
			
			$thisMessage = $CONTENT['description'];
			
			$folder = 'member';
			$thisMessage = str_replace("%%LISTINGLINK%%", '/'.$this->config->item('member_url').'/profile/'.$Member->username.'/', $thisMessage);
			
			if(isset($Employer->PhotoUrl))
				$thisMessage = str_replace("%%LISTINGIMAGE%%", '/images/users/'.$folder.'/'.$Member->PhotoUrl, $thisMessage);
			else
				$thisMessage = str_replace('%%LISTINGIMAGE%%', '/-/images/sg_index_media/placeholderg.gif', $thisMessage);
				
			foreach($Member as $row=>$data)
			{
				$fieldName = '<!--LISTING'.strtoupper($row).'-->';
				$fieldName2 = '%%LISTING'.strtoupper($row).'%%';
				if(strstr($CONTENT['member_description'], $fieldName) || strstr($CONTENT['description'], $fieldName2))
				{
					$fieldArray = array($fieldName, $fieldName2);
					$thisMessage = str_replace($fieldArray, $data, $thisMessage);
					//echo $data;
				}
			}
			
			$message[] = $thisMessage;
			$hasresults=TRUE;
			
			
		}
		
		foreach ($listings->result() as $result)
		{
			$thisMessage = isset($CONTENT['listing']) ? $CONTENT['listing'] : NULL;
				
			$thisMessage = str_replace(
				"%%LISTINGLINK%%", 
				'/' . $this->config->item('listingsUrl') . '/details/' . $result->ListingID, 
				$thisMessage
			);
			
			$thisMessage = str_replace("%%LISTINGDATESAVED%%", $result->DateSaved, $thisMessage);
			
			if ($result->ListingImage != '')
				$thisMessage = str_replace("%%LISTINGIMAGE%%", '/-/images/listings/'.$result->ListingImage, $thisMessage);
			else
				$thisMessage = str_replace('%%LISTINGIMAGE%%', '/-/images/sg_index_media/placeholderg.gif', $thisMessage);
				
			foreach ($result as $row=>$data) {
				$fieldName = '<!--LISTING'.strtoupper($row).'-->';
				$fieldName2 = '%%LISTING'.strtoupper($row).'%%';
				if (strstr($CONTENT['listing'], $fieldName) || strstr($CONTENT['listing'], $fieldName2)) {
					$fieldArray = array($fieldName, $fieldName2);
					$thisMessage = str_replace($fieldArray, $data, $thisMessage);
				}
			}
			
			$message[] = $thisMessage;
			$hasresults=TRUE;
		}
	}
	
	if ($hasresults==TRUE)
		echo join("\n", $message);
	else
		echo "<br /><br />No Listings Saved";
		
}
?>
</div>
<?php
if(usertype('admin')){
?>
<div>
<?php
	$this->db->order_by('DateSaved', 'desc');
	$listings2 = $this->db->get($this->config->item('listingsTable'));
	$listingseditform=NULL;
	if($listings2!=false && $listings2->num_rows()>0)
	{
		foreach($listings2->result() as $row)
		{
		?>
			<form action="" method="post" name="new" enctype="multipart/form-data">
			    <div class="admincat">
				<?php
				foreach ($fields as $field)
				{
				   //$allfields[$field->name] = $field->name;
				   $allfields[$field->name]['type'] = $field->type;
				   $allfields[$field->name]['length'] = $field->max_length;
				   $allfields[$field->name]['isKey'] = $field->primary_key;
				   $fieldName = $field->name;
				   if($field->primary_key != 1)
				   {
						echo $this->filters->getSettingField($field->name, $row->$fieldName, false);
				   }
				   else
						echo form_hidden($field->name, $row->$fieldName);
				   
				} 
				?>
			    </div>
			    <input type="submit" name="delete" value="Delete listing"/>
			    <input type="submit" name="update" value="Update listing"/><hr /><br />
			</form>
		<?
		}
		
		echo $listingseditform;
	}
	else
		echo "No listings to edit";
	?>
	</div>
	
	<div>
	<?php
		if(usertype('admin'))
		{ 
	?>
	<form action="" method="post" name="new" enctype="multipart/form-data">
	    <input type="submit" name="save" value="Add listing"/>
	    <div class="admincat">
		
	<?php echo $listingsform; ?>
	    </div>
	    <input type="submit" name="save" value="Add listing"/><hr /><br />
	</form>
	<?php 
		}
		else
		{
			echo "<div class='error'>You have reached your ".$this->session->userdata('Listings')." listing(s) limit . Please <a href='/user/membership/'>upgrade</a> your account to add addition listings to your dealer page.</div>";
		}
	?>
	</div>
<?php
}
?>
</div>
<div class="clear"></div>
