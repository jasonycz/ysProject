<?php
/**
*
* this is txx's code,the time is 2016.03.03
*/
namespace App\Http\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
//玉件加工过程相关Model

class CraftProcess extends Model
{
	//查询过程
	public function selectProcess($params)
	{
		if($params)
		{
			$res = DB::table('craft_process')
                    ->where('studio_id',$params['studio_id'])
                    ->Where('craft_id',$params['craft_id'])
                    ->select('process_class','describe','process_img','created_time')
                    ->get();
            return $res;
		}
		return null;
	}
}

?>