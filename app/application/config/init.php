<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// CMS Defaults
$config['restricted_sections']     = array(
        'account',
        'admin'
    );
$config['bio_max_length']        = 1000;
$config['short_bio_max_length']    = 200;
$config['SnippetLength']        = 235;
$config['AuthArticles']            = 1;
$config['headerTagType'] = 'h1 class="title" ';
$config['StreamArticles']        = 1;
$config['HomeStreamAmount']        = 5;
$config['ArticleStreamAmount']    = 15;
$config['StreamHome']            = 0;
$config['live_db']                = 'default';
$config['IndexCategory']        = 'default';
$config['dataTables'] = array(
        'state' => array('stateID', 'StateName', NULL, 'state'),
        'levelofschooling' => array('OutcomeID', 'OutcomeName', 'Order'),
        'referralsources' => array('ReferralID', 'ReferralSource', NULL, 'referral'),
        'careerfields' => array('CareerFieldID', 'CareerName', NULL, 'careerfield'),
        'curriculum' => array('CurriculumID', 'CurriculumName', NULL, 'curriculum'),
        'employers_memberships' => array('EmployerMembershipID', 'Membership', 'Order', 'membertype'),
        'gender' => array('GenderID', 'Gender', NULL, 'sex'),
        'yearsofexperience' => array('LevelID', 'Years', 'Order'),
        'membership_types' => array('MembershipTypeID', 'MembershipType', 'Order', 'membertype')
    );

// Paypal API Settings
$config['API_User']                = '';
$config['API_Password']            = '';
$config['API_Email']            = '';
$config['API_Signature']        = '';
$config['API_Request_Date']        = '';
$config['API_Version']            = '';              // NVPRequest for submitting to server

// Global Site Settings
$config['SiteName']    = 'My Domain';
$config['SiteEmail']    = 'you@yourdomain.com';
$config['NoReplyEmail']    = 'you@yourdomain.com';

$config['listingsTable'] = 'listings';
$config['listingsFolder'] = '/-/images/listings';

$config['user_folder']    = 'user';
$config['member_folder']    = 'member';

$config['user_url']    = 'user';
$config['member_url']    = 'member';

$config['user_title']    = 'users';
$config['member_title']    = 'members';
$config['news_content_urls']    = array('news');

// Site Specific Settings
$config['listingsPageTitle'] = 'My Listings';
$config['gallery_dir']    = "/-/images/gallery/";
$config['css_dir']    = "/-/css/";
$config['js_dir']    = "/-/js/";
?>