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
		if($phone)
        {
            $user = DB::table($this->table)
                    ->where('phone',$phone)
                    ->first();
            return  $user;
        }
        return null;
	}
	//手机登陆验证
	public function logInCheck($phone,$passwd)
	{
		if($phone&&$passwd)
		{
			$user = DB::table($this->table)
                    ->where('phone',$phone)
                    ->first();
            if($user)
            {
            	if($user->pwd == $passwd)
            	{
            		return $user;
            	}
            	return null;  //密码错误
            }
            return null;   //手机号不存在
		}
		return null;  //手机号或者密码为空
	}
}