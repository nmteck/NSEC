<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NSEC_ContentModel extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function getContent($type='default', $key=NULL, $table='content_templates')
    {
        $type = strtolower($type);

        if($type == 'default' || $key != NULL)
        {
            $this->db->where('TypeURL', $key);
            $contenttable = $this->db->get('content_types')->row();

            if($contenttable == false)
            {
                $this->db->where('TypeURL', $type);
                $contenttable = $this->db->get('content_types')->row();
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
                $this->db->from('content_articles a');
                $this->db->join('content_types b', 'b.TypeID=a.ContentType');
                $this->db->where('a.ContentName', $key);
                $this->db->where('a.ContentType', $row->TypeID);


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

            $this->db->from($table);
            $this->db->where('ContentName', $key);
        }

        $result['pagetype'] = $type;
        $query = $this->db->get();

        $result['content'] = $query->row();
        if($result['content'] == false)
        {
            if($table == 'content_articles') {
                show_404("1: Page ($type/$key/$table) not found in database");
                exit;
            }

            return null;
        }

        $result['contentPage'] = $key;
        $result['pagekey'] = $result['key'] = $key;

        if(!isset($result['content']->MetaTitle) || strlen($result['content']->MetaTitle) == 0)
            $result['content']->MetaTitle = $result['content']->ContentTitle;

        $result['PageTitle'] = $result['content']->ContentTitle;
        $result['metadata'] = generate_meta($key, $result['content']->MetaTitle);

        return $result;
    }

    function getContentTable($type='content', $atts=array())
    {
        extract($atts);

        switch($type){
        case 'blocks':
            $this->db->order_by('ContentTitle', 'asc');
            if(!$this->session->userdata('developer'))
            {
                if($this->session->userdata('admin'))
                    $this->db->where("Access <=", 2);
                elseif($this->session->userdata('moderator'))
                    $this->db->where("Access <=", 1);

            }
            $content = $this->db->get('content_blocks');

        break; case 'articles':
            if($this->input->post('sortarticle'))
                $this->session->set_userdata('adminsortarticles', $this->input->post('sortarticle'));
            elseif(isset($_POST['sortarticle']))
                $this->session->unset_userdata('adminsortarticles');

            $this->db->join('content_types', 'content_types.TypeID=content_articles.ContentType');
            $this->db->order_by('ContentName', 'asc');
            $this->db->order_by('DateAdded', 'asc');

            if($this->session->userdata('adminsortarticles'))
            {
                if(is_numeric($this->session->userdata('adminsortarticles')))
                    $this->db->where('TypeID', $this->session->userdata('adminsortarticles'));
                else
                    $this->db->where('TypeURL', $this->session->userdata('adminsortarticles'));
            }

            if(isset($pagination) && isset($offset))
                $this->db->limit($pagination, $offset);

            if(!$this->session->userdata('developer'))
            {
                if($this->session->userdata('admin'))
                    $this->db->where("Access <=", 2);
                elseif($this->session->userdata('moderator'))
                    $this->db->where("Access <=", 1);

            }
            $content = $this->db->get('content_articles');

        break; case 'templates':
            $this->db->order_by('DateAdded', 'asc');
            if(!$this->session->userdata('developer'))
            {
                if($this->session->userdata('admin'))
                    $this->db->where("Access <=", 2);
                elseif($this->session->userdata('moderator'))
                    $this->db->where("Access <=", 1);

            }
            $content = $this->db->get('content_templates');

        }

        return $content;
    }

    function show_options($table, $value=NULL)
    {
        if($this->db->table_exists($table))
        {
            $query = $this->db->get($table);
            if($query == false || $query->num_rows() == 0) return false;
            echo "<select name=\"$table\">";
            foreach($query->result() as $row)
            {
                if($value!=NULL && $row->code_label == $value) $sel = ' selected'; else $sel = NULL;
                echo "<option value=\"$row->code_label\"$sel>$row->value_label</option>";
            }
            echo "</select>";
        }
    }

    function getRecentPosts($TypeID=NULL, $ArticleID=NULL)
    {

        $this->db->from('articles a');
        $this->db->join('content_types b', 'b.TypeID=a.ContentType');

        if($TypeID!=NULL)
            $this->db->where('a.ContentType', $TypeID);

        if($ArticleID!=NULL)
            $this->db->where('a.ContentID', $ArticleID);

        if(!$this->session->userdata('logged_in'))
            $this->db->where('b.Protected !=', 1);

        $this->db->where('a.ContentName !=', 'index');
        $this->db->where('b.TypeID !=', 9);
        $this->db->order_by('a.DateAdded', 'desc');
        $this->db->limit(10);
        $CatContent = $this->db->get();

        $ThisContent = false;
        $ThisTemplate = NULL;

        if($CatContent!=false && $CatContent->num_rows()>0)
        {
            $ThisContentTemplate = $this->contentmodel->getContent('article_item', NULL, 'site_content');
            foreach($CatContent->result() as $CatContentPiece)
            {
                $ThisTemplate = $ThisContentTemplate['content']->Content;
                $ThisContentPiece = $this->contentmodel->getContent($CatContentPiece->TypeURL, $CatContentPiece->ContentName);
                $ThisContentPiece['content']->Content = substr(strip_tags($ThisContentPiece['content']->Content), 0, 250);
                $ThisContentPiece['content']->ContentTitle = ucwords($ThisContentPiece['content']->ContentTitle);

                foreach($ThisContentPiece['content'] as $key=>$val)
                    $ThisTemplate = str_replace(array("<!--".strtoupper($key)."-->", "%%".strtoupper($key)."%%"), $val, $ThisTemplate);

                $ThisContent = $ThisTemplate;
            }

            return array('AllContent' => $CatContent, 'ThisContent' => $ThisContent);
        }
        else
            return false;


    }

}
?>