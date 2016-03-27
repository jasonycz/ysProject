<?php

namespace App\Http\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class StudioUser extends Model
{
	protected $table = 'studio_users';
    public $codeLetter = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
    public $codeNumber = array("0","1","2","3","4","5","6","7","8","9");
    public function getRandomCode($len)
    {
        $charsLen = count($this->codeLetter) - 1; 
        shuffle($this->codeLetter);   
        $output = ""; 
        for ($i=0; $i<$len; $i++) 
        { 
            $output .= $this->codeLetter[mt_rand(0, $charsLen)]; 
        }  
        return $output;  
    }
	public function saltCode($len = 6)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        mt_srand((double)microtime() * 1000000 * getmypid());
        $code = '';
        while (strlen($code) < $len)
            $code .= substr($chars, (mt_rand() % strlen($chars)), 1);
        return $code;
    }
    //创建用户
    public function createUser($data)
    {
    	$salt = $this->saltCode();
    	if($data)
    	{
    		$user = DB::table($this->table)->insert(
    				array(
                    	'studio_id' => $data['studio_id'],
                    	'phone' => $data['tel'],
                    	'user_name' => $data['user_name'],
                    	'pwd' => crypt($data['pwd'], $salt),
                    	'salt' => $salt,
                    	'is_admin' => 0
                		)
            		);
    		return $user;
    	}
    	return null;
    }
    //根据user_name 检查用户是否已存在
    public function checkExists($uname)
    {
        $user = DB::table($this->table)
                ->where('user_name',$uname)
                ->first();
        return  $user; 
    }
	//通过user_id获取用户
	public function getUserById($userId)
	{
		  $user = DB::table($this->table)
                    ->where('user_id',$userId)
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
		 if($em = $this->getUserByPhone($phone))
		 {
			$user = DB::table($this->table)
                ->where([
                            'phone'=>$phone,
                            'pwd'=> crypt($passwd, $em->salt)
                        ])
                ->first();

                return $user?$user:null; 
		 } 
        return null;
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
			return DB::table('verify_code')
				->where('phone',$data['phone'])
				->orderBy('id','desc')
				->first();
		}else
		{
			return null;
		}
	}

	//重置密码
	public function resetPassword($data)
    {
        $salt = $this->saltCode();
        return $userInfo = DB::table($this->table)
                ->where('user_id', $data['user_id'])
                ->update(
                array(
                    'pwd' => crypt($data['new_password'], $salt),
                    'salt' => $salt,
                )
            );
    }
    //检查密码是否正确
    public function checkPassword($userId,$password)
    {
        if ($usr = $this->getUserById($userId)) {
            $user = DB::table($this->table)
                ->where([
                    'user_id' => $userId,
                    'pwd' => crypt($password, $usr->salt)
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
    //更新登录次数
    public function updateLoginNum($userId,$nums)
    {
            return $userInfo = DB::table($this->table)
                ->where('user_id', $userId)
                ->update(
                array(
                    'login_num' => $nums,
                )
            );
    }
}