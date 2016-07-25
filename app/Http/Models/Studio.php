<?php
/**
*
* this is yangping's code,the time is 2016.03.03
*/
namespace App\Http\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
//工作室相关Model

class Studio extends Model
{
	//查询工作室是否存在
	public function queryStudioInfo($data)
	{
		if($data)
		{
			$res = DB::table('studio')
					->where('tel',$data['tel'])
					->first();
			return $res;
		}
		return null;
	}
	//填写工作室申请信息
	public function submitStudioInfo($data)
	{
		if($data)
		{
			$res = DB::table('studio')
                    ->insert(
                    	array(
                    		'name' => $data['name'],
                    		'tel' => $data['tel'],
                    		'address' => $data['address'],
                    		'describe' => $data['describe']
                    		)
                    	);
            return $res;
		}
		return null;
	}
	//查询工作室下的手机号
	public function getOneField($studioid){
		return DB::table('studio')->select('tel')->first();
	}

}

?>