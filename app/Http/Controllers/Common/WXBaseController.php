<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WXBaseController extends Controller
{
    protected $appid;
    protected $secret;
    const ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=';
    public function __construct()
    {
        $this->appid  = config('wx.minPro.appid');
        $this->secret = config('wx.minPro.secret');
    }

    /**
     * 获取访问 微信API 接口凭证 token
     * @param flag  是否从从缓存中获取
     * @return |null
     */
    public function getAccessToken($flag = true)
    {

        if($flag || !Redis::get('appid:'.$this->appid.':access_token')) {
            $url = self::ACCESS_TOKEN_URL.$this->appid.'&secret='.$this->secret;
            # 返回请求信息 access_token
            $result = $this->http_get($url);
            $result = json_decode($result,true);

            if (!$result) {
                return null;
            }
            else {
                Redis::setex('appid:'.$this->appid.':access_token', 7200, $result['access_token']);
                return $result['access_token'];
            }
        }

        return Redis::get('appid:'.$this->appid.':access_token');
    }



}
