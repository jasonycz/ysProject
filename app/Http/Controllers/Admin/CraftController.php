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
use App\Http\UpYun;
class CraftController extends Controller
{
	private $loginId;
	private $studioId;
	private $craft;
	private $craftimg;
	private $post;
	private $process;
	private $upyun;
	public function __construct(Request $request){
		$sessionUser = $request->session()->get('userInfo');
        $this->loginId = $sessionUser['user_id'];
		$this->studioId = $sessionUser['studio_id'];
		// $this->loginId = 1;
		// $this->studioId = 1;
		$this->craft = new Craft();
		$this->craftimg = new Craftimg();
		$this->posts = new StudioArticle();
		$this->process = new CraftProcess();
		$this->upyun = new UpYun(env('UPYUN_AVATAR_BUCKET'),
	        env('UPYUN_USER'), env('UPYUN_PWD'),
	        env('UPYUN_SERVER'), env('UPYUN_TIMEOUT'));
	}
	/**
	*展示工作室所有未发布雕件列表
	* status 6:待发布时间轴，7:待发布软文,,8:都完成待发布
	*/
	public function showAllCraft(Request $request)
	{
		$nocrafts = $this->craft->queryNoFinish(intval($this->studioId),intval($this->loginId));
		if(empty($nocrafts))
		{
			return response()->json([
	       		'errNo' => 0,
	       		'errMsg' => '雕件数为0',
	       		'result' => '',
    		]); 
		} 
		foreach ($nocrafts as $kn => $vn) {
			$artiarr = $this->posts->getaid($vn['craft_id'],intval($this->studioId));
			$vn['aid'] = $artiarr['article_id'];
			$nocrafts[$kn] = $vn;
		}
		return response()->json([
       		'errNo' => 0,
       		'errMsg' => 'true',
       		'result' => $nocrafts,
		]);
	}

	/**已发布成品展示
	*input: 工作室id
	*output: 雕件ID，图片url，雕件的描述信息
	*图片为
	*/
	public function showCraftOfEnd(Request $request)
	{
		$crafts = $this->craft->queryCraftOfEnd(intval($this->studioId),intval($this->loginId));
		if($crafts)
		{
			foreach ($crafts as $kc => $vc) {
				$imgres = $this->craftimg->queryOneImg($this->studioId,$vc['craft_id']);
				$res[$kc] = array('craft_id'=>$vc['craft_id'],'craft_name'=>$vc['craft_name'],'describe'=>$vc['describe'],'img'=>array('url'=>$imgres['img_url'],'imgdesc'=>$imgres['describe']));
			} 
			return response()->json([
	       		'errNo' => 0,
	       		'errMsg' => 'true',
	       		'result' => $res,
			]); 				
		}
		return response()->json([
       		'errNo' => 700011,
       		'errMsg' => '雕件数为0',
       		'result' => array(),
		]); 
	}
	/**
	*雕件文章发布
	*/
	public function publishArticle(Request $request)
	{
		$aid = $request->input('aid');
		$craft_id = $request->input('craft_id');
		$hasArt = $this->posts->isHasArticle(intval($this->studioId),intval($craft_id),intval($aid));
		if(empty($hasArt))
		{
			return response()->json([
	       		'errNo' => -1110,
	       		'errMsg' => '文章参数不合法',
	       		'result' => '',
    		]);
		}
		$upres = $this->posts->updateArticle($aid,array('ispublish'=>1));
		if($upres)
		{
			return response()->json([
	       		'errNo' => ErrorCode::COMMON_OK,
	       		'errMsg' => '修改成功',
	       		'result' => '',
    		]);
		}else{
			return response()->json([
	       		'errNo' => -123,
	       		'errMsg' => '修改失败',
	       		'result' => '',
    		]);
		}
	}
	/**查看发布后的单个作品
	*input: 工作室id,作品id,及类型,默认为1时间轴,2,文章
	*/
	public function showProduction(Request $request)
	{
		$craft_id = $request->input('craft_id',0);
		$type = $request->input('type',1);
		switch ($type) {
			case 2:
				$list = $this->craft->queryTimeImage(intval($this->studioId),intval($craft_id));
				foreach ($list as $kl => $vl) {
					$arr = explode(',',$vl['process_img']);
					$list[$kl]['img'][] = $this->craftimg->queryImages($arr,intval($this->studioId),intval($craft_id));
				}
				break;
			default:
				$list = $this->craft->queryJoinArticle(intval($this->studioId),intval($craft_id));
				break;
		}
		if(empty($list)){
			return response()->json([
       			'errNo' => ErrorCode::COMMON_CRAFT_GET,
       			'errMsg' => '数据为空',
       			'result' => 'false',
			]);
		}
		return response()->json([
       			'errNo' => ErrorCode::COMMON_OK,
       			'errMsg' => '数据列表',
       			'result' => $list,
		]);
	}
	//删除玉雕
	public function delCraft(Request $request)
	{
		$craft_id = $request->input('craft_id',0);
		$craft_stat = $this->craft->delCraft(intval($craft_id));
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
	//发布 雕件时间轴页面上传图片
	public function uploadImage(Request $request){
		write_log($_FILES);
	    try {
	        $fileName = '/upload/images/craft/'.$this->studioId . '-' . str_random(10) . '.jpg';
	        $fp = fopen($_FILES['file']['tmp_name'], 'r');
	        $ret = $this->upyun->writeFile($fileName, $fp, true);
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
	//雕件时间轴页面删除图片
	public function delImage(Request $request){
		write_log($request);
		$imagePath = $request->input('imgurl');
		$imagePath = str_replace(env('UPYUN_AVATAR_DOMAIN'), '', $imagePath);
		try {
	        $ret = $this->upyun->deleteFile($imagePath);
	        return response()->json([
	            'errNo' => 0,
	            'errMsg' => '删除成功',
	            'result' => ''
	        ]);
	    } catch (Exception $e) {
	        return response()->json([
	            'errNo' => $e->getCode(),
	            'errMsg' => $e->getMessage(),
	        ]);
	    }
	}
	//返回该工作室下最后一个雕件id
	public function getLastCid()
	{
		$cid = $this->craft->getCid(intval($this->studioId));
		if(empty($cid))
		{
			return response()->json([
       			'errNo' => ErrorCode::COMMON_CRAFT_GET,
       			'errMsg' => '数据为空',
       			'result' => 'false',
			]);
		}
		return response()->json([
       			'errNo' => ErrorCode::COMMON_OK,
       			'errMsg' => '雕件id',
       			'result' => $cid,
			]);
	}
	//雕件时间轴数据提交
	public function addTimeData(Request $request)
	{
		write_log($_POST);
		//类型与图片最好是数组格式'1'=>array('','','')
		$pClass = $request->input('timeLine');
		$craft_id = $request->input('craft_id');
		// $pid = $request->input('pid');
		$exists = $this->craft->isExists($this->studioId,$craft_id);
		if(empty($exists))
		{
			return response()->json([
		       		'errNo' => -900010,
		       		'errMsg' => '玉件id不合法',
		       		'result' => array(),
	    		]);
		}
		//需要根据前台传的数据，来增加数据
		//需要新增图片及增加时间轴及更改雕件的状态
		/*if(empty($pid) && !isset($pid))
		{*/
		$this->craftimg->deleteData($this->studioId,$craft_id);
		$this->process->deleteData($this->studioId,$craft_id);
		$len = 0;
		foreach ($pClass as $kc => $vc) {
			$imgid = '';
			$timeImg = explode(',', $vc['img']);
			if(!empty($timeImg)){
				foreach ($timeImg as $ki => $vi) {
					$arr['img_url'] = $vi;
					$arr['created_time'] = date('Y-m-d H:i:s',time());
					$arr['studio_id'] = $this->studioId;
					$arr['studio_user_id'] = $this->loginId;
					$arr['craft_id'] = $craft_id;
					$imgid .= $this->craftimg->addImages($arr).',';
				}
			}
			$parr['process_class'] = $kc+1;
			$parr['process_name'] = $vc['name'];
			$parr['describe'] = $vc['describe'];
			$parr['process_img'] = rtrim(',',$imgid);
			$parr['studio_user_id'] = $this->loginId;
			$parr['studio_id'] = $this->studioId;
			$parr['craft_id'] = $craft_id;
			$lastid = $this->process->addProcess($parr);	
			if($lastid >= 1){
				$len++;
			}
		}
		if($len == count($pClass)){
			switch ($exists['status']) {
				case 6:
					$arr['status'] = 9;
					break;
				case 8:
					$arr['status'] = 9;
					break;
				default:
					$arr['status'] = 6;
					break;
			}
			$this->craft->saveStatus($this->studioId,$craft_id,$arr);
			return response()->json([
       			'errNo' => 0,
       			'errMsg' => '成功',
       			'result' => array(),
			]);
		}else{
			return response()->json([
       			'errNo' => -900020,
       			'errMsg' => '失败',
       			'result' => array(),
			]);
		}
		/*}else
		{
			$this->craftimg->deleteData($this->studioId,$craft_id);
			$this->process->deleteData($this->studioId,$craft_id);
			$len = 0;
			foreach ($pClass as $kc => $vc) {
				$imgid = '';
				foreach ($vc['img'] as $ki => $vi) {
					$arr['img_url'] = $vi;
					$arr['created_time'] = date('Y-m-d H:i:s',time());
					$arr['studio_id'] = $this->studioId;
					$arr['studio_user_id'] = $this->loginId;
					$arr['craft_id'] = $craft_id;
					$imgid .= $this->craftimg->addImages($arr).',';
				}
				$parr['process_class'] = $kc+1;
				$parr['describe'] = $kc+1;
				$parr['process_img'] = rtrim(',',$imgid);
				$parr['studio_user_id'] = $this->loginId;
				$parr['studio_id'] = $this->studioId;
				$parr['craft_id'] = $craft_id;
				$lastid = $this->process->addProcess($parr);	
				if($lastid >= 1){
					$len++;
				}
			}
			if($len == count($pClass)){
				return response()->json([
	       			'errNo' => 0,
	       			'errMsg' => '成功',
	       			'result' => array(),
				]);
			}else{
				return response()->json([
	       			'errNo' => -900020,
	       			'errMsg' => '失败',
	       			'result' => array(),
				]);
			}
		}*/
	}
	//雕件软文数据提交及修改
	public function addArticle(Request $request)
	{
		write_log($_POST);
		$aid = $request->input('aid');
		$title = $request->input('title');
		$author = $request->input('author');
		$createDate = $request->input('createDate');
		$content = $request->input('content');
		$craft_id = $request->input('craft_id',0);
		$ispublish = $request->input('publish',0);
		$measurement = $request->input('measurement');
		$type = $request->input('type');
		$imgurl = $request->input('imgurl');
		$craftname = $request->input('craftname');
		//需要增加必填项的判断
		$params['article_name'] = $title;
		$params['author'] = $author;
		$params['created_time'] = date('Y-m-d H:i:s',time());
		if(empty($createDate)){
			$params['article_time'] = date('Y-m-d H:i:s',time());	
		}else{
			$params['article_time'] = $createDate;
		}
		$params['img_url'] = $imgurl;
		$exists = $this->craft->isExists($this->studioId,$craft_id);
		if(empty($exists))
		{
			return response()->json([
		       		'errNo' => 400013,
		       		'errMsg' => '玉件id不合法',
		       		'result' => '',
	    		]);
		}
		$params['content'] = $content;
		$data['measurement'] = $measurement;
		$data['type'] = $type;
		$data['craft_name'] = $craftname;
		if(empty($aid) && !isset($aid)){
			$params['studio_id'] = $this->studioId;
			$params['craft_id'] = $craft_id;
			$params['ispublish'] = $ispublish;
			$params['studio_user_id'] = $this->loginId;
			$craft = $this->craft->updateCraft($craft_id,$data);
			$lastid = $this->posts->addArticle($params);
			if($lastid >= 1){
				if($ispublish == 1){
					switch ($exists['status']) {
							case 7:
								$arr['status'] = 9;
								break;
							default:
								$arr['status'] = 6;
								break;
						}
					}else{
						switch ($exists['status']) {
							case 7:
								$arr['status'] = 8;
								break;
							default:
								$arr['status'] = 6;
								break;
						}
					}
				$this->craft->saveStatus(intval($this->studioId),intval($craft_id),$arr);
				return response()->json([
		       		'errNo' => ErrorCode::COMMON_OK,
		       		'errMsg' => '发布成功',
		       		'result' => array('aid'=>$lastid),
	    		]);
			}else{
				return response()->json([
		       		'errNo' => ErrorCode::COMMON_FAIL_ADD_ARTICLE,
		       		'errMsg' => '发布失败',
		       		'result' => '',
	    		]);
			}
		}else{
			$hasArt = $this->posts->isHasArticle(intval($this->studioId),intval($craft_id),intval($aid));
			if(empty($hasArt))
			{
				return response()->json([
		       		'errNo' => 400012,
		       		'errMsg' => '修改文章时,参数不合法',
		       		'result' => '',
	    		]);
			}
			$craft = $this->craft->updateCraft($craft_id,$data);
			$upid = $this->posts->updateArticle($aid,$params);
			if($upid)
			{
				return response()->json([
		       		'errNo' => ErrorCode::COMMON_OK,
		       		'errMsg' => '修改成功',
		       		'result' => '',
	    		]);
			}else{
				return response()->json([
		       		'errNo' => 11,
		       		'errMsg' => '修改失败',
		       		'result' => '',
	    		]);
			}
		}
	}
	//雕件时间轴修改页面
	public function modifyTimeData(Request $request)
	{
		$craft_id = $request->input('craft_id',0);
		$plist = $this->process->selectOne($this->studioId,$craft_id);
		if(!empty($plist))
		{
			foreach ($plist as $kp => $vp) {
				$tmparr = explode(',', $vp['process_img']);
				$imgarr = $this->craftimg->selectYsImg($tmparr,$this->studioId,$craft_id);
				foreach ($imgarr as $ka=> $va) {
					$imgtmp[] = $va['img_url'];
				}
				$results[$kp] = array('name'=>$vp['process_name'],'describe'=>$vp['describe'],'img'=>$imgtmp);
			}
			$lastData['id'] = $craft_id;
			$lastData['timeLine'] = $results;
			return response()->json([
		     	'errNo' => 0,
		       	'errMsg' => '数据列表',
		       	'result' => $lastData
	    	]);
		}else
		{	
			return response()->json([
		     	'errNo' => 0,
		       	'errMsg' => '数据列表',
		       	'result' => array()
	    	]);
		}
	}
	//雕件软文修改页面
	public function modifyArticle(Request $request)
	{
		$aid = $request->input('aid',0);
		$craft_id = $request->input('craft_id',0);
		if(isset($aid) && !empty($aid)){
			$res = $this->posts->queryContent($aid,$craft_id,$this->studioId);
			if(empty($res)){
				return response()->json([
		       		'errNo' => 800010,
		       		'errMsg' => '文章id不合法',
		       		'result' => array(),
	    		]);
			}else{
				return response()->json([
		       		'errNo' => 0,
		       		'errMsg' => '文章内容',
		       		'result' => $res,
	    		]);
			}
		}else{
			$tmp['title'] = '';
			$tmp['createdate'] = '';
			$tmp['content'] = '';
			$tmp['author'] = '';
			$tmp['craft_id'] = '';
			$tmp['content'] = '';
			$tmp['measurement'] = '';
			$tmp['type'] = '';
			$tmp['aid'] = '';
			$tmp['imgurl'] = '';
			return response()->json([
		       		'errNo' => 0,
		       		'errMsg' => '文章内容',
		       		'result' => $tmp,
	    	]);
		}
	}
}
?>