<?php
/**
*
* this is yangping's code,the time is 2016.03.01
*/
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Log; 
use ErrorCode;
use Illuminate\Http\Request;
use App\Http\Models\StudioUser;
use App\Http\UpYun;
class StudioUserController extends Controller
{
	//使用手机号登陆，参数：手机号，密码
	public function login(Request $request)
	{
		$phone = $request->input('phone');
		$passwd = $request->input('passwd');
		if(!$this->_checkPhone($phone))
		{
			return response()->json([
	       		'errNo' => ErrorCode::COMMON_USER_CHECKPHONE_ERROR,
	       		'errMsg' => '手机格式不正确',
	       		'result' => 'false',
    		]); 
		}

		$studioUser = new StudioUser();
		$user = $studioUser->logInCheck($phone,$passwd);

		if($user)
		{
			return response()->json([
           		'errNo' => ErrorCode::COMMON_OK,
           		'errMsg' => '',
           		'result' => 'true',
        	]);
		} 

		return response()->json([
       		'errNo' => ErrorCode::COMMON_USER_LOGIN_ERROR,
       		'errMsg' => '手机号不存在或者密码错误',
       		'result' => 'false',
    	]); 
	}

	public function uploadHeadPortrait(Request $request)
	{
			$upyun = new UpYun(env('UPYUN_AVATAR_BUCKET'),
	        env('UPYUN_USER'), env('UPYUN_PWD'),
	        env('UPYUN_SERVER'), env('UPYUN_TIMEOUT'));
	        try {
	            $fileName = '/' . str_random(10) . '.jpg';
	            $fp = fopen($_FILES['file']['tmp_name'], 'r');
	            $ret = $upyun->writeFile($fileName, $fp, true);
	            fclose($fp);
	            return response()->json([
	                'errNo' => 0,
	                'errMsg' => '',
	                'result' => [
	                    'img_url' => env('UPYUN_AVATAR_DOMAIN') . $fileName,
	                ]
	            ]);
	        } catch (Exception $e) {
	            return response()->json([
	                'errNo' => $e->getCode(),
	                'errMsg' => $e->getMessage(),
	            ]);
	        }
	}

	//手机格式验证
    private function _checkPhone($phone)
   	{
   		return preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|17[0-9]{1}[0-9]{8}$/", $phone)?true:false; 
   	}
}
?>