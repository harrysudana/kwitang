<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Academic Free License version 3.0
 *
 * This source file is subject to the Academic Free License (AFL 3.0) that is
 * bundled with this package in the files license_afl.txt / license_afl.rst.
 * It is also available through the world wide web at this URL:
 * http://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package     CodeIgniter
 * @author      EllisLab Dev Team
 * @copyright   Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @license     http://opensource.org/licenses/AFL-3.0 Academic Free License (AFL 3.0)
 * @link        http://codeigniter.com
 * @since       Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

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
|   example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|   http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|   $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|   $route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|   $route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples: my-controller/index -> my_controller/index
|       my-controller/my-method -> my_controller/my_method
*/
$route['default_controller']   = 'app';
$route['404_override']         = 'app/error404';
$route['translate_uri_dashes'] = FALSE;

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
/* Location: ./backend/config/routes.php */
