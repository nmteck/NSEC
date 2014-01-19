<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    $dataTables = $this->config->item('dataTables');
    $CONTENT = array();

    $this->db->select('ContentName, Content');
    $this->db->from('content_blocks');
    $queryresult = $this->db->get();

    foreach($queryresult->result() as $key)
    {
        $CONTENT[$key->ContentName] = $key->Content;
        foreach($this->session->userdata as $skey=>$sval)
        {
            if(strstr($key->Content, "<!--$skey-->"))
            {
                $CONTENT[$key->ContentName] = str_replace("<!--$skey-->", $this->session->userdata($skey), $CONTENT[$key->ContentName]);
            }
            elseif(strstr($key->Content, "%%$skey%%"))
            {
                $CONTENT[$key->ContentName] = str_replace("%%$skey%%", $this->session->userdata($skey), $CONTENT[$key->ContentName]);
            }
        }

        if(isset($_POST))
        {
            foreach($_POST as $skey=>$sval)
            {
                $string = "<input name=\"$skey\" type=\"text\"";
                if(strstr($key->Content, $string))
                {
                    $CONTENT[$key->ContentName] = str_replace($string, $string." value=\"$sval\"", $CONTENT[$key->ContentName]);
                }
            }
        }

        if(isset($thisReplaceObjects))
        {

            foreach($thisReplaceObjects as $object=>$val)
            {
                if(strstr($CONTENT[$key->ContentName], "<!--$object-->") || strstr($CONTENT[$key->ContentName],"%%$object%%"))
                {
                    if(strstr($CONTENT[$key->ContentName], "<!--$object-->"))
                        $replace = "<!--$object-->";
                    else
                        $replace = "%%$object%%";

                    $replacement = $val;

                    $CONTENT[$key->ContentName] = str_replace($replace, $replacement, $CONTENT[$key->ContentName]);
                }

            }
        }

        if(strstr($key->Content, '<!--SHOW') || strstr($key->Content, '%%SHOW') && is_array($dataTables))
        {
            foreach($dataTables as $sKey=>$sArray)
            {
                $this->db->start_cache();
                $replacements = NULL;
                //if(isset($sArray[2]) && $sArray[2]!=NULL) $this->db->order_by($sArray[2], 'asc');
                if(isset($sArray[3])) $sKey2 = $sArray[3]; else $sKey2 = $sKey;

                $replacements[] = '<!--SHOW'.$sKey.'-->';
                $replacements[] = '<!--SHOW'.$sKey2.'-->';
                $replacements[] = '%%SHOW'.$sKey.'%%';
                $replacements[] = '%%SHOW'.$sKey2.'%%';
                foreach($replacements as $replacement)
                {
                    if(strstr($CONTENT[$key->ContentName], $replacement))
                    {
                        $this->db->where('Active', 1);
                        $QUERY = $this->db->get($sKey);
                        $fielddata=NULL;

                        foreach($QUERY->result_array() as $itemRow)
                        {
                            if(($this->input->post($sKey2) == $itemRow[$sArray[0]]) || ($this->input->post($sKey) == $itemRow[$sArray[0]]))
                                $sel = ' selected'; else $sel = NULL;
                            //echo $this->input->post($sKey2).'-'.$itemRow[$sArray[0]]." $sel:: ";
                            $fielddata .= "<option value=\"".$itemRow[$sArray[0]]."\" $sel>".$itemRow[$sArray[1]]."</option>\n";
                        }

                        $CONTENT[$key->ContentName] = str_replace($replacement, $fielddata, $CONTENT[$key->ContentName]);
                    }
                }

                $this->db->stop_cache();
                $this->db->flush_cache();
            }
        }
    }

    foreach($CONTENT as $ContentKey=>$ContentPiece)
    {
        if(strstr($ContentPiece, '<!--TEMPLATE'))
        {
            foreach($CONTENT as $key => $val)
            {
                if(strstr($ContentPiece, "<!--TEMPLATE$key-->"))
                    $CONTENT[$ContentKey] = str_replace("<!--TEMPLATE$key-->", $val, $CONTENT[$ContentKey]);
            }
        }
    }

    $loggedInUserProfile = getLoggedInUserProfile();