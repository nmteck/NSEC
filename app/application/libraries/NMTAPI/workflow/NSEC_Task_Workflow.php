<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NSEC_Task_Workflow extends NSEC_Abstract_Workflow
{
    public $fields = array(
        'ShopID'=>NULL,
        'subject'=>NULL,
        'start_date'=>NULL,
        'due_date'=>NULL,
        'complete_date'=>NULL,
        'reminder'=>NULL,
        'reminder_date'=>NULL,
        'reminder_time'=>NULL,
        'status'=>NULL,
        'priority'=>NULL,
        'comments'=>NULL
    );

    CONST DISPLAY_LIMIT_DEFAULT = 20;

    function __construct()
    {
        parent::__construct();

        $this->CI->load->model('taskmodel');

        $this->class = new Taskmodel();

        if (!$this->CI->session->userdata('task_start_date') || $this->CI->input->post('clearTaskFilters')) {
            $session_userdata['task_start_date'] = date('Y-m-d', strtotime('-3 days'));
            $session_userdata['task_end_date'] = date('Y-m-d', strtotime('+3 days'));
        }

        if (!$this->CI->session->userdata('task_display_limit') || $this->CI->input->post('clearTaskFilters')) {
            $session_userdata['task_display_limit'] = self::DISPLAY_LIMIT_DEFAULT;
        }

        if ($this->CI->input->post('searchTasks') || $this->CI->input->post('clearTaskFilters')) {
            if ($this->CI->input->post('searchall')) {
                $session_userdata['task_searchall'] = true;
            } else {
                $session_userdata_unset[] = 'task_searchall';
            }
        }

        if ($this->CI->input->post('showcompleted') && !$this->CI->input->post('clearTaskFilters')) {
            $session_userdata['task_showcompleted'] = true;
        } elseif ($this->CI->input->post('searchTasks')) {
            $session_userdata['task_showcompleted'] = false;
        } elseif($this->CI->input->post('clearTaskFilters')) {
            $session_userdata['task_showcompleted'] = false;
        }

        if (isset($session_userdata)) {
            $this->CI->session->set_userdata($session_userdata);
        }

        if (isset($session_userdata_unset)) {
            $this->CI->session->unset_userdata($session_userdata_unset);
        }

    }

    function saveTask()
    {
        $this->CI->db->set($_POST);
        if(isset($_POST['id']))
        {
            $this->CI->db->set('dateUpdated', date('Y-m-d H:i:s'));
            $this->CI->db->where('id', $_POST['id']);
            $this->CI->db->update($this->class->table);
        }
        else
        {
            $this->CI->db->set('AccessID', $this->CI->session->userdata('user_id'));
            $this->CI->db->set('dateAdded', date('Y-m-d H:i:s'));
            $this->CI->db->insert($this->class->table);
        }

        if($this->CI->db->affected_rows() == 1)
            return "Task successfully saved";
        else
            return false;
    }

    function getUserTasks($date=NULL, $id=NULL, $startdate=NULL, $enddate=NULL)
    {
        $this->checktable();

        if ($id!=NULL) {
            $this->CI->db->where('t.AccessID', $id);
        } elseif(!usertype('admin')) {
            $this->CI->db->where('t.AccessID', $this->CI->session->userdata('user_id'));
        }

        if (!$this->CI->session->userdata('task_searchall')) {
            if ($startdate!=NULL) {
                $this->CI->db->where('t.reminder_date >=', $startdate);
            }

            if ($enddate!=NULL) {
                $this->CI->db->where('t.reminder_date <=', $enddate.' 23:59:59');
            }

            if ($this->CI->session->userdata('task_showcompleted')) {
                $this->CI->db->where('t.status', 'complete');
            } else {
                $this->CI->db->where('t.status !=', 'complete');
            }

            $this->CI->db->limit($this->CI->session->userdata('task_display_limit'));
        }

        $this->CI->db->order_by('t.reminder_date', 'asc');
        $query = $this->CI->db->get($this->class->table.' t');

        if ($id!=NULL) {
            $session_userdata['task_sort_user'] = $id;
        }

        if ($startdate!=NULL) {
            $session_userdata['task_start_date'] = $startdate;
        }

        if ($enddate!=NULL) {
            $session_userdata['task_end_date'] = $enddate;
        }

        if (isset($session_userdata)) {
            $this->CI->session->set_userdata($session_userdata);
        }

        if ($query->num_rows() > 0) {
            return $query;
        } else {
            return false;
        }
    }

    function getTaskById($id)
    {
        $this->checktable();

        $this->CI->db->where('id', $id);
        $this->CI->db->limit(1);
        $query = $this->CI->db->get($this->class->table.' t');

        if ($query->num_rows() > 0) {
            return $query;
        } else {
            return false;
        }
    }

    function sendTaskEmailReminder($subject, $content)
    {
        $this->CI->load->library('email');

        $this->CI->email->from($this->CI->config->item('SiteEmail'), $this->CI->config->item('SiteName'));
        $this->CI->email->to($this->CI->config->item('SiteEmail'));

        $this->CI->email->subject($subject);
        $this->CI->email->message($content);

        $this->CI->email->send();
    }
}
