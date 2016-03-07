<?php
/*
use App\Services\Routes as RoutesManager;


$routesManager = new RoutesManager();
$routesManager->admin()->www();
*/



//定义路由Get
Route::get('/', 'Home\IndexController@index'); 
//yangping's code begin
//获取短信验证码
Route::post('user/getverify','Admin\StudioUserController@getVerify');
//手机登陆接口
Route::post('user/login', 'Admin\StudioUserController@login');
//工作室成品展示
Route::get('studio/showcraft', 'Admin\CraftController@showCraftOfEnd');
//申请工作室信息
Route::post('studio/apply','Admin\StudioController@submitStudioInfo');
//头像上传接口
Route::post('user/uploadheadportrait','Admin\StudioUserController@uploadHeadPortrait');
//查看发布后的作品
Route::get('studio/showonecraft','Admin\CraftController@showProduction');
//雕件文章发布
Route::post('studio/publish','Admin\CraftController@publishArticle');