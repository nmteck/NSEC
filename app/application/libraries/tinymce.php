<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* TinyMCE Inclusion Class
*
* @package        CodeIgniter
* @subpackage    Libraries
* @category    WYSIWUG
* @author        WackyWebs.net - Tom Glover
* @link        http://codeigniter.com/user_guide/libraries/zip.html
*/

class Tinymce {
/*
* Create Head Code - this converts $mode value to TinyMCE editors
* $Mode is the mode TinyMCE runs in - Please view TinyMCE manual for more detail
* $Theme is this style of running, eg advance or basic, defult advance
* $ToolLoc is the vertical location of the toolbar, Defult = 'top'
* $ToolAlign is the Horizontal Location of the toolbar, DeFult = 'left'
* $Resizeabe - Can the Client resize it on there web page.
* use in controllers like so:
* $data ['head'] = $this->tinymce->createhead('mode','theme','toolbar loc','toolbar align','resizable')
*/
    function Createhead($Mode = 'textareas', $Theme = 'advanced', $ToolLoc = 'Top', $ToolAlign = 'left', $Resizable = TRUE)
    {
		$ci     =& get_instance();
		$baseJs = $ci->config->item('base_url').'/scripts';
		$jscript = '<script src="/-/js/tiny_mce/tiny_mce.js" type="text/javascript" ></script>';
		$jscript .= '<script language="javascript" type="text/javascript">'."\n";
        $jscript .= <<<EOF
            tinyMCE.init({
				// General options
				mode : "$Mode",
				elements : "htmleditor, htmleditor2, TinyMCE",
				theme : "$Theme",
				skin : "o2k7",
				plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,autosave,imagemanager",

// Theme options
        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,


		
				// Example content CSS (should be your site CSS)
				//content_css : "/-/css/style.css",
				force_p_newlines : false,
				convert_urls : false,
				extended_valid_elements : "a[name|href|target|title|onclick|rel|class],img[id|class|src|border=0|usemap|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],map[name|id],area[shape|coords|onmouseover|onmouseout|alt],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],form[name|method|action|id|class|onsubmit],script[language|type|src],param[name|value],input[name|id|type|value|onchange|onclick|onfocus|size|checked|selected|src|width|height],select[name|id|multiple|size|onselect|onchange],option[name|id|type|value|checked],textarea[name|id|cols|rows],object[type|data|height|width|classid|codebase|title|id|name|class],param[name|value],embed[src|quality|pluginspage|type|width|height|flashvars|allowfullscreen|allowscriptaccess],iframe[align|height|width|src|scrolling|name|frameborder],link[rel|href|media],div[align|style|id|class|border],style",
				
	
				// Drop lists for link/image/media/template dialogs
				template_external_list_url : "lists/template_list.js",
				external_link_list_url : "lists/link_list.js",
				external_image_list_url : "lists/image_list.js",
				media_external_list_url : "lists/media_list.js",
		
				// Replace values for the template plugin
				template_replace_values : {
					username : "Some User",
					staffid : "991234"
				}
			});
 

			function toggleEditor(id)
			{
				if (!tinyMCE.get(id))
					tinyMCE.execCommand('mceAddControl', false, id);
				else
					tinyMCE.execCommand('mceRemoveControl', false, id);
			}
        
EOF;
            $jscript .= '</script>';
			
			return $jscript;
    }
/*
* Create Text Box
* Does not have to use variable in creation as can just return textarea, without
* $FullCode - True = Outputs full text area codein form tag! False = just the Tag no         
* Form Wrap - Defult = False
* $Methord - Post or Get - Required if FC=True - String
* $Action - Controller to Call on Submission - Required if FC=True - String - Can use
* URL Helper
* $data ['head'] = $this->tinymce->createhead('Fullcode','Methord','Action')
*/
    function Textarea($FullCode = FALSE, $Action = '', $Text = '', $type='default', $info='', $Method = "POST")
    {
    	$ci     =& get_instance();
		$size = '70';
			
		$ci->db->order_by('TypeName');
		$articletypes = $ci->db->get('content_types');
		$accesstypes = $ci->db->get('access_types');
		$options = '';
		
		$contentArea = "<textarea name=\"TinyMCE\" cols=\"70\" rows=\"20\">$Text</textarea><div><a href=\"javascript:toggleEditor('TinyMCE');\">Add/Remove editor</a></div><br />";
		
        if ($FullCode === TRUE){
			$mce  = <<<HTML
			<form action="$Action" method="$Method" name="content" id="contentManagerForm">
				<div class="contentButtons">
					<input name="seo"id="contentDetailsButton" data-value="contentMainContainer" type="button" value="Content Details">
					<input name="seo"id="seoButton" data-value="contentSeoContainer" type="button" value="SEO">
					<input name="Submit" id="saveContentButton" type="submit" value="Save Content">
				</div>
HTML;
			
			if(is_array($info))
			{
				$mce .= "Title:<br><input name=\"title\" value=\"$info[PageTitle]\" type=\"text\" size=\"$size\"><br><br>";
				$mce .= "URL/Short Name:<br><input name=\"page\" value=\"$info[page]\" type=\"text\" size=\"$size\">\n<br><br>";				
					
				if($ci->session->userdata('developer'))
				{
					
					$mce .= "<div class=\"allsections\">";
				
					$mce .= $this->showDivWithSelections(FALSE, 'access_type', $info, $accesstypes, 'Restrictions', 'AccessLevelID', 'AccessName', TRUE);
					//$mce .= $this->showDivWithSelections(TRUE, 'access', $info, $articletypes, 'Career Field', 'CareerFieldID', 'CareerName');	
					$mce .= "</div>";
				}
				
				if($type!='blocks' && $type!='templates')
				{
					
					$mce .= $this->showDivWithSelections(FALSE, 'articletype', $info, $articletypes, 'Article Type', 'TypeID', 'TypeName');	
					
					$mce .= <<<HTML
					<div style="clear:left; margin-bottom:10px;" ></div>
					{$contentArea}
					<div class="contentSeoContainer">
						Meta Title: (optional)<br><input name="metatitle" value="$info[title]" type="text" size="$size"><br><br>
						Meta Tags: (optional)<br><input name="metakeywords" value="$info[keywords]" type="text" size="$size"><br><br>
						Meta Desc: (optional)<br><input name="metadesc" value="$info[description]" type="text" size="$size">
					</div>
HTML;
				}
				elseif($type == 'templates')
				{
					$mce .= <<<HTML
					{$contentArea}
					<div class="contentSeoContainer">
						Meta Title: (optional)<br><input name="metatitle" value="$info[title]" type="text" size="$size"><br><br>
						Meta Tags: (optional)<br><input name="metakeywords" value="$info[keywords]" type="text" size="$size"><br><br>
						Meta Desc: (optional)<br><input name="metadesc" value="$info[description]" type="text" size="$size">
					</div>
HTML;
				}
				else
					$mce .= $contentArea;
					
			}
			else
			{
				if($type=='file')
				{
					$mce .= "Filename:<br><input name=\"filename\" value=\"\" type=\"text\" size=\"\">.php<br><br>";
					$mce .= $contentArea;
				}
				else
				{
					
					if($type=='articles')
					{
						foreach ($articletypes->result() as $articletype) {
							$options .= "<option value=\"$articletype->TypeID\" data-value=\"$articletype->TypeURL\">$articletype->TypeName</option>\n";
						}
						
						$mce .= <<<HTML
						<div id="contentMainContainer" class="contentManagerContainer">
							<div id="contentDetails">
								<div>Title: <input name="title" id="contentTitle" type="text" size="{$size}" validation="required"></div>
								<div class="contentDetailsUrl">Url: <a id="editContentName" href="#">
									<span id="contentPageHost">{$ci->config->item("base_url")}</span><span id="contentPageType"></span><span id="contentPageUrl"></span>
								</a>
								<input name="page" id="contentPage" type="hidden">
								</div><br />
							</div>
							
							<div class="articletype">
								Type: <select name="articletype" id="contentType">
									<option value="0">Select Type</option>
									<!--option value="" id="createArticleType">Create New...</option-->
									{$options}
								</select>
							</div>
							
							<div style="clear:left; margin-bottom:10px;" ></div>
							{$contentArea}
						</div>
						
						<div id="contentSeoContainer" class="contentManagerContainer" style="display: none;">
							Meta Title: (optional)<br><input name="metatitle" type="text" size="$size"><br><br>
							Meta Kewords: (optional)<br><textarea name="metakeywords" cols="40" rows="3"></textarea><br><br>
							Meta Desc: (optional)<br><textarea name="metadesc" cols="40" rows="3"></textarea>
						</div>
HTML;
					} else {
						$mce .= "<b>Title:</b> <input name=\"title\" value=\"\" type=\"text\" size=\"$size\"><br><br>";
						$mce .= "<b>Name:</b> <input name=\"page\" value=\"\" type=\"text\" size=\"$size\">\n<br><br>";
						$mce .= $contentArea;
					}
				}
			}
			
			$mce .= "</form>";
        	return $mce ;// Outputs to view file - String
        }else{
			$mce  = "<textarea name=\"TinyMCE\" cols=\"30\" rows=\"50\">$Text</textarea>";
			return $mce ;// Outputs to view file - String
        }
    }
	
	function getArticleArray($id, $type)
	{
    	$ci =& get_instance();
		
		$ci->db->where('ArticleID', $id);
		$query = $ci->db->get("article_".$type."_xref");
		
		$array = array();
		$count = 0;
		
		foreach($query->result() as $row)
		{
			$result[$count++] = $row->ID;
			/*print_r($row);
			exit;*/
		}
		
		if(!isset($result))
			return $array;
		else
		{
			//print_r($result);
			//exit;
			return $result;
		}
	}
	
	function showDivWithSelections($getArray, $type=NULL, $info, $object, $heading=NULL, $id=NULL, $title=NULL, $AddDefaultOption=FALSE)
	{
    	$ci =& get_instance();
		$div=NULL;
		
		if(isset($item[$id]) && $getArray)
		{
			$div .= "<div class=\"sections\">";
			$div .= "$heading:<br>";
			$div .= "<select name=\"".$type."[]\" size=\"5\" multiple >";
			$div .= "<option value=\"0\">None</option>\n";
			$array = $this->getArticleArray($info['id'], $type);
			foreach($array->result() as $item)
			{
				print_r($item);
				exit;
				if(in_array($object->$id, $array)) $sel = ' selected'; else $sel = NULL;
				$div .= "<option value=\"".$type->$id."\"$sel>$item[$title]</option>\n";
			}
			$div .= "</select>\n";
			$div .= "</div>";
			
		}	
		elseif($getArray == FALSE)
		{
			$div .= "<div class=\"$type\">";
			$div .= "$heading:<br>";
			$div .= "<select name=\"$type\">";
			
			if($AddDefaultOption)
				$div .= "<option value=\"0\">None</option>\n";
				
			foreach($object->result_array() as $item)
			{
				if(isset($info[$type]) && $info[$type] == $item[$id]) $sel = ' selected'; else $sel = NULL;
				$div .= "<option value=\"$item[$id]\"$sel>$item[$title]</option>\n";
			}
			$div .= "</select>\n";
			$div .= "</div>";
		}					
		
		return $div.'<br>';
	}
}
?>
