<?php
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
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// ==================== API ROUTES ====================
// Authentication
$route['api/login'] = 'api/login';
$route['api/logout'] = 'api/logout';

// Dashboard API
$route['api/dashboard/stats'] = 'api/dashboard_stats';
$route['api/dashboard/recent-activities'] = 'api/recent_activities';

// User Management API
$route['api/users'] = 'api/users';
$route['api/users/create'] = 'api/create_user';
$route['api/users/(:num)'] = 'api/user/$1';
$route['api/users/(:num)/update'] = 'api/update_user/$1';
$route['api/users/(:num)/delete'] = 'api/delete_user/$1';

// Blog Management API
$route['api/blogs'] = 'api/blogs';
$route['api/blogs/create'] = 'api/create_blog';
$route['api/blogs/(:num)'] = 'api/blog/$1';
$route['api/blogs/(:num)/update'] = 'api/update_blog/$1';
$route['api/blogs/(:num)/delete'] = 'api/delete_blog/$1';

// Project Management API
$route['api/projects'] = 'api/projects';
$route['api/projects/create'] = 'api/create_project';
$route['api/projects/(:num)'] = 'api/project/$1';
$route['api/projects/(:num)/update'] = 'api/update_project/$1';
$route['api/projects/(:num)/delete'] = 'api/delete_project/$1';

// Utility API
$route['api/health'] = 'api/health';
$route['api/options'] = 'api/options';

// ==================== WEB ROUTES ====================
// Dashboard
$route['dashboard'] = 'dashboard/index';
$route['dashboard/stats'] = 'dashboard/get_stats';
$route['dashboard/activities'] = 'dashboard/get_recent_activities';
$route['dashboard/updates'] = 'dashboard/get_updates';

// User Management
$route['users'] = 'users/index';
$route['users/create'] = 'users/create';
$route['users/(:num)/edit'] = 'users/edit/$1';
$route['users/(:num)/delete'] = 'users/delete/$1';

// Blog Management
$route['blogs'] = 'blogs/index';
$route['blogs/create'] = 'blogs/create';
$route['blogs/(:num)/edit'] = 'blogs/edit/$1';
$route['blogs/(:num)/delete'] = 'blogs/delete/$1';
$route['blogs/(:num)'] = 'blogs/get_blog/$1';

// Project Management
$route['projects'] = 'projects/index';
$route['projects/create'] = 'projects/create';
$route['projects/(:num)/edit'] = 'projects/edit/$1';
$route['projects/(:num)/delete'] = 'projects/delete/$1';
$route['projects/(:num)'] = 'projects/get_project/$1';

// Authentication
$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';
