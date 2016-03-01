<?php
/**
*
* this is yangping's code,the time is 2016.03.01
*/
namespace App\Http\Controllers\Admin;
use Log; 
use ErrorCode;
use Illuminate\Http\Request;
use App\Http\Models\StudioUser;
class StudioUserController extends Controller
{
	//使用手机号登陆，参数：手机号，密码
	public function login(Request $request)
	{
		$phone = $request->input('phone');
		$passwd = $request->input('passwd');
		$studioUser = new StudioUser();
		$user = $studioUser->logInCheck($phone,$passwd);
		if($user)
		{
			return response()->json([
           		'errNo' => ErrorCode::COMMON_OK,
           		'errMsg' => '',
           		'result' => 'true',
        	]);
		}else
		{
			return response()->json([
           		'errNo' => ErrorCode::COMMON_USER_LOGIN_ERROR,
           		'errMsg' => '手机号不存在或者密码错误',
           		'result' => 'false',
        	]);
		}
	}
}