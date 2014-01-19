<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
	function getSiteContent($type='default', $key=NULL, $table='content_templates')
	{
		$CI =& get_instance();
		$type = strtolower($type);
	
		if($type == 'default' || $key != NULL)
		{
			$CI->db->where('TypeURL', $key);
			$contenttable = $CI->db->get('content_types')->row();
			
			if($contenttable == false)
			{
				$CI->db->where('TypeURL', $type);
				$contenttable = $CI->db->get('content_types')->row();
			}
			else
			{
				$type = $key;
				$key = 'index';
			}
			
			if($contenttable != false)
			{
				if($key == NULL)
					$key = 'index';
				if($key == 'index' && $type == 'default')
					$result['homepage'] = true;
				
				$row = $contenttable;
				
				$key = strtolower($key);
				$CI->db->from('content_articles a');
				$CI->db->join('content_types b', 'b.TypeID=a.ContentType');
				$CI->db->where('a.ContentName', $key);
				$CI->db->where('a.ContentType', $row->TypeID);
				
				
				$result['pagelink'] = "$type/$key";
			}
			else
			{
				show_404("2: Page ($type/$key/$table) not found in database");
				exit;
			}
			
		}
		else
		{
			$key = $type;
				
			$CI->db->from($table);
			$CI->db->where('ContentName', $key);
		}
		
		$result['pagetype'] = $type;
		$query = $CI->db->get();
	
		$result['content'] = $query->row();
		if($result['content'] == false)
		{
			show_404("1: Page ($type/$key/$table) not found in database");
			exit;
		}
			
		$result['contentPage'] = $key;
		$result['key'] = $key;
		
		if(!isset($result['content']->MetaTitle) || strlen($result['content']->MetaTitle) == 0)
			$result['content']->MetaTitle = $result['content']->ContentTitle;
		
		$result['PageTitle'] = $result['content']->ContentTitle;
		$result['metadata'] = generate_meta(
			($key != null && $type != 'default') ? $type : $key, 
			$result['content']->MetaTitle,
			($key != null && $type != 'default') ? array('subsection' => $key) : array()
		);
		
		return $result;
	}
	
	
	function replaceVariablesInContent($content, $variables) {
		foreach($variables as $key=>$val)
		{
			$replacements = array('<!--'.strtoupper($key).'-->', '%%'.strtoupper($key).'%%');
			$content = str_replace($replacements, $val, $content);
		}
		
		return $content;
	}

	function replacePostVariablesInContent($content){
		$CI =& get_instance();
		if(isset($_POST))
		{
			foreach($_POST as $skey=>$sval)
			{
				if($CI->input->post($skey))
				{
					$textMatch = '/<input(.+)name=\"'.$skey.'\"(.+)type=\"text\"/';
					$textareaMatch = '/<textarea(.+)name=\"'.$skey.'\"(.+)>/';
					$checkboxMatch = '/<input(.+)name=\"'.$skey.'\"(.+)type=\"checkbox\"(.+)value=\"'.$sval.'\"/';
					$radioMatch = '/<input(.+)name=\"'.$skey.'\"(.+)type=\"radio\"(.+)value=\"'.$sval.'\"/';
	
					if(preg_match($textMatch, $content))
					{
						$content = preg_replace($textMatch, "<input type=\"text\" name=\"$skey\" value=\"$sval\" $1 $2 $3 $4", $content);
					}
					elseif(preg_match($textareaMatch, $content))
					{
						$content = preg_replace($textareaMatch, "<textarea name=\"$skey\" $1 $2 $3 $4 >\"$sval\" checked", $content);
					}
					elseif(preg_match($checkboxMatch, $content))
					{
						$content = preg_replace($checkboxMatch, "<input type=\"checkbox\" name=\"$skey\" value=\"$sval\" $1 $2 $3 $4 checked", $content);
					}
					elseif(preg_match($radioMatch, $content))
					{
						$content = preg_replace($radioMatch, "<input type=\"radio\" name=\"$skey\" value=\"$sval\" $1 $2 $3 $4 checked", $content);
					}
				}
			}
		}
	
		return $content;
	}
	
	function processFunctionsInContent($content){
		$CI = &get_instance();
		$regex = '/\{\{([^}]+)\}\}/';
		preg_match_all($regex, $content, $functions);
		
		for($i=0; $i<count($functions[0]); $i++)
		{
			list($helper, $functionParts) = explode(':', $functions[1][$i]);
			$params = array();
			$methodCall = explode('(', $functionParts);

			$function = $methodCall[0];
			if(isset($methodCall[1])) {
				$params = explode(',', trim($methodCall[1], ')'));
			}
			
			if(function_exists($function)) {
				$html = call_user_func_array($function, $params);
			} elseif(!function_exists($function)) {
				if(!function_exists('get_file_info')) {
					$CI->load->helper('file');
				}
				
				if(get_file_info('application/helpers/'.$helper.'_helper.php') != false) {
					$CI->load->helper($helper);
								
					if(function_exists($function)) {
						$html = call_user_func_array($function, $params);
					} else {
						$html = 'Error <!--'.$function.' doesnt exist-->';
					}
				} else {
					$html = 'Error <!--./'.$helper.'_helper.php doent exist-->';
				}
			}
			
			$content = str_replace($functions[0][$i], $html, $content);
			
		}
		
		return $content;
	}

?>
