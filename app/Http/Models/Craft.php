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
	/**
	*查询工作室已发布成品
	*input: 工作室ID
	*output: 雕件图片url,雕件id and so on
	*/
	public function queryCraftOfEnd($studioId)
	{
		if($studioId)
		{
			$crafts = DB::table('craft_img')
                    ->where('studio_id',$studioId)
                    ->get();
		}
		return null;
	}
}
?>