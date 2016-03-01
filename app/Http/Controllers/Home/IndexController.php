<?php 

namespace App\Http\Controllers\Home;

use Request;

class IndexController extends Controller
{
    /**
     * 博客首页
     */
    public function index()
    {
         return view('welcome');
    }

    /**
     * 文章内页
     */
    public function detail()
    {
        die('detail');
    }

}