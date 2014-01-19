<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User {

    public $id;

    private $_CI;

    const USER_TYPE = 'user';

    const CLIENT_TYPE = 'client';

    const USER_TABLE = 'users';

    const CLIENT_TABLE = 'clients';

    public function __construct(){
        $this->_CI = get_instance();
        $this->_CI->load->helper('nsec_account_helper');
    }

    public function getUserProfile($id, $type = null)
    {
        if ($id === false) {
            return null;
        }

        if ($type == self::CLIENT_TYPE) {
            $this->_CI->load->model('nsec_clientProfile');
            // @todo: update client profile
            // $profile = new Nsec_userprofile($id);
        } else {
            $this->_CI->load->model('nsec_userProfile');
            $profile = new Nsec_userprofile($id);
        }

        return $profile->profileData;
    }

    public function deleteUser($key)
    {
        $this->simplelogin->delete($key);
    }

    private function buildAccessLevelField()
    {
        return join(',', $this->_CI->input->post('access'));
    }

    private function getAdditionalSubmittedDetails() {
        $additionalDetails = $this->_CI->config->item('profile_details');

        if (is_array($additionalDetails)) {
            foreach ($additionalDetails as $_detail) {
                $return[$_detail] = $this->_CI->input->post($_detail);
            }

            return json_encode($return);
        }
    }

    public function updateUserProfile($key)
    {
        $updateArray = array();
        foreach ($this->_CI->db->list_fields('users') as $field) {
            if (isset($_POST[$field])) {
                if($field === 'password') {
                    if($this->_CI->input->post('password')) {
                        if ($this->_CI->input->post('password') === $this->_CI->input->post('confirm_password')) {
                            $updateArray[$field] = md5($this->_CI->input->post($field));
                            appendToPageLevelMessage('User password updated', 'success');
                        } else {
                            appendToPageLevelMessage('Unable to update password');
                        }
                    }
                } else {
                    $updateArray[$field] = $this->_CI->input->post($field);
                }
            }
        }

        $updateArray['AccessLevels'] = $this->buildAccessLevelField();
        $updateArray['comments'] = $this->getAdditionalSubmittedDetails();

        if (count($updateArray) > 0) {
            $this->_CI->db->where('user_id', $key);
            $this->_CI->db->set($updateArray);

            if ($this->_CI->db->update('users')) {
                if ($this->_CI->db->affected_rows() === 1) {
                    appendToPageLevelMessage('User profile updated', 'success');
                }
            } else {
                appendToPageLevelMessage('Unable to update user');
            }
        } else {
            appendToPageLevelMessage('No changes made to profile');
        }

    }
}