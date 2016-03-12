<?php
/**
*
* this is yangping's code,the time is 2016.03.01
*/
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Log;
use Session;
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
            $request->session()->put('userInfo',$user);
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
    //检查用户是否已登录
    public function checkUserLogined(Request $request)
    {
        $username = $request->input('uname');
        $sessionUser = $request->session()->get('userInfo');
        if(empty($sessionUser) || !array_key_exists('user_name', $sessionUser) || empty($sessionUser['user_name']))
        {
            return response()->json([
                'errNo' => ErrorCode::COMMON_NOT_LOGIN,
                'errMsg' => '用户未登录',
                'result' => null,
            ]);
        }else{
            return response()->json([
                'errNo' => ErrorCode::COMMON_OK,
                'errMsg' => '用户已登录',
                'result' => array('uname'=>$sessionUser['user_name'],'userid'=>$sessionUser['user_id'])
            ]);
        }
    }
    //检查用户名是否重复
    public function checkUnameExists(Request $request)
    {
        $uname = $request->input('uname');
        if(empty($uname) || !isset($uname))
        {
            return response()->json([
                'errNo' => ErrorCode::COMMON_USER_EMPTY,
                'errMsg' => '用户名不能为空',
                'result' => 'false',
            ]);
        }
        $studioUser = new StudioUser();
        $isExists = $studioUser->checkExists($uname);
        if(!empty($isExists))
        {
            return response()->json([
                'errNo' => ErrorCode::COMMON_USER_EXISTS,
                'errMsg' => '用户名已存在',
                'result' => 'false',
            ]); 
        }else
        {
            return response()->json([
                'errNo' => ErrorCode::COMMON_OK,
                'errMsg' => '可以使用',
                'result' => 'false',
            ]); 
        }

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
    /**
    *短信验证
    */

    public function getVerify(Request $request)
    {
        $data['phone'] = $request->input('phone');
        $length = 4;
        $int = rand(pow(10,($length-1)), pow(10,$length)-1);
        $data['verify_code'] = $int;
        $data['created_time'] = time();
        $studioUser = new StudioUser(); 
        if (_checkPhone($data['phone'])) {
            $result = SENDSMS::sendSeeyouSMS($data['phone'], array($int, '5'), "1");
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
    //用户密码重置,忘记密码
    public function resetPasswordPhone(Request $req)
    {
        $data['phone'] = $req->input('phone');
        $data['password'] = $req->input('password');
        $data['verify_code'] = $req->input('verify_code');
        $user = new User();
        $studioUser = new StudioUser();
        $code = $studioUser->getVerifyCode($data);
        if(!$code)
        {
                return response()->json([
                    'errNo' => ErrorCode::COMMON_VERTIFY_ERROR,
                    'errMsg' => '验证码不正确或者超时',
                    'result' => null,
                ]);
        }
        $time_differ = time() - $code->created_time -600000;
        if($code->verify_code != $data['verify_code'] || $time_differ > 0)
        {
               return response()->json([
                    'errNo' => ErrorCode::COMMON_VERTIFY_ERROR,
                    'errMsg' => '验证码不正确或者超时',
                    'result' => null,
                ]);  
        }
        $userInfo = $studioUser->getUserByPhone($data['phone']);
        $data['user_id'] = $userInfo->user_id;
        if (!$result)
        {
            return response()->json([
                    'errNo' => ErrorCode::COMMON_RESET_ERROR,
                    'errMsg' => '重置密码失败',
                    'result' => null,
                ]);
        }
        if($studioUser->resetPassword($data))
        {
            $res['user_id'] = $userInfo->id;
            return response()->json([
                'errNo' => ErrorCode::COMMON_OK,
                'errMsg' => '重置密码成功',
                'result' => [$res],
            ]);       
        }else
        {
               return response()->json([
                    'errNo' => ErrorCode::COMMON_REGISTER_ERROR,
                    'errMsg' => '重置密码失败',
                    'result' => null,
                ]);  
        }
    }
    //用户密码重置，知道密码
    public function resetPassword(Request $req)
    {
        $data['user_id'] = $req->input('user_id');
        $data['old_password'] = $req->input('old_password');
        $data['new_password'] = $req->input('new_password');
        $studioUser = new StudioUser();
        $userInfo = $studioUser->getUserById($data['user_id']);
        if($studioUser->checkPassword($data['user_id'],$data['old_password']) == false)
        {
                return response()->json([
                    'errNo' => ErrorCode::COMMON_PASSWD_ERROR, //10023
                    'errMsg' => '密码错误',
                    'result' => null,
                ]);  
        }
        if($studioUser->resetPassword($data) == false)
        {
            return response()->json([
                    'errNo' => ErrorCode::COMMON_RESET_ERROR,
                    'errMsg' => '重置密码失败',
                    'result' => null,
                ]);  
        } else {
            return response()->json([
                    'errNo' => ErrorCode::COMMON_OK,
                    'errMsg' => '重置密码成功',
                    'result' => true,
                ]);  
        }
    }
}
?>