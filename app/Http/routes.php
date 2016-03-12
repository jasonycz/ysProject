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
//检查用户是否登录
Route::post('user/checklogin','Admin\StudioUserController@checkUserLogined');
//检查用户名是否重复
Route::post('user/exists','Admin\StudioUserController@checkUnameExists');
//用户密码重置，知道密码
Route::post('user/resetpwd','Admin\StudioUserController@resetPassword')->middleware(['sessionLoginVerify']);
//用户密码重置,忘记密码
Route::post('user/resetbyphone','Admin\StudioUserController@resetPasswordPhone');
//工作室成品展示
Route::get('studio/showcraft', 'Admin\CraftController@showCraftOfEnd')->middleware(['sessionLoginVerify']);
//申请工作室信息
Route::post('studio/apply','Admin\StudioController@submitStudioInfo');//->middleware(['sessionLoginVerify']);
//头像上传接口
Route::post('user/uploadheadportrait','Admin\StudioUserController@uploadHeadPortrait')->middleware(['sessionLoginVerify']);
//查看发布后的作品
Route::get('studio/showonecraft','Admin\CraftController@showProduction')->middleware(['sessionLoginVerify']);
//雕件文章发布
Route::post('studio/publish','Admin\CraftController@publishArticle')->middleware(['sessionLoginVerify']);