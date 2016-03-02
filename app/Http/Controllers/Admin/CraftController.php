<?php
/**
*
* this is yangping's code,the time is 2016.03.02
*/
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Log; 
use ErrorCode;
use Illuminate\Http\Request;

class CraftController extends Controller
{
	/**已发布成品展示
	*input: 工作室id
	*output: 雕件ID，图片url，雕件的描述信息
	*/
	public function showCraftOfEnd(Request $request)
	{
		$studioId = $request->input('studio_id');
		if(is_numeric($studioId))
		{
			$craft = new Craft();
		}
		return response()->json([
	       		'errNo' => ErrorCode::COMMON_STUDIO_ID_ERROR,
	       		'errMsg' => '工作室id为非数字',
	       		'result' => 'false',
    		]); 
	}
}
?>