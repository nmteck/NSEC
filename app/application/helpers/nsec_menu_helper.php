<?php



function build_admin_menu($type=NULL){

    $ads = $dashboard = $profile = $billing  = $reports = $faq = NULL;

    switch($type){
    case 'ads':
        $ads = ' id="current"';
    break; case 'adv_dash':
        $dashboard = ' id="current"';
    break; case 'profile':
        $profile = ' id="current"';
    break; case 'billing':
        $billing = ' id="current"';
    break; case 'reports':
        $reports = ' id="current"';
    break; case 'faq':
        $faq = 'id="current"';
    break; default:
        $dashboard = ' id="current"';

    }

    $return  = <<<EOD
        <ol>
            <li><a href="/" class="dropdown">Homepage</a>
            <ul class="sublinks">
            <li><a href="/account/">Dashboard</a></li>
            </ul>
            </li>
            <li><a href="/account/content/" class="dropdown">Content</a>
            <ul class="sublinks">
            <li><a href="/admin/newpage/articles/">New Article</a></li>
            <li><a href="/admin/newpage/blocks/">New Block</a></li>
            <li><a href="/admin/newpage/templates/">New Template</a></li>
            </ul>
            </li>
            <li><a href="/admin/manage/content_types" class="dropdown">Setup</a>
            <ul class="sublinks">
            <li><a href="javascript:mcImageManager.open();">Images</a></li>
            </ul>
            </li>
            <li><a href="/admin/users/users/" class="dropdown">Users</a>
            <ul class="sublinks">
            <li><a href="/account/profile/">My  Profile</a></li>
            <li><a href="/admin/users/members/">Organizations</a></li>
            <li><a href="/admin/import/">Import</a></li>
            </ul>
            </li>
            <li><a href="/admin/invoices/">Invoices/Quotes</a></li>
            <li class="last"><a href="/account/logout/">Logout</a></li>
        </ol>
EOD;


    return $return;
}

function get_sidebar_news()
{
    $CI =& get_instance();
    $CI->load->helper('nsec_query');
    $return = 'None Added Yet';

    $info = get_latest_news();
    if($info != false)
    {
        $query = $CI->contentmodel->getContent($info->ContentType, $info->ContentName);
        $content = $query['content'];
        $content->Content = substr(strip_tags($content->Content), 0, 500).'...';
        $return = <<<EOD
        <blockquote>
            <h3>$content->ContentTitle</h3>
            <p>$content->Content</p>
            <a href="/{$query['pagelink']}" class="read_more">Read More</a>
        </blockquote>
EOD;

    }

    return $return;
}

?>