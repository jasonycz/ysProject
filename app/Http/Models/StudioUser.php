<?php

namespace App\Http\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class StudioUser extends Model
{
	protected $table = 'studio_users';
	//通过手机号获得用户
	public function getUserByPhone($phone)
	{ 
            $user = DB::table($this->table)
                    ->where('phone',$phone)
                    ->first();
            return  $user; 
	}
	//手机登陆验证
	public function logInCheck($phone,$passwd)
	{ 
		$user = DB::table($this->table)
                ->where([
                            'phone'=>$phone,
                            'pwd'=>$passwd])
                ->first();
        return $user?$user:null; 
	}
	//验证码
	public function insertVerifyCode($data)
	{
		if($data)
		{
			return DB::table('verify_code')
					->insert(
						array(
						'phone' => $data['phone'],
						'verify_code' => $data['verify_code'],
						'created_time' => $data['created_time'],
						)
					);	
		}
		return null;
	}
}