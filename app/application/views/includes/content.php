<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    if(strstr($ThisContent, '<!--SHOW'))
    {
        foreach($dataTables as $sKey=>$sArray)
        {
            $replacement = '<!--SHOW'.$sKey.'-->';

            if(strstr($ThisContent, $replacement))
            {
                if($sKey!='state' && $this->db->field_exists('Order', $sKey)) $this->db->order_by('Order', 'asc');
                if($this->db->field_exists('Active', $sKey)) $this->db->where('Active', 1);
                if(isset($sArray[3])) $sKey2 = $sArray[3]; else $sKey2 = $sKey;

                $QUERY = $this->db->get($sKey);
                $fielddata=NULL;

                foreach($QUERY->result_array() as $itemRow)
                {
                    if(($this->input->post($sKey2) == $itemRow[$sArray[0]]) || (is_array($this->input->post($sKey2)) && in_array($itemRow[$sArray[0]], $this->input->post($sKey2)))) $sel = ' selected'; else $sel = NULL;
                    //echo $this->input->post($sKey2).'-'.$itemRow[$sArray[0]]." $sel:: ";
                    $fielddata .= "<option value=\"".$itemRow[$sArray[0]]."\" $sel>".$itemRow[$sArray[1]]."</option>\n";
                }

                $ThisContent = str_replace("<!--SHOW$sKey-->", $fielddata, $ThisContent);
            }
        }
    }

    $ThisTemplate = NULL;
    $HideContent = FALSE;

    if(isset($pagetype) && ($pagetype!=NULL))
    {
        $this->db->where('TypeURL', $pagetype);
        $TypeProtected = $this->db->get('content_types');

        if($TypeProtected!=false && $TypeProtected->num_rows()>0)
        {
            $ContentDetails = $TypeProtected->row();
        }
    }

    if(isset($pagetype)
        && $this->config->item('StreamArticles')
        && isset($pagekey)
        && ((
            ($pagekey == 'home') && $this->config->item('StreamHome')) ||
            ($pagekey == 'index' && isset($ContentDetails) && ($ContentDetails->ModerateComments == 1))
        ) && (!$HideContent))
    {
        if($pagekey == 'home')
            $this->db->where(array('ContentType !='=>$content->TypeID, 'b.AllowComments'=>1));
        else
            $this->db->where('ContentType', $content->TypeID);

        if($pagekey == 'home' && $this->config->item('HomeStreamAmount'))
            $limit = $this->config->item('HomeStreamAmount');
        elseif($pagekey != 'home' && $this->config->item('ArticleStreamAmount'))
            $limit = $this->config->item('ArticleStreamAmount');
        else
            $limit = 10;

        $this->db->select("a.*, b.*");
        $this->db->limit($limit);
        $this->db->where('a.ContentName !=', 'index');
        $this->db->order_by('a.DateAdded', 'desc');
        $this->db->group_by('ContentID');
        $this->db->join('content_types b', 'b.TypeID=a.ContentType');
        $CatContent = $this->db->get('content_articles a');

        if($CatContent!=false && $CatContent->num_rows()>0)
        {
            $ThisContentTemplate = $this->contentmodel->getContent('article-item', NULL, 'content_templates');
            if($ThisContentTemplate != null){
                foreach($CatContent->result() as $CatContentPiece)
                {
                    $ThisTemplate = $ThisContentTemplate['content']->Content;

                    if($pagekey == 'home')
                        $ThisContentPiece = $this->contentmodel->getContent($CatContentPiece->TypeURL, $CatContentPiece->ContentName);
                    else
                        $ThisContentPiece = $this->contentmodel->getContent($content->TypeURL, $CatContentPiece->ContentName);

                    if(strlen($ThisContentPiece['content']->Content) > 500)
                    {
                        if($pagekey == 'home' || ($pagekey != 'home' && $ContentDetails->AllowComments == 1))
                            $ThisContentPiece['content']->Content = substr(strip_tags($ThisContentPiece['content']->Content), 0, 500).'...';
                    }

                    $ThisContentPiece['content']->ContentTitle = ucwords($ThisContentPiece['content']->ContentTitle);

                    foreach($ThisContentPiece['content'] as $key=>$val)
                    {
                        $ThisTemplate = str_replace(array("<!--".strtoupper($key)."-->", "%%".strtoupper($key)."%%"), $val, $ThisTemplate);
                    }

                    $ThisContent .= $ThisTemplate;
                }

                if($pagekey != 'home')
                    $ThisContent .= isset($CONTENT[$this->config->item('ContentAdsense')]) ? $CONTENT[$this->config->item('ContentAdsense')] : NULL;
            }
        }

    }

    if(strstr($ThisContent, '<!--TEMPLATE'))
    {
        foreach($CONTENT as $key => $val)
        {
            if(strstr($ThisContent, "<!--TEMPLATE$key-->"))
                $ThisContent = str_replace("<!--TEMPLATE$key-->", $val, $ThisContent);
        }
    }

    if(strstr($ThisContent, '<!--RECENTLY_ADDED-->') && isset($CONTENT['recent_listing']))
    {
        $this->db->limit(3);
        $this->db->like('DateDeleted', '0000-00-00', 'after');
        $this->db->order_by('DateSaved', 'random');
        $this->db->where('t.Approved', '1');
        $this->db->where('ForSale', '');
        $Listings = $this->db->get('member_listings t');
        $RECENTLY_ADDED = NULL;
        if($Listings!=false && $Listings->num_rows() > 0)
        {
            foreach($Listings->result() as $Listing)
            {
                $ListingDetail = $CONTENT['recent_listing'];
                $ListingDetail = str_replace("%%LISTINGLINK%%", "/listings/details/$Listing->ListingID", $ListingDetail);
                $ListingDetail = str_replace("%%LISTINGPRICE%%", number_format($Listing->Price, 2), $ListingDetail);
                if($Listing->ListingImage != '')
                    $Listing->ListingImage = $Listing->ListingImage;
                else
                    $Listing->ListingImage = '/-/images/nologo.gif';

                foreach($Listing as $sKey=>$sVal)
                {
                    $sKey = strtoupper($sKey);
                    $ListingDetail = str_replace(array("%%LISTING$sKey%%", "<!--LISTING$sKey-->"), $sVal, $ListingDetail);
                }

                $RECENTLY_ADDED .= $ListingDetail;
            }

            $ThisContent = str_replace("<!--RECENTLY_ADDED-->", $RECENTLY_ADDED, $ThisContent);
        }
    }

    $ThisContent = processFunctionsInContent($ThisContent);

    echo "<" . $this->config->item('headerTagType') . ' id="title" >' . $content->ContentTitle . '</' . $this->config->item('headerTagType') . '>';
    echo $ThisContent;

?>