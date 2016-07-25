<?php
/**
*
* this is txx's code,the time is 2016.03.03
*/
namespace App\Http\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class ArticleImg extends Model
{
	protected $table = 'article_img';
	//插入多张图片
	public function insertImgs($studio_user_id,$studio_id,$craft_id,$imgs)
	{
		foreach ($imgs as $key => $value) {
					$this->insert(
							array('studio_user_id' => $studio_user_id, 
								  'studio_id' => $studio_id,
								  'craft_id' => $craft_id,
								  'img_url' => $value
								)
					);
		}
	}
	public function queryImagesByCraftId($craft_id)
	{
		$result = $this->where('craft_id',$craft_id)
				->select('studio_user_id','studio_id','craft_id','img_url')
				->get();
		return $result;
	}
}