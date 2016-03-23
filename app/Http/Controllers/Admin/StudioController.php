<?php
/**
*
* this is yangping's code,the time is 2016.03.03
*/
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Log; 
use ErrorCode;
use Illuminate\Http\Request;
use App\Http\Models\Studio;
use App\Http\Models\StudioUser;
//this controller is about studio
class StudioController extends Controller
{
	private $user_id;
	private $studio_id;
	public function __construct(Request $request){
		$sessionUser = $request->session()->get('userInfo');
        $this->user_id = $sessionUser->user_id;
        $this->studio_id = $sessionUser->studio_id;
	}
	/**
	*studio submit identification info
	*/
	public function submitStudioInfo(Request $request)
	{
		$data['name'] = $request->input('name'); //工作室名称
		$data['user_name'] = $request->input('user_name');//联系人姓名 
		$data['tel'] = $request->input('tel');
		$data['address'] = $request->input('address');
		$data['describe'] = $request->input('describe');
		if(!$data['name']||!$data['address'])
		{
			return response()->json([
	       		'errNo' => ErrorCode::COMMON_PARAM_ERROR,
	       		'errMsg' => '参数错误',
	       		'result' => 'false',
    		]); 
		}
		if( ! _checkPhone($data['tel']))
		{
			return response()->json([
	       		'errNo' => ErrorCode::COMMON_USER_CHECKPHONE_ERROR,
	       		'errMsg' => '手机格式不正确',
	       		'result' => 'false',
    		]); 
		}
		$studioUser = new StudioUser();
		$data['phone'] = $request->input('tel');
	/*	$code = $studioUser->getVerifyCode($data);
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
        }*/
		if($data)
		{
			$studio = new Studio();
			$studioUser = new StudioUser();
			$studioInfo = $studio->queryStudioInfo($data);
			if($studioInfo)
			{
				return response()->json([
	       			'errNo' => ErrorCode::COMMON_STUDIO_EXIST,
	       			'errMsg' => '工作室已经存在',
	       			'result' => 'false',
    			]); 
			}
			$res_studio = $studio->submitStudioInfo($data);
			//填写工作室基本信息之后，在studio_users表增加管理员，0:管理员,1：普通用户
			$studioInfo = $studio->queryStudioInfo($data);
			$data['studio_id'] = $studioInfo->studio_id;
			$data['pwd'] = $request->input('tel');
			$res_user = $studioUser->createUser($data);
			if($res_studio&&$res_user)
			{
				return response()->json([
		       		'errNo' => 0,
		       		'errMsg' => 'true',
		       		'result' => $studioInfo,
    			]); 				
			}
		}
		return response()->json([
	       		'errNo' => ErrorCode::COMMON_PARAM_ERROR,
	       		'errMsg' => '参数错误',
	       		'result' => 'false',
    		]); 
	}
}
?>