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
use App\Http\Models\Craft;
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
			$crafts = $craft->queryCraftOfEnd($studioId);
			//crafts = 成品雕件
			if($crafts)
			{
				foreach ($crafts as $key => $value) {
					$res[$key]['img_id'] = $value->img_id;
					$res[$key]['img_url'] = $value->img_url;
					$res[$key]['studio_id'] = $value->studio_id;
					$res[$key]['craft_id'] = $value->craft_id;
				} 
				return response()->json([
		       		'errNo' => 0,
		       		'errMsg' => 'true',
		       		'result' => $res,
    			]); 				
			}
			return response()->json([
	       		'errNo' => 0,
	       		'errMsg' => '雕件数为0',
	       		'result' => '',
    		]); 

		}
		return response()->json([
	       		'errNo' => ErrorCode::COMMON_STUDIO_ID_ERROR,
	       		'errMsg' => '工作室id为非数字',
	       		'result' => 'false',
    		]); 
	}
}
?>