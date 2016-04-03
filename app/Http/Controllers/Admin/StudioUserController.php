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
    private $user_id;
    private $studio_id;
    public function __construct(Request $request){
        $sessionUser = $request->session()->get('userInfo');
        $this->user_id = $sessionUser['user_id'];
        $this->studio_id = $sessionUser['studio_id'];
    }
   public function test(Request $Request)
    {
        $studioUser = new StudioUser();
        $res = $studioUser->getRandomCode(5);
        die();
    }
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
            $userInfo = array();
            $userInfo['user_id'] = $user->user_id;
            $userInfo['studio_id'] = $user->studio_id;
            $request->session()->put('userInfo',$userInfo);
            $login_num = $user->login_num+1;
            $studioUser->updateLoginNum($user->user_id,$login_num);
            if($user->login_num == 0)
            {
                return response()->json([
                    'errNo' => ErrorCode::COMMON_USER_LOGIN_MODIFY,
                    'errMsg' => '',
                    'result' => 'true',
                ]);
            }
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
        $sessionUser = $request->session()->get('userInfo');
        if(empty($sessionUser) || !array_key_exists('user_name', $sessionUser) || empty($sessionUser['user_name']) || empty($sessionUser['user_id']))
        {
            return response()->json([
                'errNo' => ErrorCode::COMMON_NOT_LOGIN,
                'errMsg' => '用户未登录',
                'result' => array(),
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
        $uid = $request->input('uid');
		$upyun = new UpYun(env('UPYUN_AVATAR_BUCKET'),
        env('UPYUN_USER'), env('UPYUN_PWD'),
        env('UPYUN_SERVER'), env('UPYUN_TIMEOUT'));
        try {
            $fileName = '/upload/images/header/'.$uid. '-' . str_random(10) . '.jpg';
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
        $length = 6;
        $int = rand(pow(10,($length-1)), pow(10,$length)-1);
        $data['verify_code'] = $int;
        $data['created_time'] = time();
        $studioUser = new StudioUser(); 
        if (_checkPhone($data['phone'])) {
            $result = SENDSMS::sendSeeyouSMS($data['phone'], array($int), "77073");
            if($result->statusCode!=0) {
                 return response()->json([
                    'errNo' => ErrorCode::COMMON_GETVERTIFY_ERROR,
                    'errMsg' => '获取验证码失败',
                    'result' => 'false',
                ]);
            }else{
                $res = $studioUser->insertVerifyCode($data);
                if(!$res)
                {
                   return response()->json([
                    'errNo' => ErrorCode::COMMON_GETVERTIFY_ERROR,
                    'errMsg' => '验证码插入数据库失败',
                    'result' => 'false',
                ]); 
                }
                $smsmessage = $result->TemplateSMS;
                 return response()->json([
                    'errNo' => ErrorCode::COMMON_OK,
                    'errMsg' => '验证成功',
                    'result' =>  'true',
                ]);           
            }
        } else {
             return response()->json([
                    'errNo' => ErrorCode::COMMON_USER_CHECKPHONE_ERROR,
                    'errMsg' => '手机号格式不正确',
                    'result' => 'false',
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
                    'result' => 'false',
                ]);
        }
        $time_differ = time() - $code->created_time -600000;
        if($code->verify_code != $data['verify_code'] || $time_differ > 0)
        {
               return response()->json([
                    'errNo' => ErrorCode::COMMON_VERTIFY_ERROR,
                    'errMsg' => '验证码不正确或者超时',
                    'result' => 'false',
                ]);  
        }
        $userInfo = $studioUser->getUserByPhone($data['phone']);
        $data['user_id'] = $userInfo->user_id;
        if (!$result)
        {
            return response()->json([
                    'errNo' => ErrorCode::COMMON_RESET_ERROR,
                    'errMsg' => '重置密码失败',
                    'result' => 'false',
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
                    'errNo' => ErrorCode::COMMON_RESET_ERROR,
                    'errMsg' => '重置密码失败',
                    'result' => 'false',
                ]);  
        }
    }
    //用户密码重置，知道密码
    public function resetPassword(Request $request)
    {
        $data['user_id'] = $request->input('user_id');
        $data['old_password'] = $request->input('old_password');
        $data['new_password'] = $request->input('new_password');
        $studioUser = new StudioUser();
        $userInfo = $studioUser->getUserById($data['user_id']);
        if($studioUser->checkPassword($data['user_id'],$data['old_password']) == false)
        {
                return response()->json([
                    'errNo' => ErrorCode::COMMON_PASSWD_ERROR, //100012
                    'errMsg' => '密码错误',
                    'result' => 'false',
                ]);  
        }
        if($studioUser->resetPassword($data) == false)
        {
            return response()->json([
                    'errNo' => ErrorCode::COMMON_RESET_ERROR,
                    'errMsg' => '重置密码失败',
                    'result' => 'false',
                ]);  
        } else {
            return response()->json([
                    'errNo' => ErrorCode::COMMON_OK,
                    'errMsg' => '重置密码成功',
                    'result' => 'true',
                ]);  
        }
    }

    //控制用户授权
    /**
    *只有管理员拥有最高权限，可以设置用户的各种权限
    */
    public function setUserPower(Request $request)
    {
        
    }
    //用户退出登陆
    public function logout(Request $request)
    {
        $request->session()->forget('userInfo');
    }
}
?>