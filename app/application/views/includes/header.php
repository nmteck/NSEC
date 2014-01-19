<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<!doctype html>
<!--[if lt IE 7 ]> <html class="ie ie6 no-js" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie ie7 no-js" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie ie8 no-js" lang="en"> <![endif]-->
<!--[if IE 9 ]>    <html class="ie ie9 no-js" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--><html class="no-js" lang="en"><!--<![endif]-->
<head>
<?php
    echo build_header_scripts(
        $metadata['title'],
        $metadata['section'],
        (isset($content) && isset($content->Content)
            ? $content
            : NULL
        )
    );

    if (is_logged_in()) {
        include 'account_header.php';
    }
?>
<script src="<?php echo $this->config->item('js_dir'); ?>javascript.js" type="text/javascript" ></script>
</head>

<body class="<?php
    echo isset($pagetype) ? $pagetype . ' ' : NULL ;
    echo (isset($this->pageType) && (!isset($pagetype) || (isset($pagetype) && ($this->pageType != $pagetype))))
        ? $this->pageType : NULL;
    echo isset($admin_page) ? ' ' . $admin_page : ' site';
    echo isset($pagekey) ? ' ' . $pagekey : ' page';
    echo '" id="';
    $newkey = isset($pageid)
        ? $pageid
        : ((isset($contentPage) ? ' ' . $contentPage : NULL)
            ? $contentPage
            : (isset($content->ContentName) ? ' ' . $content->ContentName : NULL));
    echo $newkey;
    if (!isset($pagekey)){
        $pagekey = $newkey;
    }
    ?>">
    <div id="main">
    <div id="mainShell">
    <div id="wrapper">
        <div id="header">
            <?php echo build_logo_section($metadata['section']);?>
            <div id="mainnav">
                <?php echo ($this->pageType == 'account')
                    ? ((usertype('admin') && (is_logged_in()) && isset($CONTENT['headeruser'])
                        ? $CONTENT['headeruser']
                        : '<a href="/account/logout/">Logout</a>'))
                    : (isset($CONTENT['headermenu'])
                        ? $CONTENT['headermenu']
                        : '');
                ?>
            </div>
        </div>

        <?php echo (isset($homepage) && isset($CONTENT['mainfront']))
                ? $CONTENT['mainfront']
                : null;

            $position = 'left';
            if ($this->pageType != 'account') include 'sidebar.php';
        ?>

        <div id="content">