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
	protected $table = 'craft_process'; 
	//查询过程
	public function selectProcess($params)
	{
		if($params)
		{
			$res = $this
                    ->where('studio_id',$params['studio_id'])
                    ->Where('craft_id',$params['craft_id'])
                    ->select('process_class','describe','process_img','created_time')
                    ->get()->toArray;
            return $res;
		}
		return null;
	}
	//查询单个雕件过程图片
	public function selectOne($studio_id,$craft_id)
	{
		return $this->where('studio_id',$studio_id)
					->where('craft_id',$craft_id)
					->whereIn('process_class',[1,2,3,4])
					->select('process_class','process_name','process_img','describe')
					->get()->toArray();
	}
	//插入数据
	public function addProcess($data){
		return $this->insert($data);
	}
	//删除
	public function deleteData($studioid,$craft_id){
		return $this->where('studio_id',$studioid)
					->where('craft_id',$craft_id)
					->delete();
	}
}

?>