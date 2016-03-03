<?php
/*
use App\Services\Routes as RoutesManager;


$routesManager = new RoutesManager();
$routesManager->admin()->www();
*/



//定义路由Get
Route::get('/', 'Home\IndexController@index'); 
//yangping's code begin
//手机登陆接口
Route::post('user/login', 'Admin\StudioUserController@login');
Route::get('studio/showcraft', 'Admin\CraftController@showCraftOfEnd');
Route::post('studio/apply','Admin\StudioController@submitStudioInfo');
//头像上传接口
Route::post('user/uploadheadportrait','Admin\StudioUserController@uploadHeadPortrait');
//查看发布后的作品
Route::get('studio/showonecraft','Admin\CraftController@showProduction');