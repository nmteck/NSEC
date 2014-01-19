<?php

function build_template_header($metadata)
{
    $CI =& get_instance();

    if(in_array($CI->pageType, array('admin', 'account')) && usertype('admin')) {
        $logoContainer = null;
    } else {
        $logoContainer = build_logo_section($metadata['section']);
    }

    return <<<HTML
    {$logoContainer}
HTML;
    return true;
}

function build_template_footer($metadata)
{
    $CI =& get_instance();
    if(in_array($CI->pageType, array('admin', 'account')) && usertype('admin')) {
        $footerContainer = null;
    } else {
        $footerContainer = build_page_footer($metadata['section']);
    }

    return <<<HTML
    {$footerContainer}
HTML;
    return true;
}