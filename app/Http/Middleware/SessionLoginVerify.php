<?php

namespace App\Http\Middleware;

use Closure;
use ErrorCode;

class SessionLoginVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $sessionUser = $request->session()->get('userInfo');

        if(!$sessionUser)
        {
            return response()->json([
                    'errNo'=>ErrorCode::COMMON_NOT_LOGIN,
                    'errMsg'=>'用户未登录，请先登录',
                    'result'=>null,
            ]);
        } 
            $sessionUser =(array)$sessionUser;
            $userId = array_key_exists('user_id',$sessionUser) ? $sessionUser['user_id']; 
            $studioId = array_key_exists('studio_id',$sessionUser) ? $sessionUser['studio_id'];
        $request['user_id'] = $userId; //记录用户id
        $request['studio_id'] = $studioId; //记录工作室id
        return $next($request);
    }
}