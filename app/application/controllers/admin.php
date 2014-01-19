<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller {

    public $pageType = 'account';

    const CONTROLLER = 'admin';

    function __construct()
    {
        parent::__construct();

        $this->load->library(
            array(
                'tinymce',
                'pagination',
                'form_validation',
                'simplelogin',
                'user'
            )
        );

        $this->load->helper(
            array(
                'nsec_admin',
                'nsec_account',
                'nsec_admin_content'
            )
        );
    }

    function index()
    {
        redirect('account');
    }

    function content($page='home', $offset=0, $pagination=100)
    {
        $data['PageTitle'] = $this->config->item('SiteName')." - Admin Login";
        $data['head']='';
        if($page == 'articles') $page='home';
        $data['curpage']=$page;

        $this->load->helper('file');

        $data['site_content'] = array();
        $data['template_files'] = array();

        if($page != 'home' && $page != 'articles')
        {
            $data['key'] = $page;
            $data['Title'] = ucfirst($page);
        }
        else
        {
            $data['table'] = $this->nsec_contentmodel->getContentTable('articles', array('pagination'=>$pagination, 'offset'=>$offset));

            $data['key'] = 'articles';
        }

        $data['site_content'] = $this->nsec_contentmodel->getContentTable('blocks');
        $data['content'] = $this->nsec_contentmodel->getContentTable('templates');

        $data['admin_page'] = $page;
        $data['pagination'] = $pagination;
        $data['offset'] = $offset;

        $tableinfo = $this->nsec_contentmodel->getContentTable($data['key']);
        $config['base_url'] = "/admin/index/home/";
        $config['total_rows'] = $tableinfo->num_rows();
        $config['per_page'] = $pagination;
        $config['page_query_string'] = FALSE;
        $this->pagination->initialize($config);

        $data['PageTitle'] = $this->config->item('SiteName')." Admin Control Panel";

        $data['contentPage'] = 'content';
        $data['metadata'] = generate_meta('account', 'Manage Content', array('subsection'=>'content'));
        $this->load->view('account', $data);
    }

    function newpage($type='')
    {
        if($type == '') redirect('admin');
        $this->load->helper('file');
        $data['PageTitle']=NULL;

        if(!$this->input->post('page'))
            $url = url_title($this->input->post('title'));
        else
            $url = url_title($this->input->post('page'));

        $data['content'] = '';
        $data['admin_page'] = 'edit_content';

        $this->form_validation->set_error_delimiters('<div class="errors">', '</div>');
        setContentFormValidation();

        if($type == "templates")
        {
            $data['PageTitle'] = "Add Site Content";
            if ($this->form_validation->run() == FALSE) {

            } else {
                saveTemplate($url);
                redirect("admin/editpage/templates/$url");
            }
        }
        elseif($type == "blocks")
        {
            $url = url_title($this->input->post('page'), 'underscore');
            $data['PageTitle'] = "Add Template Item";
            if ($this->form_validation->run() == FALSE) {

            } else {
                saveBlock($url);
                redirect("admin/editpage/blocks/$url");
            }
        }
        else
        {
            $data['PageTitle'] = "New Article";
            if ($this->form_validation->run() == FALSE) {

            } else {
                saveContent($url);
                redirect("admin/editpage/$type/".$this->input->post('articletype')."/$url");
            }
        }

        $data['mcehead'] = $this->tinymce->createhead('exact');
        $data['mce']  = $this->tinymce->textarea(TRUE, "/admin/newpage/$type", $data['content'], $type);

        $data['contentPage'] = 'edit_content';
        $data['metadata'] = generate_meta('account', 'Create New Content', array('subsection'=>'content'));
        $this->load->view('account', $data);

    }

    function editpage($type=NULL, $key=NULL, $id=NULL)
    {
        $this->load->library('tinymce');
        $this->load->helper('file');

        if(!$this->session->userdata('developer')) {
            if($this->session->userdata('admin'))
                $this->db->where("Access <=", 2);
            elseif($this->session->userdata('moderator'))
                $this->db->where("Access <=", 1);

        }

        if($type == 'blocks') {
            extract(getBlockDetails($key));
        } elseif($type != NULL) {
            extract(
                getContentDetails (
                    $id,
                    $key,
                    $type
                )
            );
        }
        else
        {
            redirect('admin');
        }

        $data['mcehead'] = $this->tinymce->createhead();
        $data['thisType'] = $type;
        $data['mce']  = $this->tinymce->textarea(
            TRUE,
            '/admin/savepage/'.$type.'/'.$key.'/',
            htmlentities(
                $data['content'],
                ENT_QUOTES
            ),
            $type,
            $data
        );

        $data['site_content'] = $this->nsec_contentmodel->getContentTable('blocks');
        $data['content'] = $this->nsec_contentmodel->getContentTable('templates');

        $data['contentPage'] = 'edit_content';
        $data['metadata'] = generate_meta(
            'account',
            'Edit Content: '.$data['PageTitle'],
            array('subsection'=>'content')
        );
        $this->load->view('account', $data);
    }

    function deletepage($type='')
    {
        if(!$this->session->userdata('admin')) redirect('user');
        deletePage($type);

        redirect('account/content');
    }

    function savepage($type=NULL, $key=NULL)
    {
        if(!$this->input->post('page'))
            $url = url_title($this->input->post('title'));
        else
            $url = url_title($this->input->post('page'));

        $url = substr($url, 0, 50);
        if($type == '') {
             redirect("admin");
        } elseif($type == "blocks") {
            saveBlock($url, $key);
            redirect("admin/editpage/$type/$url");

        } elseif($type == 'templates') {
            saveTemplate($url, $key);
            redirect("admin/editpage/$type/$url");

        } elseif($type != NULL) {
            saveContent($url, $key);
            redirect("admin/editpage/$type/".$this->input->post('articletype')."/$url");
        } else {
            $this->session->set_userdata(
                'adminmsg',
                '<div class="error">Error updating content. No type specified.</div>'
            );
        }

    }

    function manage($type=NULL)
    {
        if(!$this->session->userdata('logged_in') || !$this->db->table_exists($type)) redirect('admin');
        $this->load->model('nsec_filters');
        $data['hideOptions'] = TRUE;
        $updated = 0;

        if ($this->input->post('addnew')) {
            $fields = $this->db->field_data($type);

            foreach ($fields as $field) {
                //echo $field->name;
                if(isset($_POST[$field->name][0])) {
                    $thesefields[$field->name] = $_POST[$field->name][0];
                } elseif(!$field->primary_key) {
                    if($field->type == 'int')
                        $thesefields[$field->name] = 0;
                }
            }

            $this->db->set($thesefields);
            if(!$this->db->insert($type))
                $this->form_validation->error_string = "<div class='error'>Error Saving setting</div>";
            else
                $this->form_validation->error_string = "<div class='success'>Setting successfully saved</div>";
        }
        elseif($this->input->post('save'))
        {

            foreach ($this->input->post('key') as $tkey=>$tval) {
                $fields = $this->db->field_data($type);
                $keys = explode("||", $tval);

                foreach ($fields as $field) {

                    if (isset($_POST[$field->name][$tkey]))
                        $thesefields[$field->name] = $_POST[$field->name][$tkey];
                    elseif (!$field->primary_key) {
                        if($field->type == 'int')
                            $thesefields[$field->name] = 0;
                    }
                }


                if(count($keys) == 2) {
                    $this->db->set($thesefields);
                    $this->db->where($keys[1], $keys[0]);
                    $this->db->update($type);

                    if($this->db->affected_rows() > 0)
                        $this->form_validation->error_string = "<div class='success'>".++$updated." Updates Successful</div>";
                }
            }

        }

        $table = $data['contentPage'] = $data['admin_page'] = 'edit_settings';
        $data['metadata'] = generate_meta('account', 'Site Setup', array('subsection'=>'settings'));
        $data['pagekey']    =    $type;
        $data['PageTitle']     =     "Admin Settings - ".ucfirst($type);

        if($this->db->field_exists('Order', $type)) $this->db->order_by('Order', 'asc');
        $data['settings']     =     $this->db->get($type);

        $this->load->view('account', $data);

    }

    function settings($section=NULL)
    {
        if(!$this->session->userdata('logged_in')) redirect('admin');
        $data['hideOptions'] = TRUE;

        $data['admin_page']='settings';
        $data['PageTitle'] = "Admin Site Settings";

        $data['settings'] = $this->db->get('site_settings');

        $this->load->view('admin', $data);

    }

    function users()
    {
        $table = $data['contentPage'] = $data['admin_page'] = 'users';
        $data['metadata'] = generate_meta('admin', 'Manage Users', array('subsection'=>'users'));
        $query = $this->db->get('users');
        $data['user_array'] = $query->result_array();

        $this->load->view('account', $data);
    }

    function user_create()
    {
        $this->form_validation->set_rules('username', 'Username', "trim|required|min_length[4]|max_length[32]|alpha_dash");
        $this->form_validation->set_rules('password', 'Password', "trim|required|matches[confirm_password]|min_length[4]|max_length[32]|alpha_dash");
        $this->form_validation->set_rules('email_address', 'Email', "trim|required|valid_email");

        if ($this->form_validation->run() === false) {
            if ($this->input->post('create'))
                appendToPageLevelMessage(validation_errors(), 'error');
        } else {
            //Create account
            $query = $this->db->query("SELECT user_id FROM users WHERE username = '"
                . $this->input->post('username')
                . "' or email_address='" . $this->input->post('email_address')
                . "' LIMIT 1"
            );

            if ($query->num_rows() === 1) {
                appendToPageLevelMessage('Username/Email conflict', 'error');
            } else {
                $this->simplelogin->create($this->input->post('username'), $this->input->post('password'));
            }
        }

        $table = $data['contentPage'] = $data['admin_page'] = 'user_create';
        $data['metadata'] = generate_meta('admin', 'Create User', array('subsection'=>'users'));
        $query = $this->db->get('users');
        $data['user_array'] = $query->result_array();

        $this->load->view('account', $data);
    }

    function user_edit($key)
    {
        if ($this->input->post('update')) {
            $this->load->library('user');
            $this->user->updateUserProfile($key);
        } elseif (isset($_POST['uploadphoto'])) {
            $this->uploadmodel->UploadImage();
        } elseif ($this->input->post('sendmessage')) {
            if ($this->usermodel->sendMessage($this->input->post('toID'))) {
                $page = 'home';
                $this->validation->error_string = '<div class="success">Your message was sent.</div>';
            }
        }

        $this->db->where('user_id', $key);

        $data['contentPage'] = $data['admin_page'] = 'user_profile';
        $data['metadata'] = generate_meta('admin', 'Edit User', array('subsection'=>'users'));
        $query = $this->db->get('users');
        $data['user_array'] = $query->result_array();

        $this->load->view('account', $data);
    }

    function user_delete($key)
    {
        $url = '/admin/';
        if($this->simplelogin->delete($key)) {
            $url = '/admin/users/';
        }

        redirect($url);
    }

    function logout()
    {
        redirect('account/logout');
    }
}