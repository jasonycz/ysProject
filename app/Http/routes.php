<?php
/*
use App\Services\Routes as RoutesManager;


$routesManager = new RoutesManager();
$routesManager->admin()->www();
*/



//定义路由Get
Route::get('/', 'Home\IndexController@index'); 
//yangping's code begin
Route::post('user/login', 'Admin\StudioUserController@login');
//头像上传接口
Route::post('user/uploadheadportrait','Admin\StudioUserController@uploadHeadPortrait');