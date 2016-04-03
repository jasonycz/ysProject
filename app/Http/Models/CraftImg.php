<?php
/**
*
* this is txx's code,the time is 2016.03.03
*/
namespace App\Http\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
//玉件加工过程相关Model

class CraftImg extends Model
{
	protected $table = 'craft_img';
	//查询玉石图片
	public function selectYsImg($imgIdStr,$studio_id,$craft_id)
	{
		if($imgIdStr && $studio_id)
		{
			$res = $this
                    ->where('studio_id',$studio_id)
                    ->where('craft_id',$craft_id)
                    ->whereIn('img_id',$imgIdStr)
                    ->select('describe','img_url','created_time')
                    ->get()->toArray();
            return $res;
		}
		return null;
	}
	//根据工作室id和玉石id，图片倒叙，选取最后一种图片
	public function queryOneImg($studioid,$craftid)
	{
		return $this->where('studio_id',$studioid)
					->where('craft_id',$craftid)
					->select('describe','img_url')
					->orderBy('created_time','desc')
					->first()->toArray();
	}
	//手机端查询时间轴的图片
	public function queryImages($imgidstr,$studioid,$craftid)
	{
		return $this->whereIn('img_id',$imgidstr)
					->where('studio_id',$studioid)
					->where('craft_id',$craftid)
					->select('describe','img_url')
					->get()->toArray();
	}
}

?>