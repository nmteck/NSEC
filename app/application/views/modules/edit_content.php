<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<div id="contentManagerWrapper" style="float:left; width:640px;">
<h2><?php echo $PageTitle?></h2>
<?php echo $mce?>
</div>

<div id="content_menu" style="float:left; width:250px;">
<?php
      if(is_logged_in())
    {
            $previewLink=NULL;
            if(isset($articletype))
            {
                $previewLink = $articletype;
                $this->db->where('TypeID', $articletype);
                $this->db->select('*');
                $previewLinkInfo = $this->db->get('content_types');
                if($previewLinkInfo != false && $previewLinkInfo->num_rows>0)
                {
                    $info = $previewLinkInfo->row();
                    $url = $info->TypeURL;
                    if($url == $this->config->item('IndexCategory'))
                    {
                        if($page == 'home')
                            $previewLink = '';
                        else
                            $previewLink = $page;
                    }
                    else
                        $previewLink = strtolower($url).'/'.$page;
                }

                echo anchor("$previewLink", 'Preview Page', array('target' => '_preview'));
                $table = $this->nsec_contentmodel->getContentTable('articles', array('pagination'=>NULL, 'offset'=>NULL));
                $curpage = 'home';
                $msg = '';

                include "content.php";
            }

    }
?>
</div>
<div style="clear:both;"></div>