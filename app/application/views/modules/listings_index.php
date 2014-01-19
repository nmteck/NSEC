<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<h2>
  <?php echo $PageTitle?>
</h2>


<div id="mySlides">
<div>
<?php
$listings2 = $listings;
$noListing = "<br /><br /><h3 id='noresults'>No Listings</h3>";

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
			
		//$listingsform .= $this->filters->getSettingField($field->name, NULL, false);
   }
   
} 

$count = 0;

if($listings==false || $listings->num_rows()==0)
	echo $noListing;
else
{
	$hasresults=NULL;
	if(isset($CONTENT['listing']))
	{
		
		foreach ($listings->result() as $result)
		{
			if (isset($curpage) && $curpage == 'details') {
				$otherimages = array();
					
				for($i=1; $i<10; $i++)
				{
					$imageField = 'ListingImage'.$i;
					if ( $result->{$imageField} != '' )
					{
						$otherimages[] = "<a class='car_photopreview' href='".$result->{$imageField}."'>
							<img src='".$result->{$imageField}."' title='$result->Title' border='0' width='80' height='75' />
						</a> ";
					}
				}
				
				$detailsPageContentObject = $this->contentmodel->getContent('seller', NULL, 'content_templates');
				
				if ($detailsPageContentObject != false) {
					$detailsPageContent = $detailsPageContentObject['content']->Content;
				
					$detailsPageContent = str_replace("<!--PHOTOS-->", join($otherimages), $detailsPageContent);
				
					foreach($result as $row=>$data)
					{
					//print_r($row);
						$fieldName = '<!--LISTING'.strtoupper($row).'-->';
						$fieldName2 = '%%LISTING'.strtoupper($row).'%%';
						if(strstr($CONTENT['listing'], $fieldName) || strstr($CONTENT['listing'], $fieldName2))
						{
							if($data == '')
								$data = 'n/a';
							$fieldArray = array($fieldName, $fieldName2);
							$detailsPageContent = str_replace($fieldArray, $data, $detailsPageContent);
							//echo $data;
						}
					}
				} else {
					$thisMessage = 'Unable to find listing details';
				}
				$message[$count++] = $detailsPageContent;
				
			}
			
			$thisMessage = isset($CONTENT['listing']) ? $CONTENT['listing'] : NULL;
			
			if($result->ListingImage != '')
				$thisMessage = str_replace("%%LISTINGIMAGE%%", '/-/images/listings/' . $result->ListingImage, $thisMessage);
			else
				$thisMessage = str_replace('%%LISTINGIMAGE%%', '/-/images/sg_index_media/placeholderg.gif', $thisMessage);
			
			$thisMessage = str_replace("%%LISTINGLINK%%", '/listings/details/'.$result->ListingID.'/', $thisMessage);
			$thisMessage = str_replace("%%LISTINGPRICE%%", number_format($result->Price, 2), $thisMessage);
			//$thisMessage = str_replace("%%LISTINGLINK%%", $result->WebAddress, $thisMessage);
			$thisMessage = str_replace("%%LISTINGDATESAVED%%", $result->DateSaved, $thisMessage);
			
			foreach($result as $row=>$data)
			{
			//print_r($row);
				$fieldName = '<!--LISTING'.strtoupper($row).'-->';
				$fieldName2 = '%%LISTING'.strtoupper($row).'%%';
				
				if(strstr($CONTENT['listing'], $fieldName) || strstr($CONTENT['listing'], $fieldName2))
				{
					if($data == '')
						$data = 'n/a';
					$fieldArray = array($fieldName, $fieldName2);
					$thisMessage = str_replace($fieldArray, $data, $thisMessage);
					//echo $data;
				}
			}
			$message[$count++] = $thisMessage;
			$hasresults=TRUE;
		}			
		
	}
	
	if($hasresults==TRUE) {
		if (isset($curpage) && $curpage == 'details') {
			$message[0] = str_replace('<!--LISTINGS-->', $message[1], $message[0]);
			unset($message[1]);
		}
		
		echo join("\n", $message);
	} else
		echo $noListing;
	
		
}

?>
</div>
<div class="clear"></div>
</div>
<div id="imageDialog" style="display:none;">
	<img id="imageHolder" src="" border="0" width="640" height"480" />
</div>