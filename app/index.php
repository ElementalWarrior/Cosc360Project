<?php
require_once('helper.php');

$path = $_SERVER['REQUEST_URI'];
// switch($path){
// 	case '/login.php':
// 	include('login.php');
// 	break;
//
// 	case '/thread.php':
// 	include('thread.php');
// 	break;
//
// 	case '/register.php':
// 	include('register.php');
// 	break;
//
// 	default:
// 	include('home.php');
// 	break;
// }
$view = 'login';
$controller = 'account';

app_include('routing.php');
check_route($_SERVER['REQUEST_URI']);
// print_r($_SERVER);
include('/views/shared/layout.php');
