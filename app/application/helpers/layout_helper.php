<?php
    function build_logo_section($type=NULL){
        $CI =& get_instance();

        if($CI->pageType != 'account')
        {
            if(is_logged_in())
            {
                $link = '
                    <div class="menu">
                        <a href="/account">My Account</a>
                        | <a href="/account/logout">Logout</a>
                    </div>
                ';
            } else {
                $link = '
                ';

            }

            $return = '<div id="top_links">'.$link.'</div>';

            return $return;
        }
    }

    function build_development_header_scripts($title=NULL, $section=NULL, $content=NULL){
        $CI =& get_instance();

        if ($content != NULL) {
            $title = $content->MetaTitle;
            $keywords = $content->MetaTags;
            $description = $content->MetaDesc;
        } else {
            $keywords = $description = NULL;
        }

        if($title != NULL) {
            $title .= ' | ';
        }

        $title .= $CI->config->item('SiteName');

        $return = <<<EOD
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <title>$title</title>
    <meta name="title" content="$title"/>
    <meta name="description" content="$description"/>
    <meta name="keywords" content="$keywords"/>
    <meta name="author" content=""/>
    <meta name="Copyright" content=""/>

    <link rel="stylesheet" href="/-/css/screen/style.css"/>
    <link rel="stylesheet" href="/-/css/jquery/screen.nmtModal.css"/>
    <link rel="stylesheet" href="/-/css/jquery/msg_styles.css"/>

    <link rel="stylesheet" href="/-/css/jquery/jquery-ui-1.8.9.custom.css"/>
    <script src="/-/js/jquery/jquery.js" type="text/javascript" ></script>
    <script src="/-/js/jquery/jquery.ui.js" type="text/javascript" ></script>
    <script src="/-/js/jquery/jquery.nmtModal.js" type="text/javascript" ></script>
    <script src="/-/js/jquery/jquery.message.js" type="text/javascript" ></script>

EOD;

        if ($section != 'account') {
            $return .= '<link rel="stylesheet" href="/-/css/screen/page.css"/>';
        } else {
            $return .= '<link rel="stylesheet" href="/-/css/screen/admin.css"/>';
        }


        return $return;
    }

    function build_header_scripts($title=NULL, $section=NULL, $content=NULL){
        if (strpos($_SERVER['HTTP_HOST'], 'www.') === false) {
            return build_development_header_scripts($title, $section, $content);
        }

        $CI =& get_instance();

        if ($content != NULL) {
            $title = $content->MetaTitle;
            $keywords = $content->MetaTags;
            $description = $content->MetaDesc;
        } else {
            $keywords = $description = NULL;
        }

        if ($title != NULL) {
            $title .= ' | ';
        }

        $title .= $CI->config->item('SiteName');

        $return = <<<EOD
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <title>$title</title>
    <meta name="title" content="$title"/>
    <meta name="description" content="$description"/>
    <meta name="keywords" content="$keywords"/>
    <meta name="author" content=""/>
    <meta name="Copyright" content=""/>

    <link rel="stylesheet" href="/-/css/screen/style.css"/>
    <link rel="stylesheet" href="/-/css/jquery.styles.min.js"/>

    <link rel="stylesheet" href="/-/css/jquery/jquery.styles.css"/>
    <script src="/-/js/jquery/jquery.package.min.js" type="text/javascript" ></script>
    <script src="/-/js/jquery/jquery.plugins.min.js" type="text/javascript" ></script>

EOD;

        if ($section != 'account') {
            $return .= '<link rel="stylesheet" href="/-/css/page.css"/>';
        } else {
            $return .= '<link rel="stylesheet" href="/-/css/screen/admin.css"/>';
        }

        return $return;
    }

    function getAnalyticsCode(){
        $CI =& get_instance();
        $return = '';

        if(strpos($_SERVER['HTTP_HOST'], 'www.') === true) {
            $return = <<<EOD
                <div style="display:none">
                    <script type="text/javascript">
                        <!--
                        var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
                        document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
                        // --></script>
                        <script type="text/javascript"><!--
                        try {
                        var pageTracker = _gat._getTracker("UA-2799369-12");
                        pageTracker._trackPageview();
                        } catch(err) {}
                        // -->
                    </script>
                </div>
EOD;
        }

        return $return;
    }

    function build_page_footer(){
        $date = date('Y');
        $statsCode = getAnalyticsCode();

        $year = date('Y');

        return <<<EOD
                <div class="links">
                    <a href="/login/" target="_top">Login</a>
                    . <a href="/" target="_top">Home</a>
                </div>

                <div class="notice">
                    &copy; {$year} MyDomain.com
                    | All rights reserved.
                </div>
                $statsCode
EOD;
    }
