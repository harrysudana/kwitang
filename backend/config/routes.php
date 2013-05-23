<?php  if ( ! defined('BASEPATH')) exit ('No direct script access allowed');
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
|    example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|    http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|    $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|    $route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = 'app';
$route['404_override']       = 'app/error404';

$route['not_found']    = 'app/error404';
$route['search']       = 'app/search';
$route['preview/(.*)'] = "app/preview/$1";
$route['rss/(.*)']     = "rss/index/$1";
$route['v/(.*)']       = "v/index/$1";

// ----> lang / [KEYWORD] / param/param/param
$route['(:any)/channel/(:any)']               = "app/structure/$1/$2";
$route['(:any)/read/(:any)']                  = "app/read/$1/$2";
$route['(:any)/read/(:any)/(:any)']           = "app/read/$1/$2/$3";
$route['(:any)/read/(:any)/(:any)/(:any)']    = "app/read/$1/$2/$3/$4";
$route['(:any)/index/(:any)']                 = "app/content_index/$1/$2";
$route['(:any)/index/(:any)/(:any)']          = "app/content_index/$1/$2/$3";
$route['(:any)/index/(:any)/(:any)/(:any)']   = "app/content_index/$1/$2/$3/$4";
$route['(:any)/archive/(:any)']               = "app/archive/$1/$2";
$route['(:any)/archive/(:any)/(:any)']        = "app/archive/$1/$2/$3";
$route['(:any)/archive/(:any)/(:any)/(:any)'] = "app/archive/$1/$2/$3/$4";
$route['(:any)/view/(:any)']                  = "app/view/$1/$2";
$route['(:any)/view/(:any)/(:any)']           = "app/view/$1/$2/$3";
$route['(:any)/view/(:any)/(:any)/(:any)']    = "app/view/$1/$2/$3/$4";

// lang = ''
// ---> [KEYWORD] / param/param/param
$route['channel/(:any)']               = "app/structure//$1";
$route['read/(:any)']                  = "app/read//$1";
$route['read/(:any)/(:any)']           = "app/read//$1/$2";
$route['read/(:any)/(:any)/(:any)']    = "app/read//$1/$2/$3";
$route['index/(:any)']                 = "app/content_index//$1";
$route['index/(:any)/(:any)']          = "app/content_index//$1/$2";
$route['index/(:any)/(:any)/(:any)']   = "app/content_index//$1/$2/$3";
$route['archive/(:any)']               = "app/archive//$1";
$route['archive/(:any)/(:any)']        = "app/archive//$1/$2";
$route['archive/(:any)/(:any)/(:any)'] = "app/archive//$1/$2/$3";
$route['view/(:any)']                  = "app/view//$1";
$route['view/(:any)/(:any)']           = "app/view//$1/$2";
$route['view/(:any)/(:any)/(:any)']    = "app/view//$1/$2/$3";


/* End of file routes.php */
/* Location: ./application/config/routes.php */
