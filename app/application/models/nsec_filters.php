<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NSEC_Filters extends CI_Model
{
      var $fields = array();

      private $_DB;

      function __construct() {
          parent::__construct();

          $this->load->helper('query');
          $this->_DB = live_db();
      }

    public $variable = array(
        'TypeName' => array('Name'=>'Content Type Name', 'fieldinfo'=>array('name'=>'TypeName')),
        'CareerName' => array('Name'=>'Career Type Name', 'fieldinfo'=>array('name'=>'CareerName')),
        'CurriculumName' => array('Name'=>'Curriculum Name', 'fieldinfo'=>array('name'=>'CurriculumName')),
        'OutcomeName' => array('Name'=>'School Experience', 'fieldinfo'=>array('name'=>'OutcomeName', 'size'=>30)),
        'MembershipType' => array('Name'=>'Membership Name', 'fieldinfo'=>array('name'=>'MembershipType')),
        'Membership' => array('Name'=>'Membership Name', 'fieldinfo'=>array('name'=>'Membership')),
        'Price' => array('Name'=>'($) Price', 'fieldinfo'=>array('name'=>'Price', 'size'=>6, 'maxlength'=>10)),
        'StateName' => array('Name'=>'State Name', 'fieldinfo'=>array('name'=>'StateName')),

        'TypeURL' => array('Name'=>'Web URL', 'fieldinfo'=>array('name'=>'TypeURL')),
        'country' => array('Name'=>'Country', 'fieldinfo'=>array('name'=>'country')),
        'Years' => array('Name'=>'Years of Experience', 'fieldinfo'=>array('name'=>'Years')),

        'ImageURL' => array('Name'=>'Image URL', 'fieldinfo'=>array('name'=>'ImageURL', 'size'=>40)),
        'WebAddress' => array('Name'=>'Website Address', 'fieldinfo'=>array('name'=>'WebAddress', 'size'=>40)),
        'Approved' => array('Name'=>'Approved', 'Type'=>'hidden', 'fieldinfo'=>array('name'=>'Approved')),

        'Title' => array('Name'=>'Name', 'fieldinfo'=>array('name'=>'Title')),
        'ForSale' => array('Name'=>'ForSale', 'Type'=>'checkbox', 'fieldinfo'=>array('name'=>'ForSale')),
        'Weight' => array('Name'=>'Weight (lbs)', 'fieldinfo'=>array('name'=>'Weight', 'size'=>3)),
        'Color' => array('Name'=>'Color', 'fieldinfo'=>array('name'=>'Color')),
        'Bloodline' => array('Name'=>'Bloodline', 'fieldinfo'=>array('name'=>'Bloodline')),
        'ListingImage' => array('Name'=>'Image', 'Type'=>'file', 'fieldinfo'=>array('name'=>'ListingImage')),

        'Active' => array('Name'=>'Active', 'fieldinfo'=>array('name'=>'Active')),
        'ReferralSource' => array('Name'=>'Referral Source', 'fieldinfo'=>array('name'=>'ReferralSource')),
        'DateUpdated' => array('Name'=>'Date Updated', 'Type'=>'hidden', 'fieldinfo'=>array('name'=>'DateUpdated')),
        'Order' => array('Name'=>'Order', 'fieldinfo'=>array('name'=>'Order', 'size'=>3)),
        'name'=> array('Name'=>'Relocate Distance', 'fieldinfo'=>array('name'=>'name')),
        'Description' => array('Name'=>'Description', 'Type'=>'textarea', 'fieldinfo'=>array('name'=>'Description', 'cols'=>35, 'rows'=>6))
    );

    function getSettingField($field, $value, $addBrackets=true)
    {
        if(usertype('admin'))
        {
            $this->variable['TableReference'] = array('Name'=>'Database Table xref', 'fieldinfo'=>array('name'=>'TableReference'));
            $this->variable['SiteValue'] = array('Name'=>'Site Post Value', 'fieldinfo'=>array('name'=>'SiteValue'));
            $this->variable['AllowComments'] = array('Name'=>'Allow Comments',  'fieldinfo'=>array('name'=>'AllowComments'));
            $this->variable['Protected'] = array('Name'=>'Protected',  'fieldinfo'=>array('name'=>'Protected'));
            $this->variable['Approved'] = array('Name'=>'Approved',  'fieldinfo'=>array('name'=>'Approved'));
            $this->variable['ModerateComments'] = array('Name'=>'Moderate Comments',  'fieldinfo'=>array('name'=>'ModerateComments'));
            $this->variable['AccessName'] = array('Name'=>'AccessName',  'fieldinfo'=>array('name'=>'AccessName'));
            $this->variable['AccessRef'] = array('Name'=>'AccessRef',  'fieldinfo'=>array('name'=>'AccessRef'));
            $this->variable['AccessPage'] = array('Name'=>'AccessPage',  'fieldinfo'=>array('name'=>'AccessPage'));
            $this->variable['ParentId'] = array('Name'=>'ParentId',  'fieldinfo'=>array('name'=>'ParentId', 'size'=>3));
        }

        $this->load->helper('form');
        $fieldhtml = NULL;

        $excludeFields = array('DateSaved', 'DateDeleted', 'DateUpdated', 'DateAdded', 'AccessID', 'UserID');

        for($i=1; $i<10; $i++)
        {
            if($i<$this->session->userdata('imagesperlisting'))
                $this->variable['ListingImage'.$i] = array('Name'=>'Image', 'Type'=>'listing_image', 'fieldinfo'=>array('name'=>'ListingImage'.$i, 'num'=>$i));
            else
                $excludeFields[] = 'ListingImage'.$i;
        }

        if(isset($this->variable[$field]))
        {
            $thisField = $this->variable[$field];

            $fieldhtmlstart = '<table cellpadding="3" cellspacing="3" border="0" width="100%">';
            $fieldhtmlstart .= '<tr>';
            $fieldhtmlstart .= '<td width="150" align="right" valign="top">* <strong>'.$thisField['Name'].':</strong></td>';
            $fieldhtmlstart .= '<td>';

            $fieldhtmlend = '</td>';
            $fieldhtmlend .= '</tr>';
            $fieldhtmlend .= '</table>';

            if($addBrackets) $thisField['fieldinfo']['name'] = $field."[]";
            if(isset($thisField['Type']))
            {
                switch($thisField['Type']){
                case 'textarea':

                    $thisField['fieldinfo']['rows'] = isset($thisField['fieldinfo']['rows']) ? $thisField['fieldinfo']['rows'] : 4;
                    $thisField['fieldinfo']['cols'] = isset($thisField['fieldinfo']['cols']) ? $thisField['fieldinfo']['cols'] : 30;
                    $thisField['fieldinfo']['value'] = $value;
                    $fieldhtml =  $fieldhtmlstart.form_textarea($thisField['fieldinfo']).$fieldhtmlend;
                break; case 'checkbox':
                    if($value == 1)
                        $thisField['fieldinfo']['checked'] = true;
                    else
                        $thisField['fieldinfo']['checked'] = false;

                    $thisField['fieldinfo']['value'] = 1;
                    $fieldhtml =  $fieldhtmlstart.form_hidden($thisField['fieldinfo']['name']);
                    $fieldhtml .=  form_checkbox($thisField['fieldinfo']).$fieldhtmlend;
                break; case 'radio':
                    if($value == 1)
                        $thisField['fieldinfo']['checked'] = true;
                    else
                        $thisField['fieldinfo']['checked'] = false;

                    $thisField['fieldinfo']['value'] = 1;
                    $fieldhtml =  $fieldhtmlstart.$value.form_radio($thisField['fieldinfo'])." Yes";

                    if($value != 1)
                        $thisField['fieldinfo']['checked'] = true;
                    else
                        $thisField['fieldinfo']['checked'] = false;

                    $thisField['fieldinfo']['value'] = 0;
                    $fieldhtml .=  form_radio($thisField['fieldinfo'])." No".$fieldhtmlend;
                break; case 'file':

                    $thisField['fieldinfo']['value'] = $value;
                    $fieldhtml =  $fieldhtmlstart.form_upload('userfile').$fieldhtmlend;
                break; case 'listing_image':

                    $thisField['fieldinfo']['value'] = $value;
                    $fieldhtml =  $fieldhtmlstart.form_upload('ListingImage'.$thisField['fieldinfo']['num']).$fieldhtmlend;
                break; case 'hidden':

                    $thisField['fieldinfo']['value'] = $value;
                    $fieldhtml =  form_hidden($thisField['fieldinfo']['name'], $thisField['fieldinfo']['value']);
                break; case 'editor':

                    $fieldhtmlstart = '<table cellpadding="3" cellspacing="3" border="0" width="100%">';
                    $fieldhtmlstart .= '<tr>';
                    $fieldhtmlstart .= '<td colspan="2" valign="top"><strong>'.$thisField['Name'].':</strong></td>';
                    $fieldhtmlstart .= '</tr>';
                    $fieldhtmlstart .= '<tr>';
                    $fieldhtmlstart .= '<td colspan="2" valign="top">';

                    $thisField['fieldinfo']['value'] = $value;
                    $fieldhtml =  $fieldhtmlstart
                        . $this->tinymce->textarea(FALSE, $this->variable[$field]['fieldinfo']['name'], $thisField['fieldinfo']['value'])
                        . $fieldhtmlend;
                break; case 'YesNo':

                    $thisField['fieldinfo']['value'] = $value;
                    $fieldhtml =  $fieldhtmlstart.form_dropdown($thisField['fieldinfo']['name'], array('No'=>'No', 'Yes'=>'Yes'), $thisField['fieldinfo']['value']).$fieldhtmlend;
                break; default:
                    $fieldhtml =  $fieldhtmlstart.$value.$fieldhtmlend;
                }

            }
            else
            {
                if(!isset($thisField['source']))
                {
                    $thisField['fieldinfo']['value'] = $value;
                    $fieldhtml =  $fieldhtmlstart.form_input($thisField['fieldinfo']).$fieldhtmlend;
                }
                else
                {

                    $extra = NULL;

                    $this->_DB->order_by('Order', 'asc');
                    $this->_DB->where('Active', 1);
                    $query = $this->_DB->get($thisField['source']);
                    $array[] = 'Select One';

                    switch(true){
                    case $thisField['source']=='categories':
                        $querykey = 'id';
                        $queryname = 'category_name';
                        $extra = 'size="25"; multiple="multiple"';
                        //$array[0] = "None";
                        $array = array();
                        $field = 'category';
                        $del = $value = NULL;
                        if($shopid!=NULL)
                        {
                            $this->_DB->where('shopid', $shopid);
                            $cats = $this->_DB->get('user_categories');
                            foreach($cats->result() as $thiscat)
                            {
                                $value .= $del.$thiscat->category_id;
                                $del = '|';
                            }
                        }
                    break; case $thisField['source']=='in_shop_service':
                        $querykey = 'id';
                        $queryname = 'service_name';
                    break; case $thisField['source']=='roadside_service':
                        $querykey = 'id';
                        $queryname = 'service_name';
                    break; case $thisField['source']=='state':
                        $querykey = 'stateID';
                        $queryname = 'StateName';
                    }

                    if($addBrackets || ($thisField['source']=='categories')) $field .= "[]";

                    if(isset($querykey))

                    {
                        foreach($query->result() as $row)
                        {
                            $array[$row->$querykey] = $row->$queryname;
                        }

                        $sep1 = explode('|', $value);
                        if(count($sep1) > 1)
                        {
                           $vals = $sep1;
                        }
                        else
                           $vals = $value;

                        ksort($array);
                        $fieldhtml =  $fieldhtmlstart.form_dropdown($field, $array, $vals, $extra).$fieldhtmlend;
                    }

                }
            }

            if(isset($thisField['heading'])) $fieldhtml = "<h2>$thisField[heading]</h2>$fieldhtml\n";
            if(isset($thisField['break'])) $fieldhtml .= "<hr \>";


        }
        elseif(usertype('admin'))
        {

            $fieldhtmlstart = '<table cellpadding="3" cellspacing="3" border="0" width="100%">';
            $fieldhtmlstart .= '<tr>';
            $fieldhtmlstart .= '<td width="150" align="right" valign="top">* <strong>'.$field.':</strong></td>';
            $fieldhtmlstart .= '<td>';

            $fieldhtmlend = '</td>';
            $fieldhtmlend .= '</tr>';
            $fieldhtmlend .= '</table>';

            $thisField['fieldinfo']['value'] = $value;
            $thisField['fieldinfo']['name'] = $field;
            if($addBrackets) $thisField['fieldinfo']['name'] = $field."[]";
            $fieldhtml =  $fieldhtmlstart.form_input($thisField['fieldinfo']).$fieldhtmlend;

        }
        else
            $fieldhtml = NULL;

        return $fieldhtml;

    }


}