<?php
/**
*
* this is yangping's code,the time is 2016.03.01
*/
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Log;
use Session;
use ErrorCode;
use Illuminate\Http\Request;
use App\Http\Models\StudioUser;
use App\Http\UpYun;
use App\Http\Models\sms\SENDSMS;
class IndexController extends Controller
{
	public function index()
    {
    	return "This is Yushifu";
        //return  response()->view('admin/index', []);
    } 
} 
?>