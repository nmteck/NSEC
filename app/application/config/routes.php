<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	www.your-site.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved 
| routes must come before any wildcard or regular expression routes.
|
*/

$route['default_controller'] = "home";
$route['scaffolding_trigger'] = "";

$route['account'] = "account";
$route['account/([a-z0-9A-Z-_]+)'] = "account/$1";

$route['admin'] = "admin";
$route['admin/([a-z0-9A-Z-_]+)'] = "admin/$1";

$route['ajax'] = "ajax";
$route['ajax/([a-z0-9A-Z-_]+)'] = "ajax/$1";

$route['listings'] = "listings";
$route['listings/([a-z0-9A-Z-_]+)'] = "listings/$1";
$route['listings/page/([0-9]+)'] = "listings/forsale//$1";
$route['listings/details/([a-z0-9A-Z-_]+)'] = "listings/forsale/$1";

$route['login'] = "login";

$route['search'] = "listings";

$route['([a-z0-9A-Z-_]+)/([a-z0-9A-Z-_]+)'] = "home/index/$1/$2";
$route['([a-z0-9A-Z-_]+)'] = "home/index/default/$1";

/* End of file routes.php */
/* Location: ./system/application/config/routes.php */
