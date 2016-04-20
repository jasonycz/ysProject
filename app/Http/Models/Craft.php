<?php
/**
*
* this is yangping's code,the time is 2016.03.02
*/
namespace App\Http\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

//雕件相关Model
class Craft extends Model
{
	protected $table = 'craft';
	public $timestamps = false;

	//查询制作完成未发布雕件
	public function queryNoFinish($studioId,$uid)
	{
		return $noFinish = $this
					->where('studio_id',$studioId)
					->where('studio_user_id',$uid)
					->whereIn('status',[6,7,8])
					->select('craft_name','status','craft_id')
					->orderBy('created_time','desc')
					->get()->toArray();
	}
	//手机端查询文章预览数据
	public function queryJoinArticle($studioid,$craftid)
	{
		$info = $this->leftJoin('studio_article', 'craft.craft_id', '=', 'studio_article.craft_id')
                    ->where('craft.craft_id' , $craftid)
                    ->where('craft.studio_id',$studioid)
                    ->where('craft.status',9)
                    ->where('craft.is_del',1)
                    ->select('craft.craft_id','studio_article.author','studio_article.article_name','craft.created_time','studio_article.content','craft.craft_name','craft.measurement','craft.type','studio_article.img_url')
                    ->orderBy('craft.created_time', 'desc')
                    ->get();
        return $info->toArray();
	}
	//手机端查询时间轴预览数据
	public function queryTimeImage($studioid,$craftid)
	{
		$info = $this->leftJoin('craft_process', 'craft.craft_id', '=', 'craft_process.craft_id')
                    ->where('craft.craft_id' , $craftid)
                    ->where('craft.studio_id',$studioid)
                    ->where('craft.status',9)
                    ->where('craft.is_del',1)
                    ->select('craft.craft_id','craft_process.process_id','craft_process.process_class','craft_process.process_img','craft.created_time','craft_process.process_name','craft_process.describe','craft.craft_name')
                    ->orderBy('craft.created_time', 'desc')
                    ->get();
        return $info->toArray();
	}
	//返回雕件id
	public function getCid($studioid)
	{
		return $this->where('studio_id',$studioid)
					->where('status',5)
					->select('craft_id')
					->first();
	}
	/**
	*查询工作室已发布成品
	*input: 工作室ID
	*output: 雕件图片url,雕件id and so on
	*/
	public function queryCraftOfEnd($studioId,$uid=0)
	{
		$crafts = $this
				->select('craft_id','craft_name','material','describe')
                ->where('studio_id',$studioId)
                // ->where('studio_user_id',$uid)
                ->where('status',9)
                ->where('is_del',1)
                ->select('craft_id','craft_name','describe')
                ->orderBy('created_time','desc')
                ->get();
        return $crafts->toArray();
	}
	/**
	*删除单个玉雕
	*/
	public function delCraft($craftId)
	{
		if($craftId)
		{
			$craft = $this
					 ->where('craft_id',$craftId)
					 ->update(['is_del'=>0]);
			return $craft->toArray();
		}
		return null;
	}
	//查询参数是否合法
	public function isExists($studioid,$craft_id)
	{
		$craft =  $this->where('studio_id',$studioid)
					 ->where('craft_id',$craft_id)
					 ->select('craft_id','status')
					 ->first();
		if($craft)
		{
			return $craft->toArray();
		}
		return null;
	}
	//更改雕件状态
	public function saveStatus($studioid,$craft_id,$data)
	{
		return $this->where('studio_id',$studioid)
					->where('craft_id',$craft_id)
					->update($data);
	}
	//更新玉石字段
	public function updateCraft($craft_id,$data)
	{
		return $this->where('craft_id',$craft_id)
					->update($data);
	}
}
?>