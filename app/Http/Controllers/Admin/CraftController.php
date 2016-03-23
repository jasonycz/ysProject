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
use App\Http\Models\StudioArticle;
use App\Http\Models\CraftProcess;
use App\Http\Models\CraftImg;
class CraftController extends Controller
{
	private $user_id;
	private $studio_id;
	public function __construct(Request $request){
		$sessionUser = $request->session()->get('userInfo');
        $this->user_id = $sessionUser['user_id'];
		$this->studio_id = $sessionUser['studio_id'];
	}
	/**已发布成品展示
	*input: 工作室id
	*output: 雕件ID，图片url，雕件的描述信息
	*/
	public function showCraftOfEnd(Request $request)
	{
		// $studioId = $request->input('studio_id');
		if(is_numeric($this->studioId))
		{
			$craft = new Craft();
			$crafts = $craft->queryCraftOfEnd($this->studioId);
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
	/**
	*雕件文章发布及预览
	*/
	public function publishArticle(Request $request){
		$title = $request->input('title');
		$author = $request->input('author');
		$createDate = $request->input('createdate');
		// $createid = $request->input('createid','','int');
		$content = $request->input('content');
		// $studio_id = $request->input('studio_id','','int');
		$craft_id = $request->input('craft_id','','int');
		$ispublish = $request->input('publish',0,'int');
		//需要增加必填项的判断
		$params['article_name'] = $title;
		$params['author'] = $author;
		$params['studio_user_id'] = $title;
		$params['created_time'] = date('Y-m-d H:i:s',time());
		if(empty($createDate)){
			$params['article_time'] = date('Y-m-d H:i:s',time());	
		}else{
			$params['article_time'] = $createDate;
		}
		$params['content'] = $content;
		$params['studio_id'] = $this->studioId;
		$params['craft_id'] = $craft_id;
		$params['ispublish'] = $ispublish;
		$params['studio_user_id'] = $this->loginId;
		$posts = new StudioArticle();
		$lastid = $posts->addArticle($params);
		if($lastid >= 1){
			return response()->json([
	       		'errNo' => ErrorCode::COMMON_OK,
	       		'errMsg' => '发布成功',
	       		'result' => '',
    		]);
		}else{
			return response()->json([
	       		'errNo' => ErrorCode::COMMON_FAIL_ADD_ARTICLE,
	       		'errMsg' => '发布失败',
	       		'result' => '',
    		]);
		}
	}
	/**查看发布后的单个作品
	*input: 工作室id,作品id,及类型,默认为1时间轴,2,文章
	*/
	public function showProduction(Request $request)
	{
		// $studio_id = $request->input('studio_id');
		$craft_id = $request->input('craft_id');
		$type = $request->input('type',1);
		switch ($type) {
			case 2:
				$data = $this->selectArticle($this->studioId,$craft_id);
				break;
			default:
				$data = $this->selectProcess($this->studioId,$craft_id);
				break;
		}
		if(empty($data)){
			return response()->json([
	       		'errNo' => ErrorCode::COMMON_EMPTY_DATA,
	       		'errMsg' => '数据为空',
	       		'result' => '',
    		]);
		}else{
			return response()->json([
	       		'errNo' => ErrorCode::COMMON_OK,
	       		'errMsg' => '数据列表',
	       		'result' => $data,
    		]);
		}
	}
	//删除玉雕
	public function delCraft(Request $request)
	{
		$craft_id = $request->input('craft_id');
		$craft = new Craft();
		$craft_stat = $craft->delCraft($craft_id);
		if($craft_stat)
		{
			return response()->json([
	       		'errNo' => ErrorCode::COMMON_OK,
	       		'errMsg' => '',
	       		'result' => 'true',
    		]);
		}
		return response()->json([
       		'errNo' => ErrorCode::COMMON_CRAFT_DEL_ERROR,
       		'errMsg' => '删除玉雕失败',
       		'result' => 'false',
		]);
	}
	//查询玉件加工过程表
	private function selectProcess($studio_id,$craft_id)
	{
		 $process = new CraftProcess();
		 $img = new CraftImg();
		 $params['studio_id'] = $studio_id;
		 $params['craft_id'] = $craft_id;
 		 $results = $process->selectProcess($params);
 		 if(!empty($results)){
 		 	foreach($results as $kr=>$vr){
 		 		$sub = $img->selectYsImg($vr['process_img'],$studio_id);
 		 	}
 		 	$results['sub'] = $sub;
 		 	return $results;
 		 }else{
 		 	return null;
 		 }
	}
	//查询玉件文章
	private function selectArticle($studio_id,$craft_id)
	{
		$article = new StudioArticle();
		$posts = $article->selectArticle(array('studio_id'=>$studio_id,'craft_id'=>$craft_id));
		if(!empty($posts)){
			return $posts;
		}else{
			return null;
		}
	}
}
?>