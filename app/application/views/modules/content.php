<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if($curpage == 'home')
{
	$pagekey = 'blog entries';
	$section = 'articles';
	$msg=NULL;
	
	//echo "<div id=\"welcome\"><span id=\"name\">".$this->session->userdata('first_name').' '.$this->session->userdata('last_name')."</span>, welcome to your homepage</div>";
}
	

	
echo "<div id=\"posts\">";
echo $msg;
echo "<h2>Content</h2>";
echo "<a href=\"/admin/newpage/articles/\">Create new page</a>";

?>
<div class="articletype">Sort Articles:<br>
<form name="sortarticles" action="" method="post">
<select name="sortarticle" onchange="document.sortarticles.submit()">
<option value="0">Show All</option>
<?php
	$articletypes = $this->db->get('content_types');
	foreach($articletypes->result() as $thisarticletype)
	{
		if($thisarticletype->TypeURL == $this->session->userdata('adminsortarticles') || $thisarticletype->TypeID == $this->session->userdata('adminsortarticles'))
			$sel = 'selected'; else $sel = NULL;
		echo "<option value='$thisarticletype->TypeURL'$sel>$thisarticletype->TypeName</option>\n";
	}

?>
</select>
</form><br />
</div>
<?php
echo $this->pagination->create_links();
$count = 0;
foreach ($table->result() as $row)
{	
	$Folder = NULL;
	
	if($row->TypeURL!=$this->config->item('IndexCategory'))
		$Folder = "$row->TypeURL/";		
	
	echo "<div class=\"posts\" width='100'>";
	echo "<form action=\"/admin/deletepage/$section/$row->TypeID\" method=\"post\" onsubmit=\"javascript: return confirmDelete()\" width='100'>";
	echo " <input type=\"image\" src=\"/-/images/delete.png\" /> ";
	echo " <input type=\"hidden\" name=\"postid\" value=\"".$row->ContentID."\" />";
	echo " <a class=\"editlink\" href='/admin/editpage/articles/$row->TypeID/".$row->ContentName."' >";
	echo " <img src=\"/-/images/edit-icon.gif\" border=\"0\" width=\"15px\" /></a> ".++$count.".";
	echo ucfirst($row->ContentTitle);
	echo " <sub>(".anchor($Folder.$row->ContentName, ucfirst($row->ContentName), array('target'=>'_preview')).")</sub> ";
	echo " <sub>".ucfirst($row->TypeName)."</sub>";
	echo "</form>";
	echo "</div>";
	$results=TRUE;
}
if($count == 0) echo "<br /><br />No articles added yet";
echo "</div><br /><hr />";
			
$count=0;
$sitecontent = NULL;
echo "<div id=\"admin-content\">";
echo "<h2>Site Content/Pages</h2>";
echo "<a href=\"/admin/newpage/templates/\">Create new template</a>";
foreach ($content->result() as $row)
{
	$sitecontent .= "<div class=\"content\" width='100'>";
	$sitecontent .=  "<form action=\"".site_url('/admin/deletepage/content/')."\" method=\"post\" onsubmit=\"javascript: return confirmDelete()\" width='100'>";
	$sitecontent .=  " <input type=\"image\" src=\"/-/images/delete.png\" />";
	$sitecontent .=  " <input type=\"hidden\" name=\"postid\" value=\"".$row->ContentID."\" />";
	$sitecontent .=  "<a class=\"editlink\" href='".site_url('/admin/editpage/templates/'.$row->ContentName)."' >";
	$sitecontent .=  "<img src=\"/-/images/edit-icon.gif\" border=\"0\" width=\"15px\" /></a> ".++$count.".  ";
	$sitecontent .=  ucfirst($row->ContentTitle);
	$sitecontent .=  "</form>";
	$sitecontent .=  "</div>";
}
echo $sitecontent;
$section=NULL;
echo "</div><br /><hr />";
	
		
$count=0;
$sitecontent = NULL;
echo "<div id=\"sitecontent\">";
echo "<h2>Blocks</h2>";
echo "<a href=\"/admin/newpage/blocks/\">Create new block</a>";

foreach ($site_content->result() as $row)
{
	$sitecontent .= "<div class=\"sitecontent\">";
	$sitecontent .=  "<form action=\"/admin/deletepage/site_content/\" method=\"post\" onsubmit=\"javascript: return confirmDelete()\" width='100'>";
	$sitecontent .=  "<input type=\"image\" src=\"/-/images/delete.png\" />";
	$sitecontent .=  "<input type=\"hidden\" name=\"postid\" value=\"".$row->ContentID."\" />";
	$sitecontent .=  "<a class=\"editlink\" href='/admin/editpage/blocks/".$row->ContentName."' >";
	$sitecontent .=  "<img src=\"/-/images/edit-icon.gif\" border=\"0\" width=\"15px\" /></a> ".++$count.".  ";
	$sitecontent .=  ucfirst($row->ContentTitle);
	$sitecontent .=  "</form>";
	$sitecontent .=  "</div>";
}
echo $sitecontent;
echo "</div>";
?>