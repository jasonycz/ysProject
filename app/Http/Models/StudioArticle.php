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
	protected $table = 'studio_article';
	public $timestamps = false;
	//查询过程
	public function selectArticle($params)
	{
		if($params)
		{
			$res = $this
                    ->where('studio_id',$params['studio_id'])
                    ->where('craft_id',$params['craft_id'])
                    ->where('ispublish',1)
                    ->select('article_name','author','created_time','content')
                    ->get();
            return $res->toArray();
		}
		return null;
	}
	//查询雕件是否发布文章
	public function isHasArticle($studioid,$craft_id,$aid)
	{
		$article = $this->where('studio_id',$studioid)
					->where('craft_id',$craft_id)
					->where('article_id',$aid)
					->select('article_id')
					->first();
		if($article)
		{
			return $article->toArray();
		}
		return null;
	}
	//查询文章内容
	public function queryContent($aid,$craft_id,$studio_id)
	{
		return $this->where('article_id',$aid)
					->where('studio_id',$studio_id)
					->where('craft_id',$craft_id)
					->select('article_name','author','article_time','content')
					->first()->toArray();
	}
	//新增文章
	public function addArticle($params)
	{
		$res = $this
                ->insert($params);
        return $res;
	}
	//修改文章
	public function updateArticle($aid,$params)
	{
		return $this->where('article_id',$aid)
					->update($params);					
	}
}

?>