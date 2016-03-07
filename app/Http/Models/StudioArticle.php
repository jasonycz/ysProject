<?php
/**
*
* this is txx's code,the time is 2016.03.03
*/
namespace App\Http\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
//玉件文章相关Model

class StudioArticle extends Model
{
	//查询过程
	public function selectArticle($params)
	{
		if($params)
		{
			$res = DB::table('studio_article')
                    ->where('studio_id',$params['studio_id'])
                    ->andWhere('craft_id',$params['craft_id'])
                    ->select('article_name','author','created_time','content')
                    ->get();
            return $res;
		}
		return null;
	}
	//文章入库
	public function addArticle($params)
	{
		$res = DB::table('studio_article')
                    ->insert($params)
        return $res;
	}
}

?>