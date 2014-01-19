<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NSEC_Abstract_Workflow
{
    public $CI;

    public $delimiter = '||';

    public $class;

    protected $fields;

    function __construct()
    {
        $this->CI = &get_instance();
    }

    public function checktable()
    {
        if (!$this->CI->db->table_exists($this->class->table))
        {
            $sql[] = 'create table '.$this->class->table.' (';
            $sql[] = 'id int(11) not null primary key auto_increment,';
            $sql[] = 'AccessID int(11) not null default 0,';
            foreach($this->fields as $key=>$val)
                $fields[] = "$key text null,";

            $sql[] = join(NULL, $fields);

            $sql[] = 'dateAdded datetime not null,';
            $sql[] = 'dateUpdated datetime not null';
            $sql[] = ')';
            $this->CI->db->query(join(NULL, $sql));

            if (!$this->CI->db->table_exists($this->class->table))
                return false;
            else
                return true;

        }
        else
        {
            $del = NULL;
            foreach($this->fields as $key=>$val)
            {
                $sql = 'alter table ' . $this->class->table;
                if(!$this->CI->db->field_exists($key, $this->class->table))
                {
                    $fields[] = "$del add column $key text null";
                    if(isset($lastfield))
                        $fields[] = " after $lastfield";

                    $del = ',';
                }
                $lastfield = $key;
            }

            if(isset($fields))
                $this->CI->db->query($sql.join(NULL, $fields));

            return true;
        }
    }
}