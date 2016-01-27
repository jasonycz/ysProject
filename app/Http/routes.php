<?php
/*
use App\Services\Routes as RoutesManager;


$routesManager = new RoutesManager();
$routesManager->admin()->www();
*/



//定义路由Get
Route::get('/', 'Home\IndexController@index'); 