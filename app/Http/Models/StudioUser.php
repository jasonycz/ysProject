<?php

namespace App\Http\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class StudioUser extends Model
{
	protected $table = 'studio_users';
	//通过user_id获取用户
	public function getUserById($userId)
	{
		  $user = DB::table($this->table)
                    ->where('id',$userId)
                    ->first();
            return  $user; 
	}
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
	//插入验证码
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
	//获取验证码
	public function getVerifyCode($data)
	{
		if($data)
		{
			return DB::table($this->table)
				->where('phone',$data['phone'])
				->orderBy('id','desc')
				->first();
		}else
		{
			return null;
		}
	}

	//重置密码
	public function resetPasswordPhone($data)
    {
        $salt = $this->saltCode();
        return $userInfo = DB::table($this->table)
                ->where('user_name', $data['phone'])
                ->update(
                array(
                    'password' => md5(crypt($data['password'], $salt)),
                    'salt' => $salt,
                )
            );
    }
    //检查密码是否正确
    public function checkPassword($userId,$password)
    {
        if ($usr = $this->getUserByName($userName)) {
            $user = DB::table($this->table)
                ->where([
                    'id' => $userId,
                    'password' => md5(crypt($password, $usr->salt))
                ])
                ->first();
            if($user)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
}