
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
	//查询玉石图片
	public function selectYsImg($imgIdStr,$studio_id)
	{
		if($imgIdStr && $studio_id)
		{
			$res = DB::table('craft_img')
                    ->where('studio_id',$studio_id)
                    ->whereIn('img_id',$imgIdStr)
                    ->select('describe','img_url','created_time')
                    ->get();
            return $res;
		}
		return null;
	}
}

?>