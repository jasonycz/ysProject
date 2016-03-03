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
                    		'address' => $data['address']
                    		)
                    	);
            return $res;
		}
		return null;
	}

}

?>