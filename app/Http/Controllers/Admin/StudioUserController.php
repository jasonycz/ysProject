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
use App\Http\Models\sms\SENDSMS;
class StudioUserController extends Controller
{
	//使用手机号登陆，参数：手机号，密码
	public function login(Request $request)
	{
		$phone = $request->input('phone');
		$passwd = $request->input('passwd');
		if(!_checkPhone($phone))
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
	   //this is yangping's code begin 
    //app注册
    /**短信验证
    */

    public function getVerify(Request $request)
    {
        $data['phone'] = $request->input('phone');
        $length = 4;
        $int = rand(pow(10,($length-1)), pow(10,$length)-1);
        $data['verify_code'] = $int;
        $data['created_time'] = time();
        $studioUser = new StudioUser();
        //$int = DARRAY::sms_rand(6);
        if (_checkPhone($data['phone'])) {
            $result = SENDSMS::sendSMS($data['phone'], array($int, '5'), "35155");
            if($result->statusCode!=0) {
                 return response()->json([
                    'errNo' => ErrorCode::COMMON_GETVERTIFY_ERROR,
                    'errMsg' => '获取验证码失败',
                    'result' => null,
                ]);
            }else{
                $res = $studioUser->insertVerifyCode($data);
                if(!$res)
                {
                   return response()->json([
                    'errNo' => ErrorCode::COMMON_GETVERTIFY_ERROR,
                    'errMsg' => '验证码插入数据库失败',
                    'result' => null,
                ]); 
                }
                $smsmessage = $result->TemplateSMS;
                 return response()->json([
                    'errNo' => ErrorCode::COMMON_OK,
                    'errMsg' => '验证成功',
                    'result' =>  null,
                ]);           
            }
        } else {
             return response()->json([
                    'errNo' => ErrorCode::COMMON_USER_CHECKPHONE_ERROR,
                    'errMsg' => '手机号格式不正确',
                    'result' => null,
                ]);  
        }
    }
}
?>