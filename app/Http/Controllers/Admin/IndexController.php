<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Log;
use Session;
use ErrorCode;
use Illuminate\Http\Request;
use App\Http\Models\StudioUser;
use App\Http\UpYun;
use App\Http\Models\sms\SENDSMS;
use Swagger\Annotations as SWG;
/** 
 * @SWG\Info( 
 *   title="玉师傅API文档", 
 *   description="版本 0.1", 
 * ) 
 * 
 */  
class IndexController extends Controller
{
	public function index()
    {
    	return "This is Yushifu";
        //return  response()->view('admin/index', []);
    } 
} 
?>