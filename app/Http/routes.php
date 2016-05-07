<?php 
Route::get('user/test','Admin\StudioUserController@test');

//定义路由Get
Route::get('/', 'Admin\IndexController@index'); 
//yangping's code begin
//获取短信验证码
Route::post('user/getverify','Admin\StudioUserController@getVerify');
//手机登陆接口
Route::post('user/login', 'Admin\StudioUserController@login');
//检查用户是否登录
Route::post('api/me','Admin\StudioUserController@checkUserLogined');
//检查用户名是否重复
Route::post('user/exists','Admin\StudioUserController@checkUnameExists');
//用户密码重置，知道密码
Route::post('user/resetpwd','Admin\StudioUserController@resetPassword')->middleware(['sessionLoginVerify']);
//用户密码重置,忘记密码
Route::post('user/resetbyphone','Admin\StudioUserController@resetPasswordPhone');
//头像上传接口
Route::post('user/uploadheadportrait','Admin\StudioUserController@uploadHeadPortrait')->middleware(['sessionLoginVerify']);
Route::post('user/logout','Admin\StudioUserController@logout')->middleware(['sessionLoginVerify']);
//工作室接口列表
//申请工作室信息
Route::post('studio/apply','Admin\StudioController@submitStudioInfo');//->middleware(['sessionLoginVerify']);
//工作室成品展示
Route::get('studio/showcraft', 'Admin\CraftController@showCraftOfEnd')->middleware(['sessionLoginVerify']);
//所有未发布雕件列表
Route::get('studio/listnofinish','Admin\CraftController@showAllCraft')->middleware(['sessionLoginVerify']);
//查看发布后的作品
Route::get('studio/showonecraft','Admin\CraftController@showProduction')->middleware(['sessionLoginVerify']);
//点击创建时,返回最后以后雕件id
Route::get('studio/getcid','Admin\CraftController@getLastCid')->middleware(['sessionLoginVerify']);
//新建雕件文章或修改雕件文章
Route::post('studio/upData','Admin\CraftController@addArticle')->middleware(['sessionLoginVerify']);
//新增雕件时间轴或修改时间轴
Route::post('studio/upTimeData','Admin\CraftController@addTimeData')->middleware(['sessionLoginVerify']);
//雕件文章发布
Route::post('studio/publish','Admin\CraftController@publishCraft')->middleware(['sessionLoginVerify']);
//删除已发布雕件
Route::post('studio/delcraft','Admin\CraftController@delCraft')->middleware(['sessionLoginVerify']);;
//时间轴图片上传到upyun
Route::post('studio/uploaduyimg','Admin\CraftController@uploadImage')->middleware(['sessionLoginVerify']);
//时间轴删除上传到upyun的图片
Route::post('studio/deluyimg','Admin\CraftController@delImage')->middleware(['sessionLoginVerify']);
//软文修改页面
Route::get('studio/modifyArticle','Admin\CraftController@modifyArticle')->middleware(['sessionLoginVerify']);
//时间轴修改页面
Route::get('studio/modifyTime','Admin\CraftController@modifyTimeData')->middleware(['sessionLoginVerify']);
//图片库---返回某工作室下的所有图片
Route::get('studio/allimges','Admin\CraftController@returnAllImages')->middleware(['sessionLoginVerify']);

//文章图片添加
Route::post('craft/uploadarticleimages','Admin\CraftController@uploadArticleImages')->middleware(['sessionLoginVerify']);
//文章图片获取
Route::get('craft/getarticleimages','Admin\CraftController@getArticleImages')->middleware(['sessionLoginVerify']);
//手机端手机预览雕件
Route::get('wap/show','Wap\CraftController@showDetail');
//手机端查看所有已发布作品
Route::get('wap/showall','Wap\CraftController@showAllWorks');
//返回分享需要的js代码
Route::get('wap/sharesdk','Wap\CraftController@showWxSdk');

//设置员工权限
Route::post('user/setpower','Admin\StudioUserController@setUserPower');
