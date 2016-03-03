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
//this controller is about studio
class StudioController extends Controller
{
	/**
	*studio submit identification info
	*/
	public function submitStudioInfo(Request $request)
	{
		$data['name'] = $request->input('name');
		$data['tel'] = $request->input('tel');
		$data['address'] = $request->input('address');
		if(!$data['name']||!$data['address'])
		{
			return response()->json([
	       		'errNo' => ErrorCode::COMMON_PARAM_ERROR,
	       		'errMsg' => '参数错误',
	       		'result' => 'false',
    		]); 
		}
		if(!$this->_checkPhone($data['tel']))
		{
			return response()->json([
	       		'errNo' => ErrorCode::COMMON_USER_CHECKPHONE_ERROR,
	       		'errMsg' => '手机格式不正确',
	       		'result' => 'false',
    		]); 
		}
		if($data)
		{
			$studio = new Studio();
			$res = $studio->submitStudioInfo($data);
			if($res)
			{
				return response()->json([
		       		'errNo' => 0,
		       		'errMsg' => 'true',
		       		'result' => $res,
    			]); 				
			}
		}
		return response()->json([
	       		'errNo' => ErrorCode::COMMON_PARAM_ERROR,
	       		'errMsg' => '参数错误',
	       		'result' => 'false',
    		]); 
	}
	//手机格式验证
    private function _checkPhone($phone)
   	{
   		return preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|17[0-9]{1}[0-9]{8}$/", $phone)?true:false; 
   	}
}
?>