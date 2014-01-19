<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    $sidebarKey = "_{$position}column";
    $SIDEBAR = NULL;
    if (isset($pagetype)) $rightColumnPageKey = str_replace('-', '_', $pagetype);
    if (isset($pageid)) $rightContentName = str_replace('-', '_', $pageid);
    elseif (isset($content->ContentName)) $rightContentName = str_replace('-', '_', $content->ContentName);

    if(isset($rightContentName) &&
      ((isset($rightColumnPageKey) &&
          (isset($CONTENT[url_title($rightColumnPageKey.'_'.$rightContentName.$sidebarKey, '_')])
        || isset($CONTENT[url_title($rightColumnPageKey.'_'.$rightContentName.$sidebarKey.$this->session->userdata('membertype'), '_')])))
      || isset($CONTENT[$rightContentName.$sidebarKey])
      || isset($CONTENT[url_title($rightContentName.$sidebarKey.$this->session->userdata('membertype'), '_')]))) {
        if(isset($CONTENT[$rightContentName.$sidebarKey])) {
            $columnName = url_title($rightContentName.$sidebarKey, '_');
        } elseif(isset($CONTENT[url_title($rightColumnPageKey.'_'.$rightContentName.$sidebarKey, '_')])) {
            $columnName = url_title($rightColumnPageKey.'_'.$rightContentName.$sidebarKey, '_');
        } elseif(isset($CONTENT[url_title($rightContentName.$sidebarKey.$this->session->userdata('membertype'), '_')])) {
            $columnName = url_title($rightContentName.$sidebarKey.$this->session->userdata('membertype'), '_');
        } elseif(isset($CONTENT[url_title($rightColumnPageKey.'_'.$rightContentName.$sidebarKey.$this->session->userdata('membertype'), '_')])) {
            $columnName = url_title($rightColumnPageKey.'_'.$rightContentName
                . $sidebarKey.$this->session->userdata('membertype'), '_');
        }

        if (isset($columnName)) {
            $SIDEBAR = $CONTENT[$columnName];
        }
    } elseif(isset($rightContentName) && isset($rightColumnPageKey) && isset($CONTENT[url_title($rightColumnPageKey.$sidebarKey.$this->session->userdata('membertype'), '_')])) {
        $SIDEBAR = $CONTENT[url_title($rightColumnPageKey.$sidebarKey.$this->session->userdata('membertype'), '_')];
    } elseif(isset($rightContentName)
        && $rightContentName!="{$position}column"
        && isset($CONTENT[$rightContentName.$this->session->userdata('membertype')])
    ) {
        $SIDEBAR = $CONTENT[$rightContentName.$this->session->userdata('membertype')];
    } elseif(isset($rightColumnPageKey) && isset($CONTENT[url_title($rightColumnPageKey.$sidebarKey, '_')])) {
        $SIDEBAR = $CONTENT[url_title($rightColumnPageKey.$sidebarKey, '_')];
    } elseif(isset($rightContentName) && $rightContentName!="{$position}column" && isset($CONTENT[$rightContentName])) {
        $SIDEBAR = $CONTENT[$rightContentName];
    } elseif(isset($pagekey) && isset($CONTENT[$pagekey.$sidebarKey])) {
        $SIDEBAR = $CONTENT[$pagekey.$sidebarKey];
    } else {
        $SIDEBAR = isset($CONTENT["{$position}column"]) ? $CONTENT["{$position}column"] : $SIDEBAR;
    }

    if ($SIDEBAR != NULL) {
        echo '<div class="sidebar" id="' . $position . '-mid">';
        if (strstr($SIDEBAR, '<!--TEMPLATE')) {
            foreach ($CONTENT as $key => $val) {
                if (strstr($SIDEBAR, "<!--TEMPLATE$key-->")) {
                    $SIDEBAR = str_replace("<!--TEMPLATE$key-->", $val, $SIDEBAR);
                }
            }
        }

        echo processFunctionsInContent($SIDEBAR);;
        echo '</div>';
    }
?>