<?php
/**
*
* 扫描二维码进入雕件溯源页面
*/
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;
use Log; 
use ErrorCode;
use Illuminate\Http\Request;
use App\Http\Models\Craft;
use App\Http\Models\StudioArticle;
use App\Http\Models\CraftProcess;
use App\Http\Models\CraftImg;
class CraftController extends Controller
{
	private $craft;
	private $process;
	public function __construct(Request $request){
		$this->craft = new Craft();
		$this->cimg = new CraftImg();
	}
	//工作室id,雕件id,类型type(时间轴1或软文2)
	public function showDetail(Request $request)
	{
		$type = $request->input('type',1);
		$studioid = $request->input('studioid',0);
		$craftid = $request->input('craftid',0);
		switch ($type) {
			case 2: 
				$list = $this->craft->queryTimeImage(intval($studioid),intval($craftid));
				foreach ($list as $kl => $vl) {
					$arr = explode(',',$vl['process_img']);
					$list[$kl]['img'][] = $this->cimg->queryImages($arr,intval($studioid),intval($craftid));
				}
				break;
			default:
				$list = $this->craft->queryJoinArticle(intval($studioid),intval($craftid));
				break;
		}
		if(empty($list)){
			return response()->json([
       			'errNo' => 0,
       			'errMsg' => '数据为空',
       			'result' => 'false',
			]);
		}
		return response()->json([
       			'errNo' => 200,
       			'errMsg' => '数据为空',
       			'result' => $list,
		]);
	}
	//需要引入分享js代码
	public function showWxSdk()
	{
		//分享需要的参数
		$appid = Config::get('weixin.APPID');
		$appsecret = Config::get('weixin.APPSECRET');
        $jsapiTicket = $this->getJsApiTicket();
        $tmp_REQUEST_URI = $_SERVER[REQUEST_URI];
        $tick_url = "http://$_SERVER[HTTP_HOST]$tmp_REQUEST_URI";
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$tick_url";
        $signature = sha1($string);

		$shareScript = <<<EOF
		<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
		<script>
		    wx.config({
		      debug: true,
		      appId: "$appid",
		      timestamp: $timestamp,
		      nonceStr: "$nonceStr",
		      signature: "$signature",
		      jsApiList: [
		        //'checkJsApi',
		        'onMenuShareTimeline',
		        'onMenuShareAppMessage',
		        'onMenuShareQQ',
		        'closeWindow', 
		      ]
		      });
		          
		     wx.ready(function () {
		       //调用 API
		       // wx.checkJsApi({
		       //   jsApiList: [
		       //     'checkJsApi',         
		       //     'onMenuShareTimeline',
		       //     'onMenuShareAppMessage',
		       //     'onMenuShareQQ' 
		       //   ],
		       //   success: function (res) {
		       //     alert(JSON.stringify(res));
		       //   }
		       // });
		                    
		        //分享给朋友
		      wx.onMenuShareAppMessage({
		          title: window.shareData.tTitle,
		          desc: window.shareData.tContent,
		          link: window.shareData.sendFriendLink,
		          imgUrl: window.shareData.imgUrl,
		          trigger: function (res) {
		//            alert('用户点击发送给朋友');
		          },
		          success: function (res) {
		          },
		          cancel: function (res) {
		//            alert('已取消');
		          },
		          fail: function (res) {
		//            alert(JSON.stringify(res));
		          }
		     });

		          
		        //分享到朋友圈
		      wx.onMenuShareTimeline({
		          title: window.shareData.tTitle,
		          link: window.shareData.timeLineLink,
		          imgUrl: window.shareData.imgUrl,       
		        trigger: function (res) {
		//          alert('用户点击分享到朋友圈');
		        },
		        success: function (res) {
		        },
		        cancel: function (res) {
		//          alert('已取消');
		        },
		        fail: function (res) {
		//          alert(JSON.stringify(res));
		        }
		      });

		    
		        //分享到qq
		      wx.onMenuShareQQ({
		          title: window.shareData.tTitle,
		          desc: window.shareData.tContent,
		          link: window.shareData.weiboLink,
		          imgUrl: window.shareData.imgUrl,
		        trigger: function (res) {
		//          alert('用户点击分享到QQ');
		        },
		        complete: function (res) {
		//          alert(JSON.stringify(res));
		        },
		        success: function (res) {
		        },
		        cancel: function (res) {
		//          alert('已取消');
		        },
		        fail: function (res) {
		//          alert(JSON.stringify(res));
		        }
		      });
		                    
		     });
		          
		  wx.error(function(res){   
		      // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
		      //alert(res.errMsg);    
		  });
		</script>
EOF;
	return $shareScript;
	}
	private function createNonceStr($length = 16) {
	    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	    $str = "";
	    for ($i = 0; $i < $length; $i++) {
	      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	    }
	    return $str;
  	}
  	private function getJsApiTicket() {
  		$ticketFile = './resources/weixin/ticketfile';
	    $data = json_decode(file_get_contents($ticketFile));
	    if ($data->expire_time < time()) {
	      $accessToken = $this->getAccessToken();
	      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
	      $res = json_decode($this->curlGetInfo($url));
	      $ticket = $res->ticket;
	      if ($ticket) {
		        $data->expire_time = time() + 7000;
		        $data->jsapi_ticket = $ticket;
		        file_put_contents($ticketFile,json_encode($data),LOCK_EX);
	      }
	    } else {
	      $ticket = $data->jsapi_ticket;
	    }
	    return $ticket;
  	}
  	private function getAccessToken() {
  		$accessTokenFile = './resources/weixin/accesstokenfile';;
	    $data = json_decode(file_get_contents($accessTokenFile));
	    if ($data->expire_time < time()) {
	      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;      
	      $res = json_decode($this->curlGetInfo($url));
	      $access_token = $res->access_token;
	      if ($access_token) {
	         	$data->expire_time = time() + 7000;
	         	$data->access_token = $access_token;
	         	file_put_contents($accessTokenFile,json_encode($data),LOCK_EX);
	      }
	    } else {
	        $access_token = $data->access_token;
	    }
	    return $access_token;
  	}
  	//curl抓取网页
    private function curlGetInfo($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         
        $info = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Errno'.curl_error($ch);
        }
        
        return $info;
    }
	//查看工作室下所有已发布作品  工作室id
	public function showAllWorks(Request $request)
	{
		$studioid = $request->input('studioid',0);
		$alllist = $this->craft->queryCraftOfEnd(intval($studioid));
		if(empty($alllist)){
			return response()->json([
       			'errNo' => 0,
       			'errMsg' => '数据为空',
       			'result' => 'false',
			]);
		}
		foreach ($alllist as $kc => $vc) {
			$imgres = $this->cimg->queryOneImg($this->studioId,$vc['craft_id']);
			$res[$kc] = array('craft_id'=>$vc['craft_id'],'craft_name'=>$vc['craft_name'],'describe'=>$vc['describe'],'img'=>array('url'=>$imgres['img_url'],'imgdesc'=>$imgres['describe']));
		} 
		return response()->json([
       			'errNo' => 200,
       			'errMsg' => '数据为空',
       			'result' => $res,
		]);
	}
}
?>